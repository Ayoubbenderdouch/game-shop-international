<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add columns to existing settings table
        Schema::table('settings', function (Blueprint $table) {
            $table->string('key')->unique()->after('id');
            $table->text('value')->nullable()->after('key');
            $table->string('type')->default('string')->after('value'); // string, boolean, integer, json, array
            $table->string('group')->nullable()->after('type'); // general, business, payment, etc.
            $table->text('description')->nullable()->after('group');

            // Add indexes
            $table->index('key');
            $table->index('group');
        });

        // Seed default settings
        $this->seedDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the added columns
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['key']);
            $table->dropIndex(['group']);
            $table->dropColumn(['key', 'value', 'type', 'group', 'description']);
        });
    }

    /**
     * Seed default settings
     */
    private function seedDefaultSettings(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => config('app.name', 'Laravel Store'), 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_description', 'value' => 'Your online store description', 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_email', 'value' => 'admin@example.com', 'group' => 'general', 'type' => 'email'],
            ['key' => 'site_phone', 'value' => '+1234567890', 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_address', 'value' => '123 Main St, City, Country', 'group' => 'general', 'type' => 'string'],

            // Business Settings
            ['key' => 'currency', 'value' => 'USD', 'group' => 'business', 'type' => 'string'],
            ['key' => 'currency_symbol', 'value' => '$', 'group' => 'business', 'type' => 'string'],
            ['key' => 'tax_rate', 'value' => '0', 'group' => 'business', 'type' => 'integer'],
            ['key' => 'shipping_fee', 'value' => '0', 'group' => 'business', 'type' => 'integer'],
            ['key' => 'free_shipping_threshold', 'value' => '100', 'group' => 'business', 'type' => 'integer'],

            // Payment Settings
            ['key' => 'payment_methods', 'value' => json_encode(['credit_card', 'paypal', 'stripe']), 'group' => 'payment', 'type' => 'json'],
            ['key' => 'stripe_public_key', 'value' => '', 'group' => 'payment', 'type' => 'string'],
            ['key' => 'stripe_secret_key', 'value' => '', 'group' => 'payment', 'type' => 'string'],
            ['key' => 'paypal_client_id', 'value' => '', 'group' => 'payment', 'type' => 'string'],
            ['key' => 'paypal_secret', 'value' => '', 'group' => 'payment', 'type' => 'string'],

            // Email Settings
            ['key' => 'email_from_address', 'value' => 'noreply@example.com', 'group' => 'email', 'type' => 'email'],
            ['key' => 'email_from_name', 'value' => 'Store Admin', 'group' => 'email', 'type' => 'string'],
            ['key' => 'smtp_host', 'value' => 'smtp.mailtrap.io', 'group' => 'email', 'type' => 'string'],
            ['key' => 'smtp_port', 'value' => '2525', 'group' => 'email', 'type' => 'integer'],
            ['key' => 'smtp_username', 'value' => '', 'group' => 'email', 'type' => 'string'],
            ['key' => 'smtp_password', 'value' => '', 'group' => 'email', 'type' => 'string'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'group' => 'email', 'type' => 'string'],

            // Store Settings
            ['key' => 'store_status', 'value' => 'open', 'group' => 'store', 'type' => 'string'],
            ['key' => 'maintenance_message', 'value' => 'We are currently under maintenance. Please check back later.', 'group' => 'store', 'type' => 'string'],
            ['key' => 'allow_registration', 'value' => '1', 'group' => 'store', 'type' => 'boolean'],
            ['key' => 'require_email_verification', 'value' => '1', 'group' => 'store', 'type' => 'boolean'],
            ['key' => 'allow_guest_checkout', 'value' => '1', 'group' => 'store', 'type' => 'boolean'],

            // Product Settings
            ['key' => 'products_per_page', 'value' => '12', 'group' => 'product', 'type' => 'integer'],
            ['key' => 'default_product_sorting', 'value' => 'newest', 'group' => 'product', 'type' => 'string'],
            ['key' => 'show_out_of_stock', 'value' => '1', 'group' => 'product', 'type' => 'boolean'],
            ['key' => 'enable_reviews', 'value' => '1', 'group' => 'product', 'type' => 'boolean'],
            ['key' => 'review_approval_required', 'value' => '1', 'group' => 'product', 'type' => 'boolean'],

            // Order Settings
            ['key' => 'order_prefix', 'value' => 'ORD', 'group' => 'order', 'type' => 'string'],
            ['key' => 'minimum_order_amount', 'value' => '0', 'group' => 'order', 'type' => 'integer'],
            ['key' => 'order_status_after_payment', 'value' => 'processing', 'group' => 'order', 'type' => 'string'],
            ['key' => 'auto_complete_order', 'value' => '0', 'group' => 'order', 'type' => 'boolean'],
            ['key' => 'order_cancellation_time', 'value' => '24', 'group' => 'order', 'type' => 'integer'],

            // Social Media
            ['key' => 'facebook_url', 'value' => '', 'group' => 'social', 'type' => 'string'],
            ['key' => 'twitter_url', 'value' => '', 'group' => 'social', 'type' => 'string'],
            ['key' => 'instagram_url', 'value' => '', 'group' => 'social', 'type' => 'string'],
            ['key' => 'youtube_url', 'value' => '', 'group' => 'social', 'type' => 'string'],
            ['key' => 'linkedin_url', 'value' => '', 'group' => 'social', 'type' => 'string'],

            // SEO Settings
            ['key' => 'meta_title', 'value' => 'Online Store', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'meta_description', 'value' => 'Shop the best products online', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'meta_keywords', 'value' => 'online store, shopping, products', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'google_analytics_id', 'value' => '', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'facebook_pixel_id', 'value' => '', 'group' => 'seo', 'type' => 'string'],

            // API Settings
            ['key' => 'api_enabled', 'value' => '0', 'group' => 'api', 'type' => 'boolean'],
            ['key' => 'api_key', 'value' => '', 'group' => 'api', 'type' => 'string'],
            ['key' => 'api_secret', 'value' => '', 'group' => 'api', 'type' => 'string'],
            ['key' => 'api_endpoint', 'value' => '', 'group' => 'api', 'type' => 'string'],
            ['key' => 'api_version', 'value' => 'v1', 'group' => 'api', 'type' => 'string'],

            // API Sync Settings
            ['key' => 'api_sync_auto_sync', 'value' => '0', 'group' => 'api', 'type' => 'boolean'],
            ['key' => 'api_sync_update_prices', 'value' => '1', 'group' => 'api', 'type' => 'boolean'],
            ['key' => 'api_sync_update_images', 'value' => '1', 'group' => 'api', 'type' => 'boolean'],
            ['key' => 'api_sync_remove_deleted', 'value' => '0', 'group' => 'api', 'type' => 'boolean'],
        ];

        foreach ($settings as $setting) {
            // Check if setting already exists
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();

            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
