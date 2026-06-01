<?php
// Expects: $product (single product array)
$price_display = '$' . number_format($product['price_cents'] / 100, 2);
$has_variants = count($product['variants']) > 1;
if ($has_variants) {
    $prices = array_column($product['variants'], 'price_cents');
    $min = min($prices);
    $max = max($prices);
    if ($min !== $max) {
        $price_display = 'From $' . number_format($min / 100, 2);
    }
}
?>
<a href="/product/<?= htmlspecialchars($product['slug']) ?>/"
   class="group block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md hover:border-brand-green/30 transition-all">
    <?php $img = $product['images'][0] ?? ''; $img_exists = $img && file_exists(ROOT_DIR . '/public' . $img); ?>
    <?php if ($img_exists): ?>
        <div class="bg-gray-100 p-6 flex items-center justify-center h-48">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                 class="h-full object-contain group-hover:scale-105 transition-transform">
        </div>
    <?php else: ?>
        <div class="bg-gray-100 flex items-center justify-center p-8 h-48">
            <div class="text-6xl text-gray-300 group-hover:scale-110 transition-transform">
                <?php if ($product['type'] === 'book'): ?>
                    &#128214;
                <?php elseif ($product['type'] === 't-shirt'): ?>
                    &#128085;
                <?php else: ?>
                    &#127991;
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="p-4">
        <p class="text-xs font-semibold text-brand-green uppercase tracking-wide mb-1">
            <?= htmlspecialchars($product['type']) ?>
        </p>
        <h3 class="text-lg font-semibold font-heading text-gray-900 group-hover:text-brand-green transition-colors">
            <?= htmlspecialchars($product['name']) ?>
        </h3>
        <p class="text-sm text-gray-500 mt-1 line-clamp-2">
            <?= htmlspecialchars($product['short_description']) ?>
        </p>
        <p class="text-lg font-bold text-gray-900 mt-3">
            <?= $price_display ?>
        </p>
        <?php if ($product['type'] === 'book'): ?>
            <p class="mt-2">
                <span class="bg-brand-navy text-white text-xs font-bold uppercase px-3 py-1 rounded-pill">Coming Soon</span>
            </p>
        <?php endif; ?>
    </div>
</a>
