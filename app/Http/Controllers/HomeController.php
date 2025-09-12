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
                ->limit(7) // Leave room for PUBG UC special category
                ->get();
        });

        // Fetch featured products (best sellers)
        $featuredProducts = Cache::remember('featured_products', 1800, function () {
            return Product::where('is_active', true)
                ->where('is_available', true)
                ->where(function($query) {
                    $query->whereNull('stock_quantity')
                          ->orWhere('stock_quantity', '>', 0);
                })
                ->with(['category', 'reviews'])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderByDesc('sales_count')
                ->orderByDesc('reviews_avg_rating')
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

        return view('home', compact('categories', 'featuredProducts', 'stats'));
    }

    public function setLocale(Request $request)
    {
        $locale = $request->input('locale', 'en');

        if (in_array($locale, ['en', 'ar', 'fr'])) {
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
}
