# 🚀 Quick Start - Game Shop International

## Schnellstart in 3 Schritten

### 1️⃣ Setup ausführen

```bash
cd "/Users/macbook/Desktop/Sami Web/Game-shop-international"

# Automatisches Setup-Skript ausführen
./setup-international.sh

# Oder manuell:
php artisan migrate
php artisan db:seed --class=InternationalDataSeeder
```

---

### 2️⃣ Konfiguration (.env)

Füge diese Werte zu deiner `.env` hinzu:

```env
# Stripe für internationale Zahlungen
STRIPE_KEY=pk_test_xxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxx

# ExchangeRate API für Währungsumrechnung
# Kostenlos bei: https://www.exchangerate-api.com/
EXCHANGERATE_API_KEY=dein_api_key
```

---

### 3️⃣ Server starten & testen

```bash
# Development Server starten
php artisan serve

# Öffne im Browser
http://localhost:8000
```

---

## ✨ Features testen

### 🌐 Sprache wechseln
1. Klicke auf die **Sprachauswahl** (🌍) in der Navigation
2. Wähle eine Sprache: EN, DE, FR, ES, IT, AR
3. Die gesamte Seite wird übersetzt

### 💱 Währung wechseln
1. Klicke auf die **Währungsauswahl** (💵) in der Navigation
2. Wähle eine Währung: USD, EUR, GBP, AED, SAR, etc.
3. Alle Preise werden automatisch umgerechnet

### 🛒 Guest Checkout (Kaufen ohne Account)
1. Gehe zu einem Produkt (z.B. Google Play Gift Card)
2. Klicke "Buy Now" **OHNE** einzuloggen
3. Fülle deine Email-Adresse aus
4. Bezahle mit Testkarte: `4242 4242 4242 4242`
5. Erhalte sofort deine Codes!

---

## 🗂️ Projektstruktur

```
Game-shop-international/
├── app/
│   ├── Http/Controllers/
│   │   ├── InternationalController.php     # Sprache & Währung
│   │   └── GuestCheckoutController.php     # Guest Checkout
│   ├── Services/
│   │   └── CurrencyService.php             # Währungskonvertierung
│   └── Models/
│       ├── Country.php                      # Länder mit VAT
│       └── CurrencyRate.php                 # Wechselkurse
├── resources/views/
│   ├── components/
│   │   └── price.blade.php                  # Preis-Component
│   ├── checkout/
│   │   ├── guest.blade.php                  # Guest Checkout
│   │   └── guest-success.blade.php          # Bestellbestätigung
│   └── layouts/
│       ├── navigation.blade.php             # Nav mit Switcher
│       └── guest.blade.php                  # Guest Layout
├── database/migrations/
│   ├── 2025_11_05_100000_add_international_features.php
│   └── 2025_11_06_100000_add_guest_checkout_support.php
└── lang/                                    # Übersetzungen
    ├── en/
    ├── de/
    ├── fr/
    ├── es/
    ├── it/
    └── ar/
```

---

## 🎯 Wichtigste Routen

### Öffentliche Routen
```
/                           → Homepage
/shop                       → Shop mit allen Produkten
/product/{slug}             → Produktdetails
/language/{locale}          → Sprache wechseln
/currency/switch            → Währung wechseln (POST)
```

### Guest Checkout Routen
```
/guest/checkout             → Guest Checkout Seite
/guest/checkout/process     → Bestellung aufgeben (POST)
/guest/checkout/success     → Bestellbestätigung
```

### Für angemeldete User
```
/cart                       → Warenkorb
/checkout                   → Checkout (mit Account)
/orders                     → Meine Bestellungen
```

---

## 💳 Test-Kreditkarten (Stripe)

```
Karte:     4242 4242 4242 4242
Ablauf:    Beliebiges zukünftiges Datum (z.B. 12/25)
CVC:       Beliebige 3 Ziffern (z.B. 123)
PLZ:       Beliebig (z.B. 12345)
```

**Weitere Test-Karten:**
- 3D Secure: `4000 0027 6000 3184`
- Abgelehnt: `4000 0000 0000 0002`
- EUR-Karte: `4000 0025 0000 3155`

