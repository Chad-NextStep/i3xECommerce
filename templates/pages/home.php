<?php
// Expects: $products, $categories
?>

<!-- Hero -->
<section class="relative overflow-hidden" style="background: linear-gradient(to bottom right, rgba(0,160,9,1), rgba(0,54,127,1));">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center relative z-10">
        <img src="/assets/i3x-logo-bw.png" alt="i3X" class="h-16 sm:h-20 mx-auto mb-4 brightness-0 invert">
        <p class="mt-4 text-xl text-green-100 max-w-2xl mx-auto">
            Books, tees, and stickers for developers and creators.
        </p>
    </div>
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-32 h-32 border border-white rounded-full"></div>
        <div class="absolute bottom-10 right-20 w-48 h-48 border border-white rounded-full"></div>
        <div class="absolute top-1/2 left-1/3 w-24 h-24 border border-white rounded-full"></div>
    </div>
</section>

<!-- All Products -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-900 font-heading mb-6">All Products</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($products as $product): ?>
            <?php include __DIR__ . '/../partials/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
