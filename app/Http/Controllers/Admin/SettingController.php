<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        // Get all settings from database
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();

        // Default settings structure
        $defaultSettings = [
            // General Settings
            'site_name' => config('app.name', 'Laravel Store'),
            'site_description' => 'Your online store description',
            'site_email' => 'admin@example.com',
            'site_phone' => '+1234567890',
            'site_address' => '123 Main St, City, Country',

            // Business Settings
            'currency' => 'USD',
            'currency_symbol' => '$',
            'tax_rate' => '0',
            'shipping_fee' => '0',
            'free_shipping_threshold' => '100',

            // Payment Settings
            'payment_methods' => json_encode(['credit_card', 'paypal', 'stripe']),
            'stripe_public_key' => '',
            'stripe_secret_key' => '',
            'paypal_client_id' => '',
            'paypal_secret' => '',

            // Email Settings
            'email_from_address' => 'noreply@example.com',
            'email_from_name' => 'Store Admin',
            'smtp_host' => 'smtp.mailtrap.io',
            'smtp_port' => '2525',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',

            // Store Settings
            'store_status' => 'open',
            'maintenance_message' => 'We are currently under maintenance. Please check back later.',
            'allow_registration' => '1',
            'require_email_verification' => '1',
            'allow_guest_checkout' => '1',

            // Product Settings
            'products_per_page' => '12',
            'default_product_sorting' => 'newest',
            'show_out_of_stock' => '1',
            'enable_reviews' => '1',
            'review_approval_required' => '1',

            // Order Settings
            'order_prefix' => 'ORD',
            'minimum_order_amount' => '0',
            'order_status_after_payment' => 'processing',
            'auto_complete_order' => '0',
            'order_cancellation_time' => '24',

            // Social Media
            'facebook_url' => '',
            'twitter_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'linkedin_url' => '',

            // SEO Settings
            'meta_title' => 'Online Store',
            'meta_description' => 'Shop the best products online',
            'meta_keywords' => 'online store, shopping, products',
            'google_analytics_id' => '',
            'facebook_pixel_id' => '',

            // API Settings
            'api_enabled' => '0',
            'api_key' => '',
            'api_secret' => '',
            'api_endpoint' => '',
            'api_version' => 'v1',
        ];

        // Merge with existing settings
        $settings = array_merge($defaultSettings, $settings);

        // Group settings by category
        $groupedSettings = [
            'general' => [
                'title' => 'General Settings',
                'icon' => 'fas fa-cog',
                'settings' => [
                    'site_name', 'site_description', 'site_email',
                    'site_phone', 'site_address'
                ]
            ],
            'business' => [
                'title' => 'Business Settings',
                'icon' => 'fas fa-briefcase',
                'settings' => [
                    'currency', 'currency_symbol', 'tax_rate',
                    'shipping_fee', 'free_shipping_threshold'
                ]
            ],
            'payment' => [
                'title' => 'Payment Settings',
                'icon' => 'fas fa-credit-card',
                'settings' => [
                    'payment_methods', 'stripe_public_key', 'stripe_secret_key',
                    'paypal_client_id', 'paypal_secret'
                ]
            ],
            'email' => [
                'title' => 'Email Settings',
                'icon' => 'fas fa-envelope',
                'settings' => [
                    'email_from_address', 'email_from_name', 'smtp_host',
                    'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption'
                ]
            ],
            'store' => [
                'title' => 'Store Settings',
                'icon' => 'fas fa-store',
                'settings' => [
                    'store_status', 'maintenance_message', 'allow_registration',
                    'require_email_verification', 'allow_guest_checkout'
                ]
            ],
            'product' => [
                'title' => 'Product Settings',
                'icon' => 'fas fa-box',
                'settings' => [
                    'products_per_page', 'default_product_sorting', 'show_out_of_stock',
                    'enable_reviews', 'review_approval_required'
                ]
            ],
            'order' => [
                'title' => 'Order Settings',
                'icon' => 'fas fa-shopping-cart',
                'settings' => [
                    'order_prefix', 'minimum_order_amount', 'order_status_after_payment',
                    'auto_complete_order', 'order_cancellation_time'
                ]
            ],
            'social' => [
                'title' => 'Social Media',
                'icon' => 'fas fa-share-alt',
                'settings' => [
                    'facebook_url', 'twitter_url', 'instagram_url',
                    'youtube_url', 'linkedin_url'
                ]
            ],
            'seo' => [
                'title' => 'SEO Settings',
                'icon' => 'fas fa-search',
                'settings' => [
                    'meta_title', 'meta_description', 'meta_keywords',
                    'google_analytics_id', 'facebook_pixel_id'
                ]
            ],
            'api' => [
                'title' => 'API Settings',
                'icon' => 'fas fa-plug',
                'settings' => [
                    'api_enabled', 'api_key', 'api_secret',
                    'api_endpoint', 'api_version'
                ]
            ]
        ];

        return view('admin.settings.index', compact('settings', 'groupedSettings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get all input except CSRF token
            $settings = $request->except('_token', '_method');

            foreach ($settings as $key => $value) {
                // Handle special cases
                if ($key === 'payment_methods' && is_array($value)) {
                    $value = json_encode($value);
                }

                // Update or insert setting
                DB::table('settings')->updateOrInsert(
                    ['key' => $key],
                    [
                        'value' => $value,
                        'updated_at' => now()
                    ]
                );
            }

            // Clear cache
            Cache::flush();

            // Update config values
            if (isset($settings['site_name'])) {
                config(['app.name' => $settings['site_name']]);
            }

            if (isset($settings['smtp_host'])) {
                config([
                    'mail.mailers.smtp.host' => $settings['smtp_host'],
                    'mail.mailers.smtp.port' => $settings['smtp_port'] ?? 587,
                    'mail.mailers.smtp.username' => $settings['smtp_username'] ?? '',
                    'mail.mailers.smtp.password' => $settings['smtp_password'] ?? '',
                    'mail.mailers.smtp.encryption' => $settings['smtp_encryption'] ?? 'tls',
                ]);
            }

            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return redirect()->route('admin.settings.index')
                ->with('success', 'Cache cleared successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Test email settings
     */
    public function testEmail(Request $request)
    {
        try {
            $request->validate([
                'test_email' => 'required|email'
            ]);

            // Send test email
            \Mail::raw('This is a test email from your store settings.', function ($message) use ($request) {
                $message->to($request->test_email)
                        ->subject('Test Email - Store Settings');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }
}
