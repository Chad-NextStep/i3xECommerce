<?php
// Expects: $category, $category_products
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="text-sm text-gray-500 mb-6">
        <a href="/" class="hover:text-brand-green">Home</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900"><?= htmlspecialchars($category['name']) ?></span>
    </nav>

    <h1 class="text-3xl font-bold text-gray-900 font-heading mb-2"><?= htmlspecialchars($category['name']) ?></h1>
    <p class="text-gray-500 mb-8"><?= htmlspecialchars($category['description']) ?></p>

    <?php if (empty($category_products)): ?>
        <p class="text-gray-400 text-center py-12">No products in this category yet.</p>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($category_products as $product): ?>
                <?php include __DIR__ . '/../partials/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
