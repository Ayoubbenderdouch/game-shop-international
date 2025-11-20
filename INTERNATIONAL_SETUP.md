# Game Shop International - Setup Guide

## 🌍 International Features

This version of Game Shop includes comprehensive international features:

### ✨ Features Implemented

1. **Multi-Language Support**
   - English (EN)
   - Deutsch/German (DE)
   - Français/French (FR)
   - Español/Spanish (ES)
   - Italiano/Italian (IT)
   - العربية/Arabic (AR)

2. **Multi-Currency Support**
   - USD (US Dollar)
   - EUR (Euro)
   - GBP (British Pound)
   - AED (UAE Dirham)
   - SAR (Saudi Riyal)
   - EGP (Egyptian Pound)
   - JPY (Japanese Yen)
   - CAD (Canadian Dollar)
   - AUD (Australian Dollar)

3. **Country-Specific Features**
   - Auto-detection of user's country from IP
   - Country-specific VAT/Tax rates
   - Regional pricing support
   - Local payment methods

4. **Additional Features**
   - Automatic currency conversion
   - Real-time exchange rate updates
   - Timezone support
   - Localized content
   - Language & Currency switcher in UI

---

## 📋 Installation Steps

### 1. Run Migrations

```bash
cd "/Users/macbook/Desktop/Game-shop-international"
php artisan migrate
```

### 2. Seed International Data

```bash
php artisan db:seed --class=InternationalDataSeeder
```

This will initialize:
- Default currencies (USD, EUR, GBP, AED, SAR, etc.)
- Default countries (US, GB, DE, FR, ES, IT, AE, SA, etc.)
- Exchange rates

### 3. Configure Exchange Rate API (Optional but Recommended)

