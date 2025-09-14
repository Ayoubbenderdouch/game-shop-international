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
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $likeCardService;

    public function __construct(LikeCardApiService $likeCardService)
    {
        $this->likeCardService = $likeCardService;
    }

    public function index()
    {
        // Get period from request (default 30 days)
        $period = request('period', 30);

        // Cache dashboard stats for 5 minutes to improve performance
        $stats = Cache::remember('dashboard_stats', 300, function () {
            return [
                'todayRevenue' => Order::where('payment_status', 'paid')
                    ->whereDate('created_at', Carbon::today())
                    ->sum('total_amount'),
                'todayOrders' => Order::whereDate('created_at', Carbon::today())->count(),
                'activeProducts' => Product::where('is_active', true)->count(),
                'totalProducts' => Product::count(),
                'totalUsers' => User::where('role', 'customer')->count(),
                'newUsersToday' => User::where('role', 'customer')
                    ->whereDate('created_at', Carbon::today())
                    ->count(),
                'pendingOrders' => Order::where('status', 'pending')->count(),
                'lowStockProducts' => Product::where('stock_quantity', '>', 0)
                    ->where('stock_quantity', '<', 10)
                    ->count(),
            ];
        });

        // Calculate growth percentages (cached for 1 hour)
        $growthStats = Cache::remember('dashboard_growth_' . $period, 3600, function () {
            $yesterday = Carbon::yesterday();
            $yesterdayRevenue = Order::where('payment_status', 'paid')
                ->whereDate('created_at', $yesterday)
                ->sum('total_amount');

            $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();

            $todayRevenue = Order::where('payment_status', 'paid')
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount');

            $todayOrders = Order::whereDate('created_at', Carbon::today())->count();

            return [
                'revenueGrowth' => $yesterdayRevenue > 0
                    ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
                    : 0,
                'ordersGrowth' => $yesterdayOrders > 0
                    ? round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1)
                    : 0,
            ];
        });

        // Get chart data based on period (limit to prevent infinite growth)
        $chartData = $this->getChartData($period);

        // Get category data (cached for 30 minutes) - Using correct column names
        $categoryData = Cache::remember('dashboard_categories_' . $period, 1800, function () use ($period) {
            $startDate = Carbon::now()->subDays($period);

            // Fixed query using total_price instead of price
            return DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.payment_status', 'paid')
                ->where('orders.created_at', '>=', $startDate)
                ->select(
                    'categories.name',
                    DB::raw('SUM(order_items.total_price) as revenue')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('revenue')
                ->limit(4)
                ->get();
        });

        // Get recent activities (limit to last 5)
        $recentActivities = Cache::remember('recent_activities', 300, function () {
            // This would be from an activity log table if you have one
            // For now, returning empty array
            return collect([]);
        });

        // Try to get API balance (with timeout to prevent slow loading) - FIXED: Extract balance value from array
        $apiBalanceData = Cache::remember('api_balance_data', 600, function () {
            try {
                $balanceResponse = $this->likeCardService->getBalance();

                // Check if the response is successful and has balance
                if (is_array($balanceResponse) && isset($balanceResponse['success']) && $balanceResponse['success']) {
                    return [
                        'balance' => $balanceResponse['balance'] ?? 0,
                        'currency' => $balanceResponse['currency'] ?? 'USD',
                        'success' => true
                    ];
                }

                return null;
            } catch (\Exception $e) {
                return null;
            }
        });

        // Extract the balance value for the view
        $apiBalance = null;
        $apiCurrency = 'USD';

        if ($apiBalanceData && isset($apiBalanceData['success']) && $apiBalanceData['success']) {
            $apiBalance = $apiBalanceData['balance'];
            $apiCurrency = $apiBalanceData['currency'];
        }

        // Get last sync time
        $lastSync = Cache::get('last_api_sync_time', 'Never');

        // Get top selling products for the period
        $topProducts = Cache::remember('dashboard_top_products_' . $period, 1800, function () use ($period) {
            $startDate = Carbon::now()->subDays($period);

            return DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.payment_status', 'paid')
                ->where('orders.created_at', '>=', $startDate)
                ->select(
                    'products.name',
                    'products.id',
                    DB::raw('COUNT(DISTINCT orders.id) as sales_count'),
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.total_price) as revenue')
                )
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('revenue')
                ->limit(5)
                ->get();
        });

        return view('admin.dashboard.dashboard', array_merge($stats, $growthStats, [
            'revenueLabels' => $chartData['labels'],
            'revenueData' => $chartData['data'],
            'categoryLabels' => $categoryData->pluck('name')->toArray(),
            'categoryData' => $categoryData->pluck('revenue')->map(function ($value) {
                return round($value, 2);
            })->toArray(),
            'topCategories' => $categoryData->take(3),
            'topProducts' => $topProducts,
            'recentActivities' => $recentActivities,
            'apiBalance' => $apiBalance,  // Now this is a numeric value or null
            'apiCurrency' => $apiCurrency,  // Currency for display
            'lastSync' => $lastSync,
            'period' => $period,
        ]));
    }

    /**
     * Get chart data for the specified period
     * Limits data points to prevent infinite growth
     */
    private function getChartData($period)
    {
        // Limit period to maximum 90 days for performance
        $period = min($period, 90);

        // Determine appropriate grouping based on period
        if ($period <= 7) {
            // Daily data for last 7 days
            $data = $this->getDailyChartData($period);
        } elseif ($period <= 30) {
            // Daily data for last 30 days (but sample every few days if needed)
            $data = $this->getDailyChartData($period, ceil($period / 15));
        } else {
            // Weekly data for periods > 30 days
            $data = $this->getWeeklyChartData($period);
        }

        return $data;
    }

    /**
     * Get daily chart data
     */
    private function getDailyChartData($days, $step = 1)
    {
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days);

        $salesData = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate->endOfDay()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();

        $labels = [];
        $data = [];

        // Generate labels and fill data (with sampling if step > 1)
        for ($i = $days; $i >= 0; $i -= $step) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $data[] = isset($salesData[$dateStr]) ? round($salesData[$dateStr], 2) : 0;
        }

        // Limit to max 20 data points for chart readability
        if (count($labels) > 20) {
            $labels = array_slice($labels, -20);
            $data = array_slice($data, -20);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get weekly chart data
     */
    private function getWeeklyChartData($days)
    {
        $weeks = ceil($days / 7);
        $endDate = Carbon::today()->endOfWeek();
        $startDate = Carbon::today()->subWeeks($weeks)->startOfWeek();

        $salesData = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('YEARWEEK(created_at) as week'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        $labels = [];
        $data = [];

        foreach ($salesData as $sale) {
            // Convert week to readable format
            $year = substr($sale->week, 0, 4);
            $week = substr($sale->week, 4, 2);
            $date = Carbon::now()->setISODate($year, $week);

            $labels[] = 'Week of ' . $date->format('M d');
            $data[] = round($sale->revenue, 2);
        }

        // Limit to max 12 weeks
        if (count($labels) > 12) {
            $labels = array_slice($labels, -12);
            $data = array_slice($data, -12);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}
