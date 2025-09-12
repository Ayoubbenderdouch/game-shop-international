<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $stripeKey;
    protected $stripeSecret;

    public function __construct()
    {
        $this->stripeKey = config('services.stripe.key');
        $this->stripeSecret = config('services.stripe.secret');
    }

    /**
     * Process payment for an order
     */
    public function processPayment(Order $order, array $paymentData)
    {
        try {
            // Mock Stripe payment processing
            // In production, you would use actual Stripe SDK here:
            // \Stripe\Stripe::setApiKey($this->stripeSecret);

            $paymentMethod = $paymentData['payment_method'] ?? 'stripe';

            switch ($paymentMethod) {
                case 'stripe':
                    return $this->processStripePayment($order, $paymentData);
                case 'wallet':
                    return $this->processWalletPayment($order);
                default:
                    throw new \Exception('Invalid payment method');
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process Stripe payment (Mock implementation)
     */
    protected function processStripePayment(Order $order, array $paymentData)
    {
        try {
            // Mock payment processing
            // In production, use Stripe SDK:
            /*
            $stripe = new \Stripe\StripeClient($this->stripeSecret);

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $order->total_amount * 100, // Amount in cents
                'currency' => strtolower($order->currency),
                'payment_method' => $paymentData['payment_method_id'],
                'confirm' => true,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);
            */

            // Mock successful payment (95% success rate for testing)
            $isSuccessful = rand(1, 100) <= 95;

            if ($isSuccessful) {
                // Generate mock payment intent ID
                $paymentIntentId = 'pi_mock_' . uniqid();

                Log::info('Mock payment successful for order: ' . $order->order_number);

                return [
                    'success' => true,
                    'payment_intent_id' => $paymentIntentId,
                    'status' => 'succeeded',
                ];
            } else {
                Log::warning('Mock payment failed for order: ' . $order->order_number);

                return [
                    'success' => false,
                    'error' => 'Payment declined',
                    'status' => 'failed',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Stripe payment error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'error',
            ];
        }
    }

    /**
     * Process wallet payment
     */
    protected function processWalletPayment(Order $order)
    {
        try {
            $user = $order->user;

            // Check if user has sufficient balance
            if ($user->wallet_balance < $order->total_amount) {
                return [
                    'success' => false,
                    'error' => 'Insufficient wallet balance',
                ];
            }

            // Deduct from wallet
            $user->decrement('wallet_balance', $order->total_amount);

            Log::info('Wallet payment successful for order: ' . $order->order_number);

            return [
                'success' => true,
                'payment_method' => 'wallet',
                'status' => 'succeeded',
            ];
        } catch (\Exception $e) {
            Log::error('Wallet payment error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund payment for an order
     */
    public function refund(Order $order)
    {
        try {
            if (!$order->isPaid()) {
                throw new \Exception('Order is not paid');
            }

            switch ($order->payment_method) {
                case 'stripe':
                    return $this->refundStripePayment($order);
                case 'wallet':
                    return $this->refundWalletPayment($order);
                default:
                    throw new \Exception('Invalid payment method for refund');
            }
        } catch (\Exception $e) {
            Log::error('Refund processing error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund Stripe payment (Mock implementation)
     */
    protected function refundStripePayment(Order $order)
    {
        try {
            // Mock refund processing
            // In production, use Stripe SDK:
            /*
            $stripe = new \Stripe\StripeClient($this->stripeSecret);

            $refund = $stripe->refunds->create([
                'payment_intent' => $order->payment_intent_id,
                'amount' => $order->total_amount * 100, // Amount in cents
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);
            */

            // Mock successful refund
            $refundId = 're_mock_' . uniqid();

            $order->update([
                'payment_status' => 'refunded',
                'status' => 'refunded',
            ]);

            Log::info('Mock refund successful for order: ' . $order->order_number);

            return [
                'success' => true,
                'refund_id' => $refundId,
                'status' => 'succeeded',
            ];
        } catch (\Exception $e) {
            Log::error('Stripe refund error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refund wallet payment
     */
    protected function refundWalletPayment(Order $order)
    {
        try {
            $user = $order->user;

            // Add amount back to wallet
            $user->increment('wallet_balance', $order->total_amount);

            $order->update([
                'payment_status' => 'refunded',
                'status' => 'refunded',
            ]);

            Log::info('Wallet refund successful for order: ' . $order->order_number);

            return [
                'success' => true,
                'payment_method' => 'wallet',
                'status' => 'succeeded',
            ];
        } catch (\Exception $e) {
            Log::error('Wallet refund error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate payment method data
     */
    public function validatePaymentMethod(array $data)
    {
        $rules = [
            'payment_method' => 'required|in:stripe,wallet',
        ];

        if ($data['payment_method'] === 'stripe') {
            $rules['stripeToken'] = 'required|string';
        }

        $validator = validator($data, $rules);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        return true;
    }

    /**
     * Create payment intent (for future payment)
     */
    public function createPaymentIntent(Order $order)
    {
        try {
            // Mock payment intent creation
            // In production, use Stripe SDK

            $paymentIntentId = 'pi_mock_' . uniqid();
            $clientSecret = 'pi_mock_secret_' . uniqid();

            return [
                'success' => true,
                'payment_intent_id' => $paymentIntentId,
                'client_secret' => $clientSecret,
                'amount' => $order->total_amount,
                'currency' => $order->currency,
            ];
        } catch (\Exception $e) {
            Log::error('Payment intent creation error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
