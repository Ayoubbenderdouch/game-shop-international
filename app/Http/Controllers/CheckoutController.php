<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\LikeCardApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $likeCardService;

    public function __construct(LikeCardApiService $likeCardService)
    {
        $this->middleware('auth');
        $this->likeCardService = $likeCardService;
    }

    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('messages.cart.empty'));
        }

        $subtotal = 0;
        $vatAmount = 0;

        foreach ($cartItems as $item) {
            $itemTotal = $item->product->selling_price * $item->quantity;
            $subtotal += $itemTotal;

            if ($item->product->vat_percentage > 0) {
                $vatAmount += $itemTotal * ($item->product->vat_percentage / 100);
            }
        }

        $total = $subtotal + $vatAmount;

        return view('checkout', compact('cartItems', 'subtotal', 'vatAmount', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe',
            'stripeToken' => 'required_if:payment_method,stripe',
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', __('messages.cart.empty'));
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            $vatAmount = 0;

            foreach ($cartItems as $item) {
                $itemTotal = $item->product->selling_price * $item->quantity;
                $subtotal += $itemTotal;

                if ($item->product->vat_percentage > 0) {
                    $vatAmount += $itemTotal * ($item->product->vat_percentage / 100);
                }
            }

            $total = $subtotal + $vatAmount;

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $total,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_api_id' => $cartItem->product->api_id,
                    'quantity' => $cartItem->quantity,
                    'cost_price' => $cartItem->product->cost_price,
                    'selling_price' => $cartItem->product->selling_price,
                    'total_price' => $cartItem->product->selling_price * $cartItem->quantity,
                    'optional_fields_data' => $cartItem->optional_fields_data,
                    'status' => 'pending',
                ]);
            }

            // Mock Stripe Payment Processing
            // In production, you would use actual Stripe SDK here
            $paymentSuccessful = $this->mockStripePayment($request->stripeToken, $total);

            if ($paymentSuccessful) {
                // Mark order as paid
                $order->markAsPaid();

                // Process order with LikeCard API
                $this->processOrderWithApi($order);

                // Clear cart
                auth()->user()->cartItems()->delete();

                DB::commit();

                return redirect()->route('checkout.success')
                    ->with('success', __('messages.checkout.order_placed'));
            } else {
                DB::rollBack();
                return redirect()->route('checkout.index')
                    ->with('error', __('messages.checkout.payment_failed'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());

            return redirect()->route('checkout.index')
                ->with('error', __('messages.checkout.payment_failed'));
        }
    }

    protected function mockStripePayment($token, $amount)
    {
        // Mock payment processing
        // In production, use Stripe SDK:
        // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        // $charge = \Stripe\Charge::create([...]);

        // Simulate 95% success rate for testing
        return rand(1, 100) <= 95;
    }

    protected function processOrderWithApi(Order $order)
    {
        foreach ($order->orderItems as $item) {
            try {
                // Create order with LikeCard API
                $apiResponse = $this->likeCardService->createOrder(
                    $item->product_api_id,
                    $order->reference_id,
                    $item->quantity,
                    $item->optional_fields_data
                );

                if ($apiResponse && isset($apiResponse['orderId'])) {
                    // Update order item with API response
                    $item->markAsDelivered($apiResponse['serials'] ?? null);

                    // Update order with API order ID
                    if (!$order->api_order_id) {
                        $order->update(['api_order_id' => $apiResponse['orderId']]);
                    }
                } else {
                    $item->markAsFailed();
                    Log::error('LikeCard API failed for order item: ' . $item->id);
                }
            } catch (\Exception $e) {
                $item->markAsFailed();
                Log::error('Error processing order item ' . $item->id . ': ' . $e->getMessage());
            }
        }

        // Check if all items are delivered
        $allDelivered = $order->orderItems()->where('status', 'delivered')->count() === $order->orderItems->count();

        if ($allDelivered) {
            $order->markAsCompleted();
        } elseif ($order->orderItems()->where('status', 'failed')->exists()) {
            // If some items failed, mark order as partially failed
            $order->update(['status' => 'processing']);
        }
    }

    public function success()
    {
        $order = auth()->user()->orders()->latest()->first();

        if (!$order) {
            return redirect()->route('home');
        }

        return view('checkout.success', compact('order'));
    }

    public function cancel()
    {
        return redirect()->route('cart.index')
            ->with('info', __('messages.checkout.cancelled'));
    }
}
