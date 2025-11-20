# 🚀 Quick Start Guide - Game Shop International

## ⚡ Schnellstart in 5 Minuten

### Schritt 1: Datenbank migrieren
```bash
cd "/Users/macbook/Desktop/Game-shop-international"
php artisan migrate
```

### Schritt 2: Internationale Daten initialisieren
```bash
php artisan db:seed --class=InternationalDataSeeder
```

### Schritt 3: Switcher zum Layout hinzufügen

Öffne: `resources/views/layouts/app.blade.php`

Füge in der Navigation (oder im Header) ein:
```blade
<x-international-switcher />
```

Beispiel:
```blade
<header>
    <nav>
        <a href="/">Home</a>
        <a href="/shop">Shop</a>
        
        <!-- International Switcher hier einfügen -->
        <x-international-switcher />
    </nav>
</header>
```

### Schritt 4: Server starten und testen
```bash
php artisan serve
```

Öffne: http://localhost:8000

**Teste:**
1. ✅ Klicke auf den Sprach-Switcher → Wähle "Deutsch"
2. ✅ Klicke auf den Währungs-Switcher → Wähle "EUR"
3. ✅ Preise werden automatisch konvertiert!

---

## 🔑 Optional: Exchange Rate API (Empfohlen)

### 1. Kostenlosen API Key holen
Gehe zu: https://www.exchangerate-api.com/
- Registrierung (1 Minute)
- Free Tier: 1,500 requests/month
- Kopiere deinen API Key

### 2. In .env einfügen
```env
EXCHANGERATE_API_KEY=dein_key_hier
```

### 3. Rates manuell aktualisieren
```bash
php artisan tinker
>>> app(\App\Services\CurrencyService::class)->updateExchangeRates();
```

---

## 📋 Was ist verfügbar?

### Sprachen (6)
- 🇬🇧 English
- 🇩🇪 Deutsch
- 🇫🇷 Français
- 🇪🇸 Español
- 🇮🇹 Italiano
- 🇸🇦 العربية

### Währungen (9)
- $ USD (US Dollar)
- € EUR (Euro)
- £ GBP (British Pound)
- د.إ AED (UAE Dirham)
- ر.س SAR (Saudi Riyal)
- ج.م EGP (Egyptian Pound)
- ¥ JPY (Japanese Yen)
- C$ CAD (Canadian Dollar)
- A$ AUD (Australian Dollar)

### Länder mit spezifischen Einstellungen
- 🇺🇸 USA (USD, EN)
- 🇬🇧 UK (GBP, EN, VAT 20%)
- 🇩🇪 Germany (EUR, DE, MwSt 19%)
- 🇫🇷 France (EUR, FR, TVA 20%)
- 🇪🇸 Spain (EUR, ES, IVA 21%)
- 🇮🇹 Italy (EUR, IT, IVA 22%)
- 🇦🇪 UAE (AED, AR, VAT 5%)
- 🇸🇦 Saudi Arabia (SAR, AR, VAT 15%)

---

## 💻 Verwendung im Code

### In Controllers
```php
use App\Services\CurrencyService;

$currencyService = app(CurrencyService::class);

// Preis konvertieren
$price = $currencyService->convertPrice(100); // $100 → EUR/GBP/etc

// Preis formatieren
$formatted = $currencyService->formatPrice(100); // "$100.00" oder "€92.00"
```

### In Blade Templates
```blade
<!-- Übersetzte Texte -->
<h1>{{ __('app.home.hero_title_1') }}</h1>

<!-- Preise anzeigen -->
<p class="price">
    {{ app(\App\Services\CurrencyService::class)->formatPrice($product->selling_price) }}
</p>

<!-- Für eingeloggte Benutzer -->
@auth
    {{ auth()->user()->formatPrice($product->selling_price) }}
@endauth
```

---

## 🔧 Troubleshooting

### Preise werden nicht konvertiert?
```bash
# Cache leeren
php artisan cache:clear

# Config neu laden
php artisan config:clear
```

### Sprache wechselt nicht?
```bash
# Prüfe ob Übersetzungsdateien existieren
ls lang/de/
ls lang/fr/
```

### Switcher wird nicht angezeigt?
Prüfe ob `<x-international-switcher />` im Layout eingefügt wurde.

---

## 📚 Weitere Informationen

- **Vollständige Dokumentation:** `INTERNATIONAL_SETUP.md`
- **Implementierungs-Details:** `IMPLEMENTIERUNG_ZUSAMMENFASSUNG.md`
- **Laravel Logs:** `storage/logs/laravel.log`

---

## ✅ Checkliste

- [ ] `php artisan migrate` ausgeführt
- [ ] `php artisan db:seed --class=InternationalDataSeeder` ausgeführt
- [ ] `<x-international-switcher />` im Layout eingefügt
- [ ] Browser-Test: Sprach-Wechsel funktioniert
- [ ] Browser-Test: Währungs-Wechsel funktioniert
- [ ] (Optional) Exchange Rate API Key konfiguriert
- [ ] (Optional) Scheduled Task für tägliche Updates eingerichtet

---

## 🎉 Fertig!

Dein Game Shop International ist jetzt einsatzbereit!

**Support:** Bei Problemen siehe die detaillierten Dokumentationen oder Laravel Logs.

---

**Viel Erfolg! 🚀🌍**
