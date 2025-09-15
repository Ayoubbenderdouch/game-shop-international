<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OrderController extends Controller implements HasMiddleware
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
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['orderItems.product'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order (checkout).
     */
    public function create()
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('orders.cart_empty'));
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->selling_price * $item->quantity;
        });

        return view('orders.create', compact('cartItems', 'total'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required_if:requires_shipping,true|string|nullable',
            'phone' => 'required|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string|max:500'
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('orders.cart_empty'));
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->product->selling_price * $item->quantity;
            }

            // Create order
            $order = auth()->user()->orders()->create([
                'order_number' => $this->generateOrderNumber(),
                'subtotal' => $subtotal,
                'total' => $subtotal, // Can add tax, shipping, etc. later
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'currency' => config('app.currency', 'USD'),
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $order->orderItems()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->selling_price,
                    'total' => $cartItem->product->selling_price * $cartItem->quantity,
                    'optional_fields_data' => $cartItem->optional_fields_data,
                ]);

                // Update product stock if applicable
                if ($cartItem->product->stock_quantity !== null) {
                    $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
                }

                // Update product sales count
                $cartItem->product->increment('sales_count', $cartItem->quantity);
            }

            // Clear cart
            auth()->user()->cartItems()->delete();

            DB::commit();

            // Send order confirmation email (if implemented)
            // Mail::to(auth()->user())->send(new OrderConfirmation($order));

            return redirect()->route('orders.show', $order)
                ->with('success', __('orders.placed_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Order creation failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', __('orders.creation_failed'));
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // FIXED: Removed 'transaction' relationship that doesn't exist
        $order->load(['orderItems.product']);

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel an order.
     */
    public function cancel(Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()
                ->with('error', __('orders.cannot_cancel'));
        }

        DB::beginTransaction();

        try {
            // Restore product stock
            foreach ($order->orderItems as $item) {
                if ($item->product->stock_quantity !== null) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
                // Decrement sales count
                $item->product->decrement('sales_count', $item->quantity);
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', __('orders.cancelled_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Order cancellation failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', __('orders.cancellation_failed'));
        }
    }

    /**
     * Download order invoice.
     */
    public function invoice(Order $order)
    {
        // Ensure user can only download their own invoices
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Generate and return invoice PDF
        // This would require a PDF library like DomPDF or similar
        // For now, just show the order details
        return view('orders.invoice', compact('order'));
    }

    /**
     * Generate a unique order number.
     */
    private function generateOrderNumber()
    {
        $prefix = 'ORD';
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(uniqid(), -4));

        return $prefix . '-' . $timestamp . '-' . $random;
    }
}
