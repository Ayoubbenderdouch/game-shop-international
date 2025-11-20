# 🌍 Game Shop International - Complete Setup Guide

## ✨ International Features Implemented

### 1. **Multi-Language Support** (6 Languages)
- 🇬🇧 English (EN)
- 🇩🇪 German (DE)
- 🇫🇷 French (FR)
- 🇪🇸 Spanish (ES)
- 🇮🇹 Italian (IT)
- 🇸🇦 Arabic (AR)

**Features:**
- ✅ Language switcher in navigation & auth pages
- ✅ Auto-detection from browser preferences
- ✅ RTL support for Arabic
- ✅ Complete translations in `/lang/*` directories
- ✅ Session-based language persistence

---

### 2. **Multi-Currency System** (9 Currencies)
- 💵 USD (US Dollar)
- 💶 EUR (Euro)
- 💷 GBP (British Pound)
- 🇦🇪 AED (UAE Dirham)
- 🇸🇦 SAR (Saudi Riyal)
- 🇪🇬 EGP (Egyptian Pound)
- 💴 JPY (Japanese Yen)
- 🇨🇦 CAD (Canadian Dollar)
- 🇦🇺 AUD (Australian Dollar)

**Features:**
- ✅ Real-time currency conversion
- ✅ Automatic exchange rate updates via API
- ✅ Currency switcher in navigation
- ✅ Price display in user's preferred currency
- ✅ Multi-currency payment support (Stripe, PayPal)
- ✅ Country-specific VAT/Tax rates

---

### 3. **Guest Checkout** 🛒
- ✅ Purchase without registration
- ✅ Guest cart in session
- ✅ Email confirmation for guest orders
- ✅ Order tracking by reference number
- ✅ Supports all payment methods

**Routes:**
```php
/guest/cart/add          - Add to guest cart
/guest/checkout          - Guest checkout page
/guest/checkout/process  - Process guest order
/guest/checkout/success  - Order success page
```

---

### 4. **Country-Specific Features** (8+ Countries)
- 🇺🇸 United States (USD, English)
- 🇬🇧 United Kingdom (GBP, English) - 20% VAT
- 🇩🇪 Germany (EUR, German) - 19% MwSt
- 🇫🇷 France (EUR, French) - 20% TVA
- 🇪🇸 Spain (EUR, Spanish) - 21% IVA
- 🇮🇹 Italy (EUR, Italian) - 22% IVA
- 🇦🇪 UAE (AED, Arabic) - 5% VAT
- 🇸🇦 Saudi Arabia (SAR, Arabic) - 15% VAT

**Features:**
- ✅ IP-based country detection
- ✅ Automatic VAT calculation per country
- ✅ Country-specific default currency & language
- ✅ Localized country names in all languages

---

## 🚀 Installation & Setup

### Step 1: Environment Configuration

Add to your `.env`:

```env
# App Configuration
APP_LOCALE=en
APP_CURRENCY=USD

# Stripe (for international payments)
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key

# ExchangeRate API (for currency conversion)
# Get free API key from: https://www.exchangerate-api.com/
EXCHANGERATE_API_KEY=your_api_key_here
EXCHANGERATE_API_URL=https://v6.exchangerate-api.com/v6
```

---

### Step 2: Run Migrations

```bash
cd "/Users/macbook/Desktop/Sami Web/Game-shop-international"

# Run all migrations (including international features)
php artisan migrate

# The following will be created:
# - currency_rates table
# - countries table
# - Added guest fields to orders table
# - Added international fields to users table
```

---

### Step 3: Seed International Data

```bash
# Seed currencies, countries, and exchange rates
php artisan db:seed --class=InternationalDataSeeder

# This creates:
# - 9 active currencies with current exchange rates
# - 8+ countries with VAT rates and settings
# - Default currency symbols and formatting
```

---

### Step 4: Update Exchange Rates

```bash
# Manual update
php artisan tinker
>>> app(\App\Services\CurrencyService::class)->updateExchangeRates();

# Or setup automatic daily updates in app/Console/Kernel.php:
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        app(\App\Services\CurrencyService::class)->updateExchangeRates();
    })->daily();
}
```

---

### Step 5: Stripe Setup for Multi-Currency

1. **Create Stripe Account**: https://stripe.com
2. **Enable International Payments**:
   - Go to Dashboard → Settings → Payment Methods
   - Enable cards for all supported countries
3. **Configure Multi-Currency**:
   - Dashboard → Settings → Currencies
   - Enable: USD, EUR, GBP, AED, SAR, etc.
4. **Get API Keys**:
   - Dashboard → Developers → API Keys
   - Copy Publishable & Secret keys to `.env`

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
    
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        
        // Convert price to user's currency
        $price = $this->currencyService->convertPrice($product->selling_price);
        
        // Format with currency symbol
        $formattedPrice = $this->currencyService->formatPrice($price);
        
        return view('product.show', compact('product', 'price', 'formattedPrice'));
    }
}
```

---

### In Blade Templates

**Using Price Component:**
```blade
<!-- Automatic conversion to user's currency -->
<x-price :price="$product->selling_price" />

