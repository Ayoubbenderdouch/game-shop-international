// Product Detail Page JavaScript

let currentProduct = null;
let selectedPlatform = 'pc';
let selectedEdition = 'standard';
let currentImageIndex = 0;

// Mock product images
const productImages = [
    'assets/img/products/game-1.jpg',
    'assets/img/products/game-2.jpg',
    'assets/img/products/game-3.jpg',
    'assets/img/products/game-4.jpg'
];

// Mock reviews
const mockReviews = [
    {
        id: 1,
        user: 'John Doe',
        avatar: 'JD',
        rating: 5,
        date: '2024-01-15',
        title: 'Amazing Game!',
        comment: 'Best Call of Duty game in years. The campaign is incredible and multiplayer is so much fun!',
        helpful: 45,
        verified: true
    },
    {
        id: 2,
        user: 'Sarah Smith',
        avatar: 'SS',
        rating: 4,
        date: '2024-01-10',
        title: 'Great but needs optimization',
        comment: 'Gameplay is fantastic but it needs better optimization for PC. Still worth buying!',
        helpful: 32,
        verified: true
    },
    {
        id: 3,
        user: 'Mike Johnson',
        avatar: 'MJ',
        rating: 5,
        date: '2024-01-08',
        title: 'Worth every penny',
        comment: 'The zombies mode alone makes this worth it. Graphics are stunning on PS5.',
        helpful: 28,
        verified: false
    }
];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadProductDetails();
    loadRelatedProducts();
    loadReviews();
    updateCartCount();
});

// Load product details from URL parameter
function loadProductDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id') || 1;

    // Find product from mock data
    currentProduct = mockData.products.find(p => p.id == productId) || mockData.products[0];

    // Update page content
    document.getElementById('product-name').textContent = currentProduct.name;
    document.getElementById('breadcrumb-name').textContent = currentProduct.name;
    document.title = `${currentProduct.name} - GameShop`;

    // Update images
    document.getElementById('main-product-image').src = currentProduct.thumbnail_image;

    // Update price
    document.getElementById('offer-price').textContent = `$${currentProduct.offer_price}`;
    if (currentProduct.regular_price > currentProduct.offer_price) {
        document.getElementById('regular-price').textContent = `$${currentProduct.regular_price}`;
        document.getElementById('regular-price').style.display = 'inline';
        document.getElementById('discount-badge').textContent = `-${currentProduct.discount}% OFF`;
    } else {
        document.getElementById('regular-price').style.display = 'none';
        document.getElementById('discount-badge').style.display = 'none';
    }

    // Update rating
    updateRating(currentProduct.average_rating);

    // Update description
    document.getElementById('short-description').textContent = currentProduct.description ||
        'Experience the ultimate gaming adventure with stunning graphics and immersive gameplay.';

    // Update meta
    document.getElementById('product-sku').textContent = `GAME-${currentProduct.id}`;
    const category = mockData.categories.find(c => c.id === currentProduct.category_id);
    document.getElementById('product-category').textContent = category ? category.name : 'Gaming';

    // Update review count
    document.getElementById('total-reviews').textContent = currentProduct.total_sale || 256;
    document.getElementById('review-count').textContent = currentProduct.total_sale || 256;
}

// Update rating display
function updateRating(rating) {
    const ratingStars = document.getElementById('rating-stars');
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;

    let starsHTML = '';
    for (let i = 0; i < fullStars; i++) {
        starsHTML += '<span class="text-yellow-400">★</span>';
    }
    if (hasHalfStar) {
        starsHTML += '<span class="text-yellow-400 opacity-50">★</span>';
    }
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        starsHTML += '<span class="text-gray-600">★</span>';
    }

    ratingStars.innerHTML = starsHTML;
}

// Change main image
function changeImage(index) {
    currentImageIndex = index;
    document.getElementById('main-product-image').src = productImages[index];

    // Update thumbnail borders
    const thumbnails = document.querySelectorAll('.thumbnail-btn');
    thumbnails.forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('border-primary-blue');
            thumb.classList.remove('border-transparent');
        } else {
            thumb.classList.remove('border-primary-blue');
            thumb.classList.add('border-transparent');
        }
    });
}

// Open image modal
function openImageModal() {
    const modal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');
    modalImage.src = document.getElementById('main-product-image').src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Close image modal
function closeImageModal() {
    const modal = document.getElementById('image-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Select platform
function selectPlatform(platform) {
    selectedPlatform = platform;

    // Update button styles
    const buttons = document.querySelectorAll('.platform-btn');
    buttons.forEach(btn => {
        if (btn.textContent.toLowerCase().includes(platform)) {
            btn.classList.add('bg-primary-blue', 'text-black', 'border-primary-blue');
            btn.classList.remove('bg-transparent', 'text-white', 'border-[#23262B]');
        } else {
            btn.classList.remove('bg-primary-blue', 'text-black', 'border-primary-blue');
            btn.classList.add('bg-transparent', 'text-white', 'border-[#23262B]');
        }
    });
}

// Quantity controls
function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    quantityInput.value = parseInt(quantityInput.value) + 1;
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}

// Add to cart
function addProductToCart() {
    if (!currentProduct) return;

    const quantity = parseInt(document.getElementById('quantity').value);
    const edition = document.getElementById('edition-select').value;

    // Get price based on edition
    let price = currentProduct.offer_price;
    if (edition === 'deluxe') price = 79.99;
    if (edition === 'ultimate') price = 99.99;

    const cartItem = {
        ...currentProduct,
        quantity: quantity,
        edition: edition,
        platform: selectedPlatform,
        offer_price: price
    };

    // Add to cart (using main.js function)
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingItem = cart.find(item =>
        item.id === cartItem.id &&
        item.edition === cartItem.edition &&
        item.platform === cartItem.platform
    );

    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push(cartItem);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showToast(`${currentProduct.name} added to cart!`);
}

// Toggle wishlist
function toggleProductWishlist() {
    if (!currentProduct) return;

    let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    const index = wishlist.findIndex(item => item.id === currentProduct.id);

    if (index > -1) {
        wishlist.splice(index, 1);
        showToast('Removed from wishlist');
    } else {
        wishlist.push(currentProduct);
        showToast('Added to wishlist!');
    }

    localStorage.setItem('wishlist', JSON.stringify(wishlist));
}

// Share product
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: currentProduct.name,
            text: `Check out ${currentProduct.name} on GameShop!`,
            url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        showToast('Link copied to clipboard!');
    }
}

