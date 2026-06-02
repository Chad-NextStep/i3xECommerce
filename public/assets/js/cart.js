/**
 * cart.js — Global cart utilities + badge update
 * Loaded on every page.
 */
const Cart = {
    async get() {
        const res = await fetch('/api/cart');
        return res.json();
    },

    async add(sku, quantity = 1) {
        const res = await fetch('/api/cart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sku, quantity }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Failed to add item');
        Cart.updateBadge(data.item_count);
        return data;
    },

    async update(sku, quantity) {
        const res = await fetch('/api/cart', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sku, quantity }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Failed to update item');
        Cart.updateBadge(data.item_count);
        return data;
    },

    async remove(sku) {
        const res = await fetch('/api/cart', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ sku }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Failed to remove item');
        Cart.updateBadge(data.item_count);
        return data;
    },

    updateBadge(count) {
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    },

    formatPrice(cents) {
        return '$' + (cents / 100).toFixed(2);
    },

    async init() {
        try {
            const data = await Cart.get();
            Cart.updateBadge(data.item_count);
        } catch (e) {
            // Silent fail on badge init
        }
    },
};

// Initialize badge on every page load
document.addEventListener('DOMContentLoaded', () => Cart.init());
