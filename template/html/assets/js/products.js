// Products Page JavaScript

let allProducts = [...mockData.products];
let filteredProducts = [...allProducts];
let currentPage = 1;
const productsPerPage = 9;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Load categories filter
    loadCategoryFilter();

    // Initialize price slider
    initPriceSlider();

    // Load products
    loadProducts();

    // Check URL parameters
    checkURLParams();

    // Setup event listeners
    setupEventListeners();

    // Update cart count
    updateCartCount();
});

// Load Category Filter
function loadCategoryFilter() {
    const categoryFilter = document.getElementById('category-filter');

    if (categoryFilter) {
        categoryFilter.innerHTML = `
            <li class="mb-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="category" value="all" checked class="category-radio">
                    <span class="text-white hover:text-primary-blue transition-all">All Categories</span>
                </label>
            </li>
            ${mockData.categories.map(category => `
                <li class="mb-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="category" value="${category.id}" class="category-radio">
                        <span class="text-white hover:text-primary-blue transition-all">${category.name}</span>
                    </label>
                </li>
            `).join('')}
        `;
    }
}

// Initialize Price Slider
function initPriceSlider() {
    const priceSlider = document.getElementById('price-slider');

    if (priceSlider) {
        noUiSlider.create(priceSlider, {
            start: [0, 100],
            connect: true,
            range: {
                'min': 0,
                'max': 100
            },
            format: {
                to: function (value) {
                    return Math.round(value);
                },
                from: function (value) {
                    return Number(value);
                }
            }
        });

        priceSlider.noUiSlider.on('update', function (values, handle) {
            document.getElementById('min-price').textContent = values[0];
            document.getElementById('max-price').textContent = values[1];
        });

        priceSlider.noUiSlider.on('change', function () {
            applyFilters();
        });
    }
}

// Setup Event Listeners
function setupEventListeners() {
    // Category filter
    document.querySelectorAll('.category-radio').forEach(radio => {
        radio.addEventListener('change', applyFilters);
    });

    // Rating filter
    document.querySelectorAll('.rating-filter').forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });

    // Sort select
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.addEventListener('change', sortProducts);
    }

    // Search input
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });
    }
}

// Check URL Parameters
function checkURLParams() {
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    const search = urlParams.get('search');

    if (category) {
        const categoryId = mockData.categories.find(c => c.slug === category)?.id;
        if (categoryId) {
            const radio = document.querySelector(`.category-radio[value="${categoryId}"]`);
            if (radio) {
                radio.checked = true;
                applyFilters();
            }
        }
    }

    if (search) {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = search;
            searchProducts();
        }
    }
}

// Apply Filters
function applyFilters() {
    // Get selected category
    const selectedCategory = document.querySelector('.category-radio:checked')?.value;

    // Get price range
    const priceSlider = document.getElementById('price-slider');
    let minPrice = 0;
    let maxPrice = 100;

    if (priceSlider && priceSlider.noUiSlider) {
        const values = priceSlider.noUiSlider.get();
        minPrice = parseFloat(values[0]);
        maxPrice = parseFloat(values[1]);
    }

    // Get selected ratings
    const selectedRatings = Array.from(document.querySelectorAll('.rating-filter:checked'))
        .map(checkbox => parseFloat(checkbox.value));

    // Filter products
    filteredProducts = allProducts.filter(product => {
        // Category filter
        if (selectedCategory !== 'all' && product.category_id !== parseInt(selectedCategory)) {
            return false;
        }

        // Price filter
        const productPrice = product.offer_price || product.regular_price;
        if (productPrice < minPrice || productPrice > maxPrice) {
            return false;
        }

        // Rating filter
        if (selectedRatings.length > 0) {
            const productRating = Math.floor(product.average_rating);
            if (!selectedRatings.includes(productRating)) {
                return false;
            }
        }

        return true;
    });

    // Reset to first page
    currentPage = 1;

    // Load filtered products
    loadProducts();
}

