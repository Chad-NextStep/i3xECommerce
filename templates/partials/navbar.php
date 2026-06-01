<nav class="bg-brand-dark shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-8">
                <a href="/" class="flex-shrink-0">
                    <img src="/assets/i3x-logo-bw.png" alt="i3X" class="h-9 brightness-0 invert">
                </a>
                <div class="hidden sm:flex space-x-6">
                    <?php foreach ($categories as $cat): ?>
                        <a href="/category/<?= htmlspecialchars($cat['slug']) ?>/"
                           class="text-gray-300 hover:text-brand-green font-medium transition-colors">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="/cart/" class="relative text-gray-300 hover:text-brand-green transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                <span id="cart-badge"
                      class="hidden absolute -top-2 -right-2 bg-brand-green text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                    0
                </span>
            </a>
        </div>
    </div>
    <!-- Mobile menu -->
    <div class="sm:hidden border-t border-gray-700 px-4 py-2 space-y-1">
        <?php foreach ($categories as $cat): ?>
            <a href="/category/<?= htmlspecialchars($cat['slug']) ?>/"
               class="block text-gray-300 hover:text-brand-green py-1 font-medium">
                <?= htmlspecialchars($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</nav>
