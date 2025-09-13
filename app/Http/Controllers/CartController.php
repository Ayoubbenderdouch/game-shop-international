<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CartController extends Controller implements HasMiddleware
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

    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();
        $total = $cartItems->sum(function ($item) {
            return $item->product->selling_price * $item->quantity;
        });

        return view('cart', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is available
        if (!$product->is_active || !$product->is_available) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('cart.product_not_available', 'Product is not available')
                ], 400);
            }
            return back()->with('error', __('cart.product_not_available'));
        }

        // Check stock if applicable
        if ($product->stock_quantity !== null && $product->stock_quantity < $request->quantity) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('cart.insufficient_stock', 'Insufficient stock')
                ], 400);
            }
            return back()->with('error', __('cart.insufficient_stock'));
        }

        // Check if product is forbidden in user's country
        if (auth()->user()->country && $product->isForbiddenInCountry(auth()->user()->country)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('cart.product_not_available_in_country', 'Product not available in your country')
                ], 400);
            }
            return back()->with('error', __('cart.product_not_available_in_country'));
        }

        // Get optional fields data if provided
        $optionalFieldsData = null;
        if ($product->hasOptionalFields() && $request->has('optional_fields')) {
            $optionalFieldsData = $request->optional_fields;

            // Validate required optional fields
            foreach ($product->getRequiredOptionalFields() as $field) {
                if (!isset($optionalFieldsData[$field['fieldCode']]) || empty($optionalFieldsData[$field['fieldCode']])) {
                    $errorMessage = __('cart.required_field_missing', ['field' => $field['label']], 'Required field missing: ' . $field['label']);
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 400);
                    }
                    return back()->with('error', $errorMessage);
                }
            }
        }

        // Check if item already exists in cart
        $existingItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingItem) {
            // Update quantity
            $existingItem->increment('quantity', $request->quantity);
            $cartItem = $existingItem;
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'optional_fields_data' => $optionalFieldsData,
            ]);
        }

        // Get updated cart count
        $cartCount = auth()->user()->cartItems()->sum('quantity');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('cart.product_added', 'Product added to cart'),
                'cartCount' => $cartCount,
                'cartItem' => [
                    'id' => $cartItem->id,
                    'product_name' => $product->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->selling_price
                ]
            ]);
        }

        return back()->with('success', __('cart.product_added'));
    }

    public function update(Request $request, CartItem $cartItem)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check stock if applicable
        if ($cartItem->product->stock_quantity !== null &&
            $cartItem->product->stock_quantity < $request->quantity) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('cart.insufficient_stock', 'Insufficient stock')
                ], 400);
            }
            return back()->with('error', __('cart.insufficient_stock'));
        }

        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->ajax()) {
            $cartCount = auth()->user()->cartItems()->sum('quantity');
            $total = auth()->user()->cartItems()->with('product')->get()->sum(function ($item) {
                return $item->product->selling_price * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'message' => __('cart.cart_updated', 'Cart updated'),
                'cartCount' => $cartCount,
                'total' => $total,
                'itemTotal' => $cartItem->product->selling_price * $cartItem->quantity
            ]);
        }

        return back()->with('success', __('cart.cart_updated'));
    }

    public function remove(CartItem $cartItem)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        if (request()->ajax()) {
            $cartCount = auth()->user()->cartItems()->sum('quantity');
            $total = auth()->user()->cartItems()->with('product')->get()->sum(function ($item) {
                return $item->product->selling_price * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'message' => __('cart.product_removed', 'Product removed from cart'),
                'cartCount' => $cartCount,
                'total' => $total
            ]);
        }

        return back()->with('success', __('cart.product_removed'));
    }

    public function clear()
    {
        auth()->user()->cartItems()->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('cart.cart_cleared', 'Cart cleared'),
                'cartCount' => 0
            ]);
        }

        return back()->with('success', __('cart.cart_cleared'));
    }

    /**
     * Get cart count for AJAX requests
     */
    public function getCount()
    {
        $count = auth()->user()->cartItems()->sum('quantity');

        return response()->json([
            'count' => $count,
            'success' => true
        ]);
    }
}
