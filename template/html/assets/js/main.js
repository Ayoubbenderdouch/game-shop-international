// Main JavaScript for GameShop

// Mock Data
const mockData = {
    categories: [
        { id: 1, name: "Action", slug: "action", icon: "assets/img/icons/action.svg" },
        { id: 2, name: "Adventure", slug: "adventure", icon: "assets/img/icons/adventure.svg" },
        { id: 3, name: "RPG", slug: "rpg", icon: "assets/img/icons/rpg.svg" },
        { id: 4, name: "Sports", slug: "sports", icon: "assets/img/icons/sports.svg" },
        { id: 5, name: "Racing", slug: "racing", icon: "assets/img/icons/racing.svg" },
        { id: 6, name: "Strategy", slug: "strategy", icon: "assets/img/icons/strategy.svg" },
        { id: 7, name: "Simulation", slug: "simulation", icon: "assets/img/icons/simulation.svg" },
        { id: 8, name: "Horror", slug: "horror", icon: "assets/img/icons/horror.svg" }
    ],

    products: [
        {
            id: 1,
            name: "Call of Duty: Modern Warfare III",
            slug: "call-of-duty-mw3",
            thumbnail_image: "assets/img/products/cod-mw3.jpg",
            regular_price: 69.99,
            offer_price: 59.99,
            discount: 14,
            average_rating: 4.5,
            total_sale: 1250,
            category_id: 1,
            description: "The ultimate modern warfare experience"
        },
        {
            id: 2,
            name: "The Legend of Zelda: Tears of the Kingdom",
            slug: "zelda-totk",
            thumbnail_image: "assets/img/products/zelda-totk.jpg",
            regular_price: 69.99,
            offer_price: 64.99,
            discount: 7,
            average_rating: 5.0,
            total_sale: 2150,
            category_id: 2,
            description: "An epic adventure awaits"
        },
        {
            id: 3,
            name: "Baldur's Gate 3",
            slug: "baldurs-gate-3",
            thumbnail_image: "assets/img/products/baldurs-gate-3.jpg",
            regular_price: 59.99,
            offer_price: 49.99,
            discount: 17,
            average_rating: 4.9,
            total_sale: 3250,
            category_id: 3,
            description: "Forge your legend in this epic RPG"
        },
        {
            id: 4,
            name: "FIFA 24",
            slug: "fifa-24",
            thumbnail_image: "assets/img/products/fifa-24.jpg",
            regular_price: 69.99,
            offer_price: 54.99,
            discount: 21,
            average_rating: 4.2,
            total_sale: 1850,
            category_id: 4,
            description: "The world's game"
        },
        {
            id: 5,
            name: "Gran Turismo 7",
            slug: "gran-turismo-7",
            thumbnail_image: "assets/img/products/gt7.jpg",
            regular_price: 69.99,
            offer_price: 44.99,
            discount: 36,
            average_rating: 4.6,
            total_sale: 950,
            category_id: 5,
            description: "The real driving simulator"
        },
        {
            id: 6,
            name: "Civilization VI",
            slug: "civilization-6",
            thumbnail_image: "assets/img/products/civ6.jpg",
            regular_price: 59.99,
            offer_price: 29.99,
            discount: 50,
            average_rating: 4.7,
            total_sale: 2450,
            category_id: 6,
            description: "Build an empire to stand the test of time"
        },
        {
            id: 7,
            name: "The Sims 4",
            slug: "sims-4",
            thumbnail_image: "assets/img/products/sims4.jpg",
            regular_price: 39.99,
            offer_price: 19.99,
            discount: 50,
            average_rating: 4.3,
            total_sale: 3650,
            category_id: 7,
            description: "Play with life"
        },
        {
            id: 8,
            name: "Resident Evil 4 Remake",
            slug: "resident-evil-4",
            thumbnail_image: "assets/img/products/re4.jpg",
            regular_price: 59.99,
            offer_price: 49.99,
            discount: 17,
            average_rating: 4.8,
            total_sale: 1750,
            category_id: 8,
            description: "Survival is just the beginning"
        },
        {
            id: 9,
            name: "Spider-Man 2",
            slug: "spider-man-2",
            thumbnail_image: "assets/img/products/spiderman2.jpg",
            regular_price: 69.99,
            offer_price: 64.99,
            discount: 7,
            average_rating: 4.9,
            total_sale: 2850,
            category_id: 1,
            description: "Be greater together"
        },
        {
            id: 10,
            name: "Hogwarts Legacy",
            slug: "hogwarts-legacy",
            thumbnail_image: "assets/img/products/hogwarts.jpg",
            regular_price: 59.99,
            offer_price: 39.99,
            discount: 33,
            average_rating: 4.4,
            total_sale: 4150,
            category_id: 2,
            description: "Live the unwritten"
        }
    ],

    giftCards: [
        { id: 1, name: "Steam Gift Card $10", price: 10, image: "assets/img/gift-cards/steam-10.jpg" },
        { id: 2, name: "PlayStation Store $25", price: 25, image: "assets/img/gift-cards/ps-25.jpg" },
        { id: 3, name: "Xbox Gift Card $50", price: 50, image: "assets/img/gift-cards/xbox-50.jpg" }
    ]
};

