<!-- resources/views/components/category-navigation.blade.php -->
<div class="w-full">
    <!-- Category Selection Buttons -->
    <div class="flex justify-center items-center space-x-4 mb-8 overflow-x-auto py-2">
        <!-- All Categories -->
        <button
            onclick="switchCategory('all')"
            id="btn-all"
            class="category-select-btn active flex flex-col items-center group"
        >
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#49b8ef] to-[#3da2d4] flex items-center justify-center mb-2 group-hover:scale-110 transition-all duration-300">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </div>
            <span class="text-xs text-gray-400 group-hover:text-white transition-colors">All</span>
        </button>

        <!-- Games -->
     <button
  onclick="switchCategory('games')"
  id="btn-games"
  class="category-select-btn flex flex-col items-center group"
>
  <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center mb-2 group-hover:bg-gradient-to-br group-hover:from-[#49b8ef] group-hover:to-[#3da2d4] group-hover:scale-110 transition-all duration-300">
    <svg class="w-8 h-8 text-gray-400 group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <!-- Controller body -->
      <path
        d="M8 9h8a4 4 0 0 1 4 4v1a3 3 0 0 1-3 3c-1.3 0-2.42-.83-2.82-2H9.82A3 3 0 1 1 6 14v-1a4 4 0 0 1 4-4Z"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      />
      <!-- Grips (subtle handles) -->
      <path
        d="M7.5 16.5c-.4 1.2-1.54 2-2.8 2A2.7 2.7 0 0 1 2 15.8V13.5"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      />
      <path
        d="M16.5 16.5c.4 1.2 1.54 2 2.8 2a2.7 2.7 0 0 0 2.7-2.7V13.5"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
      />
      <!-- D-pad -->
      <path d="M7 12h2M8 11v2" stroke-width="2" stroke-linecap="round" />
      <!-- Face buttons -->
      <circle cx="16" cy="11" r="0.9" fill="currentColor"/>
      <circle cx="18" cy="13" r="0.9" fill="currentColor"/>
      <circle cx="16" cy="15" r="0.9" fill="currentColor"/>
      <circle cx="14" cy="13" r="0.9" fill="currentColor"/>
      <!-- Analog sticks -->
      <circle cx="10" cy="14.5" r="0.85" fill="currentColor"/>
      <circle cx="13.5" cy="12.5" r="0.85" fill="currentColor"/>
    </svg>
  </div>
  <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Games</span>
</button>


        <!-- Subscriptions -->
        <button
            onclick="switchCategory('subscriptions')"
            id="btn-subscriptions"
            class="category-select-btn flex flex-col items-center group"
        >
            <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center mb-2 group-hover:bg-gradient-to-br group-hover:from-[#49b8ef] group-hover:to-[#3da2d4] group-hover:scale-110 transition-all duration-300">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4"></path>
                </svg>
            </div>
            <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Subscriptions</span>
        </button>

        <!-- App Stores -->
        <button
            onclick="switchCategory('app_stores')"
            id="btn-app_stores"
            class="category-select-btn flex flex-col items-center group"
        >
            <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center mb-2 group-hover:bg-gradient-to-br group-hover:from-[#49b8ef] group-hover:to-[#3da2d4] group-hover:scale-110 transition-all duration-300">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v6m8-6h6a2 2 0 012 2v2m0 0v6a2 2 0 01-2 2h-2m-4 0H6a2 2 0 01-2-2v-2"></path>
                </svg>
            </div>
            <span class="text-xs text-gray-400 group-hover:text-white transition-colors">App Stores</span>
        </button>

        <!-- Binance -->
        <button
            onclick="switchCategory('binance')"
            id="btn-binance"
            class="category-select-btn flex flex-col items-center group"
        >
            <div class="w-16 h-16 rounded-full bg-gray-800 flex items-center justify-center mb-2 group-hover:bg-gradient-to-br group-hover:from-[#49b8ef] group-hover:to-[#3da2d4] group-hover:scale-110 transition-all duration-300">
                <svg class="w-8 h-8 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-xs text-gray-400 group-hover:text-white transition-colors">Binance</span>
        </button>
    </div>

    <!-- Category Display Area -->
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        @if(isset($categorizedNav))
            @foreach(['all', 'games', 'subscriptions', 'app_stores', 'binance'] as $categoryType)
                <div
                    id="category-{{ $categoryType }}"
                    class="category-content {{ $categoryType === 'all' ? '' : 'hidden' }}"
                >
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($categorizedNav[$categoryType] as $category)
                            <a
                                href="{{ route('category.show', $category->slug) }}"
                                class="group bg-black border border-gray-800 rounded-xl p-6 hover:border-[#49b8ef] transition-all duration-300 hover:transform hover:scale-105"
                            >
                                <div class="flex flex-col items-center text-center">
                                    @if($category->image)
                                        <img
                                            src="{{ $category->image }}"
                                            alt="{{ $category->name }}"
                                            class="w-20 h-20 mb-4 object-contain"
                                        >
                                    @else
                                        <div class="w-20 h-20 mb-4 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl flex items-center justify-center group-hover:from-[#49b8ef]/20 group-hover:to-[#3da2d4]/20">
                                            <svg class="w-10 h-10 text-gray-600 group-hover:text-[#49b8ef]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <h3 class="text-sm font-semibold text-white mb-1 line-clamp-2">
                                        {{ $category->name }}
                                    </h3>
                                    @if($category->products_count > 0)
                                        <span class="text-xs text-[#49b8ef]">
                                            {{ $category->products_count }} items
                                        </span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    @if(count($categorizedNav[$categoryType]) === 0)
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📦</div>
                            <p class="text-gray-400">No categories available in this section</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🔍</div>
                <p class="text-gray-400">Loading categories...</p>
            </div>
        @endif
    </div>
</div>

<script>
function switchCategory(categoryType) {
    // Hide all category contents
    document.querySelectorAll('.category-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Show selected category content
    const selectedContent = document.getElementById(`category-${categoryType}`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }

    // Update button states
    document.querySelectorAll('.category-select-btn').forEach(btn => {
        btn.classList.remove('active');
        // Reset button styles
        const circle = btn.querySelector('div');
        if (circle) {
            circle.classList.remove('bg-gradient-to-br', 'from-[#49b8ef]', 'to-[#3da2d4]');
            circle.classList.add('bg-gray-800');
            const icon = circle.querySelector('svg');
            if (icon) {
                icon.classList.remove('text-white');
                icon.classList.add('text-gray-400');
            }
        }
    });

    // Activate selected button
    const activeBtn = document.getElementById(`btn-${categoryType}`);
    if (activeBtn) {
        activeBtn.classList.add('active');
        const circle = activeBtn.querySelector('div');
        if (circle) {
            circle.classList.remove('bg-gray-800');
            circle.classList.add('bg-gradient-to-br', 'from-[#49b8ef]', 'to-[#3da2d4]');
            const icon = circle.querySelector('svg');
            if (icon) {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-white');
            }
        }
    }
}

// Initialize with 'all' category selected
document.addEventListener('DOMContentLoaded', function() {
    switchCategory('all');
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.category-select-btn {
    transition: all 0.3s ease;
    cursor: pointer;
    background: none;
    border: none;
    outline: none;
}

.category-select-btn.active div {
    background: linear-gradient(to bottom right, #49b8ef, #3da2d4) !important;
}

.category-select-btn.active svg {
    color: white !important;
}

.category-select-btn.active span {
    color: #49b8ef !important;
    font-weight: 600;
}
</style>
