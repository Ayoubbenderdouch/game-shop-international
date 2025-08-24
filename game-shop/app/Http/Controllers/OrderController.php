<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\ProductCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('orderItems.product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        $order->load('orderItems.product', 'orderItems.productCode');

        return view('orders.show', compact('order'));
    }

    public function checkout(Request $request)
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Your cart is empty!');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('checkout', compact('cartItems', 'total'));
    }

    public function processCheckout(Request $request)
    {
        DB::beginTransaction();

        try {
            $cartItems = CartItem::where('user_id', Auth::id())
                ->with('product')
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect('/cart')->with('error', 'Your cart is empty!');
            }

            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'status' => 'completed', // Mock payment - automatically complete
                'payment_method' => 'mock',
                'country' => $request->ip(),
            ]);

            // Create order items and assign codes
            foreach ($cartItems as $cartItem) {
                for ($i = 0; $i < $cartItem->quantity; $i++) {
                    // Get available code
                    $code = ProductCode::where('product_id', $cartItem->product_id)
                        ->where('is_used', false)
                        ->first();

                    if ($code) {
                        $code->is_used = true;
                        $code->used_by = Auth::id();
                        $code->used_at = now();
                        $code->save();
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'product_code_id' => $code ? $code->id : null,
                        'quantity' => 1,
                        'price' => $cartItem->product->price,
                    ]);
                }
            }

            // Clear cart
            CartItem::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect('/orders/' . $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'An error occurred processing your order.');
        }
    }
}