Get a free API key from [ExchangeRate-API](https://www.exchangerate-api.com/)

Add to `.env`:
```env
EXCHANGERATE_API_KEY=your_api_key_here
```

### 4. Update Exchange Rates

```bash
# Manual update
php artisan tinker
>>> app(\App\Services\CurrencyService::class)->updateExchangeRates();

# Or setup a scheduled task in app/Console/Kernel.php:
$schedule->call(function () {
    app(\App\Services\CurrencyService::class)->updateExchangeRates();
})->daily();
```

### 5. Register Middleware

The middleware `SetInternationalPreferences` should be added to `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetInternationalPreferences::class,
    ]);
})
```

### 6. Add Switcher to Layout

Add the international switcher to your main layout file (e.g., `resources/views/layouts/app.blade.php`):

```blade
<x-international-switcher />
```

Typical placement in navigation bar:

```blade
<nav>
    <!-- Your navigation items -->
    
    <!-- Add switcher here -->
    <x-international-switcher />
</nav>
```

---

## 🔧 Configuration

### App Configuration

`config/app.php`:
```php
'locale' => env('APP_LOCALE', 'en'),
'currency' => env('APP_CURRENCY', 'USD'),
'available_locales' => ['en', 'de', 'fr', 'es', 'it', 'ar'],
```

### Services Configuration

`config/services.php`:
```php
'exchangerate_api' => [
    'key' => env('EXCHANGERATE_API_KEY'),
    'base_url' => env('EXCHANGERATE_API_URL', 'https://v6.exchangerate-api.com/v6'),
],
```

---

## 💻 Usage Examples

### In Controllers

```php
use App\Services\CurrencyService;

class ProductController extends Controller
{
    protected $currencyService;
    
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    
    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        // Convert price to user's currency
        $price = $this->currencyService->convertPrice($product->selling_price);
        
        // Format price with currency symbol
        $formattedPrice = $this->currencyService->formatPrice($price);
        
        return view('product.show', compact('product', 'price', 'formattedPrice'));
    }
}
```

### In Blade Templates

```blade
<!-- Display price in user's currency -->
<p class="price">
    {{ app(\App\Services\CurrencyService::class)->formatPrice($product->selling_price) }}
</p>

<!-- Or if user is authenticated -->
@auth
    <p class="price">
        {{ auth()->user()->formatPrice($product->selling_price) }}
    </p>
@endauth

<!-- Translate text -->
<h1>{{ __('app.home.hero_title_1') }}</h1>
<p>{{ __('app.shop.search_placeholder') }}</p>
```

### JavaScript Currency Conversion

```javascript
// Convert price via AJAX
fetch('/currency/convert', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        amount: 100,
        from: 'USD',
        to: 'EUR'
    })
})
.then(response => response.json())
.then(data => {
    console.log(data.formatted); // "€92.00"
});
```

---

## 🗄️ Database Structure

### New Tables

1. **currency_rates**
   - Stores exchange rates for all currencies
   - Updated automatically via API

2. **countries**
   - Country information with localized names
   - Default currency and language per country
   - VAT/Tax rates

### Modified Tables

1. **users** - Added:
   - `country_code` (ISO 3166-1 alpha-2)
   - `currency` (ISO 4217)
   - `timezone`
   - `phone_country_code`
   - `preferred_language`

2. **products** - Added:
   - `regional_prices` (JSON) - For country-specific pricing

3. **orders** - Added:
   - `exchange_rate` - Exchange rate used for order
   - `customer_country` - Customer's country code

---

## 🎨 Frontend Integration

### Language Switcher

Users can switch languages from the dropdown:
- Automatically saves preference to session
- Saves to user profile if authenticated
- Persists across sessions

### Currency Switcher

Users can switch currencies from the dropdown:
- Real-time price conversion on frontend
- Automatically saves preference
- All prices update dynamically

---

## 📱 Mobile Responsive

The international switcher is fully responsive:
- Horizontal layout on desktop
- Vertical/stacked layout on mobile
- Touch-friendly dropdowns

---

## 🔐 Admin Features

### Currency Management

Access: `/admin/international/currencies`

- View all currencies
- Enable/disable currencies
- Update exchange rates manually
- Auto-update via API

### Country Management

Access: `/admin/international/countries`

- Manage country settings
- Set default currency per country
- Configure VAT/Tax rates
- Enable/disable countries

---

## 🌐 API Endpoints

### Public Endpoints

```
GET  /language/{locale}           - Switch language
POST /currency/switch             - Switch currency
GET  /currency/{currency}         - Get currency data
POST /currency/convert            - Convert amount
GET  /api/currencies              - Get all active currencies
```

### Admin Endpoints

```
POST /admin/international/currencies/update-rates  - Update exchange rates
```

---

## 🚀 Performance Optimization

1. **Caching**
   - Currency rates cached for 1 hour
   - Country data cached
   - Conversion rates cached

2. **Database Indexing**
   - Indexed currency codes
   - Indexed country codes
   - Indexed active status

---

## 🔍 Testing

### Test Currency Conversion

```bash
php artisan tinker

>>> $service = app(\App\Services\CurrencyService::class);
>>> $service->convertPrice(100, 'USD', 'EUR');  # Convert $100 to EUR
>>> $service->formatPrice(100, 'EUR');          # Format price with symbol
```

### Test Country Detection

```bash
>>> $service->detectCountryFromIP();
```

---

## 📝 Notes

1. **IP Detection**: Uses ip-api.com (free tier, no key required)
2. **Exchange Rates**: Updates daily (configure schedule)
3. **Default Rates**: Fallback rates provided if API fails
4. **Session Storage**: Language & currency preferences stored in session
5. **User Preferences**: Authenticated users' preferences saved to database

---

## 🐛 Troubleshooting

### Exchange Rates Not Updating

1. Check API key in `.env`
2. Check internet connection
3. Check API rate limits
4. View logs: `storage/logs/laravel.log`

### Currency Not Switching

1. Clear cache: `php artisan cache:clear`
2. Check if currency is active in database
3. Check browser console for errors

### Language Not Switching

1. Ensure translation files exist in `lang/{locale}/`
2. Clear config cache: `php artisan config:clear`
3. Check session storage

---

## 📚 Additional Resources

- [Laravel Localization](https://laravel.com/docs/localization)
- [ExchangeRate-API Documentation](https://www.exchangerate-api.com/docs)
- [ISO 4217 Currency Codes](https://www.iso.org/iso-4217-currency-codes.html)
- [ISO 3166 Country Codes](https://www.iso.org/iso-3166-country-codes.html)

---

## ✅ Checklist

- [ ] Run migrations
- [ ] Seed international data
- [ ] Configure Exchange Rate API
- [ ] Add middleware to bootstrap
- [ ] Add switcher to layout
- [ ] Test currency conversion
- [ ] Test language switching
- [ ] Configure scheduled tasks
- [ ] Test on mobile devices
- [ ] Update .env with API keys

---

## 🆘 Support

For issues or questions, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Browser console
3. Database query logs

---

**🎉 You're all set! Your Game Shop International is ready to serve customers worldwide!**
