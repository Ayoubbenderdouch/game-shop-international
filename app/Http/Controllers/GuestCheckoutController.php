<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\LikeCardApiService;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GuestCheckoutController extends Controller
{
    protected $likeCardService;
    protected $currencyService;

    public function __construct(LikeCardApiService $likeCardService, CurrencyService $currencyService)
    {
        $this->likeCardService = $likeCardService;
        $this->currencyService = $currencyService;
    }

    /**
     * Show guest checkout page
     */
    public function index()
    {
        // Get cart from session
        $cartItems = session('guest_cart', []);

        if (empty($cartItems)) {
            return redirect()->route('shop')
                ->with('error', __('Your cart is empty'));
        }

        // Load products and calculate totals
        $products = [];
        $subtotal = 0;
        $currency = $this->currencyService->getUserCurrency();

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                // Convert price to user's currency
                $price = $this->currencyService->convertPrice($product->selling_price, $currency);
                $itemTotal = $price * $item['quantity'];
                
                $products[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $itemTotal,
                    'optional_fields_data' => $item['optional_fields_data'] ?? null,
                ];
                
                $subtotal += $itemTotal;
            }
        }

        // Get user's country for VAT calculation
        $country = $this->currencyService->detectCountryFromIP();
        $vatRate = 0;
        
        if ($country) {
            $countryModel = \App\Models\Country::where('code', $country)->first();
            if ($countryModel) {
                $vatRate = $countryModel->vat_rate ?? 0;
            }
        }

        $vatAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        return view('checkout.guest', compact('products', 'subtotal', 'vatAmount', 'total', 'currency', 'vatRate'));
    }

    /**
     * Process guest checkout
     */
    public function process(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'payment_method' => 'required|in:stripe,paypal',
            'stripeToken' => 'required_if:payment_method,stripe',
            'country' => 'nullable|string|max:2',
        ]);

        $cartItems = session('guest_cart', []);

        if (empty($cartItems)) {
            return redirect()->route('shop')
                ->with('error', __('Your cart is empty'));
        }

        DB::beginTransaction();

        try {
            $currency = $this->currencyService->getUserCurrency();
            $exchangeRate = \App\Models\CurrencyRate::getByCurrency($currency);
            
            // Calculate totals
            $subtotal = 0;
            $products = [];
            
            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $price = $this->currencyService->convertPrice($product->selling_price, $currency);
                    $itemTotal = $price * $item['quantity'];
                    $subtotal += $itemTotal;
                    
                    $products[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'price' => $price,
                        'price_usd' => $product->selling_price,
                        'optional_fields_data' => $item['optional_fields_data'] ?? null,
                    ];
                }
            }

            // Calculate VAT
            $country = $request->country ?? $this->currencyService->detectCountryFromIP();
            $vatRate = 0;
            
            if ($country) {
                $countryModel = \App\Models\Country::where('code', $country)->first();
                if ($countryModel) {
                    $vatRate = $countryModel->vat_rate ?? 0;
                }
            }

            $vatAmount = $subtotal * ($vatRate / 100);
            $total = $subtotal + $vatAmount;

            // Create guest order (user_id = null)
            $order = Order::create([
                'user_id' => null, // Guest order
                'guest_email' => $request->email,
                'guest_name' => $request->name,
                'guest_phone' => $request->phone,
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $total,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate ? $exchangeRate->rate_to_usd : 1,
                'customer_country' => $country,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            // Create order items
            foreach ($products as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'product_api_id' => $item['product']->api_id,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['product']->cost_price,
                    'selling_price' => $item['price_usd'], // Store in USD
                    'total_price' => $item['price_usd'] * $item['quantity'],
                    'optional_fields_data' => $item['optional_fields_data'],
                    'status' => 'pending',
                ]);
            }

            // Process payment
            $paymentSuccessful = false;
            
            if ($request->payment_method === 'stripe') {
                $paymentSuccessful = $this->processStripePayment($request->stripeToken, $total, $currency, $order);
            } elseif ($request->payment_method === 'paypal') {
                $paymentSuccessful = $this->processPayPalPayment($request, $total, $currency, $order);
            }

            if ($paymentSuccessful) {
                // Mark order as paid
                $order->markAsPaid();

                // Process order with LikeCard API
                $this->processOrderWithApi($order);

                // Clear guest cart
                session()->forget('guest_cart');

                // Store order reference for success page
                session(['guest_order_id' => $order->id]);

                DB::commit();

                return redirect()->route('guest.checkout.success')
                    ->with('success', __('Order placed successfully! Check your email for details.'));
            } else {
                DB::rollBack();
                return redirect()->route('guest.checkout.index')
                    ->with('error', __('Payment failed. Please try again.'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Guest checkout error: ' . $e->getMessage());

            return redirect()->route('guest.checkout.index')
                ->with('error', __('An error occurred. Please try again.'));
        }
    }

    /**
     * Process Stripe payment for guest
     */
    protected function processStripePayment($token, $amount, $currency, $order)
    {
        try {
            // Initialize Stripe
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Create charge
            $charge = \Stripe\Charge::create([
                'amount' => round($amount * 100), // Amount in cents
                'currency' => strtolower($currency),
                'source' => $token,
                'description' => 'Order #' . $order->reference_id . ' (Guest)',
                'metadata' => [
                    'order_id' => $order->id,
                    'order_reference' => $order->reference_id,
                ],
            ]);

            if ($charge->status === 'succeeded') {
                $order->update([
                    'payment_transaction_id' => $charge->id,
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Stripe payment error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Process PayPal payment for guest
     */
    protected function processPayPalPayment($request, $amount, $currency, $order)
    {
        // TODO: Implement PayPal integration
        // For now, mock success for testing
        return true;
    }

    /**
     * Process order with LikeCard API
     */
    protected function processOrderWithApi(Order $order)
    {
        foreach ($order->orderItems as $item) {
            try {
                $apiResponse = $this->likeCardService->createOrder(
                    $item->product_api_id,
                    $order->reference_id,
                    $item->quantity,
                    $item->optional_fields_data
                );

                if ($apiResponse && isset($apiResponse['orderId'])) {
                    $item->markAsDelivered($apiResponse['serials'] ?? null);

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

        // Update order status
        $allDelivered = $order->orderItems()->where('status', 'delivered')->count() === $order->orderItems->count();

        if ($allDelivered) {
            $order->markAsCompleted();
        } elseif ($order->orderItems()->where('status', 'failed')->exists()) {
            $order->update(['status' => 'processing']);
        }
    }

    /**
     * Show success page for guest
     */
    public function success()
    {
        $orderId = session('guest_order_id');
        
        if (!$orderId) {
            return redirect()->route('home');
        }

        $order = Order::with('orderItems.product')->find($orderId);

        if (!$order) {
            return redirect()->route('home');
        }

        return view('checkout.guest-success', compact('order'));
    }

    /**
     * Add item to guest cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'optional_fields_data' => 'nullable|array',
        ]);

        $cart = session('guest_cart', []);
        
        $cart[] = [
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'optional_fields_data' => $request->optional_fields_data,
        ];

        session(['guest_cart' => $cart]);

        return response()->json([
            'success' => true,
            'cart_count' => count($cart),
            'message' => __('Product added to cart'),
        ]);
    }

    /**
     * Get guest cart
     */
    public function getCart()
    {
        $cartItems = session('guest_cart', []);
        $products = [];
        $total = 0;
        $currency = $this->currencyService->getUserCurrency();

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $price = $this->currencyService->convertPrice($product->selling_price, $currency);
                $itemTotal = $price * $item['quantity'];
                
                $products[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $this->currencyService->formatPrice($price, $currency),
                    'total' => $this->currencyService->formatPrice($itemTotal, $currency),
                ];
                
                $total += $itemTotal;
            }
        }

        return response()->json([
            'items' => $products,
            'total' => $this->currencyService->formatPrice($total, $currency),
            'count' => count($cartItems),
        ]);
    }
}