// Sort Products
function sortProducts() {
    const sortValue = document.getElementById('sort-select').value;

    switch (sortValue) {
        case 'price-low':
            filteredProducts.sort((a, b) => (a.offer_price || a.regular_price) - (b.offer_price || b.regular_price));
            break;
        case 'price-high':
            filteredProducts.sort((a, b) => (b.offer_price || b.regular_price) - (a.offer_price || a.regular_price));
            break;
        case 'rating':
            filteredProducts.sort((a, b) => b.average_rating - a.average_rating);
            break;
        case 'popular':
            filteredProducts.sort((a, b) => b.total_sale - a.total_sale);
            break;
        default:
            filteredProducts = [...allProducts];
            applyFilters();
            return;
    }

    loadProducts();
}

// Search Products
function searchProducts() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();

    if (searchTerm === '') {
        filteredProducts = [...allProducts];
    } else {
        filteredProducts = allProducts.filter(product => {
            return product.name.toLowerCase().includes(searchTerm) ||
                   product.description.toLowerCase().includes(searchTerm);
        });
    }

    currentPage = 1;
    loadProducts();
}

// Load Products
function loadProducts() {
    const productsGrid = document.getElementById('products-grid');
    const productCount = document.getElementById('product-count');
    const loadMoreBtn = document.getElementById('load-more');

    if (!productsGrid) return;

    // Calculate products to show
    const startIndex = 0;
    const endIndex = currentPage * productsPerPage;
    const productsToShow = filteredProducts.slice(startIndex, endIndex);

    // Update product count
    if (productCount) {
        productCount.textContent = filteredProducts.length;
    }

    // Display products
    if (productsToShow.length === 0) {
        productsGrid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <p class="text-xl text-gray-400 mb-4">No products found</p>
                <button onclick="resetFilters()" class="bg-primary-blue text-black px-6 py-2 rounded-md hover:bg-white transition-all font-medium">
                    Reset Filters
                </button>
            </div>
        `;
    } else {
        productsGrid.innerHTML = productsToShow.map(product => createProductCard(product)).join('');
    }

    // Show/hide load more button
    if (loadMoreBtn) {
        if (endIndex >= filteredProducts.length) {
            loadMoreBtn.style.display = 'none';
        } else {
            loadMoreBtn.style.display = 'inline-block';
        }
    }
}

// Load More Products
function loadMoreProducts() {
    currentPage++;
    loadProducts();
}

// Reset Filters
function resetFilters() {
    // Reset category
    const allCategoryRadio = document.querySelector('.category-radio[value="all"]');
    if (allCategoryRadio) {
        allCategoryRadio.checked = true;
    }

    // Reset price slider
    const priceSlider = document.getElementById('price-slider');
    if (priceSlider && priceSlider.noUiSlider) {
        priceSlider.noUiSlider.set([0, 100]);
    }

    // Reset rating filters
    document.querySelectorAll('.rating-filter').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Reset search
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.value = '';
    }

    // Reset sort
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) {
        sortSelect.value = 'default';
    }

    // Reset filtered products
    filteredProducts = [...allProducts];
    currentPage = 1;

    // Load products
    loadProducts();
}

// Add more sample products for better pagination demonstration
for (let i = 11; i <= 30; i++) {
    allProducts.push({
        id: i,
        name: `Game Title ${i}`,
        slug: `game-title-${i}`,
        thumbnail_image: `assets/img/products/game-${i % 10 + 1}.jpg`,
        regular_price: Math.floor(Math.random() * 40) + 30,
        offer_price: Math.floor(Math.random() * 30) + 20,
        discount: Math.floor(Math.random() * 50) + 10,
        average_rating: (Math.random() * 2 + 3).toFixed(1),
        total_sale: Math.floor(Math.random() * 5000) + 100,
        category_id: (i % 8) + 1,
        description: `Amazing game ${i} with incredible graphics and gameplay`
    });
}

// Update filteredProducts with all products
filteredProducts = [...allProducts];
