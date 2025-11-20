#!/bin/bash

echo "🚀 Railway Deployment Setup - Vollautomatisch"
echo "=============================================="

cd "/Users/macbook/Desktop/Sami Web/Game-shop-international"

# Check if logged in
if ! railway whoami &>/dev/null; then
    echo "❌ Bitte erst 'railway login' ausführen!"
    exit 1
fi

echo "✅ Railway Login OK"

# Link to project
echo "🔗 Verbinde mit Projekt..."
railway link

# Get service IDs
echo "📋 Suche Services..."
GAME_SHOP_SERVICE=$(railway service list --json | grep -o '"id":"[^"]*"' | head -1 | cut -d'"' -f4)
MYSQL_SERVICE=$(railway service list --json | grep -o '"id":"[^"]*"' | tail -1 | cut -d'"' -f4)

echo "📊 Game Shop Service: $GAME_SHOP_SERVICE"
echo "🗄️  MySQL Service: $MYSQL_SERVICE"

# Get MySQL credentials
echo "🔐 Hole MySQL Credentials..."
export RAILWAY_SERVICE=$MYSQL_SERVICE
DB_HOST=$(railway variables get MYSQLHOST 2>/dev/null || echo "mysql.railway.internal")
DB_PORT=$(railway variables get MYSQLPORT 2>/dev/null || echo "3306")
DB_DATABASE=$(railway variables get MYSQLDATABASE 2>/dev/null || echo "railway")
DB_USER=$(railway variables get MYSQLUSER 2>/dev/null || echo "root")
DB_PASSWORD=$(railway variables get MYSQLPASSWORD 2>/dev/null || railway variables get MYSQL_ROOT_PASSWORD)

echo "✅ DB Host: $DB_HOST"
echo "✅ DB Port: $DB_PORT"
echo "✅ DB Name: $DB_DATABASE"
echo "✅ DB User: $DB_USER"

# Switch to game-shop service
export RAILWAY_SERVICE=$GAME_SHOP_SERVICE

# Generate APP_KEY
echo "🔑 Generiere APP_KEY..."
APP_KEY="base64:$(openssl rand -base64 32)"

# Set all environment variables
echo "⚙️  Setze Environment Variables..."

railway variables set APP_NAME="Game Shop International"
railway variables set APP_ENV="production"
railway variables set APP_DEBUG="false"
railway variables set APP_KEY="$APP_KEY"
railway variables set APP_URL="https://game-shop-international-production.up.railway.app"

railway variables set DB_CONNECTION="mysql"
railway variables set DB_HOST="$DB_HOST"
railway variables set DB_PORT="$DB_PORT"
railway variables set DB_DATABASE="$DB_DATABASE"
railway variables set DB_USERNAME="$DB_USER"
railway variables set DB_PASSWORD="$DB_PASSWORD"

railway variables set SUPABASE_URL="https://hganerlglgrtyvyuelpv.supabase.co"
railway variables set SUPABASE_KEY="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhnYW5lcmxnbGdydHl2eXVlbHB2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzE5NTA1NDUsImV4cCI6MjA0NzUyNjU0NX0.wKWXvNPOy0VVjDZ7wlzW4rLEiE5P_D7xPRZq7Jpr2pc"

railway variables set LOG_CHANNEL="stack"
railway variables set LOG_LEVEL="error"

railway variables set SESSION_DRIVER="file"
railway variables set QUEUE_CONNECTION="sync"
railway variables set CACHE_DRIVER="file"

echo ""
echo "✅ Alle Environment Variables gesetzt!"
echo ""
echo "🔄 Railway deployed automatisch neu..."
echo "⏳ Warte 60 Sekunden bis Deployment fertig ist..."
sleep 60

echo ""
echo "🗄️  Führe Datenbank Migrationen aus..."
railway run php artisan migrate --force

echo ""
echo "🌱 Führe Database Seeder aus..."
railway run php artisan db:seed --class=InternationalDataSeeder --force

echo ""
echo "✅ FERTIG! Deine App ist live!"
echo "🌐 URL: https://game-shop-international-production.up.railway.app"
echo ""
railway open
