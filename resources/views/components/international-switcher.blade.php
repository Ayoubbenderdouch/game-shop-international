<!-- Language & Currency Switcher Component -->
<div class="international-switcher" style="display: flex; gap: 15px; align-items: center;">
    
    <!-- Language Switcher -->
    <div class="language-switcher dropdown">
        <button class="switcher-btn" onclick="toggleDropdown('languageDropdown')" style="background: transparent; border: 1px solid #e0e0e0; padding: 8px 15px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 1.2rem;">{{ $availableLocales[$currentLocale]['flag'] ?? '🌐' }}</span>
            <span style="font-weight: 500;">{{ $availableLocales[$currentLocale]['name'] ?? 'English' }}</span>
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div id="languageDropdown" class="dropdown-menu" style="display: none; position: absolute; background: white; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-top: 8px; min-width: 180px; z-index: 1000;">
            @foreach($availableLocales as $code => $locale)
                <a href="{{ route('language.switch', $code) }}" 
                   class="dropdown-item {{ $currentLocale == $code ? 'active' : '' }}"
                   style="display: flex; align-items: center; gap: 10px; padding: 10px 15px; text-decoration: none; color: #333; transition: background 0.2s; {{ $currentLocale == $code ? 'background: #f5f5f5; font-weight: 600;' : '' }}">
                    <span style="font-size: 1.2rem;">{{ $locale['flag'] }}</span>
                    <span>{{ $locale['name'] }}</span>
                    @if($currentLocale == $code)
                        <svg style="width: 16px; height: 16px; margin-left: auto; color: #4CAF50;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Currency Switcher -->
    <div class="currency-switcher dropdown">
        <button class="switcher-btn" onclick="toggleDropdown('currencyDropdown')" style="background: transparent; border: 1px solid #e0e0e0; padding: 8px 15px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <span style="font-weight: 600; font-size: 1.1rem;">{{ $currentCurrency }}</span>
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <div id="currencyDropdown" class="dropdown-menu" style="display: none; position: absolute; background: white; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-top: 8px; min-width: 200px; z-index: 1000; max-height: 300px; overflow-y: auto;">
            @foreach($availableCurrencies as $currency)
                <form method="POST" action="{{ route('currency.switch') }}" class="currency-form">
                    @csrf
                    <input type="hidden" name="currency" value="{{ $currency->currency }}">
                    <button type="submit" 
                            class="dropdown-item {{ $currentCurrency == $currency->currency ? 'active' : '' }}"
                            style="width: 100%; text-align: left; background: none; border: none; display: flex; align-items: center; gap: 10px; padding: 10px 15px; cursor: pointer; color: #333; transition: background 0.2s; {{ $currentCurrency == $currency->currency ? 'background: #f5f5f5; font-weight: 600;' : '' }}">
                        <span style="font-size: 1.2rem;">{{ $currency->currency_symbol }}</span>
                        <span>{{ $currency->currency }}</span>
                        <span style="font-size: 0.85rem; color: #666;">{{ $currency->currency_name }}</span>
                        @if($currentCurrency == $currency->currency)
                            <svg style="width: 16px; height: 16px; margin-left: auto; color: #4CAF50;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>

</div>

<script>
function toggleDropdown(dropdownId) {
    // Close all other dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== dropdownId) {
            menu.style.display = 'none';
        }
    });
    
    // Toggle the clicked dropdown
    const dropdown = document.getElementById(dropdownId);
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

// Hover effects
document.querySelectorAll('.dropdown-item').forEach(item => {
    if (!item.classList.contains('active')) {
        item.addEventListener('mouseenter', function() {
            this.style.background = '#f9f9f9';
        });
        item.addEventListener('mouseleave', function() {
            this.style.background = 'transparent';
        });
    }
});
</script>

<style>
.international-switcher {
    position: relative;
}

.dropdown {
    position: relative;
}

.switcher-btn:hover {
    background: #f5f5f5 !important;
    border-color: #ccc !important;
}

.dropdown-item:hover {
    background: #f9f9f9;
}

@media (max-width: 768px) {
    .international-switcher {
        flex-direction: column;
        gap: 10px;
    }
    
    .switcher-btn {
        width: 100%;
        justify-content: space-between;
    }
    
    .dropdown-menu {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 300px;
    }
}
</style>
