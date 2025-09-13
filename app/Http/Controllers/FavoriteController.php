<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FavoriteController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Display a listing of user's favorites.
     */
    public function index()
    {
        $favorites = auth()->user()->favorites()
            ->with(['product' => function($query) {
                $query->with('category', 'reviews');
            }])
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite status for a product.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        $favorite = Favorite::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
            $message = __('favorites.removed');
        } else {
            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            $isFavorited = true;
            $message = __('favorites.added');
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_favorited' => $isFavorited,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove a favorite.
     */
    public function remove(Favorite $favorite)
    {
        // Ensure the favorite belongs to the authenticated user
        if ($favorite->user_id !== auth()->id()) {
            abort(403);
        }

        $favorite->delete();

        return back()->with('success', __('favorites.removed'));
    }

    /**
     * Check if a product is favorited (API).
     */
    public function check(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $isFavorited = Favorite::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->exists();

        return response()->json([
            'is_favorited' => $isFavorited
        ]);
    }
}
