<?php

declare(strict_types=1);

// Cart stored in $_SESSION['cart'] as [ sku => quantity, ... ]

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$method = get_request_method();

switch ($method) {
    case 'GET':
        handle_cart_get();
        break;
    case 'POST':
        handle_cart_add();
        break;
    case 'PUT':
        handle_cart_update();
        break;
    case 'DELETE':
        handle_cart_remove();
        break;
    default:
        json_error('Method not allowed', 405);
}

function enrich_cart(): array
{
    $cart = $_SESSION['cart'] ?? [];
    $items = [];
    $total_cents = 0;

    foreach ($cart as $sku => $quantity) {
        $found = find_product_by_sku($sku);
        if (!$found) {
            continue;
        }
        $product = $found['product'];
        $variant = $found['variant'];
        $stock = get_stock($sku);
        $line_total = $variant['price_cents'] * $quantity;
        $total_cents += $line_total;

        $items[] = [
            'sku'            => $sku,
            'product_name'   => $product['name'],
            'variant_label'  => $variant['label'],
            'price_cents'    => $variant['price_cents'],
            'currency'       => $product['currency'],
            'quantity'       => $quantity,
            'line_total'     => $line_total,
            'stock'          => $stock,
            'image'          => $product['images'][0] ?? null,
            'product_slug'   => $product['slug'],
        ];
    }

    return [
        'items'       => $items,
        'item_count'  => array_sum($cart),
        'total_cents' => $total_cents,
    ];
}

function handle_cart_get(): void
{
    json_response(enrich_cart());
}

function handle_cart_add(): void
{
    $body = get_json_body();
    $sku = $body['sku'] ?? '';
    $quantity = (int)($body['quantity'] ?? 1);

    if ($sku === '' || $quantity < 1) {
        json_error('Invalid sku or quantity');
    }

    $found = find_product_by_sku($sku);
    if (!$found) {
        json_error('Product not found');
    }

    $current_qty = $_SESSION['cart'][$sku] ?? 0;
    $requested = $current_qty + $quantity;
    $stock = get_stock($sku);

    if ($requested > $stock) {
        json_error("Insufficient stock. Available: {$stock}", 409);
    }

    $_SESSION['cart'][$sku] = $requested;
    json_response(enrich_cart());
}

function handle_cart_update(): void
{
    $body = get_json_body();
    $sku = $body['sku'] ?? '';
    $quantity = (int)($body['quantity'] ?? 0);

    if ($sku === '') {
        json_error('SKU is required');
    }

    if ($quantity < 1) {
        unset($_SESSION['cart'][$sku]);
        json_response(enrich_cart());
        return;
    }

    $found = find_product_by_sku($sku);
    if (!$found) {
        json_error('Product not found');
    }

    $stock = get_stock($sku);
    if ($quantity > $stock) {
        json_error("Insufficient stock. Available: {$stock}", 409);
    }

    $_SESSION['cart'][$sku] = $quantity;
    json_response(enrich_cart());
}

function handle_cart_remove(): void
{
    $body = get_json_body();
    $sku = $body['sku'] ?? '';

    if ($sku === '') {
        json_error('SKU is required');
    }

    unset($_SESSION['cart'][$sku]);
    json_response(enrich_cart());
}
