<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $likeCardService;
    protected $paymentService;

    public function __construct(LikeCardApiService $likeCardService, PaymentService $paymentService)
    {
        $this->likeCardService = $likeCardService;
        $this->paymentService = $paymentService;
    }

    /**
     * Create order from cart items
     */
    public function createOrderFromCart(User $user, array $paymentData)
    {
        DB::beginTransaction();

        try {
            $cartItems = $user->cartItems()->with('product')->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

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

            $totalAmount = $subtotal + $vatAmount;

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $paymentData['payment_method'] ?? 'stripe',
                'payment_data' => $paymentData,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
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

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process payment for order
     */
    public function processPayment(Order $order, array $paymentData)
    {
        try {
            // Process payment through payment service
            $paymentResult = $this->paymentService->processPayment($order, $paymentData);

            if ($paymentResult['success']) {
                $order->markAsPaid();
                $order->update([
                    'payment_intent_id' => $paymentResult['payment_intent_id'] ?? null,
                ]);

                // Process order with API
                $this->processOrderWithApi($order);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process order with LikeCard API
     */
    public function processOrderWithApi(Order $order)
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

                    // Update product stock if applicable
                    if ($item->product->stock_quantity !== null) {
                        $item->product->decrement('stock_quantity', $item->quantity);
                    }

                    // Increment sales count
                    $item->product->increment('sales_count', $item->quantity);
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

            // Send order completion notification
            $this->sendOrderCompletionNotification($order);
        } elseif ($order->orderItems()->where('status', 'failed')->exists()) {
            // If some items failed, mark order as partially failed
            $order->update(['status' => 'processing']);

            // Send failure notification
            $this->sendOrderFailureNotification($order);
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order)
    {
        if (!$order->canBeCancelled()) {
            throw new \Exception('Order cannot be cancelled');
        }

        DB::beginTransaction();

        try {
            $order->update([
                'status' => 'cancelled',
            ]);

            // Restore product stock if applicable
            foreach ($order->orderItems as $item) {
                if ($item->product->stock_quantity !== null) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            // Process refund if payment was made
            if ($order->isPaid()) {
                $this->paymentService->refund($order);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send order completion notification
     */
    protected function sendOrderCompletionNotification(Order $order)
    {
        try {
            $order->user->notify(new \App\Notifications\OrderCompleted($order));
        } catch (\Exception $e) {
            Log::error('Failed to send order completion notification: ' . $e->getMessage());
        }
    }

    /**
     * Send order failure notification
     */
    protected function sendOrderFailureNotification(Order $order)
    {
        try {
            $order->user->notify(new \App\Notifications\OrderFailed($order));
        } catch (\Exception $e) {
            Log::error('Failed to send order failure notification: ' . $e->getMessage());
        }
    }
}
