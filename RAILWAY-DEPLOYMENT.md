# Railway Deployment Guide

## Game Shop International - Railway Setup (EINFACH!)

Railway ist der einfachste Weg dein Laravel Projekt zu deployen!

---

## ✅ Schritt 1: Railway Account erstellen (2 Min)

1. Gehe zu: **https://railway.app**
2. Klicke **"Login with GitHub"**
3. Autorisiere Railway Zugriff auf deine GitHub Repositories
4. Fertig! Account erstellt ✅

---

## ✅ Schritt 2: Projekt deployen (5 Min)

### 1. Neues Projekt erstellen
1. Im Railway Dashboard → Klicke **"New Project"**
2. Wähle **"Deploy from GitHub repo"**
3. Suche und wähle: **`game-shop-international`**
4. Railway erkennt automatisch dass es ein Laravel Projekt ist!

### 2. Warte auf den ersten Build
- Status: Building → Deploying → Live
- Dauert ca. 3-5 Minuten beim ersten Mal
- ⚠️ Es wird ERST FEHLSCHLAGEN - das ist normal! Wir müssen noch die Datenbank hinzufügen.

---

## ✅ Schritt 3: MySQL Datenbank hinzufügen (2 Min)

### 1. Datenbank erstellen
1. In deinem Railway Projekt → Klicke **"+ New"**
2. Wähle **"Database"**
3. Wähle **"Add MySQL"**
4. Railway erstellt automatisch eine MySQL Datenbank!

### 2. Datenbank verbinden
Railway verbindet die Datenbank automatisch!

Die folgenden Environment Variables werden automatisch gesetzt:
- `DATABASE_URL`
- `MYSQL_URL`
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLDATABASE`
- `MYSQLUSER`
- `MYSQLPASSWORD`

---

## ✅ Schritt 4: Laravel Environment Variables setzen (5 Min)

### 1. Gehe zu deinem Laravel Service
1. Klicke auf dein **Laravel App Service** (nicht die Datenbank)
2. Gehe zum **"Variables"** Tab
3. Füge diese Variables hinzu:

### 2. Kopiere und füge ein:

```env
# App Settings
APP_NAME="Game Shop International"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

# Database (Railway setzt diese automatisch, aber wir müssen sie mappen)
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Supabase (Optional - falls du es nutzen willst)
SUPABASE_URL=https://hganerlglgrtyvyuelpv.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhnYW5lcmxnbGdydHl2eXVlbHB2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjM1ODYwNjIsImV4cCI6MjA3OTE2MjA2Mn0.2UKLHwFtvsSn8-45RVx5F66IS2GakhoFFWA_IdMDufA

# Mail (Optional - für Order Confirmations)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@game-shop.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 3. APP_KEY generieren
1. Scrolle zu **APP_KEY** Variable
2. Klicke auf das Service-Menü (3 Punkte) → **"Open Shell"**
3. Führe aus:
```bash
php artisan key:generate --show
```
4. Kopiere den Output (z.B. `base64:xxxxx...`)
5. Füge ihn als Wert für **APP_KEY** ein

---

## ✅ Schritt 5: Domain aktivieren (1 Min)

### 1. Public Domain aktivieren
1. In deinem Laravel Service → **"Settings"** Tab
2. Scrolle zu **"Networking"**
3. Klicke **"Generate Domain"**
4. Railway gibt dir eine URL wie: `game-shop-production.up.railway.app`

### 2. URL in Environment Variable setzen
1. Gehe zurück zu **"Variables"**
2. Setze **APP_URL** auf deine Railway Domain (wird automatisch als `${{RAILWAY_PUBLIC_DOMAIN}}` gesetzt)

---

## ✅ Schritt 6: Redeploy (2 Min)

1. Gehe zu **"Deployments"**
2. Klicke auf das Menü (3 Punkte) beim letzten Deployment
3. Wähle **"Redeploy"**
4. Warte 2-3 Minuten

Railway führt automatisch aus:
```bash
composer install --no-dev
npm install && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

---

## ✅ Schritt 7: Datenbank seeden (2 Min)

### 1. Öffne Railway Shell
1. Klicke auf dein Laravel Service
2. Klicke **"Open Shell"** oder **"Terminal"**

### 2. Führe Migrationen und Seeder aus
```bash
# Alle Migrationen ausführen
php artisan migrate --force

# International Data seeden
php artisan db:seed --class=InternationalDataSeeder --force
```

Das erstellt:
- ✅ Währungen (USD, EUR, GBP, etc.)
- ✅ Länder
- ✅ Standard Kategorien
- ✅ Beispiel-Produkte (optional)

---

## ✅ Schritt 8: Testen! 🎉

### 1. Öffne deine App
Klicke auf die Domain-URL oder gehe zu:
```
https://game-shop-production.up.railway.app
```

### 2. Teste alle Features
- ✅ Homepage lädt
- ✅ Produkte anzeigen
- ✅ Registrierung funktioniert
- ✅ Login funktioniert
- ✅ Multi-Language Switcher (EN, DE, FR, ES, IT, AR)
- ✅ Multi-Currency Switcher
- ✅ Warenkorb funktioniert
- ✅ Checkout funktioniert

---

## 🚀 Automatische Deployments

**Jeder Git Push deployed automatisch!**

```bash
cd "/Users/macbook/Desktop/Sami Web/Game-shop-international"

