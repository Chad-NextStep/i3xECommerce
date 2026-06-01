<?php

declare(strict_types=1);

require_once ROOT_DIR . '/vendor/autoload.php';

$stripe_key = env('STRIPE_SECRET_KEY');
$webhook_secret = env('STRIPE_WEBHOOK_SECRET');

if ($stripe_key === '' || $webhook_secret === '') {
    json_error('Stripe is not configured', 500);
}

\Stripe\Stripe::setApiKey($stripe_key);

$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);
} catch (\UnexpectedValueException $e) {
    json_error('Invalid payload', 400);
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    json_error('Invalid signature', 400);
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $cart_json = $session->metadata->cart_json ?? '{}';
    $cart = json_decode($cart_json, true);

    if (!is_array($cart) || empty($cart)) {
        json_response(['status' => 'ignored', 'reason' => 'empty cart']);
    }

    // Decrement inventory with file locking
    $inventory_path = ROOT_DIR . '/data/inventory.json';
    $fp = fopen($inventory_path, 'r+');

    if ($fp && flock($fp, LOCK_EX)) {
        $contents = stream_get_contents($fp);
        $inventory = json_decode($contents, true) ?? [];

        foreach ($cart as $sku => $quantity) {
            if (isset($inventory[$sku])) {
                $inventory[$sku] = max(0, $inventory[$sku] - $quantity);
            }
        }

        fseek($fp, 0);
        ftruncate($fp, 0);
        fwrite($fp, json_encode($inventory, JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    // Save order record
    $orders_dir = ROOT_DIR . '/orders';
    if (!is_dir($orders_dir)) {
        mkdir($orders_dir, 0755, true);
    }

    $order = [
        'id'                 => $session->id,
        'payment_intent'     => $session->payment_intent,
        'customer_email'     => $session->customer_details->email ?? null,
        'amount_total'       => $session->amount_total,
        'currency'           => $session->currency,
        'cart'               => $cart,
        'created_at'         => date('c'),
    ];

    file_put_contents(
        $orders_dir . '/' . $session->id . '.json',
        json_encode($order, JSON_PRETTY_PRINT)
    );

    json_response(['status' => 'fulfilled']);
}

json_response(['status' => 'ignored']);
