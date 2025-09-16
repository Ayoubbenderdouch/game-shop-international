<!-- resources/views/components/category-navigation.blade.php -->
<div class="bg-gray-900 border-b border-gray-800">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <nav class="flex items-center space-x-1 py-3 overflow-x-auto scrollbar-hide">
            <!-- All Categories -->
            <button
                onclick="toggleCategoryDropdown('all')"
                class="category-nav-btn flex items-center px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-all duration-200 whitespace-nowrap"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                All Categories
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Games -->
            <button
                onclick="toggleCategoryDropdown('games')"
                class="category-nav-btn flex items-center px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-all duration-200 whitespace-nowrap"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Games
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Subscriptions -->
            <button
                onclick="toggleCategoryDropdown('subscriptions')"
                class="category-nav-btn flex items-center px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-all duration-200 whitespace-nowrap"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4"></path>
                </svg>
                Subscriptions
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- App Stores -->
            <button
                onclick="toggleCategoryDropdown('app_stores')"
                class="category-nav-btn flex items-center px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-all duration-200 whitespace-nowrap"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6m8-6h6a2 2 0 012 2v2m0 0v6a2 2 0 01-2 2h-2m-4 0H6a2 2 0 01-2-2v-2"></path>
                </svg>
                App Stores
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Binance -->
            <button
                onclick="toggleCategoryDropdown('binance')"
                class="category-nav-btn flex items-center px-4 py-2 text-sm font-medium text-gray-300 hover:text-white hover:bg-gray-800 rounded-lg transition-all duration-200 whitespace-nowrap"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Binance
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </nav>
    </div>
</div>

<!-- Category Dropdowns -->
<div class="relative">
    @if(isset($categorizedNav))
        @foreach(['all', 'games', 'subscriptions', 'app_stores', 'binance'] as $categoryType)
            <div
                id="dropdown-{{ $categoryType }}"
                class="category-dropdown absolute left-0 right-0 bg-gray-900 border-b border-gray-800 shadow-xl z-40 hidden"
            >
                <div class="max-w-[1170px] mx-auto px-5 lg:px-0 py-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($categorizedNav[$categoryType] as $category)
                            <a
                                href="{{ route('category.show', $category->slug) }}"
                                class="group flex flex-col items-center text-center p-3 rounded-lg hover:bg-gray-800 transition-all duration-200"
                            >
                                @if($category->image)
                                    <img
                                        src="{{ $category->image }}"
                                        alt="{{ $category->name }}"
                                        class="w-12 h-12 mb-2 object-contain"
                                    >
                                @else
                                    <div class="w-12 h-12 mb-2 bg-gray-800 rounded-lg flex items-center justify-center group-hover:bg-[#49b8ef]/20">
                                        <svg class="w-6 h-6 text-gray-400 group-hover:text-[#49b8ef]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                <span class="text-xs text-gray-400 group-hover:text-white line-clamp-2">
                                    {{ $category->name }}
                                </span>
                                @if($category->products_count > 0)
                                    <span class="text-xs text-gray-600 mt-1">
                                        {{ $category->products_count }} items
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
let activeDropdown = null;

function toggleCategoryDropdown(categoryType) {
    const dropdown = document.getElementById(`dropdown-${categoryType}`);
    const allDropdowns = document.querySelectorAll('.category-dropdown');

    // If clicking the same dropdown, toggle it
    if (activeDropdown === categoryType) {
        dropdown.classList.toggle('hidden');
        if (dropdown.classList.contains('hidden')) {
            activeDropdown = null;
        }
    } else {
        // Hide all dropdowns
        allDropdowns.forEach(d => d.classList.add('hidden'));
        // Show the selected dropdown
        dropdown.classList.remove('hidden');
        activeDropdown = categoryType;
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const navButtons = document.querySelectorAll('.category-nav-btn');
    const dropdowns = document.querySelectorAll('.category-dropdown');

    let isClickInside = false;

    // Check if click is on a nav button
    navButtons.forEach(btn => {
        if (btn.contains(event.target)) {
            isClickInside = true;
        }
    });

    // Check if click is inside a dropdown
    dropdowns.forEach(dropdown => {
        if (dropdown.contains(event.target)) {
            isClickInside = true;
        }
    });

    // If click is outside, close all dropdowns
    if (!isClickInside) {
        dropdowns.forEach(dropdown => dropdown.classList.add('hidden'));
        activeDropdown = null;
    }
});
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