# Änderungen machen
# ...

# Committen und pushen
git add .
git commit -m "Update feature"
git push origin main

# Railway deployed automatisch in 2-3 Minuten!
```

---

## 💰 Kosten

**Railway Pricing:**
- **Hobby Plan**: $5/Monat
  - 500 Stunden Laufzeit
  - Perfekt für kleine Projekte
  - Inkl. Datenbank

- **Pro Plan**: $20/Monat
  - Unbegrenzte Laufzeit
  - Mehr Ressourcen
  - Priority Support

**FREE Trial**: $5 Credit zum Testen!

---

## 🔧 Troubleshooting

### "500 Internal Server Error"

**Lösung:**
1. Gehe zu **Deployments → View Logs**
2. Prüfe auf Fehler
3. Häufigste Probleme:
   - `APP_KEY` nicht gesetzt
   - Datenbank-Verbindung fehlgeschlagen
   - Migration-Fehler

**Fix:**
```bash
# In Railway Shell
php artisan key:generate
php artisan migrate:fresh --force
php artisan config:cache
```

### "Database connection failed"

**Lösung:**
1. Prüfe ob MySQL Service läuft (grüner Punkt)
2. Prüfe Environment Variables:
   - `DB_HOST=${{MYSQLHOST}}`
   - `DB_DATABASE=${{MYSQLDATABASE}}`
3. Redeploy das Laravel Service

### "Assets not loading / 404"

**Lösung:**
```bash
# In Railway Shell
npm run build
php artisan storage:link
php artisan config:cache
```

### Migration Error: "Table already exists"

**Lösung:**
```bash
# In Railway Shell
php artisan migrate:fresh --force
php artisan db:seed --class=InternationalDataSeeder --force
```

---

## 📊 Monitoring & Logs

### Logs anzeigen
1. Railway Dashboard → Dein Service
2. **"Deployments"** Tab
3. Klicke auf ein Deployment → **"View Logs"**

### Echtzeit Logs
```bash
# In Railway Shell
tail -f storage/logs/laravel.log
```

### Database Metrics
1. Klicke auf **MySQL Service**
2. **"Metrics"** Tab
3. Sieh CPU, Memory, Storage Usage

---

## 🌐 Custom Domain (Optional)

### Eigene Domain verwenden:

1. **In Railway:**
   - Service → Settings → Custom Domain
   - Klicke **"Add Domain"**
   - Trage ein: `shop.deinedomain.com`

2. **Bei deinem Domain Provider** (z.B. Namecheap):
   - Erstelle einen **CNAME Record**:
     - Name: `shop`
     - Value: `game-shop-production.up.railway.app`

3. Warte auf DNS Propagation (5 Minuten - 24 Stunden)
4. **SSL wird automatisch aktiviert!** 🔒

---

## 📁 Nützliche Railway Befehle

```bash
# Shell öffnen
# Im Railway Dashboard → Service → "Open Shell"

# Logs anzeigen
php artisan log:tail

# Cache leeren
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Datenbank zurücksetzen
php artisan migrate:fresh --force

# Neue Migration ausführen
php artisan migrate --force

# Seeder ausführen
php artisan db:seed --force

# Tinker (Laravel REPL)
php artisan tinker
```

---

## 🎯 Checkliste

- [ ] Railway Account erstellt
- [ ] GitHub verbunden
- [ ] Projekt deployed
- [ ] MySQL Datenbank hinzugefügt
- [ ] Environment Variables gesetzt
- [ ] APP_KEY generiert
- [ ] Domain aktiviert
- [ ] Migrationen ausgeführt
- [ ] Seeder ausgeführt
- [ ] App getestet
- [ ] Alle Features funktionieren

---

## 🆚 Railway vs Laravel Cloud

| Feature | Railway | Laravel Cloud |
|---------|---------|---------------|
| **Preis** | $5/Monat | $15/Monat |
| **Setup** | ⭐⭐⭐⭐⭐ Sehr einfach | ⭐⭐⭐ Mittel |
| **Auto-Deploy** | ✅ Ja | ✅ Ja |
| **Datenbank** | ✅ MySQL | ✅ MySQL/Postgres |
| **SSL** | ✅ Automatisch | ✅ Automatisch |
| **Custom Domain** | ✅ Ja | ✅ Ja |
| **Support** | Community | Laravel Team |

**Empfehlung:** Railway ist perfekt für dein Projekt! ⭐

---

## 🚀 Zusammenfassung

**Das war's! Dein Laravel Shop ist jetzt live auf Railway!**

**Deine App URL:**
```
https://game-shop-production.up.railway.app
```

**Bei Fragen:**
- Railway Docs: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- Railway Support: help@railway.app

---

**Viel Erfolg! 🎉**
