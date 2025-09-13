<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Use Auth facade to avoid Intelephense warnings
        if (Auth::check()) {
            $user = Auth::user();
            $canReview = $user->canReview($product->id);
            $hasReviewed = $user->hasReviewed($product->id);
        }

        return view('product', compact('product', 'relatedProducts', 'canReview', 'hasReviewed'));
    }

    /**
     * Display products by category.
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $categories = Category::active()
            ->withCount(['products' => function ($query) {
                $query->active()->available();
            }])
            ->orderBy('sort_order')
            ->get();

        $products = Product::active()
            ->available()
            ->where('category_id', $category->id)
            ->with(['category', 'reviews'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12);

        return view('shop', compact('categories', 'products', 'category'));
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

    /**
     * Search products (API endpoint for AJAX).
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return redirect()->route('shop');
        }

        $categories = Category::active()
            ->withCount(['products' => function ($q) {
                $q->active()->available();
            }])
            ->orderBy('sort_order')
            ->get();

        $products = Product::active()
            ->available()
            ->with(['category', 'reviews'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($query) {
                      $categoryQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->paginate(12)
            ->withQueryString();

        return view('shop', compact('categories', 'products', 'query'));
    }

    /**
     * Search products API for autocomplete.
     */
    public function searchApi(Request $request)
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->available()
            ->with('category')
            ->where('name', 'like', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'price' => $product->selling_price,
                    'formatted_price' => '$' . number_format($product->selling_price, 2),
                    'url' => route('product.show', $product->slug),
                    'image' => $product->image,
                ];
            });

        return response()->json($products);
    }

    /**
     * Filter products (AJAX endpoint).
     */
    public function filter(Request $request)
    {
        $query = Product::active()
            ->available()
            ->with(['category', 'reviews']);

        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->has('min_price') && $request->min_price) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('selling_price', '<=', $request->max_price);
        }

        // Rating filter
        if ($request->has('min_rating') && $request->min_rating) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
            });
        }

        // Sort
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

        return response()->json([
            'html' => view('partials.products-grid', compact('products'))->render(),
            'pagination' => $products->links()->render(),
            'total' => $products->total(),
        ]);
    }
}
