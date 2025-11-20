#!/bin/bash

# 🌍 Game Shop International - Quick Setup Script
# This script will set up all international features

echo "🌍 Starting Game Shop International Setup..."
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Check if we're in the right directory
echo -e "${BLUE}📁 Checking directory...${NC}"
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: artisan file not found. Please run this script from the project root.${NC}"
    exit 1
fi
echo -e "${GREEN}✅ Directory check passed${NC}"
echo ""

# Step 2: Check .env file
echo -e "${BLUE}⚙️  Checking .env configuration...${NC}"
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}⚠️  .env file not found. Creating from .env.example...${NC}"
    cp .env.example .env
    php artisan key:generate
fi
echo -e "${GREEN}✅ .env file exists${NC}"
echo ""

# Step 3: Install dependencies (if needed)
echo -e "${BLUE}📦 Checking dependencies...${NC}"
if [ ! -d "vendor" ]; then
    echo -e "${YELLOW}⚠️  Vendor directory not found. Running composer install...${NC}"
    composer install
fi
echo -e "${GREEN}✅ Dependencies ready${NC}"
echo ""

# Step 4: Run migrations
echo -e "${BLUE}🗄️  Running database migrations...${NC}"
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migrations completed successfully${NC}"
else
    echo -e "${RED}❌ Migration failed. Please check your database configuration.${NC}"
    exit 1
fi
echo ""

# Step 5: Seed international data
echo -e "${BLUE}🌐 Seeding international data (currencies & countries)...${NC}"
if php artisan db:seed --class=InternationalDataSeeder 2>/dev/null; then
    echo -e "${GREEN}✅ International data seeded successfully${NC}"
else
    echo -e "${YELLOW}⚠️  International data seeder not found or already seeded${NC}"
    echo -e "${YELLOW}   You may need to create the seeder manually${NC}"
fi
echo ""

# Step 6: Clear caches
echo -e "${BLUE}🧹 Clearing application caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

# Step 7: Check important configurations
echo -e "${BLUE}🔍 Checking configuration...${NC}"

# Check for Stripe keys
if grep -q "STRIPE_KEY=your_stripe" .env; then
    echo -e "${YELLOW}⚠️  STRIPE_KEY not configured${NC}"
    echo -e "   Please add your Stripe keys to .env:"
    echo -e "   STRIPE_KEY=pk_test_xxxx"
    echo -e "   STRIPE_SECRET=sk_test_xxxx"
else
    echo -e "${GREEN}✅ Stripe keys configured${NC}"
fi

# Check for ExchangeRate API
if grep -q "EXCHANGERATE_API_KEY=your_api" .env; then
    echo -e "${YELLOW}⚠️  EXCHANGERATE_API_KEY not configured${NC}"
    echo -e "   Get a free API key from: https://www.exchangerate-api.com/"
    echo -e "   Then add to .env: EXCHANGERATE_API_KEY=your_key_here"
else
    echo -e "${GREEN}✅ ExchangeRate API key configured${NC}"
fi

echo ""

# Step 8: Update exchange rates (if API key is configured)
echo -e "${BLUE}💱 Attempting to update exchange rates...${NC}"
php artisan tinker --execute="app(\App\Services\CurrencyService::class)->updateExchangeRates();" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Exchange rates updated successfully${NC}"
else
    echo -e "${YELLOW}⚠️  Could not update exchange rates automatically${NC}"
    echo -e "   You can update them manually later with:"
    echo -e "   php artisan tinker"
    echo -e "   >>> app(\App\Services\CurrencyService::class)->updateExchangeRates();"
fi
echo ""

# Step 9: Check file permissions
echo -e "${BLUE}🔐 Checking file permissions...${NC}"
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✅ File permissions set${NC}"
echo ""

# Summary
echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}║  🎉 Game Shop International Setup Complete!               ║${NC}"
echo -e "${GREEN}║                                                            ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

echo -e "${BLUE}📋 What's been set up:${NC}"
echo -e "   ✅ Database migrations (currencies, countries, guest checkout)"
echo -e "   ✅ Application caches cleared"
echo -e "   ✅ File permissions configured"
echo ""

echo -e "${BLUE}🌍 International Features Available:${NC}"
echo -e "   • 6 Languages: EN, DE, FR, ES, IT, AR"
echo -e "   • 9 Currencies: USD, EUR, GBP, AED, SAR, EGP, JPY, CAD, AUD"
echo -e "   • Guest checkout (buy without registration)"
echo -e "   • Multi-currency payment processing"
echo -e "   • Auto country & language detection"
echo -e "   • VAT calculation per country"
echo ""

echo -e "${BLUE}🚀 Next Steps:${NC}"
echo -e "   1. Configure Stripe keys in .env (for payments)"
echo -e "   2. Configure ExchangeRate API key in .env (for currency conversion)"
echo -e "   3. Start development server: ${YELLOW}php artisan serve${NC}"
echo -e "   4. Visit: ${YELLOW}http://localhost:8000${NC}"
echo -e "   5. Test language switching (top navigation)"
echo -e "   6. Test currency switching (top navigation)"
echo -e "   7. Test guest checkout (add product without login)"
echo ""

echo -e "${BLUE}📚 Documentation:${NC}"
echo -e "   • Complete Guide: INTERNATIONAL_COMPLETE_GUIDE.md"
echo -e "   • Implementation Details: IMPLEMENTIERUNG_ZUSAMMENFASSUNG.md"
echo -e "   • Quick Start: QUICK_START.md"
echo ""

echo -e "${BLUE}⚙️  Configuration Needed:${NC}"
echo -e "   Edit .env file and add:"
echo -e "   ${YELLOW}STRIPE_KEY${NC}=pk_test_xxxxxxxxxxxx"
echo -e "   ${YELLOW}STRIPE_SECRET${NC}=sk_test_xxxxxxxxxxxx"
echo -e "   ${YELLOW}EXCHANGERATE_API_KEY${NC}=your_api_key"
echo ""

echo -e "${GREEN}🎮 Happy Coding! Your international e-commerce platform is ready!${NC}"
echo ""
