# Laravel Cloud Deployment Guide

## ✅ Vorbereitung ist fertig!

Alle nötigen Konfigurationsdateien wurden erstellt:
- `.laravel-cloud.yml` - Laravel Cloud Konfiguration
- `.env.cloud` - Environment Variables Template
- Safe Migration File - Verhindert "Table already exists" Fehler

## 🚀 Deployment Schritte

### 1. Gehe zu Laravel Cloud Dashboard
**URL:** https://cloud.laravel.com

### 2. Erstelle ein neues Projekt
1. Klicke auf **"New Project"**
2. Wähle **GitHub Repository**: `Ayoubbenderdouch/game-shop-international`
3. Branch: `main`

### 3. Database Setup
Laravel Cloud erstellt automatisch eine MySQL Datenbank.

### 4. Environment Variables setzen

Gehe zu **Settings → Environment** und füge hinzu:

```bash
APP_NAME="Game Shop International"
APP_ENV=production
APP_DEBUG=false
APP_KEY=[Laravel Cloud generiert das automatisch]

# Supabase
SUPABASE_URL=https://hganerlglgrtyvyuelpv.supabase.co
SUPABASE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhnYW5lcmxnbGdydHl2eXVlbHB2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzE5NTA1NDUsImV4cCI6MjA0NzUyNjU0NX0.wKWXvNPOy0VVjDZ7wlzW4rLEiE5P_D7xPRZq7Jpr2pc

# Cache & Queue
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=file
```

**Hinweis:** DB_* Variables werden von Laravel Cloud automatisch gesetzt!

### 5. Deploy!
1. Klicke auf **"Deploy"**
2. Laravel Cloud wird automatisch:
   - Dependencies installieren
   - Assets builden (npm run build)
   - Migrationen ausführen
   - App starten

### 6. Nach dem Deployment

Wenn Migration-Fehler auftreten ("Table already exists"):

#### Option A: Dashboard Console
Gehe zu **Console** und führe aus:
```bash
php artisan migrate:fresh --force
php artisan db:seed --class=InternationalDataSeeder --force
```

#### Option B: Migration Files löschen
Falls immer noch Fehler:
1. Gehe zu **Console**
2. Führe aus:
```bash
php artisan db:wipe --force
php artisan migrate --force
php artisan db:seed --class=InternationalDataSeeder --force
```

## 🔧 Troubleshooting

### "Table already exists" Fehler
Die `0000_00_00_000000_safe_migration_check.php` sollte das verhindern.
Falls nicht:
```bash
php artisan migrate:fresh --force
```

### APP_KEY Fehler
```bash
php artisan key:generate --force
```

### Assets nicht gefunden
```bash
npm run build
```

## 📊 Nach erfolgreichem Deployment

Deine App ist live unter:
```
https://your-project-name.laravel.cloud
```

## 🌍 Features aktivieren

### Multi-Language Support
Bereits integriert! Verfügbare Sprachen:
- English (EN)
- Deutsch (DE)
- Français (FR)
- Español (ES)
- Italiano (IT)
- العربية (AR)

### Multi-Currency Support
Bereits integriert! Verfügbare Währungen:
- USD, EUR, GBP
- AED, SAR, EGP
- JPY, CAD, AUD

### Supabase Integration
Automatisch konfiguriert mit deinen Credentials!

## 💡 Wichtige Befehle

### Console zugreifen
In Laravel Cloud Dashboard → **Console**

### Logs anzeigen
Dashboard → **Logs**

### Cache leeren
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Neue Migration ausführen
```bash
php artisan migrate --force
```

## 🎯 Nächste Schritte

1. ✅ Custom Domain hinzufügen (Settings → Domains)
2. ✅ SSL wird automatisch konfiguriert
3. ✅ Monitoring aktiviert
4. ✅ Auto-Scaling aktiviert

Viel Erfolg! 🚀
