<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add international fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('email'); // ISO 3166-1 alpha-2
            $table->string('currency', 3)->default('USD')->after('country_code'); // ISO 4217
            $table->string('timezone', 50)->default('UTC')->after('currency');
            $table->string('phone_country_code', 5)->nullable()->after('phone');
            $table->string('preferred_language', 5)->default('en')->after('timezone');
        });

        // Create currency_rates table for exchange rates
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3)->unique(); // ISO 4217 currency code
            $table->string('currency_name');
            $table->string('currency_symbol', 10);
            $table->decimal('rate_to_usd', 10, 6); // Exchange rate to USD
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_updated')->nullable();
            $table->timestamps();

            $table->index('currency');
            $table->index('is_active');
        });

        // Create countries table for country-specific settings
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('name_de')->nullable();
            $table->string('name_fr')->nullable();
            $table->string('name_es')->nullable();
            $table->string('name_it')->nullable();
            $table->string('default_currency', 3)->default('USD');
            $table->string('default_language', 5)->default('en');
            $table->decimal('vat_rate', 5, 2)->default(0); // VAT/Tax percentage
            $table->string('tax_name', 50)->default('VAT'); // VAT, GST, Sales Tax, etc.
            $table->json('supported_currencies')->nullable();
            $table->json('supported_languages')->nullable();
            $table->json('payment_methods')->nullable(); // Available payment methods
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });

        // Add multi-currency support to products
        Schema::table('products', function (Blueprint $table) {
            $table->json('regional_prices')->nullable()->after('selling_price'); // Store prices for different regions
        });

        // Add multi-currency support to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('exchange_rate', 10, 6)->default(1)->after('currency');
            $table->string('customer_country', 2)->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'currency', 'timezone', 'phone_country_code', 'preferred_language']);
        });

        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('countries');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('regional_prices');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'customer_country']);
        });
    }
};
