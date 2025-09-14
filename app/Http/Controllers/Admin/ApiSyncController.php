<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiSyncController extends Controller
{
    protected $apiService;

    public function __construct()
    {
        // Initialize API service if it exists
        // $this->apiService = new ApiService();
    }

    /**
     * Display the API sync dashboard
     */
    public function index()
    {
        // Get sync statistics
        $totalCategories = Category::count();
        $totalProducts = Product::count();
        $categoriesSynced = Category::whereNotNull('last_synced_at')->count();
        $productsSynced = Product::whereNotNull('updated_at')
            ->where('updated_at', '>=', now()->subDay())
            ->count();

        // Get last sync information
        $lastSync = DB::table('sync_logs')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastSyncTime = $lastSync ? $lastSync->created_at : null;

        // Get API balance (mock data for now)
        $apiBalance = session('api_balance', 0.00);

        // Recent sync logs
        $syncLogs = DB::table('sync_logs')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Sync statistics
        $newProducts = Product::where('created_at', '>=', now()->subDay())->count();
        $updatedProducts = Product::where('updated_at', '>=', now()->subDay())
            ->where('created_at', '<', now()->subDay())
            ->count();
        $failedSyncs = 0; // Would come from sync_logs table

        return view('admin.api-sync.index', compact(
            'totalCategories',
            'totalProducts',
            'categoriesSynced',
            'productsSynced',
            'lastSync',
            'lastSyncTime',
            'apiBalance',
            'syncLogs',
            'newProducts',
            'updatedProducts',
            'failedSyncs'
        ));
    }

    /**
     * Test API connection
     */
    public function testConnection(Request $request)
    {
        try {
            // Mock API connection test
            // In real implementation, this would test the actual API
            $connected = true;

            if ($connected) {
                return response()->json([
                    'success' => true,
                    'message' => 'API connection successful'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to API'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check API balance
     */
    public function balance(Request $request)
    {
        try {
            // Mock API balance check
            // In real implementation, this would fetch from actual API
            $balance = rand(100, 500) / 10; // Random balance between 10.0 and 50.0

            session(['api_balance' => $balance]);

            return response()->json([
                'success' => true,
                'balance' => $balance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch balance: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync categories from API
     */
    public function syncCategories(Request $request)
    {
        try {
            DB::beginTransaction();

            // Mock category sync
            // In real implementation, this would fetch from actual API
            $apiCategories = [
                ['id' => 'cat_1', 'name' => 'Gaming', 'description' => 'Gaming products'],
                ['id' => 'cat_2', 'name' => 'Software', 'description' => 'Software licenses'],
                ['id' => 'cat_3', 'name' => 'Entertainment', 'description' => 'Entertainment products'],
            ];

            $synced = 0;
            foreach ($apiCategories as $apiCategory) {
                Category::updateOrCreate(
                    ['api_id' => $apiCategory['id']],
                    [
                        'name' => $apiCategory['name'],
                        'slug' => Str::slug($apiCategory['name']),
                        'description' => $apiCategory['description'] ?? null,
                        'is_active' => true,
                        'last_synced_at' => now(),
                    ]
                );
                $synced++;
            }

            // Log the sync
            DB::table('sync_logs')->insert([
                'type' => 'categories',
                'status' => 'success',
                'records_synced' => $synced,
                'message' => "Successfully synced {$synced} categories",
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$synced} categories"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            DB::table('sync_logs')->insert([
                'type' => 'categories',
                'status' => 'failed',
                'records_synced' => 0,
                'message' => $e->getMessage(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Sync products from API
     */
    public function syncProducts(Request $request)
    {
        try {
            DB::beginTransaction();

            // Mock product sync
            // In real implementation, this would fetch from actual API
            $categories = Category::all();
            $synced = 0;
            $failed = 0;

            // Generate some mock products
            for ($i = 1; $i <= 10; $i++) {
                try {
                    $category = $categories->random();

                    Product::updateOrCreate(
                        ['api_id' => 'prod_' . uniqid()],
                        [
                            'category_id' => $category->id,
                            'name' => 'Product ' . $i,
                            'slug' => Str::slug('Product ' . $i),
                            'description' => 'Description for product ' . $i,
                            'cost_price' => rand(10, 100),
                            'selling_price' => rand(15, 150),
                            'currency' => 'USD',
                            'is_available' => true,
                            'is_active' => true,
                            'stock_quantity' => rand(0, 100),
                        ]
                    );
                    $synced++;
                } catch (\Exception $e) {
                    $failed++;
                    Log::error('Failed to sync product: ' . $e->getMessage());
                }
            }

            // Log the sync
            DB::table('sync_logs')->insert([
                'type' => 'products',
                'status' => $failed > 0 ? 'partial' : 'success',
                'records_synced' => $synced,
                'message' => "Synced {$synced} products" . ($failed > 0 ? ", {$failed} failed" : ""),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$synced} products" . ($failed > 0 ? " ({$failed} failed)" : "")
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error
            DB::table('sync_logs')->insert([
                'type' => 'products',
                'status' => 'failed',
                'records_synced' => 0,
                'message' => $e->getMessage(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Perform full sync (categories and products)
     */
    public function fullSync(Request $request)
    {
        try {
            // First sync categories
            $categoriesResult = $this->syncCategories($request);
            $categoriesData = json_decode($categoriesResult->getContent(), true);

            if (!$categoriesData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category sync failed: ' . $categoriesData['message']
                ]);
            }

            // Then sync products
            $productsResult = $this->syncProducts($request);
            $productsData = json_decode($productsResult->getContent(), true);

            if (!$productsData['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product sync failed: ' . $productsData['message']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Full sync completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Full sync failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save API sync settings
     */
    public function saveSettings(Request $request)
    {
        try {
            $settings = $request->validate([
                'auto_sync' => 'boolean',
                'update_prices' => 'boolean',
                'update_images' => 'boolean',
                'remove_deleted' => 'boolean',
            ]);

            // Save settings to database or config
            foreach ($settings as $key => $value) {
                DB::table('settings')->updateOrInsert(
                    ['key' => 'api_sync_' . $key],
                    ['value' => $value ? '1' : '0', 'updated_at' => now()]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ]);
        }
    }
}
