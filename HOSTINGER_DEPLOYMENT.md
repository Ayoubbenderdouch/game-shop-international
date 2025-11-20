# 🚀 Hostinger Deployment Anleitung

## FTP-Zugangsdaten
- **Host:** ftp://147.93.88.164
- **Username:** u289969187
- **Password:** [Dein FTP Passwort - erstelle eins in Hostinger]
- **Port:** 21
- **Upload-Ordner:** public_html

---

## Schritt 1: FileZilla herunterladen & verbinden

### FileZilla installieren:
1. Gehe zu: https://filezilla-project.org/download.php?type=client
2. Lade FileZilla Client herunter (kostenlos)
3. Installiere es auf deinem Mac

### Mit Hostinger verbinden:
1. Öffne FileZilla
2. Trage oben ein:
   - **Host:** `147.93.88.164`
   - **Benutzername:** `u289969187`
   - **Passwort:** [Dein FTP Passwort]
   - **Port:** `21`
3. Klicke auf "Verbinden"

---

## Schritt 2: Dateien hochladen

### WICHTIG - Diese Dateien hochladen:

**In den Ordner `public_html` auf Hostinger hochladen:**

```
Game-shop-international/
├── app/                    ✅ Hochladen
├── bootstrap/              ✅ Hochladen
├── config/                 ✅ Hochladen
├── database/              ✅ Hochladen
├── lang/                  ✅ Hochladen
├── public/                ✅ Hochladen
├── resources/             ✅ Hochladen
├── routes/                ✅ Hochladen
├── storage/               ✅ Hochladen (aber Inhalt leer lassen - nur Struktur)
├── .htaccess              ✅ Hochladen (NEU erstellt)
├── .env.production        ✅ Hochladen (dann umbenennen zu .env)
├── artisan                ✅ Hochladen
├── composer.json          ✅ Hochladen
├── composer.lock          ✅ Hochladen
├── package.json           ✅ Hochladen
└── vite.config.js         ✅ Hochladen
```

**NICHT hochladen:**
- ❌ node_modules/
- ❌ vendor/
- ❌ .env (die lokale Version)
- ❌ .git/
- ❌ tests/

---

## Schritt 3: Auf dem Server (Hostinger Terminal/SSH)

### Terminal öffnen in Hostinger:
1. Gehe zu Hostinger Panel → "Advanced" → "SSH Access"
2. Oder nutze den "File Manager" → Terminal

### Befehle ausführen:

```bash
# 1. In dein Projektverzeichnis gehen
cd domains/palevioletred-moose-285929.hostingersite.com

# 2. .env.production zu .env umbenennen
mv .env.production .env

# 3. Storage Berechtigungen setzen
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# 4. Composer Dependencies installieren
composer install --optimize-autoloader --no-dev

# 5. Datenbank migrieren
php artisan migrate --force

# 6. Config cachen
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Storage Link erstellen
php artisan storage:link
```

---

## Schritt 4: Testen

Öffne im Browser:
https://palevioletred-moose-285929.hostingersite.com

---

## Troubleshooting

### 500 Error:
```bash
# Logs anschauen
tail -f storage/logs/laravel.log

# Cache löschen
php artisan cache:clear
php artisan config:clear
```

### Permission Errors:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

---

## 🎯 Wichtige Punkte:

1. ✅ .env.production wurde mit deinen Hostinger-Datenbank-Credentials erstellt
2. ✅ APP_URL ist auf deine Domain gesetzt
3. ✅ APP_DEBUG ist auf `false` (Production)
4. ✅ .htaccess für Laravel ist erstellt
5. ⚠️ Nach dem Upload musst du `composer install` auf dem Server ausführen!

---

**Bei Fragen oder Problemen, melde dich!** 🚀