// Switch tabs
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });

    // Show selected tab
    document.getElementById(`${tabName}-tab`).classList.remove('hidden');

    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.textContent.toLowerCase().includes(tabName)) {
            btn.classList.add('text-white', 'border-primary-blue');
            btn.classList.remove('text-gray-400', 'border-transparent');
        } else {
            btn.classList.remove('text-white', 'border-primary-blue');
            btn.classList.add('text-gray-400', 'border-transparent');
        }
    });
}

// Load reviews
function loadReviews() {
    const reviewsList = document.getElementById('reviews-list');

    reviewsList.innerHTML = mockReviews.map(review => `
        <div class="border-b border-[#23262B] pb-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-primary-blue/20 rounded-full flex items-center justify-center text-primary-blue font-semibold">
                    ${review.avatar}
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span class="text-white font-semibold">${review.user}</span>
                            ${review.verified ? '<span class="ml-2 text-xs bg-green-500/20 text-green-500 px-2 py-1 rounded">Verified Purchase</span>' : ''}
                        </div>
                        <span class="text-gray-400 text-sm">${formatDate(review.date)}</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <div class="flex space-x-1 mr-3">
                            ${generateStars(review.rating)}
                        </div>
                        <span class="text-white font-semibold">${review.title}</span>
                    </div>
                    <p class="text-gray-400 mb-3">${review.comment}</p>
                    <div class="flex items-center space-x-4">
                        <button onclick="markHelpful(${review.id})" class="text-sm text-gray-400 hover:text-primary-blue transition-all">
                            Helpful (${review.helpful})
                        </button>
                        <button class="text-sm text-gray-400 hover:text-primary-blue transition-all">
                            Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Generate star rating HTML
function generateStars(rating) {
    let stars = '';
    for (let i = 0; i < rating; i++) {
        stars += '<span class="text-yellow-400">★</span>';
    }
    for (let i = rating; i < 5; i++) {
        stars += '<span class="text-gray-600">★</span>';
    }
    return stars;
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
    return `${Math.floor(diffDays / 365)} years ago`;
}

// Mark review as helpful
function markHelpful(reviewId) {
    const review = mockReviews.find(r => r.id === reviewId);
    if (review) {
        review.helpful++;
        loadReviews();
        showToast('Thanks for your feedback!');
    }
}

// Load more reviews
function loadMoreReviews() {
    // In a real app, this would load more reviews from the server
    showToast('Loading more reviews...');
}

// Open review modal
function openReviewModal() {
    // In a real app, this would open a modal for writing a review
    alert('Review modal would open here for writing a review!');
}

// Load related products
function loadRelatedProducts() {
    const relatedProductsGrid = document.getElementById('related-products');

    // Get products from the same category
    const relatedProducts = mockData.products
        .filter(p => p.category_id === currentProduct.category_id && p.id !== currentProduct.id)
        .slice(0, 4);

    // If not enough products in same category, add random products
    if (relatedProducts.length < 4) {
        const additionalProducts = mockData.products
            .filter(p => p.id !== currentProduct.id && !relatedProducts.includes(p))
            .slice(0, 4 - relatedProducts.length);
        relatedProducts.push(...additionalProducts);
    }

    relatedProductsGrid.innerHTML = relatedProducts.map(product => `
        <div class="product-card bg-[#0B0E12] border border-[#3C3E42] rounded-lg overflow-hidden group">
            <a href="product-detail.html?id=${product.id}">
                <div class="product-image relative h-[194px]">
                    <img src="${product.thumbnail_image}" alt="${product.name}" class="w-full h-full object-cover">
                    ${product.discount > 0 ? `<span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">-${product.discount}%</span>` : ''}
                </div>
                <div class="p-4">
                    <h3 class="text-white font-semibold text-base mb-2 line-clamp-2">${product.name}</h3>
                    <div class="flex items-center space-x-2 mb-3">
                        ${createStarRating(product.average_rating)}
                        <span class="text-sm text-gray-400">${product.average_rating}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-primary-blue font-bold text-lg">$${product.offer_price}</span>
                            ${product.regular_price > product.offer_price ?
                                `<span class="text-gray-500 line-through text-sm ml-2">$${product.regular_price}</span>` : ''}
                        </div>
                    </div>
                </div>
            </a>
        </div>
    `).join('');
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
