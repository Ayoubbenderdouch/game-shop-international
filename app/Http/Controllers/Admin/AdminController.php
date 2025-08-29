<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\PubgUcOrder;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount') +
                               PubgUcOrder::where('status', 'completed')->sum('price'),
            'total_orders' => Order::count() + PubgUcOrder::count(),
            'total_products' => Product::count(),
            'total_users' => User::count(),
            'recent_orders' => Order::with('user')->latest()->limit(5)->get(),
            'low_stock_products' => Product::whereHas('productCodes', function($q) {
                $q->where('is_used', false);
            }, '<', 10)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