<!-- With custom CSS class -->
<x-price :price="$product->selling_price" class="text-2xl font-bold" />
```

**Using CurrencyService Directly:**
```blade
@php
    $currencyService = app(\App\Services\CurrencyService::class);
    $currency = $currencyService->getUserCurrency();
    $price = $currencyService->formatPrice($product->selling_price);
@endphp

<p>Price: {{ $price }}</p>
<p>Currency: {{ $currency }}</p>
```

**Language Translation:**
```blade
<!-- Simple translation -->
<h1>{{ __('app.home.welcome') }}</h1>

<!-- With parameters -->
<p>{{ __('app.cart.items_count', ['count' => $count]) }}</p>

<!-- In navigation -->
<a href="{{ route('home') }}">{{ __('app.nav.home') }}</a>
```

---

### JavaScript Currency Conversion

```javascript
// Convert price via AJAX
async function convertPrice(amount, fromCurrency, toCurrency) {
    const response = await fetch('/currency/convert', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            amount: amount,
            from: fromCurrency,
            to: toCurrency
        })
    });
    
    const data = await response.json();
    return data.converted_amount;
}

// Get all active currencies
async function getCurrencies() {
    const response = await fetch('/api/currencies');
    const currencies = await response.json();
    return currencies;
}
```

---

## 🛠️ Key Files & Components

### Controllers
- `InternationalController.php` - Language & currency switching
- `GuestCheckoutController.php` - Guest checkout functionality

### Services
- `CurrencyService.php` - Currency conversion & formatting
- `PricingService.php` - Price calculations with VAT

### Middleware
- `SetInternationalPreferences.php` - Auto-detect & set language/currency

### Models
- `Country.php` - Country data with localized names
- `CurrencyRate.php` - Exchange rates with caching
- `Order.php` - Extended for guest orders & multi-currency

### Views
- `layouts/navigation.blade.php` - International switcher
- `layouts/guest.blade.php` - Guest pages with switcher
- `components/price.blade.php` - Price display component
- `checkout/guest.blade.php` - Guest checkout page
- `checkout/guest-success.blade.php` - Guest order success

### Migrations
- `2025_11_05_100000_add_international_features.php` - Currencies & countries
- `2025_11_06_100000_add_guest_checkout_support.php` - Guest order fields

---

## 🔧 Configuration Files

### config/app.php
```php
'locale' => env('APP_LOCALE', 'en'),
'currency' => env('APP_CURRENCY', 'USD'),
'available_locales' => ['en', 'de', 'fr', 'es', 'it', 'ar'],
```

### config/services.php
```php
'exchangerate_api' => [
    'key' => env('EXCHANGERATE_API_KEY'),
    'base_url' => env('EXCHANGERATE_API_URL', 'https://v6.exchangerate-api.com/v6'),
],

'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
],
```

---

## 📝 Routes Summary

### Public Routes
```php
GET  /language/{locale}      - Switch language
POST /currency/switch        - Switch currency
GET  /currency/{currency}    - Get currency data
POST /currency/convert       - Convert price (AJAX)
GET  /api/currencies         - List active currencies
```

### Guest Checkout Routes
```php
POST /guest/cart/add           - Add to guest cart
GET  /guest/cart               - Get guest cart
GET  /guest/checkout           - Guest checkout page
POST /guest/checkout/process   - Process guest order
GET  /guest/checkout/success   - Order success page
```

### Authenticated Routes
```php
GET  /checkout           - Regular checkout (requires auth)
POST /checkout/process   - Process order (requires auth)
GET  /checkout/success   - Order success (requires auth)
```

---

## 🎨 Frontend Features

### Language & Currency Switcher
- **Location**: Top navigation bar (desktop & mobile)
- **Guest Pages**: Top-right corner on login/register pages
- **Features**:
  - Dropdown with flags for languages
  - Currency symbols for currency selector
  - Active state indication
  - Mobile responsive
  - Alpine.js for interactions

### Price Display
- Automatic conversion to user's currency
- Shows original USD price for transparency
- VAT information when applicable
- Formatted with correct currency symbol

### Checkout Experience
- Guest or authenticated checkout options
- Multi-currency payment processing
- Country-specific VAT calculation
- Email confirmation in user's language

---

## 🧪 Testing

### Test Currency Conversion
```bash
php artisan tinker

$service = app(\App\Services\CurrencyService::class);
$service->updateExchangeRates();

# Convert $100 USD to EUR
$service->convertPrice(100, 'EUR');

