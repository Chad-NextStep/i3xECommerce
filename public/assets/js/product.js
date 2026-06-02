/**
 * product.js — Variant selection + add-to-cart on product pages
 */
document.addEventListener('DOMContentLoaded', () => {
    const selectorEl = document.getElementById('variant-selector');
    const skuInput = document.getElementById('selected-sku');
    const priceEl = document.getElementById('product-price');
    const stockEl = document.getElementById('stock-status');
    const addBtn = document.getElementById('add-to-cart');
    const qtyInput = document.getElementById('quantity');
    const qtyMinus = document.getElementById('qty-minus');
    const qtyPlus = document.getElementById('qty-plus');
    const messageEl = document.getElementById('cart-message');
    const inventory = window.__productInventory || {};

    // If no variant selector (single variant products), SKU is already set
    if (!selectorEl) {
        initAddToCart();
        return;
    }

    const variants = JSON.parse(selectorEl.dataset.variants || '[]');
    const axes = JSON.parse(selectorEl.dataset.axes || '[]');
    const selected = {};

    // Initialize with first value of each axis
    axes.forEach(axis => {
        const firstBtn = selectorEl.querySelector(`[data-axis="${axis}"]`);
        if (firstBtn) {
            selected[axis] = firstBtn.dataset.value;
        }
    });

    updateSelectedVariant();

    // Variant button click handlers
    selectorEl.addEventListener('click', (e) => {
        const btn = e.target.closest('.variant-btn');
        if (!btn) return;

        const axis = btn.dataset.axis;
        const value = btn.dataset.value;
        selected[axis] = value;

        // Update button styles for this axis
        const group = btn.closest('[data-axis]');
        group.querySelectorAll('.variant-btn').forEach(b => {
            if (b.dataset.value === value) {
                b.className = b.className
                    .replace('border-gray-300 text-gray-700 hover:border-brand-green', '')
                    + ' border-brand-green bg-green-50 text-brand-green';
            } else {
                b.className = b.className
                    .replace('border-brand-green bg-green-50 text-brand-green', '')
                    + ' border-gray-300 text-gray-700 hover:border-brand-green';
            }
        });

        updateSelectedVariant();
    });

    function updateSelectedVariant() {
        // Find the variant matching all selected attributes
        const match = variants.find(v =>
            axes.every(axis => v.attributes[axis] === selected[axis])
        );

        if (match) {
            skuInput.value = match.sku;
            priceEl.textContent = Cart.formatPrice(match.price_cents);

            const stock = inventory[match.sku] || 0;
            if (stock > 0) {
                stockEl.innerHTML = `<span class="text-brand-green font-medium">In Stock (${stock} available)</span>`;
                addBtn.disabled = false;
            } else {
                stockEl.innerHTML = `<span class="text-brand-red font-medium">Out of Stock</span>`;
                addBtn.disabled = true;
            }
        } else {
            skuInput.value = '';
            stockEl.innerHTML = `<span class="text-gray-400">Select options</span>`;
            addBtn.disabled = true;
        }
    }

    initAddToCart();

    function initAddToCart() {
        // Quantity buttons
        if (qtyMinus) {
            qtyMinus.addEventListener('click', () => {
                const val = parseInt(qtyInput.value) || 1;
                qtyInput.value = Math.max(1, val - 1);
            });
        }
        if (qtyPlus) {
            qtyPlus.addEventListener('click', () => {
                const val = parseInt(qtyInput.value) || 1;
                qtyInput.value = val + 1;
            });
        }

        // Add to cart
        if (addBtn) {
            addBtn.addEventListener('click', async () => {
                const sku = skuInput.value;
                const quantity = parseInt(qtyInput.value) || 1;

                if (!sku) {
                    showMessage('Please select all options.', 'error');
                    return;
                }

                addBtn.disabled = true;
                addBtn.textContent = 'Adding...';

                try {
                    await Cart.add(sku, quantity);
                    showMessage('Added to cart!', 'success');
                } catch (err) {
                    showMessage(err.message, 'error');
                } finally {
                    addBtn.disabled = false;
                    addBtn.textContent = 'Add to Cart';
                }
            });
        }
    }

    function showMessage(text, type) {
        if (!messageEl) return;
        messageEl.textContent = text;
        messageEl.className = 'mt-3 text-sm font-medium ' +
            (type === 'success' ? 'text-brand-green' : 'text-brand-red');
        messageEl.classList.remove('hidden');
        setTimeout(() => messageEl.classList.add('hidden'), 3000);
    }
});
