<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            return back()->with('error', __('cart.product_not_available'));
        }

        // Check stock if applicable
        if ($product->stock_quantity !== null && $product->stock_quantity < $request->quantity) {
            return back()->with('error', __('cart.insufficient_stock'));
        }

        // Check if product is forbidden in user's country
        if (auth()->user()->country && $product->isForbiddenInCountry(auth()->user()->country)) {
            return back()->with('error', __('cart.product_not_available_in_country'));
        }

        // Get optional fields data if provided
        $optionalFieldsData = null;
        if ($product->hasOptionalFields() && $request->has('optional_fields')) {
            $optionalFieldsData = $request->optional_fields;

            // Validate required optional fields
            foreach ($product->getRequiredOptionalFields() as $field) {
                if (!isset($optionalFieldsData[$field['fieldCode']]) || empty($optionalFieldsData[$field['fieldCode']])) {
                    return back()->with('error', __('cart.required_field_missing', ['field' => $field['label']]));
                }
            }
        }

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'quantity' => $request->quantity,
                'optional_fields_data' => $optionalFieldsData,
            ]
        );

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
            return back()->with('error', __('cart.insufficient_stock'));
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', __('cart.cart_updated'));
    }

    public function remove(CartItem $cartItem)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', __('cart.product_removed'));
    }

    public function clear()
    {
        auth()->user()->cartItems()->delete();

        return back()->with('success', __('cart.cart_cleared'));
    }
}
