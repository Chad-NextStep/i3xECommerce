<section class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
    <div id="checkout-processing">
        <svg class="animate-spin h-12 w-12 text-brand-green mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <h1 class="text-2xl font-bold text-gray-900 font-heading mb-2">Preparing Checkout...</h1>
        <p class="text-gray-500">You'll be redirected to our secure payment page.</p>
    </div>

    <div id="checkout-error" class="hidden">
        <h1 class="text-2xl font-bold text-brand-red font-heading mb-2">Checkout Error</h1>
        <p id="checkout-error-msg" class="text-gray-500 mb-6"></p>
        <a href="/cart/" class="text-brand-green font-semibold hover:underline">Return to Cart</a>
    </div>
</section>
