<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch categories with product count
        $categories = Cache::remember('home_categories', 3600, function () {
            return Category::where('is_active', true)
                ->whereNull('parent_id') // Root categories only
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true)
                          ->where('is_available', true);
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(12)
                ->get();
        });

        // Get categorized navigation
        $categorizedNav = CategoryNavigationController::getCategorizedNavigation();

        // Fetch featured products (best sellers)
        $featuredProducts = Cache::remember('featured_products', 1800, function () {
            return Product::where('is_active', true)
                ->where('is_available', true)
                ->with(['category'])
                ->inRandomOrder() // Show random products for now
                ->limit(8)
                ->get()
                ->map(function ($product) {
                    // Calculate discount percentage if original price exists
                    if ($product->original_price && $product->original_price > $product->selling_price) {
                        $product->discount_percentage = round(
                            (($product->original_price - $product->selling_price) / $product->original_price) * 100
                        );
                    } else {
                        $product->discount_percentage = 0;
                    }
                    return $product;
                });
        });

        // Get statistics for the platform
        $stats = Cache::remember('platform_stats', 3600, function () {
            return [
                'total_customers' => \App\Models\User::where('role', 'customer')->count(),
                'total_products' => Product::where('is_active', true)->count(),
                'total_orders' => \App\Models\Order::where('status', 'completed')->count(),
                'countries_served' => 30, // You can make this dynamic based on your needs
            ];
        });

        return view('home-abady', compact('categories', 'featuredProducts', 'stats', 'categorizedNav'));
    }

    public function setLocale($locale)
    {
        if (in_array($locale, ['en', 'ar'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);

            if (auth()->check()) {
                auth()->user()->update(['preferred_language' => $locale]);
            }
        }

        return redirect()->back();
    }

    /**
     * Refresh cached data from API
     */
    public function refreshData()
    {
        // Clear relevant caches
        Cache::forget('home_categories');
        Cache::forget('featured_products');
        Cache::forget('platform_stats');

        // Optionally trigger API sync
        if (auth()->user() && auth()->user()->isAdmin()) {
            app(\App\Services\LikeCardApiService::class)->syncCategories();
            app(\App\Services\LikeCardApiService::class)->syncProducts();
        }

        return redirect()->route('home')->with('success', 'Data refreshed successfully');
    }

    /**
     * Show category page with products from LikeCard API
     */
    public function showCategory($slug)
    {
        // Redirect gift cards to country selection flow
        $giftCardProducts = [
            'google-play' => 'Google Play',
            'itunes' => 'Apple Gift Card',
            'playstation' => 'PlayStation',
            'xbox' => 'XBOX',
            'steam' => 'Steam',
            'razer-gold' => 'Razer Gold',
        ];

        if (isset($giftCardProducts[$slug])) {
            return redirect()->route('product.select-country', $slug);
        }

        // Map homepage slugs to LikeCard category API IDs
        $likecardMapping = [
            'free-fire' => 343,        // FreeFire
            'free-fire-code' => 343,   // FreeFire
            'pubg-mobile' => 201,      // PUBG
            'pubg-code' => 201,        // PUBG
            'genshin-impact' => 675,   // Genshin Impact
            'yala-ludo' => null,       // Not available yet - will show "Coming Soon"
        ];

        // Check if it's a LikeCard game category
        if (isset($likecardMapping[$slug])) {
            $categoryApiId = $likecardMapping[$slug];

            // If category API ID is null, show "Coming Soon"
            if ($categoryApiId === null) {
                // Get a nice name from slug
                $categoryName = ucwords(str_replace('-', ' ', $slug));
                return view('likecard.products', [
                    'category' => $categoryName,
                    'products' => collect([]), // Empty collection
                    'slug' => $slug
                ]);
            }

            // Get category from database
            $category = Category::where('api_id', $categoryApiId)->first();

            if ($category) {
                // Get products from database only (no API sync for faster loading)
                $products = Product::where('category_id', $category->id)
                    ->where('is_active', true)
                    ->where('is_available', true)
                    ->get();

                return view('likecard.products', [
                    'category' => $category->name,
                    'products' => $products,
                    'slug' => $slug
                ]);
            }
        }

        // Not found
        abort(404);
    }
}
