<footer class="bg-brand-dark text-gray-400 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <img src="/assets/i3x-logo-color.png" alt="i3X" class="h-8 mb-4 brightness-0 invert">
                <p class="text-sm">Books, tees, and stickers for developers and creators.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold font-heading mb-4">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="/category/<?= htmlspecialchars($cat['slug']) ?>/"
                               class="hover:text-brand-green transition-colors">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold font-heading mb-4">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/cart/" class="hover:text-brand-green transition-colors">Shopping Cart</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-sm text-center">
            &copy; <?= date('Y') ?> i3X. All rights reserved.
        </div>
    </div>
</footer>