---

## 🌍 Unterstützte Länder & Währungen

| Land | Währung | Sprache | MwSt/VAT |
|------|---------|---------|----------|
| 🇺🇸 USA | USD $ | English | 0% |
| 🇬🇧 UK | GBP £ | English | 20% |
| 🇩🇪 Deutschland | EUR € | Deutsch | 19% |
| 🇫🇷 Frankreich | EUR € | Français | 20% |
| 🇪🇸 Spanien | EUR € | Español | 21% |
| 🇮🇹 Italien | EUR € | Italiano | 22% |
| 🇦🇪 UAE | AED د.إ | العربية | 5% |
| 🇸🇦 Saudi Arabien | SAR ﷼ | العربية | 15% |

---

## 🐛 Troubleshooting

### Problem: "Class 'InternationalDataSeeder' not found"
**Lösung:**
```bash
composer dump-autoload
php artisan db:seed --class=InternationalDataSeeder
```

### Problem: Währungen werden nicht angezeigt
**Lösung:**
```bash
php artisan tinker
>>> \App\Models\CurrencyRate::create([
    'currency' => 'EUR',
    'currency_name' => 'Euro',
    'currency_symbol' => '€',
    'rate_to_usd' => 1.10,
    'is_active' => true
]);
```

### Problem: Preise werden nicht konvertiert
**Lösung:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Problem: Migration Error
**Lösung:**
```bash
# Falls Tabellen bereits existieren
php artisan migrate:fresh

# Oder nur neue Migrations
php artisan migrate --path=/database/migrations/2025_11_05_100000_add_international_features.php
```

---

## 📚 Weitere Dokumentation

- **Vollständiger Guide**: `INTERNATIONAL_COMPLETE_GUIDE.md`
- **Implementation Details**: `IMPLEMENTIERUNG_ZUSAMMENFASSUNG.md`
- **Setup Guide**: `INTERNATIONAL_SETUP.md`

---

## ✅ Feature Checklist

- [x] 6 Sprachen (EN, DE, FR, ES, IT, AR)
- [x] 9 Währungen mit Live-Konvertierung
- [x] Guest Checkout (ohne Registrierung)
- [x] Multi-Currency Payments (Stripe)
- [x] Automatische Länder-Erkennung (IP)
- [x] VAT-Berechnung pro Land
- [x] Responsive Design (Mobile & Desktop)
- [x] RTL Support für Arabisch
- [x] Email-Benachrichtigungen
- [x] Bestellverfolgung für Gäste

---

## 🎨 Verwendung im Code

### Preis anzeigen (mit Auto-Konvertierung)
```blade
<!-- Einfach -->
<x-price :price="$product->selling_price" />

<!-- Mit CSS Klasse -->
<x-price :price="99.99" class="text-2xl font-bold text-blue-500" />
```

### Übersetzung verwenden
```blade
<h1>{{ __('app.welcome') }}</h1>
<button>{{ __('app.cart.add_to_cart') }}</button>
```

### Währung im Controller
```php
use App\Services\CurrencyService;

public function show($id)
{
    $product = Product::find($id);
    $currencyService = app(CurrencyService::class);
    
    // Konvertieren
    $price = $currencyService->convertPrice($product->price);
    
    // Formatieren
    $formatted = $currencyService->formatPrice($price);
    
    return view('product', compact('product', 'formatted'));
}
```

---

## 🚀 Ready for Production?

### Vor dem Launch:

1. ✅ `.env` → `APP_DEBUG=false`
2. ✅ Echte Stripe Keys eintragen
3. ✅ ExchangeRate API Key konfigurieren
4. ✅ SSL-Zertifikat einrichten
5. ✅ Alle Test-Käufe durchführen
6. ✅ Email-Versand testen
7. ✅ Backup-Strategie einrichten

---

**Viel Erfolg mit deinem internationalen E-Commerce Shop! 🌍🚀**

Bei Fragen: Siehe `INTERNATIONAL_COMPLETE_GUIDE.md`
