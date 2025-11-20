# Laravel Cloud Deployment Guide

## Game Shop International - Laravel Cloud Setup

---

## Schritt 1: Laravel Cloud Account erstellen

1. Gehe zu: **https://cloud.laravel.com**
2. Klicke auf **"Get Started"** oder **"Sign Up"**
3. Registriere dich mit deiner Email-Adresse
4. Bestätige deine Email

---

## Schritt 2: GitHub verbinden

1. Im Laravel Cloud Dashboard
2. Gehe zu **Settings → GitHub**
3. Klicke **"Connect GitHub Account"**
4. Autorisiere Laravel Cloud Zugriff auf deine Repositories

---

## Schritt 3: Neues Projekt erstellen

1. Im Dashboard → **"New Project"**
2. Fülle aus:
   - **Project Name**: `game-shop-international`
   - **GitHub Repository**: `Ayoubbenderdouch/game-shop-international`
   - **Branch**: `main`
   - **Region**: Wähle die nächste Region (z.B. Europe - Frankfurt)

3. Klicke **"Create Project"**

---

## Schritt 4: Datenbank konfigurieren

Laravel Cloud erstellt automatisch eine MySQL Datenbank.

**Datenbank Info:**
- Host: `127.0.0.1` (automatisch konfiguriert)
- Database: `forge` (Standard)
- Username: `forge`
- Password: (wird automatisch gesetzt)

---

## Schritt 5: Environment Variables setzen

Gehe zu **Project → Environment** und setze:

```env
# App Settings
APP_NAME="Game Shop International"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.laravel.cloud

# Supabase (Optional - falls du es nutzen willst)
SUPABASE_URL=https://hganerlglgrtyvyuelpv.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhnYW5lcmxnbGdydHl2eXVlbHB2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjM1ODYwNjIsImV4cCI6MjA3OTE2MjA2Mn0.2UKLHwFtvsSn8-45RVx5F66IS2GakhoFFWA_IdMDufA

# Stripe (falls du Zahlungen nutzen willst)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...

# Mail Settings (für Order Confirmations)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@game-shop.com"
MAIL_FROM_NAME="Game Shop International"

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## Schritt 6: Deployment Commands

Laravel Cloud führt automatisch aus:

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Wenn du zusätzliche Commands brauchst, gehe zu **Deployment → Build Commands**

---

## Schritt 7: Erste Deployment

1. Klicke **"Deploy Now"**
2. Warte 2-5 Minuten
3. Status wird angezeigt: Building → Deploying → Active

---

## Schritt 8: Domain konfigurieren (Optional)

### Eigene Domain hinzufügen:

1. Gehe zu **Project → Domains**
2. Klicke **"Add Domain"**
3. Trage deine Domain ein: `shop.example.com`
4. Bei deinem Domain-Provider (z.B. Namecheap):
   - Erstelle einen **A Record**:
     - Name: `shop` (oder `@` für Root)
     - Value: `IP-Adresse von Laravel Cloud` (wird angezeigt)
   - Oder **CNAME Record**:
     - Name: `shop`
     - Value: `your-app.laravel.cloud`

5. Warte auf DNS-Propagation (bis zu 24h)
6. SSL wird automatisch aktiviert

---

## Schritt 9: Datenbank Seeding (Wichtig!)

Nach dem ersten Deployment:

1. Gehe zu **Project → Console**
2. Öffne die Terminal-Konsole
3. Führe aus:

```bash
php artisan db:seed --class=InternationalDataSeeder
```

Das erstellt:
- ✅ Währungen
- ✅ Länder
- ✅ Standard-Kategorien
- ✅ Währungskurse

---

## Schritt 10: Testen

1. Öffne deine App-URL: `https://your-app.laravel.cloud`
2. Teste:
   - ✅ Homepage lädt
   - ✅ Registrierung funktioniert
   - ✅ Login funktioniert
   - ✅ Produkte werden angezeigt
   - ✅ Multi-Language Switcher
   - ✅ Multi-Currency Switcher
   - ✅ Warenkorb funktioniert
   - ✅ Checkout funktioniert

---

## Automatische Updates

Bei jedem Git Push zu `main` Branch wird automatisch deployed:

```bash
git add .
git commit -m "Update feature"
git push origin main
```

Laravel Cloud erkennt den Push und deployed automatisch!

---

## Monitoring & Logs

1. **Logs anzeigen**: Project → Logs
2. **Performance**: Project → Metrics
3. **Errors**: Project → Error Tracking

---

## Kosten

**Laravel Cloud Pricing:**
- **Hobby Plan**: ~$10-15/Monat
  - 1 vCPU
  - 512 MB RAM
  - 10 GB Storage
  - SSL inklusive

- **Professional Plan**: ~$30/Monat
  - 2 vCPU
  - 2 GB RAM
  - 50 GB Storage
  - Auto-Scaling

---

## Troubleshooting

### "500 Internal Server Error"

1. Gehe zu **Logs**
2. Prüfe auf Fehler
3. Häufige Probleme:
   - `APP_KEY` nicht gesetzt → `php artisan key:generate`
   - Datenbank-Migration fehlgeschlagen → Logs prüfen
   - Permission Fehler → `storage/` und `bootstrap/cache/` müssen beschreibbar sein

### "Database connection failed"

1. Prüfe **Environment Variables**
2. DB_HOST sollte `127.0.0.1` sein
3. DB_DATABASE sollte `forge` sein

### Assets werden nicht geladen

1. Führe aus: `npm run build`
2. Prüfe `APP_URL` in Environment Variables

---

## Alternative: Andere Hosting-Optionen

Falls Laravel Cloud zu teuer ist:

### 1. **Railway** ($5/Monat)
- Einfaches Setup
- GitHub Integration
- Automatisches Deployment

### 2. **Hostinger** (€3/Monat)
- Sehr günstig
- cPanel
- Gut für Anfänger

### 3. **DigitalOcean + Laravel Forge** ($18/Monat)
- Professionell
- Volle Kontrolle
- Scaling möglich

---

## Support

- Laravel Cloud Docs: https://cloud.laravel.com/docs
- Laravel Discord: https://discord.gg/laravel
- Deine .env.production Datei ist bereits vorbereitet!

---

**Viel Erfolg mit deinem Deployment! 🚀**
