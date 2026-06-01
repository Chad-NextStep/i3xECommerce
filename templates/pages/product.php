<?php
// Expects: $product, $inventory
$default_variant = $product['variants'][0];
$price_display = '$' . number_format($default_variant['price_cents'] / 100, 2);
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="text-sm text-gray-500 mb-6">
        <a href="/" class="hover:text-brand-green">Home</a>
        <span class="mx-2">/</span>
        <a href="/category/<?= htmlspecialchars($product['category']) ?>/" class="hover:text-brand-green capitalize">
            <?= htmlspecialchars($product['category']) ?>
        </a>
        <span class="mx-2">/</span>
        <span class="text-gray-900"><?= htmlspecialchars($product['name']) ?></span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Product Image -->
        <div class="bg-gray-100 rounded-lg flex items-center justify-center p-16">
            <?php $img = $product['images'][0] ?? ''; $img_exists = $img && file_exists(ROOT_DIR . '/public' . $img); ?>
            <?php if ($img_exists): ?>
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                     class="max-h-full max-w-full object-contain">
            <?php else: ?>
                <div class="text-9xl text-gray-300">
                    <?php if ($product['type'] === 'book'): ?>
                        &#128214;
                    <?php elseif ($product['type'] === 't-shirt'): ?>
                        &#128085;
                    <?php else: ?>
                        &#127991;
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Details -->
        <div>
            <p class="text-sm font-semibold text-brand-green uppercase tracking-wide mb-2">
                <?= htmlspecialchars($product['type']) ?>
            </p>
            <h1 class="text-3xl font-bold text-gray-900 font-heading mb-4">
                <?= htmlspecialchars($product['name']) ?>
            </h1>

            <p id="product-price" class="text-2xl font-bold text-gray-900 mb-4"
               data-base-price="<?= $default_variant['price_cents'] ?>">
                <?= $price_display ?>
            </p>
            <?php if ($product['type'] === 'book'): ?>
                <p class="mb-6">
                    <span class="bg-brand-navy text-white text-sm font-bold uppercase px-4 py-1.5 rounded-pill">Coming Soon</span>
                </p>
            <?php endif; ?>

            <p class="text-gray-600 mb-6"><?= htmlspecialchars($product['description']) ?></p>

            <?php if (!empty($product['metadata'])): ?>
                <dl class="grid grid-cols-2 gap-2 text-sm mb-6">
                    <?php foreach ($product['metadata'] as $key => $val): ?>
                        <dt class="text-gray-500 capitalize"><?= htmlspecialchars(str_replace('_', ' ', $key)) ?></dt>
                        <dd class="text-gray-900 font-medium">
                            <?= is_bool($val) ? ($val ? 'Yes' : 'No') : htmlspecialchars((string)$val) ?>
                        </dd>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>

            <!-- Variant selector -->
            <?php include __DIR__ . '/../partials/variant-selector.php'; ?>

            <!-- Stock status -->
            <?php if ($product['type'] !== 'book'): ?>
                <div id="stock-status" class="mt-4 text-sm">
                    <?php
                    $first_sku = $default_variant['sku'];
                    $stock = $inventory[$first_sku] ?? 0;
                    ?>
                    <?php if ($stock > 0): ?>
                        <span class="text-brand-green font-medium">In Stock (<?= $stock ?> available)</span>
                    <?php else: ?>
                        <span class="text-brand-red font-medium">Out of Stock</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Quantity + Add to Cart -->
            <?php if ($product['type'] === 'book'): ?>
                <div class="mt-6">
                    <button type="button" disabled
                            class="w-full bg-gray-300 text-gray-500 font-semibold py-3 px-6 rounded-pill cursor-not-allowed">
                        Coming Soon
                    </button>
                </div>
            <?php else: ?>
                <div class="mt-6 flex items-center space-x-4">
                    <div class="flex items-center border border-gray-300 rounded-md">
                        <button type="button" id="qty-minus"
                                class="px-3 py-2 text-gray-600 hover:text-gray-900 font-bold">-</button>
                        <input type="number" id="quantity" value="1" min="1"
                               class="w-14 text-center border-l border-r border-gray-300 py-2 text-sm">
                        <button type="button" id="qty-plus"
                                class="px-3 py-2 text-gray-600 hover:text-gray-900 font-bold">+</button>
                    </div>
                    <button type="button" id="add-to-cart"
                            class="flex-1 bg-brand-green text-white font-semibold py-3 px-6 rounded-pill hover:bg-brand-navy transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                            data-product-id="<?= $product['id'] ?>">
                        Add to Cart
                    </button>
                </div>
            <?php endif; ?>

            <div id="cart-message" class="mt-3 text-sm hidden"></div>
        </div>
    </div>

    <!-- Inventory data for JS -->
    <script>
        window.__productInventory = <?= json_encode(
            array_combine(
                array_column($product['variants'], 'sku'),
                array_map(fn($v) => $inventory[$v['sku']] ?? 0, $product['variants'])
            )
        ) ?>;
    </script>
</section>
