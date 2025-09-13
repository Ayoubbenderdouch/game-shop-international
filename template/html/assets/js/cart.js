// Cart Page JavaScript

// Load cart items
function loadCart() {
    const cartContent = document.getElementById('cart-content');
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    if (cart.length === 0) {
        cartContent.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-32 h-32 mx-auto mb-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-white mb-4">Your cart is empty</h2>
                <p class="text-gray-400 mb-8">Looks like you haven't added anything to your cart yet</p>
                <a href="products.html" class="inline-block bg-primary-blue text-black px-8 py-3 rounded-md hover:bg-white transition-all font-medium">
                    Continue Shopping
                </a>
            </div>
        `;
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + (item.offer_price * item.quantity), 0);
    const tax = subtotal * 0.1; // 10% tax
    const shipping = subtotal > 50 ? 0 : 9.99; // Free shipping over $50
    const total = subtotal + tax + shipping;

    cartContent.innerHTML = `
        <div class="w-full">
            <!-- Cart Table -->
            <div class="w-full mb-[30px]">
                <div class="bg-black border border-[#23262B] rounded-lg overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden md:block">
                        <table class="w-full">
                            <thead class="bg-[#1a1a1a]">
                                <tr>
                                    <th class="text-left p-4 text-white font-semibold">Product</th>
                                    <th class="text-center p-4 text-white font-semibold">Price</th>
                                    <th class="text-center p-4 text-white font-semibold">Quantity</th>
                                    <th class="text-center p-4 text-white font-semibold">Total</th>
                                    <th class="text-center p-4 text-white font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${cart.map(item => `
                                    <tr class="border-t border-[#23262B]">
                                        <td class="p-4">
                                            <div class="flex items-center space-x-4">
                                                <img src="${item.thumbnail_image}" alt="${item.name}" class="w-20 h-20 object-cover rounded">
                                                <div>
                                                    <h3 class="text-white font-semibold">${item.name}</h3>
                                                    <p class="text-gray-400 text-sm">ID: #${item.id}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center p-4">
                                            <span class="text-primary-blue font-semibold">$${item.offer_price}</span>
                                            ${item.regular_price > item.offer_price ?
                                                `<br><span class="text-gray-500 line-through text-sm">$${item.regular_price}</span>` : ''}
                                        </td>
                                        <td class="text-center p-4">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})"
                                                        class="w-8 h-8 bg-[#1a1a1a] text-white rounded hover:bg-primary-blue hover:text-black transition-all">
                                                    -
                                                </button>
                                                <input type="number" value="${item.quantity}" min="1"
                                                       onchange="updateQuantity(${item.id}, this.value)"
                                                       class="w-16 text-center bg-transparent border border-[#23262B] text-white rounded px-2 py-1">
                                                <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})"
                                                        class="w-8 h-8 bg-[#1a1a1a] text-white rounded hover:bg-primary-blue hover:text-black transition-all">
                                                    +
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-center p-4">
                                            <span class="text-white font-semibold">$${(item.offer_price * item.quantity).toFixed(2)}</span>
                                        </td>
                                        <td class="text-center p-4">
                                            <button onclick="removeFromCart(${item.id})"
                                                    class="text-red-500 hover:text-red-400 transition-all">
                                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View -->
                    <div class="md:hidden">
                        ${cart.map(item => `
                            <div class="border-b border-[#23262B] p-4">
                                <div class="flex space-x-4">
                                    <img src="${item.thumbnail_image}" alt="${item.name}" class="w-20 h-20 object-cover rounded">
                                    <div class="flex-1">
                                        <h3 class="text-white font-semibold mb-1">${item.name}</h3>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-primary-blue font-semibold">$${item.offer_price}</span>
                                            <button onclick="removeFromCart(${item.id})"
                                                    class="text-red-500 hover:text-red-400 transition-all">
                                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})"
                                                        class="w-6 h-6 bg-[#1a1a1a] text-white rounded text-sm">
                                                    -
                                                </button>
                                                <span class="text-white w-8 text-center">${item.quantity}</span>
                                                <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})"
                                                        class="w-6 h-6 bg-[#1a1a1a] text-white rounded text-sm">
                                                    +
                                                </button>
                                            </div>
                                            <span class="text-white font-semibold">Total: $${(item.offer_price * item.quantity).toFixed(2)}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>

            <!-- Coupon and Summary Row -->
            <div class="w-full lg:flex lg:space-x-8">
                <!-- Coupon Code -->
                <div class="flex-1 mb-6 lg:mb-0">
                    <div class="border border-[#23262B] px-[30px] py-[26px] rounded-[5px] bg-black">
                        <h3 class="text-xl font-bold text-white mb-4">Have a Coupon?</h3>
                        <div class="flex space-x-2">
                            <input type="text" id="coupon-code" placeholder="Enter coupon code"
                                   class="flex-1 bg-transparent border border-[#23262B] text-white px-4 py-2 rounded-md outline-none focus:border-primary-blue">
                            <button onclick="applyCoupon()"
                                    class="bg-primary-blue text-black px-6 py-2 rounded-md hover:bg-white transition-all font-medium">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="w-full lg:w-[370px]">
                    <div class="border border-[#23262B] px-[30px] py-[26px] rounded-[5px] bg-black">
                        <h3 class="text-xl font-bold text-white mb-6">Cart Summary</h3>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal</span>
                                <span class="text-white font-semibold">$${subtotal.toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Tax (10%)</span>
                                <span class="text-white font-semibold">$${tax.toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Shipping</span>
                                <span class="text-white font-semibold">${shipping === 0 ? 'FREE' : '$' + shipping.toFixed(2)}</span>
                            </div>
                            ${shipping > 0 ? `
                                <div class="text-sm text-gray-400">
                                    Add $${(50 - subtotal).toFixed(2)} more for free shipping!
                                </div>
                            ` : ''}
                            <div class="border-t border-[#23262B] pt-4">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-white">Total</span>
                                    <span class="text-lg font-bold text-primary-blue">$${total.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>

                        <button onclick="proceedToCheckout()"
                                class="w-full bg-primary-blue text-black py-3 rounded-md hover:bg-white transition-all font-semibold mb-3">
                            Proceed to Checkout
                        </button>

                        <a href="products.html" class="block text-center text-gray-400 hover:text-white transition-all">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Update quantity
function updateQuantity(productId, newQuantity) {
    newQuantity = parseInt(newQuantity);

    if (newQuantity < 1) {
        removeFromCart(productId);
        return;
    }

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const item = cart.find(i => i.id === productId);

    if (item) {
        item.quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
        updateCartCount();
        showToast('Cart updated');
    }
}

// Remove from cart
function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item => item.id !== productId);
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
        updateCartCount();
        showToast('Item removed from cart');
    }
}

// Apply coupon
function applyCoupon() {
    const couponCode = document.getElementById('coupon-code').value.trim().toUpperCase();

    // Mock coupon codes
    const validCoupons = {
        'SAVE10': 0.1,
        'SAVE20': 0.2,
        'WELCOME': 0.15
    };

    if (validCoupons[couponCode]) {
        showToast(`Coupon applied! ${validCoupons[couponCode] * 100}% discount`);
        // In a real app, this would apply the discount to the total
    } else {
        showToast('Invalid coupon code', 'error');
    }
}

// Proceed to checkout
function proceedToCheckout() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    if (cart.length === 0) {
        showToast('Your cart is empty', 'error');
        return;
    }

    // In a real application, this would redirect to a checkout page
    alert('Checkout functionality would be implemented here!\n\nThis is where you would:\n1. Collect shipping information\n2. Process payment\n3. Confirm order');
}

// Load cart on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
    updateCartCount();
});
