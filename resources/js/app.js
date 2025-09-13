import './bootstrap';
import Alpine from 'alpinejs';

// Make Alpine available globally
window.Alpine = Alpine;

// Start Alpine.js
Alpine.start();

// Add dark mode toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Ensure dark mode is always active for this theme
    if (!document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.add('dark');
    }

    // Initialize any additional JavaScript functionality here
    initializeAnimations();
    initializeTooltips();
    initializeModals();
    initializeCartAndFavorites();
});

// Initialize scroll animations
function initializeAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);

    // Observe elements with data-animate attribute
    document.querySelectorAll('[data-animate]').forEach(el => {
        observer.observe(el);
    });
}

// Initialize tooltips
function initializeTooltips() {
    // Tooltip initialization code
}

// Initialize modals
function initializeModals() {
    // Modal initialization code
}

// Initialize Cart and Favorites functionality
function initializeCartAndFavorites() {
    // Add to cart with AJAX
    document.querySelectorAll('.add-to-cart-ajax').forEach(button => {
        button.addEventListener('click', handleAddToCart);
    });

    // Toggle favorite with AJAX
    document.querySelectorAll('.toggle-favorite').forEach(button => {
        button.addEventListener('click', handleToggleFavorite);
    });
}

// Handle Add to Cart
window.handleAddToCart = function(event) {
    event.preventDefault();

    const button = event.currentTarget;
    const form = button.closest('form');
    const formData = new FormData(form);

    // Disable button and show loading
    button.disabled = true;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Adding...';

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
        credentials: 'same-origin' // Important for session cookies
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount();

            // Show success notification
            showNotification(data.message || 'Product added to cart!', 'success');

            // Animate the cart icon
            animateCartIcon();
        } else {
            showNotification(data.message || 'Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Re-enable button and restore text
        button.disabled = false;
        button.innerHTML = originalText;
    });
};

// Handle Toggle Favorite - FIXED URL STRUCTURE
window.toggleFavorite = function(productId) {
    const button = document.getElementById(`favorite-btn-${productId}`);
    if (!button) return;

    // Disable button during request
    button.disabled = true;

    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);

    fetch('/favorites/toggle', {  // Fixed: Use correct URL without product ID in path
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
        credentials: 'same-origin' // Important for session cookies
    })
    .then(response => {
        if (response.status === 401) {
            throw new Error('Please login to add favorites');
        }
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update button appearance
            if (data.is_favorited) {
                button.classList.remove('bg-slate-800', 'border-slate-700');
                button.classList.add('bg-red-500', 'border-red-500');
                button.innerHTML = '<svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path></svg>';
            } else {
                button.classList.add('bg-slate-800', 'border-slate-700');
                button.classList.remove('bg-red-500', 'border-red-500');
                button.innerHTML = '<svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>';
            }

            // Show notification
            showNotification(data.message || (data.is_favorited ? 'Added to favorites!' : 'Removed from favorites'), 'success');

            // Animate the heart
            button.classList.add('scale-125');
            setTimeout(() => {
                button.classList.remove('scale-125');
            }, 200);
        } else {
            showNotification(data.message || 'Failed to update favorites', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.message === 'Please login to add favorites') {
            showNotification('Please login to add favorites', 'error');
            // Optionally redirect to login
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            showNotification('An error occurred. Please try again.', 'error');
        }
    })
    .finally(() => {
        button.disabled = false;
    });
};

// Update cart count - FIXED with proper authentication
window.updateCartCount = function() {
    // Check if user is authenticated by looking for the cart icon
    const cartCountElement = document.getElementById('cart-count');
    if (!cartCountElement) {
        // User is not logged in, skip cart count update
        return;
    }

    fetch('/api/cart/count', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        credentials: 'same-origin' // Important for session cookies
    })
    .then(response => {
        if (response.status === 401) {
            // User is not authenticated, hide cart count
            cartCountElement.classList.add('hidden');
            return null;
        }
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data) {
            const mobileCartCount = document.querySelector('.cart-count-mobile');

            if (data.count > 0) {
                cartCountElement.textContent = data.count;
                cartCountElement.classList.remove('hidden');
                if (mobileCartCount) {
                    mobileCartCount.textContent = data.count;
                }
            } else {
                cartCountElement.classList.add('hidden');
                if (mobileCartCount) {
                    mobileCartCount.textContent = '0';
                }
            }
        }
    })
    .catch(error => {
        console.error('Error updating cart count:', error);
        // Hide cart count on error
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.classList.add('hidden');
        }
    });
};

// Animate cart icon
function animateCartIcon() {
    const cartIcon = document.querySelector('a[href*="cart"] svg');
    if (cartIcon) {
        cartIcon.classList.add('animate-bounce');
        setTimeout(() => {
            cartIcon.classList.remove('animate-bounce');
        }, 1000);
    }
}

// Cart update notification
window.showNotification = function(message, type = 'success') {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 transform translate-x-0 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    } text-white`;

    // Add icon based on type
    const icon = type === 'success'
        ? '<svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
        : '<svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';

    notification.innerHTML = icon + message;
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.add('translate-x-0');
    }, 10);

    // Remove after delay
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
};

// Quantity controls for product pages
window.increaseQuantity = function() {
    const input = document.getElementById('quantity');
    const max = input.getAttribute('max');
    const currentValue = parseInt(input.value);

    if (!max || currentValue < parseInt(max)) {
        input.value = currentValue + 1;
    }
};

window.decreaseQuantity = function() {
    const input = document.getElementById('quantity');
    const currentValue = parseInt(input.value);

    if (currentValue > 1) {
        input.value = currentValue - 1;
    }
};

// Initialize on page load - only for authenticated users
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        // Only update cart count if user is authenticated (cart icon exists)
        if (document.getElementById('cart-count')) {
            updateCartCount();
        }
    });
} else {
    // Only update cart count if user is authenticated (cart icon exists)
    if (document.getElementById('cart-count')) {
        updateCartCount();
    }
}

// Make functions globally available
window.handleAddToCart = handleAddToCart;
window.toggleFavorite = toggleFavorite;
window.updateCartCount = updateCartCount;
window.showNotification = showNotification;
