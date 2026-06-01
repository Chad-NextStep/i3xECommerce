<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 font-heading mb-8">Shopping Cart</h1>

    <div id="cart-loading" class="text-center py-12 text-gray-400">Loading cart...</div>

    <div id="cart-empty" class="hidden text-center py-12">
        <p class="text-gray-400 text-lg mb-4">Your cart is empty.</p>
        <a href="/" class="text-brand-green font-semibold hover:underline">Continue Shopping</a>
    </div>

    <div id="cart-content" class="hidden">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 divide-y divide-gray-200">
            <div id="cart-items">
                <!-- Rendered by checkout.js -->
            </div>
        </div>

        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center text-lg font-bold text-gray-900">
                <span>Total</span>
                <span id="cart-total">$0.00</span>
            </div>
            <button id="checkout-btn"
                    class="mt-4 w-full bg-brand-green text-white font-semibold py-3 px-6 rounded-pill hover:bg-brand-navy transition-colors">
                Proceed to Checkout
            </button>
            <a href="/" class="block mt-3 text-center text-sm text-brand-green hover:underline">
                Continue Shopping
            </a>
        </div>
    </div>
</section>