# Format price
$service->formatPrice(100, 'EUR');
```

### Test Guest Checkout
1. Visit any product page
2. Add to cart (without logging in)
3. Go to `/guest/checkout`
4. Fill in guest information
5. Complete payment with test card:
   - Card: 4242 4242 4242 4242
   - Exp: Any future date
   - CVC: Any 3 digits

### Test Language Switching
1. Click language dropdown in navigation
2. Select a language (e.g., Deutsch)
3. Verify all UI elements are translated
4. Check RTL for Arabic

### Test Currency Switching
1. Click currency dropdown
2. Select a currency (e.g., EUR)
3. Verify all prices are converted
4. Check checkout calculates correctly

---

## 📊 Database Schema Changes

### orders table (extended)
```sql
user_id                 # Now nullable for guest orders
guest_email             # Guest customer email
guest_name              # Guest customer name
guest_phone             # Guest customer phone (optional)
payment_transaction_id  # Stripe/PayPal transaction ID
currency                # Order currency (USD, EUR, etc.)
exchange_rate           # Exchange rate used
customer_country        # Customer's country code
```

### currency_rates table (new)
```sql
currency        # ISO 4217 code (USD, EUR, etc.)
currency_name   # Full name (US Dollar, Euro, etc.)
currency_symbol # Symbol ($, €, £, etc.)
rate_to_usd     # Exchange rate to USD
is_active       # Active/Inactive
last_updated    # Last rate update timestamp
```

### countries table (new)
```sql
code              # ISO 3166-1 alpha-2 (US, GB, DE, etc.)
name              # English name
localized_names   # JSON with names in all languages
default_currency  # Default currency for country
default_language  # Default language for country
vat_rate          # VAT/Tax percentage
phone_code        # International phone code (+1, +44, etc.)
is_active         # Active/Inactive
```

---

## 🌐 Supported Payment Methods

### Stripe (Credit/Debit Cards)
- ✅ Multi-currency support
- ✅ 3D Secure authentication
- ✅ Instant payment confirmation
- ✅ Automatic currency conversion

### PayPal (Coming Soon)
- 🔄 Integration in progress
- 🔄 Support for international accounts
- 🔄 Multiple currency wallets

---

## 📧 Email Notifications

All emails are sent in the customer's preferred language:
- Order confirmation
- Order status updates
- Guest order details with codes
- Payment receipts

---

## 🔒 Security Features

- ✅ CSRF protection on all forms
- ✅ SSL required for payment processing
- ✅ Secure session management
- ✅ Input validation and sanitization
- ✅ Rate limiting on API endpoints
- ✅ Encrypted payment data

---

## 📈 Performance Optimizations

- ✅ Exchange rates cached for 1 hour
- ✅ Currency data cached per user session
- ✅ Optimized database queries with indexes
- ✅ Lazy loading for product images
- ✅ API timeout protection (5 seconds)

---

## 🆘 Troubleshooting

### Exchange rates not updating
```bash
# Check API key
php artisan config:clear

# Manually update
php artisan tinker
>>> app(\App\Services\CurrencyService::class)->updateExchangeRates();
```

### Language not switching
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Currency not converting
```bash
# Check if currency is active
php artisan tinker
>>> \App\Models\CurrencyRate::where('currency', 'EUR')->first();

# Verify exchange rate exists
>>> \App\Models\CurrencyRate::getByCurrency('EUR');
```

### Guest checkout not working
```bash
# Check migration ran
php artisan migrate:status

# Verify orders table has guest fields
>>> \DB::select("SHOW COLUMNS FROM orders LIKE 'guest_email'");
```

---

## 🎯 Next Steps / Future Enhancements

- [ ] PayPal integration
- [ ] More payment methods (Apple Pay, Google Pay)
- [ ] Crypto payments
- [ ] Regional pricing strategies
- [ ] Automatic geolocation redirect
- [ ] More languages (Chinese, Japanese, Portuguese)
- [ ] Tax invoice generation per country
- [ ] Multi-warehouse inventory
- [ ] Regional shipping options

---

## 📞 Support

For questions or issues:
- Email: support@xreload.com
- Documentation: [INTERNATIONAL_SETUP.md](INTERNATIONAL_SETUP.md)
- Implementation Details: [IMPLEMENTIERUNG_ZUSAMMENFASSUNG.md](IMPLEMENTIERUNG_ZUSAMMENFASSUNG.md)

---

## ✅ Checklist for Production

Before going live:
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure real Stripe keys (remove test keys)
- [ ] Set up ExchangeRate API key
- [ ] Configure scheduled task for exchange rate updates
- [ ] Test all payment flows in production mode
- [ ] Verify email notifications are working
- [ ] Check SSL certificate is valid
- [ ] Set up backup strategy for database
- [ ] Configure proper error logging
- [ ] Test guest checkout flow completely
- [ ] Verify all currencies are displaying correctly
- [ ] Test language switching on all pages

---

**Version**: 1.0.0 (International)  
**Last Updated**: November 6, 2025  
**Author**: Xemum0

---

🌍 **Ready for Global E-Commerce!** 🚀
