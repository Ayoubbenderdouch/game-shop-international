<?php

namespace App\Http\Controllers;

use App\Models\FreeFireOrder;
use App\Services\FreeFireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreeFireController extends Controller
{
    protected $freeFireService;

    public function __construct(FreeFireService $freeFireService)
    {
        $this->freeFireService = $freeFireService;
    }

    public function index()
    {
        $packages = [
            ['diamonds' => 100, 'price' => 1.99, 'bonus' => 0],
            ['diamonds' => 310, 'price' => 4.99, 'bonus' => 10],
            ['diamonds' => 520, 'price' => 9.99, 'bonus' => 20],
            ['diamonds' => 1060, 'price' => 19.99, 'bonus' => 60],
            ['diamonds' => 2180, 'price' => 39.99, 'bonus' => 180],
            ['diamonds' => 5600, 'price' => 99.99, 'bonus' => 600],
        ];

        return view('freefire.index', compact('packages'));
    }

    public function charge(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|string',
            'diamond_amount' => 'required|integer|in:100,310,520,1060,2180,5600',
        ]);

        $prices = [
            100 => 1.99,
            310 => 4.99,
            520 => 9.99,
            1060 => 19.99,
            2180 => 39.99,
            5600 => 99.99,
        ];

        $price = $prices[$validated['diamond_amount']];

        // Create order
        $order = FreeFireOrder::create([
            'user_id' => Auth::id(),
            'player_id' => $validated['player_id'],
            'diamond_amount' => $validated['diamond_amount'],
            'price' => $price,
            'status' => 'pending',
        ]);

        // Process payment (mock for now)
        $result = $this->freeFireService->chargeDiamonds(
            $validated['player_id'],
            $validated['diamond_amount']
        );

        if ($result['success']) {
            $redemptionCode = $this->freeFireService->generateRedemptionCode();
            $order->status = 'completed';
            $order->transaction_id = $result['transaction_id'];
            $order->redemption_code = $redemptionCode;
            $order->save();

            return redirect()->route('freefire.success', $order)
                ->with('success', 'Diamonds charged successfully!');
        } else {
            $order->status = 'failed';
            $order->save();

            return redirect()->back()
                ->with('error', 'Failed to charge diamonds. Please try again.');
        }
    }

    public function orders()
    {
        $orders = FreeFireOrder::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('freefire.orders', compact('orders'));
    }

    public function success(FreeFireOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('freefire.success', compact('order'));
    }

    public function howToRedeem()
    {
        return view('freefire.how-to-redeem');
    }
}
