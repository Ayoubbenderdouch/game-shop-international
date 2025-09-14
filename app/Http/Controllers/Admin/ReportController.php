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

        // Product statistics
        $topProducts = Product::select('products.*')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.payment_status', 'paid')
            ->groupBy('products.id')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // Category performance
        $categoryPerformance = Category::select('categories.*')
            ->selectRaw('COUNT(DISTINCT orders.id) as order_count')
            ->selectRaw('SUM(order_items.total_price) as revenue')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.payment_status', 'paid')
            ->groupBy('categories.id')
            ->orderBy('revenue', 'desc')
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

        $productPerformance = Product::select('products.*')
            ->selectRaw('COUNT(DISTINCT order_items.order_id) as order_count')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->selectRaw('AVG(order_items.price) as avg_selling_price')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$startDate, $endDate])
                     ->where('orders.payment_status', 'paid');
            })
            ->groupBy('products.id')
            ->orderBy('total_revenue', 'desc')
            ->paginate(30);

        // Low stock products
        $lowStockProducts = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        // Best performing categories
        $categoryPerformance = Category::select('categories.*')
            ->selectRaw('COUNT(DISTINCT products.id) as product_count')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->selectRaw('SUM(order_items.total_price) as revenue')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function ($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$startDate, $endDate])
                     ->where('orders.payment_status', 'paid');
            })
            ->groupBy('categories.id')
            ->orderBy('revenue', 'desc')
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

        // Top customers
        $topCustomers = User::select('users.*')
            ->selectRaw('COUNT(DISTINCT orders.id) as order_count')
            ->selectRaw('SUM(orders.total_amount) as total_spent')
            ->selectRaw('AVG(orders.total_amount) as avg_order_value')
            ->selectRaw('MAX(orders.created_at) as last_order_date')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.payment_status', 'paid')
            ->groupBy('users.id')
            ->orderBy('total_spent', 'desc')
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
        $retentionRate = $totalCustomers > 0 ? ($activeCustomers / $totalCustomers) * 100 : 0;

        return view('admin.reports.customers', compact(
            'topCustomers',
            'customerAcquisition',
            'totalCustomers',
            'activeCustomers',
            'retentionRate',
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

        // Implementation for export functionality would go here
        // This could generate CSV, Excel, or PDF reports

        return back()->with('info', 'Export functionality will be implemented soon');
    }
}
