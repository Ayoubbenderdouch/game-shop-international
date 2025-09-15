<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        // Get date range from request or default to last 30 days
        $startDate = request('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        // Revenue statistics
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $previousPeriodRevenue = Order::whereBetween('created_at', [
                Carbon::parse($startDate)->subDays(30),
                Carbon::parse($startDate)->subDay()
            ])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $revenueGrowth = $previousPeriodRevenue > 0
            ? (($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100
            : 0;

        // Order statistics
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Customer statistics
        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $returningCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('user', function ($q) use ($startDate) {
                $q->where('created_at', '<', $startDate);
            })
            ->distinct('user_id')
            ->count();

        // Product statistics - ALTERNATIVE SOLUTION using subquery
        $topProducts = Product::withCount(['orderItems as total_sold' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('SUM(quantity)'))
                    ->whereHas('order', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid');
                    });
            }])
            ->withCount(['orderItems as total_revenue' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('SUM(total_price)'))
                    ->whereHas('order', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid');
                    });
            }])
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Category performance - ALTERNATIVE SOLUTION using subquery
        $categoryPerformance = Category::withCount(['products as order_count' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COUNT(DISTINCT orders.id)'))
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.payment_status', 'paid');
            }])
            ->withCount(['products as revenue' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('SUM(order_items.total_price)'))
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.payment_status', 'paid');
            }])
            ->orderByDesc('revenue')
            ->get();

        // Daily revenue chart data
        $dailyRevenue = Order::selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('COUNT(*) as orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.index', compact(
            'totalRevenue',
            'revenueGrowth',
            'totalOrders',
            'completedOrders',
            'averageOrderValue',
            'newCustomers',
            'returningCustomers',
            'topProducts',
            'categoryPerformance',
            'dailyRevenue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display sales report
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $salesData = Order::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_orders')
            ->selectRaw('SUM(total_amount) as revenue')
            ->selectRaw('AVG(total_amount) as avg_order_value')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(30);

        // Monthly summary
        $monthlySummary = Order::selectRaw('YEAR(created_at) as year')
            ->selectRaw('MONTH(created_at) as month')
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw('SUM(total_amount) as revenue')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('admin.reports.sales', compact(
            'salesData',
            'monthlySummary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display products report
     */
    public function products(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Product performance using subqueries
        $productPerformance = Product::withCount(['orderItems as order_count' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COUNT(DISTINCT order_id)'))
                    ->whereHas('order', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid');
                    });
            }])
            ->withCount(['orderItems as total_sold' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                    ->whereHas('order', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid');
                    });
            }])
            ->withCount(['orderItems as total_revenue' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COALESCE(SUM(total_price), 0)'))
                    ->whereHas('order', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid');
                    });
            }])
            ->withCount(['orderItems as avg_selling_price' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COALESCE(AVG(price), 0)'))
                    ->whereHas('order', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate])
                          ->where('payment_status', 'paid');
                    });
            }])
            ->orderByDesc('total_revenue')
            ->paginate(30);

        // Low stock products
        $lowStockProducts = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        // Best performing categories using subqueries
        $categoryPerformance = Category::withCount(['products as product_count'])
            ->withCount(['products as total_sold' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COALESCE(SUM(order_items.quantity), 0)'))
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.payment_status', 'paid');
            }])
            ->withCount(['products as revenue' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COALESCE(SUM(order_items.total_price), 0)'))
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.payment_status', 'paid');
            }])
            ->orderByDesc('revenue')
            ->get();

        return view('admin.reports.products', compact(
            'productPerformance',
            'lowStockProducts',
            'categoryPerformance',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display customers report
     */
    public function customers(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Top customers using subqueries
        $topCustomers = User::withCount(['orders as order_count' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('payment_status', 'paid');
            }])
            ->withCount(['orders as total_spent' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COALESCE(SUM(total_amount), 0)'))
                      ->whereBetween('created_at', [$startDate, $endDate])
                      ->where('payment_status', 'paid');
            }])
            ->withCount(['orders as avg_order_value' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('COALESCE(AVG(total_amount), 0)'))
                      ->whereBetween('created_at', [$startDate, $endDate])
                      ->where('payment_status', 'paid');
            }])
            ->withCount(['orders as last_order_date' => function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw('MAX(created_at)'))
                      ->whereBetween('created_at', [$startDate, $endDate])
                      ->where('payment_status', 'paid');
            }])
            ->having('order_count', '>', 0)
            ->orderByDesc('total_spent')
            ->paginate(30);

        // Customer acquisition
        $customerAcquisition = User::selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as new_customers')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Customer retention
        $totalCustomers = User::count();
        $activeCustomers = User::whereHas('orders', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        })->count();
        $retentionRate = $totalCustomers > 0 ?
            ($activeCustomers / $totalCustomers) * 100 : 0;

        // Customer segments
        $customerSegments = [
            'new' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'returning' => User::has('orders', '>=', 2)->count(),
            'vip' => User::whereHas('orders', function ($q) {
                $q->havingRaw('SUM(total_amount) > ?', [1000]);
            })->count(),
            'inactive' => User::whereDoesntHave('orders', function ($q) {
                $q->where('created_at', '>=', now()->subDays(90));
            })->count()
        ];

        return view('admin.reports.customers', compact(
            'topCustomers',
            'customerAcquisition',
            'retentionRate',
            'customerSegments',
            'totalCustomers',
            'activeCustomers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export report data
     */
    public function export(Request $request)
    {
        $type = $request->input('type', 'sales');
        $format = $request->input('format', 'csv');
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Generate export based on type
        // This is a placeholder for export functionality
        return back()->with('info', 'Export functionality coming soon');
    }
}
