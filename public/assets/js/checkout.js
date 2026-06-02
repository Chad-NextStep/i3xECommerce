/**
 * checkout.js — Cart page rendering + Stripe redirect
 * Used on /cart/ and /checkout/ pages.
 */
document.addEventListener('DOMContentLoaded', () => {
    const cartItems = document.getElementById('cart-items');
    const cartContent = document.getElementById('cart-content');
    const cartEmpty = document.getElementById('cart-empty');
    const cartLoading = document.getElementById('cart-loading');
    const cartTotal = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');

    // Cart page
    if (cartItems) {
        loadCart();
    }

    // Checkout page — immediately create session and redirect
    const checkoutProcessing = document.getElementById('checkout-processing');
    if (checkoutProcessing && !cartItems) {
        startCheckout();
    }

    async function loadCart() {
        try {
            const data = await Cart.get();
            if (cartLoading) cartLoading.classList.add('hidden');

            if (!data.items || data.items.length === 0) {
                if (cartContent) cartContent.classList.add('hidden');
                if (cartEmpty) cartEmpty.classList.remove('hidden');
                return;
            }

            if (cartEmpty) cartEmpty.classList.add('hidden');
            if (cartContent) cartContent.classList.remove('hidden');
            renderCartItems(data);
        } catch (err) {
            if (cartLoading) {
                cartLoading.textContent = 'Failed to load cart.';
            }
        }
    }

    function renderCartItems(data) {
        cartItems.innerHTML = data.items.map(item => `
            <div class="flex items-center p-4 gap-4" data-sku="${item.sku}">
                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                    ${item.image
                        ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.product_name)}" class="max-h-full max-w-full object-contain">`
                        : `<span class="text-2xl text-gray-300">${item.product_slug.includes('book') ? '&#128214;' : item.product_slug.includes('tee') ? '&#128085;' : '&#127991;'}</span>`}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="/product/${item.product_slug}/" class="font-medium text-gray-900 hover:text-brand-green">
                        ${escapeHtml(item.product_name)}
                    </a>
                    <p class="text-sm text-gray-500">${escapeHtml(item.variant_label)}</p>
                    <p class="text-sm font-medium text-gray-900">${Cart.formatPrice(item.price_cents)}</p>
                </div>
                <div class="flex items-center border border-gray-300 rounded-md">
                    <button class="cart-qty-btn px-2 py-1 text-gray-600 hover:text-gray-900" data-action="decrease" data-sku="${item.sku}">-</button>
                    <span class="px-3 py-1 text-sm">${item.quantity}</span>
                    <button class="cart-qty-btn px-2 py-1 text-gray-600 hover:text-gray-900" data-action="increase" data-sku="${item.sku}">+</button>
                </div>
                <div class="text-right w-20 flex-shrink-0">
                    <p class="font-medium text-gray-900">${Cart.formatPrice(item.line_total)}</p>
                </div>
                <button class="cart-remove-btn text-gray-400 hover:text-red-600 flex-shrink-0" data-sku="${item.sku}" title="Remove">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `).join('');

        if (cartTotal) {
            cartTotal.textContent = Cart.formatPrice(data.total_cents);
        }

        // Attach event listeners
        cartItems.querySelectorAll('.cart-qty-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const sku = btn.dataset.sku;
                const action = btn.dataset.action;
                const item = data.items.find(i => i.sku === sku);
                if (!item) return;
                const newQty = action === 'increase' ? item.quantity + 1 : item.quantity - 1;
                try {
                    if (newQty < 1) {
                        await Cart.remove(sku);
                    } else {
                        await Cart.update(sku, newQty);
                    }
                    loadCart();
                } catch (err) {
                    alert(err.message);
                }
            });
        });

        cartItems.querySelectorAll('.cart-remove-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                try {
                    await Cart.remove(btn.dataset.sku);
                    loadCart();
                } catch (err) {
                    alert(err.message);
                }
            });
        });
    }

    // Checkout button → navigate to checkout page
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', () => {
            window.location.href = '/checkout/';
        });
    }

    async function startCheckout() {
        const errorEl = document.getElementById('checkout-error');
        const errorMsg = document.getElementById('checkout-error-msg');

        try {
            const res = await fetch('/api/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
            });
            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.error || 'Checkout failed');
            }

            if (data.checkout_url) {
                window.location.href = data.checkout_url;
            } else {
                throw new Error('No checkout URL received');
            }
        } catch (err) {
            if (checkoutProcessing) checkoutProcessing.classList.add('hidden');
            if (errorEl) errorEl.classList.remove('hidden');
            if (errorMsg) errorMsg.textContent = err.message;
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
