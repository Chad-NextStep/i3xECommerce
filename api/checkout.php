<?php

declare(strict_types=1);

if (get_request_method() !== 'POST') {
    json_error('Method not allowed', 405);
}

require_once ROOT_DIR . '/vendor/autoload.php';

$stripe_key = env('STRIPE_SECRET_KEY');
if ($stripe_key === '') {
    json_error('Stripe is not configured', 500);
}

\Stripe\Stripe::setApiKey($stripe_key);

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    json_error('Cart is empty');
}

// Validate inventory for all items
$inventory = load_inventory();
$line_items = [];

foreach ($cart as $sku => $quantity) {
    $found = find_product_by_sku($sku);
    if (!$found) {
        json_error("Product not found for SKU: {$sku}");
    }

    $stock = $inventory[$sku] ?? 0;
    if ($quantity > $stock) {
        json_error("Insufficient stock for {$found['product']['name']} ({$found['variant']['label']}). Available: {$stock}", 409);
    }

    $line_items[] = [
        'price_data' => [
            'currency'     => $found['product']['currency'],
            'unit_amount'  => $found['variant']['price_cents'],
            'product_data' => [
                'name'        => $found['product']['name'] . ' — ' . $found['variant']['label'],
                'description' => $found['product']['short_description'],
            ],
        ],
        'quantity' => $quantity,
    ];
}

$base_url = env('BASE_URL', 'http://localhost:8000');

// Serialize cart into metadata so webhook can process even if session expired
$cart_json = json_encode($cart);

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items'           => $line_items,
        'mode'                 => 'payment',
        'success_url'          => $base_url . '/checkout/success/?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'           => $base_url . '/checkout/cancel/',
        'metadata'             => [
            'cart_json' => $cart_json,
        ],
    ]);

    json_response(['checkout_url' => $session->url]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    json_error('Stripe error: ' . $e->getMessage(), 500);
}
