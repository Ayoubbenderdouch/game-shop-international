<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Services\LikeCardApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $likeCardService;

    public function __construct(LikeCardApiService $likeCardService)
    {
        $this->likeCardService = $likeCardService;
    }

    public function index()
    {
        // Get statistics
        $stats = [
            'total_sales' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_products' => Product::where('is_active', true)->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'today_sales' => Order::where('payment_status', 'paid')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount'),
            'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
            'new_customers' => User::where('role', 'customer')
                ->whereDate('created_at', Carbon::today())
                ->count(),
        ];

        // Get API balance
        $apiBalance = $this->likeCardService->getBalance();

        // Recent orders
        $recentOrders = Order::with(['user', 'orderItems'])
            ->latest()
            ->limit(10)
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        // Sales chart data (last 30 days)
        $salesData = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total_amount) as sales')
            ]);

        // Top selling products
        $topProducts = Product::withCount(['orderItems as sold_count' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('sold_count', 'desc')
            ->limit(5)
            ->get();

        // Top categories
        $topCategories = Category::withCount(['products as sales_count' => function ($query) {
                $query->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->select(DB::raw('COUNT(order_items.id)'));
            }])
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.dashboard', compact(
            'stats',
            'apiBalance',
            'recentOrders',
            'lowStockProducts',
            'salesData',
            'topProducts',
            'topCategories'
        ));
    }
}
