<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReviewController extends Controller implements HasMiddleware
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
     * Store a newly created review.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $user = auth()->user();
        $productId = $request->product_id;

        // Check if user has purchased the product
        if (!$user->hasPurchased($productId)) {
            return back()->with('error', __('reviews.must_purchase_first'));
        }

        // Check if user has already reviewed the product
        if ($user->hasReviewed($productId)) {
            return back()->with('error', __('reviews.already_reviewed'));
        }

        // Get the order where the user purchased this product
        $order = $user->orders()
            ->where('status', 'completed')
            ->whereHas('orderItems', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->first();

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'order_id' => $order ? $order->id : null,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_verified_purchase' => true,
            'is_approved' => true // Auto-approve verified purchases
        ]);

        // Update product's average rating
        $product = Product::find($productId);
        $product->updateAverageRating();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('reviews.created_successfully'),
                'review' => $review->load('user')
            ]);
        }

        return back()->with('success', __('reviews.created_successfully'));
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        // Ensure the review belongs to the authenticated user
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        // Update product's average rating
        $review->product->updateAverageRating();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('reviews.updated_successfully'),
                'review' => $review->load('user')
            ]);
        }

        return back()->with('success', __('reviews.updated_successfully'));
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        // Ensure the review belongs to the authenticated user or user is admin
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $product = $review->product;
        $review->delete();

        // Update product's average rating
        $product->updateAverageRating();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('reviews.deleted_successfully')
            ]);
        }

        return back()->with('success', __('reviews.deleted_successfully'));
    }
}
