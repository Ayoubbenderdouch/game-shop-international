<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()
            ->withCount(['products' => function ($query) {
                $query->active()->available();
            }])
            ->orderBy('sort_order')
            ->get();

        $query = Product::active()
            ->available()
            ->with(['category', 'reviews']);

        // Filter by category
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('selling_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')
                      ->orderBy('reviews_avg_rating', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('shop', compact('categories', 'products'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with(['category', 'reviews.user'])
            ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->available()
            ->limit(4)
            ->get();

        // Check if user can review
        $canReview = false;
        $hasReviewed = false;

        if (auth()->check()) {
            $canReview = auth()->user()->canReview($product->id);
            $hasReviewed = auth()->user()->hasReviewed($product->id);
        }

        return view('product', compact('product', 'relatedProducts', 'canReview', 'hasReviewed'));
    }

    public function pubgUc()
    {
        $category = Category::where('slug', 'pubg-uc')->first();

        if (!$category) {
            // Create PUBG UC category if it doesn't exist
            $category = Category::create([
                'api_id' => 'pubg-uc',
                'name' => 'PUBG UC',
                'slug' => 'pubg-uc',
                'description' => 'PUBG Mobile UC Top-up - Instant Delivery',
                'is_active' => true,
            ]);
        }

        $products = Product::where('category_id', $category->id)
            ->active()
            ->available()
            ->orderBy('cost_price', 'asc')
            ->get();

        return view('pubg-uc', compact('products'));
    }
}