// Cart Management
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

// Initialize Swiper
document.addEventListener('DOMContentLoaded', function() {
    // Hero Slider
    if (document.querySelector('.heroSwiper')) {
        new Swiper('.heroSwiper', {
            loop: true,
            speed: 1000,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            effect: 'fade',
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }

    // Load Categories
    loadCategories();

    // Load Best Selling Products
    loadBestSellingProducts();

    // Load Gift Cards
    loadGiftCards();

    // Load Recommended Products
    loadRecommendedProducts();

    // Update Cart Count
    updateCartCount();

    // Mobile Menu Toggle
    setupMobileMenu();
});

// Load Categories
function loadCategories() {
    const categoriesGrid = document.getElementById('categories-grid');
    const footerCategories = document.getElementById('footer-categories');

    if (categoriesGrid) {
        categoriesGrid.innerHTML = mockData.categories.map(category => `
            <a href="products.html?category=${category.slug}"
               class="categories-item w-full sm:h-[142px] h-[100px] flex justify-center items-center p-2 rounded-[5px] bg-black border border-[#23262B] hover:shadow-lg hover:border-primary-blue hover:shadow-primary-blue transition-all duration-300 group">
                <div class="flex flex-col space-y-2 items-center">
                    <div class="w-10 h-10 bg-primary-blue/20 rounded-full flex items-center justify-center group-hover:bg-primary-blue/30 transition-all">
                        <svg class="w-6 h-6 text-primary-blue" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-center font-semibold text-white transition-all duration-300">
                        ${category.name}
                    </p>
                </div>
            </a>
        `).join('');
    }

    if (footerCategories) {
        footerCategories.innerHTML = mockData.categories.slice(0, 5).map(category => `
            <li>
                <a href="products.html?category=${category.slug}"
                   class="text-base hover:text-primary-blue transition-all duration-300 leading-[34px]">
                    ${category.name}
                </a>
            </li>
        `).join('');
    }
}

// Load Best Selling Products
function loadBestSellingProducts() {
    const categoryTabs = document.getElementById('category-tabs');
    const productsGrid = document.getElementById('best-selling-grid');

    if (categoryTabs) {
        // Load category tabs
        const categories = mockData.categories.slice(0, 3);
        categoryTabs.innerHTML = categories.map((category, index) => `
            <button onclick="filterProducts(${category.id})"
                    class="category-tab py-4 px-[25px] flex space-x-2.5 items-center hover:text-black hover:bg-primary-blue transition-all duration-300 rounded-[5px] border hover:border-transparent border-[#66676B] ${index === 0 ? 'bg-primary-blue text-black border-transparent' : 'bg-transparent text-white'}"
                    data-category="${category.id}">
                <span class="text-base font-medium leading-5">${category.name}</span>
            </button>
        `).join('');
    }

    if (productsGrid) {
        // Load products for first category
        const filteredProducts = mockData.products.filter(p => p.category_id === 1).slice(0, 6);
        productsGrid.innerHTML = filteredProducts.map(product => createProductCard(product, 'row')).join('');
    }
}

// Filter Products by Category
function filterProducts(categoryId) {
    const productsGrid = document.getElementById('best-selling-grid');
    const tabs = document.querySelectorAll('.category-tab');

    // Update active tab
    tabs.forEach(tab => {
        if (tab.dataset.category == categoryId) {
            tab.classList.add('bg-primary-blue', 'text-black', 'border-transparent');
            tab.classList.remove('bg-transparent', 'text-white');
        } else {
            tab.classList.remove('bg-primary-blue', 'text-black', 'border-transparent');
            tab.classList.add('bg-transparent', 'text-white');
        }
    });

    // Filter and display products
    const filteredProducts = mockData.products.filter(p => p.category_id === categoryId).slice(0, 6);
    productsGrid.innerHTML = filteredProducts.map(product => createProductCard(product, 'row')).join('');
}

// Create Product Card HTML
function createProductCard(product, type = 'column') {
    const isInWishlist = wishlist.some(item => item.id === product.id);

    if (type === 'row') {
        return `
            <div class="product-card bg-[#0B0E12] border border-[#3C3E42] rounded-lg p-4 flex space-x-4">
                <div class="w-[120px] h-[120px] rounded-lg overflow-hidden flex-shrink-0">
                    <img src="${product.thumbnail_image}" alt="${product.name}" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-white font-semibold text-base mb-2 line-clamp-1">${product.name}</h3>
                        <p class="text-gray-400 text-sm line-clamp-2 mb-2">${product.description}</p>
                        <div class="flex items-center space-x-2 mb-2">
                            ${createStarRating(product.average_rating)}
                            <span class="text-sm text-gray-400">(${product.total_sale} sold)</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-primary-blue font-bold text-lg">$${product.offer_price}</span>
                            ${product.regular_price > product.offer_price ?
                                `<span class="text-gray-500 line-through text-sm">$${product.regular_price}</span>` : ''}
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="toggleWishlist(${product.id})"
                                    class="w-8 h-8 rounded-full bg-white/10 hover:bg-primary-blue/20 flex items-center justify-center transition-all">
                                <svg width="18" height="16" fill="${isInWishlist ? '#FF5757' : 'none'}" stroke="${isInWishlist ? '#FF5757' : 'currentColor'}" viewBox="0 0 18 16">
                                    <path d="M8.997 15.1457C8.76252 15.1457 8.53645 15.0608 8.36028 14.9065C7.6949 14.3247 7.05341 13.7779 6.48743 13.2956L6.48454 13.2931C4.8252 11.879 3.39229 10.6579 2.3953 9.45493C1.28082 8.11011 0.761719 6.83503 0.761719 5.44208C0.761719 4.08872 1.22578 2.84016 2.06834 1.92623C2.92094 1.00149 4.09084 0.492188 5.3629 0.492188C6.31365 0.492188 7.18435 0.792768 7.95075 1.38551C8.33753 1.68471 8.68812 2.05088 8.997 2.478C9.306 2.05088 9.65646 1.68471 10.0434 1.38551C10.8098 0.792768 11.6805 0.492188 12.6312 0.492188C13.9032 0.492188 15.0732 1.00149 15.9258 1.92623C16.7683 2.84016 17.2323 4.08872 17.2323 5.44208C17.2323 6.83503 16.7133 8.11011 15.5988 9.4548C14.6018 10.6579 13.169 11.8789 11.51 13.2929C10.943 13.7759 10.3005 14.3235 9.63359 14.9067C9.45754 15.0608 9.23135 15.1457 8.997 15.1457Z"/>
                                </svg>
                            </button>
                            <button onclick="addToCart(${product.id})"
                                    class="bg-primary-blue text-black px-4 py-2 rounded-md hover:bg-white transition-all text-sm font-medium">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    return `
        <div class="product-card bg-[#0B0E12] border border-[#3C3E42] rounded-lg overflow-hidden group">
            <div class="product-image relative">
                <img src="${product.thumbnail_image}" alt="${product.name}" class="w-full h-full object-cover">
                <button onclick="toggleWishlist(${product.id})" class="wishlist-btn">
                    <svg width="18" height="16" fill="${isInWishlist ? '#FF5757' : 'none'}" stroke="${isInWishlist ? '#FF5757' : '#616161'}" viewBox="0 0 18 16">
                        <path d="M8.997 15.1457C8.76252 15.1457 8.53645 15.0608 8.36028 14.9065C7.6949 14.3247 7.05341 13.7779 6.48743 13.2956L6.48454 13.2931C4.8252 11.879 3.39229 10.6579 2.3953 9.45493C1.28082 8.11011 0.761719 6.83503 0.761719 5.44208C0.761719 4.08872 1.22578 2.84016 2.06834 1.92623C2.92094 1.00149 4.09084 0.492188 5.3629 0.492188C6.31365 0.492188 7.18435 0.792768 7.95075 1.38551C8.33753 1.68471 8.68812 2.05088 8.997 2.478C9.306 2.05088 9.65646 1.68471 10.0434 1.38551C10.8098 0.792768 11.6805 0.492188 12.6312 0.492188C13.9032 0.492188 15.0732 1.00149 15.9258 1.92623C16.7683 2.84016 17.2323 4.08872 17.2323 5.44208C17.2323 6.83503 16.7133 8.11011 15.5988 9.4548C14.6018 10.6579 13.169 11.8789 11.51 13.2929C10.943 13.7759 10.3005 14.3235 9.63359 14.9067C9.45754 15.0608 9.23135 15.1457 8.997 15.1457Z"/>
                    </svg>
                </button>
                ${product.discount > 0 ? `<span class="discount-badge">-${product.discount}%</span>` : ''}
            </div>
            <div class="p-4">
                <h3 class="text-white font-semibold text-base mb-2 line-clamp-2">${product.name}</h3>
                <div class="flex items-center space-x-2 mb-3">
                    ${createStarRating(product.average_rating)}
                    <span class="text-sm text-gray-400">${product.average_rating}</span>
                </div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="text-primary-blue font-bold text-lg">$${product.offer_price}</span>
                        ${product.regular_price > product.offer_price ?
                            `<span class="text-gray-500 line-through text-sm ml-2">$${product.regular_price}</span>` : ''}
                    </div>
                </div>
                <button onclick="addToCart(${product.id})"
                        class="w-full bg-primary-blue text-black py-2 rounded-md hover:bg-white transition-all font-medium">
                    Add to Cart
                </button>
            </div>
        </div>
    `;
}

// Create Star Rating HTML
function createStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let stars = '';

    for (let i = 0; i < fullStars; i++) {
        stars += `<svg width="16" height="16" fill="#FFB321" viewBox="0 0 20 20">
            <path d="M10 0L12.09 6.26L19 7.27L14.5 11.97L15.82 19L10 15.58L4.18 19L5.5 11.97L1 7.27L7.91 6.26L10 0Z"/>
        </svg>`;
    }

    if (hasHalfStar) {
        stars += `<svg width="16" height="16" fill="#FFB321" viewBox="0 0 20 20">
            <path d="M10 0L12.09 6.26L19 7.27L14.5 11.97L15.82 19L10 15.58L4.18 19L5.5 11.97L1 7.27L7.91 6.26L10 0Z" opacity="0.5"/>
        </svg>`;
    }

    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        stars += `<svg width="16" height="16" fill="#333" viewBox="0 0 20 20">
            <path d="M10 0L12.09 6.26L19 7.27L14.5 11.97L15.82 19L10 15.58L4.18 19L5.5 11.97L1 7.27L7.91 6.26L10 0Z"/>
        </svg>`;
    }

    return `<div class="flex items-center space-x-1">${stars}</div>`;
}

// Load Gift Cards
function loadGiftCards() {
    const giftCardsGrid = document.getElementById('gift-cards-grid');

    if (giftCardsGrid) {
        giftCardsGrid.innerHTML = mockData.giftCards.map(card => `
            <a href="product-detail.html?id=${card.id}" class="border border-[#23262B] rounded-md hover:border-primary-blue p-[6px] transition-all duration-300 h-[170px] block">
                <div class="w-full h-full bg-gradient-to-br from-purple-600 to-blue-600 rounded-md flex items-center justify-center">
                    <div class="text-center">
                        <h3 class="text-white font-bold text-xl mb-2">${card.name}</h3>
                        <p class="text-white/80">Instant Delivery</p>
                    </div>
                </div>
            </a>
        `).join('');
    }
}

// Load Recommended Products
function loadRecommendedProducts() {
    const recommendedGrid = document.getElementById('recommended-grid');

    if (recommendedGrid) {
        const recommendedProducts = mockData.products.slice(0, 6);
        recommendedGrid.innerHTML = recommendedProducts.map(product => createProductCard(product, 'row')).join('');
    }
}

// Cart Functions
function addToCart(productId) {
    const product = mockData.products.find(p => p.id === productId);
    if (!product) return;

    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            ...product,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showToast('Product added to cart!');
}

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;
    }
}

// Wishlist Functions
function toggleWishlist(productId) {
    const product = mockData.products.find(p => p.id === productId);
    if (!product) return;

    const index = wishlist.findIndex(item => item.id === productId);

    if (index > -1) {
        wishlist.splice(index, 1);
        showToast('Removed from wishlist');
    } else {
        wishlist.push(product);
        showToast('Added to wishlist!');
    }

    localStorage.setItem('wishlist', JSON.stringify(wishlist));

    // Refresh the page to update wishlist icons
    location.reload();
}

// Mobile Menu
function setupMobileMenu() {
    const menuToggle = document.getElementById('mobile-menu-toggle');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            // Create mobile menu if it doesn't exist
            let mobileMenu = document.querySelector('.mobile-menu');
            let overlay = document.querySelector('.mobile-menu-overlay');

            if (!mobileMenu) {
                // Create menu HTML
                const menuHTML = `
                    <div class="mobile-menu">
                        <div class="p-5">
                            <div class="flex justify-between items-center mb-8">
                                <img src="assets/img/logo.png" alt="GameShop" class="h-[30px]">
                                <button onclick="closeMobileMenu()" class="text-white">
                                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                    </svg>
                                </button>
                            </div>
                            <nav class="space-y-4">
                                <a href="index.html" class="block text-white hover:text-primary-blue transition-all py-2">Home</a>
                                <a href="products.html" class="block text-white hover:text-primary-blue transition-all py-2">Products</a>
                                <a href="about.html" class="block text-white hover:text-primary-blue transition-all py-2">About</a>
                                <a href="blogs.html" class="block text-white hover:text-primary-blue transition-all py-2">Blogs</a>
                                <a href="contact.html" class="block text-white hover:text-primary-blue transition-all py-2">Contact</a>
                            </nav>
                        </div>
                    </div>
                    <div class="mobile-menu-overlay" onclick="closeMobileMenu()"></div>
                `;

                document.body.insertAdjacentHTML('beforeend', menuHTML);
                mobileMenu = document.querySelector('.mobile-menu');
                overlay = document.querySelector('.mobile-menu-overlay');
            }

            // Toggle menu
            mobileMenu.classList.add('active');
            overlay.classList.add('active');
        });
    }
}

function closeMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    const overlay = document.querySelector('.mobile-menu-overlay');

    if (mobileMenu) mobileMenu.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
}

// Toast Notification
function showToast(message, type = 'success') {
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = type === 'success' ? '#45F882' : '#FF5757';
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Search Functionality
function setupSearch() {
    const searchInput = document.getElementById('search-input');
    const searchButton = document.getElementById('search-button');

    if (searchInput && searchButton) {
        searchButton.addEventListener('click', () => {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = `products.html?search=${encodeURIComponent(query)}`;
            }
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = searchInput.value.trim();
                if (query) {
                    window.location.href = `products.html?search=${encodeURIComponent(query)}`;
                }
            }
        });
    }
}

// Initialize search on page load
document.addEventListener('DOMContentLoaded', setupSearch);
