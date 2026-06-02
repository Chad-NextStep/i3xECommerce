<?php

declare(strict_types=1);

/**
 * build.php — Static site generator
 *
 * Reads product/category JSON and renders PHP templates into static HTML
 * files in the public/ directory.
 */

define('ROOT_DIR', __DIR__);

echo "=== i3x E-Commerce Build ===\n\n";

// Load data
$products = json_decode(file_get_contents(ROOT_DIR . '/data/products.json'), true);
$inventory = json_decode(file_get_contents(ROOT_DIR . '/data/inventory.json'), true);
$categories = json_decode(file_get_contents(ROOT_DIR . '/data/categories.json'), true);

// Sort categories by sort_order
usort($categories, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

$public_dir = ROOT_DIR . '/public';

// Ensure output directories exist (don't wipe — preserve assets/js)
$dirs = [
    $public_dir,
    $public_dir . '/assets/js',
    $public_dir . '/cart',
    $public_dir . '/checkout',
    $public_dir . '/checkout/success',
    $public_dir . '/checkout/cancel',
];
foreach ($categories as $cat) {
    $dirs[] = $public_dir . '/category/' . $cat['slug'];
}
foreach ($products as $product) {
    $dirs[] = $public_dir . '/product/' . $product['slug'];
}
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

/**
 * Render a page template wrapped in the layout.
 */
function render_page(string $template, array $vars, array $page_scripts = []): string
{
    // Render the inner page content
    extract($vars);
    ob_start();
    include ROOT_DIR . '/templates/pages/' . $template;
    $content = ob_get_clean();

    // Render the full layout
    ob_start();
    include ROOT_DIR . '/templates/layout.php';
    return ob_get_clean();
}

$count = 0;

// --- Home page ---
$html = render_page('home.php', [
    'products'   => $products,
    'categories' => $categories,
    'page_title' => 'i3x Store — Books, Tees & Stickers',
]);
file_put_contents($public_dir . '/index.html', $html);
echo "  [+] /index.html\n";
$count++;

// --- Category pages ---
foreach ($categories as $category) {
    $category_products = array_filter($products, fn($p) => $p['category'] === $category['slug']);
    $html = render_page('category.php', [
        'category'          => $category,
        'category_products' => array_values($category_products),
        'categories'        => $categories,
        'page_title'        => $category['name'] . ' — i3x Store',
    ]);
    $path = '/category/' . $category['slug'] . '/index.html';
    file_put_contents($public_dir . $path, $html);
    echo "  [+] {$path}\n";
    $count++;
}

// --- Product pages ---
foreach ($products as $product) {
    $html = render_page('product.php', [
        'product'      => $product,
        'inventory'    => $inventory,
        'categories'   => $categories,
        'page_title'   => $product['name'] . ' — i3x Store',
        'page_scripts' => ['/assets/js/product.js'],
    ]);
    $path = '/product/' . $product['slug'] . '/index.html';
    file_put_contents($public_dir . $path, $html);
    echo "  [+] {$path}\n";
    $count++;
}

// --- Cart page ---
$html = render_page('cart.php', [
    'categories'   => $categories,
    'page_title'   => 'Cart — i3x Store',
    'page_scripts' => ['/assets/js/checkout.js'],
]);
file_put_contents($public_dir . '/cart/index.html', $html);
echo "  [+] /cart/index.html\n";
$count++;

// --- Checkout page ---
$html = render_page('checkout.php', [
    'categories'   => $categories,
    'page_title'   => 'Checkout — i3x Store',
    'page_scripts' => ['/assets/js/checkout.js'],
]);
file_put_contents($public_dir . '/checkout/index.html', $html);
echo "  [+] /checkout/index.html\n";
$count++;

// --- Checkout success ---
$html = render_page('checkout-success.php', [
    'categories' => $categories,
    'page_title' => 'Order Confirmed — i3x Store',
]);
file_put_contents($public_dir . '/checkout/success/index.html', $html);
echo "  [+] /checkout/success/index.html\n";
$count++;

// --- Checkout cancel ---
$html = render_page('checkout-cancel.php', [
    'categories' => $categories,
    'page_title' => 'Checkout Cancelled — i3x Store',
]);
file_put_contents($public_dir . '/checkout/cancel/index.html', $html);
echo "  [+] /checkout/cancel/index.html\n";
$count++;

// Copy favicon to public root
if (file_exists(ROOT_DIR . '/favicon.ico')) {
    copy(ROOT_DIR . '/favicon.ico', $public_dir . '/favicon.ico');
    echo "  [+] /favicon.ico\n";
}

echo "\nBuild complete: {$count} pages generated.\n";
