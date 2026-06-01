<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

function load_env(): void
{
    $path = ROOT_DIR . '/.env';
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

function env(string $key, string $default = ''): string
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

function load_json(string $filename): array
{
    $path = ROOT_DIR . '/data/' . $filename;
    if (!file_exists($path)) {
        return [];
    }
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

function load_products(): array
{
    return load_json('products.json');
}

function load_inventory(): array
{
    return load_json('inventory.json');
}

function load_categories(): array
{
    return load_json('categories.json');
}

function find_product_by_sku(string $sku): ?array
{
    $products = load_products();
    foreach ($products as $product) {
        foreach ($product['variants'] as $variant) {
            if ($variant['sku'] === $sku) {
                return [
                    'product' => $product,
                    'variant' => $variant,
                ];
            }
        }
    }
    return null;
}

function get_stock(string $sku): int
{
    $inventory = load_inventory();
    return $inventory[$sku] ?? 0;
}

function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function json_error(string $message, int $status = 400): void
{
    json_response(['error' => $message], $status);
}

function get_json_body(): array
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function get_request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}
