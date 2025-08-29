<?php

namespace App\Http\Controllers;

use App\Models\PubgUcOrder;
use App\Services\PubgUcService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PubgUcController extends Controller
{
    protected $pubgService;

    public function __construct(PubgUcService $pubgService)
    {
        $this->pubgService = $pubgService;
    }

    public function index()
    {
        $packages = [
            ['uc' => 60, 'price' => 0.99, 'bonus' => 0],
            ['uc' => 325, 'price' => 4.99, 'bonus' => 25],
            ['uc' => 660, 'price' => 9.99, 'bonus' => 60],
            ['uc' => 1800, 'price' => 24.99, 'bonus' => 200],
            ['uc' => 3850, 'price' => 49.99, 'bonus' => 450],
            ['uc' => 8100, 'price' => 99.99, 'bonus' => 1000],
        ];

        return view('pubg-uc.index', compact('packages'));
    }

    public function charge(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|string',
            'uc_amount' => 'required|integer|in:60,325,660,1800,3850,8100',
        ]);

        $prices = [
            60 => 0.99,
            325 => 4.99,
            660 => 9.99,
            1800 => 24.99,
            3850 => 49.99,
            8100 => 99.99,
        ];

        $price = $prices[$validated['uc_amount']];

        // Create order
        $order = PubgUcOrder::create([
            'user_id' => Auth::id(),
            'player_id' => $validated['player_id'],
            'uc_amount' => $validated['uc_amount'],
            'price' => $price,
            'status' => 'pending',
        ]);

        // Process payment (mock for now)
        $result = $this->pubgService->chargeUc(
            $validated['player_id'],
            $validated['uc_amount']
        );

        if ($result['success']) {
            $order->status = 'completed';
            $order->transaction_id = $result['transaction_id'];
            $order->save();

            return redirect()->route('pubg-uc.success', $order)
                ->with('success', 'UC charged successfully!');
        } else {
            $order->status = 'failed';
            $order->save();

            return redirect()->back()
                ->with('error', 'Failed to charge UC. Please try again.');
        }
    }

    public function orders()
    {
        $orders = PubgUcOrder::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pubg-uc.orders', compact('orders'));
    }

    public function success(PubgUcOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pubg-uc.success', compact('order'));
    }
}
