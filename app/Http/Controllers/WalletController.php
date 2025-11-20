<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class WalletController extends Controller
{
    /**
     * Display wallet overview
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get recent transactions
        $transactions = $user->walletTransactions()
            ->latest()
            ->paginate(20);

        // Get statistics
        $stats = [
            'total_deposits' => $user->walletTransactions()
                ->where('type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_spent' => $user->walletTransactions()
                ->where('type', 'purchase')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_transactions' => $user->walletTransactions()
                ->where('status', 'completed')
                ->count(),
        ];

        return view('wallet.index', compact('user', 'transactions', 'stats'));
    }

    /**
     * Show deposit form
     */
    public function deposit()
    {
        $user = auth()->user();
        
        // Predefined amounts
        $amounts = [10, 25, 50, 100, 250, 500];
        
        return view('wallet.deposit', compact('user', 'amounts'));
    }

    /**
     * Process deposit via Stripe
     */
    public function processDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:5|max:10000',
        ]);

        $user = auth()->user();
        $amount = $request->amount;

        try {
            // Initialize Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create Stripe Checkout Session
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower(config('app.currency', 'eur')),
                        'product_data' => [
                            'name' => 'Wallet Deposit',
                            'description' => 'Add funds to your wallet',
                        ],
                        'unit_amount' => $amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('wallet.deposit.success') . '?session_id={CHECKOUT_SESSION_ID}&amount=' . $amount,
                'cancel_url' => route('wallet.deposit.cancel'),
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => $user->id,
                    'type' => 'wallet_deposit',
                    'amount' => $amount,
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful deposit
     */
    public function depositSuccess(Request $request)
    {
        $sessionId = $request->session_id;
        $amount = $request->amount;

        if (!$sessionId || !$amount) {
            return redirect()->route('wallet.index')
                ->with('error', 'Invalid deposit session');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $user = auth()->user();
                
                // Add funds to wallet
                $transaction = $user->addToWallet(
                    $amount,
                    'Wallet deposit via Stripe',
                    'stripe',
                    $sessionId
                );

                return redirect()->route('wallet.index')
                    ->with('success', 'Wallet funded successfully! Added ' . number_format($amount, 2) . ' ' . config('app.currency', 'EUR'));
            }

            return redirect()->route('wallet.index')
                ->with('error', 'Payment verification failed');

        } catch (\Exception $e) {
            return redirect()->route('wallet.index')
                ->with('error', 'Deposit verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle cancelled deposit
     */
    public function depositCancel()
    {
        return redirect()->route('wallet.index')
            ->with('info', 'Deposit cancelled');
    }

    /**
     * Show transaction history
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->walletTransactions();

        // Filter by type
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from') && $request->from) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->has('to') && $request->to) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->latest()->paginate(50);

        return view('wallet.history', compact('transactions'));
    }

    /**
     * Get wallet balance (API endpoint)
     */
    public function getBalance()
    {
        $user = auth()->user();
        
        return response()->json([
            'balance' => $user->getWalletBalance(),
            'formatted_balance' => $user->formatted_wallet_balance,
        ]);
    }
}

