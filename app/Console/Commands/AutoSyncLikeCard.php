<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LikeCardApiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AutoSyncLikeCard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'likecard:auto-sync
                            {--test : Run once for testing}
                            {--verbose : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically sync categories (every 5 hours) and products (every 30 minutes) from LikeCard API';

    protected $likeCardService;

    /**
     * Create a new command instance.
     */
    public function __construct(LikeCardApiService $likeCardService)
    {
        parent::__construct();
        $this->likeCardService = $likeCardService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('test')) {
            $this->runSync();
            return Command::SUCCESS;
        }

        $this->info('Starting LikeCard auto-sync service...');
        $this->info('Categories will sync every 5 hours');
        $this->info('Products will sync every 30 minutes');
        $this->info('Press Ctrl+C to stop');

        while (true) {
            $this->runSync();

            // Sleep for 30 minutes
            $this->info('Next sync in 30 minutes...');
            sleep(1800); // 30 minutes
        }
    }

    /**
     * Run the synchronization
     */
    protected function runSync()
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $this->info("[{$timestamp}] Running synchronization...");

        try {
            // Check if we need to sync categories (every 5 hours)
            $lastCategorySync = Cache::get('likecard_last_category_sync');
            $shouldSyncCategories = !$lastCategorySync ||
                now()->diffInHours($lastCategorySync) >= 5;

            if ($shouldSyncCategories) {
                $this->syncCategories();
                Cache::put('likecard_last_category_sync', now(), now()->addHours(6));
            }

            // Always sync products (every 30 minutes as per schedule)
            $this->syncProducts();

            // Update sync status
            Cache::put('likecard_last_sync', [
                'timestamp' => now(),
                'status' => 'success',
                'next_sync' => now()->addMinutes(30)
            ], now()->addHours(1));

            $this->info("✓ Synchronization completed successfully");

        } catch (\Exception $e) {
            $this->error("✗ Synchronization failed: " . $e->getMessage());
            Log::error('Auto-sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update sync status with error
            Cache::put('likecard_last_sync', [
                'timestamp' => now(),
                'status' => 'error',
                'error' => $e->getMessage(),
                'next_sync' => now()->addMinutes(30)
            ], now()->addHours(1));
        }
    }

    /**
     * Sync categories
     */
    protected function syncCategories()
    {
        $this->info("→ Syncing categories...");

        $startTime = microtime(true);
        $result = $this->likeCardService->syncCategories();
        $duration = round(microtime(true) - $startTime, 2);

        if ($result) {
            $count = \App\Models\Category::count();
            $this->info("  ✓ {$count} categories synced in {$duration}s");

            if ($this->option('verbose')) {
                $categories = \App\Models\Category::whereNull('parent_id')->get();
                foreach ($categories as $category) {
                    $childCount = $category->children()->count();
                    $this->line("    - {$category->name} ({$childCount} subcategories)");
                }
            }
        } else {
            $this->error("  ✗ Failed to sync categories");
        }
    }

    /**
     * Sync products
     */
    protected function syncProducts()
    {
        $this->info("→ Syncing products...");

        // Get categories that need product sync
        $categories = $this->getCategoriesToSync();

        if ($categories->isEmpty()) {
            $this->info("  ℹ No categories need syncing");
            return;
        }

        $total = $categories->count();
        $synced = 0;
        $failed = 0;

        foreach ($categories as $category) {
            try {
                $startTime = microtime(true);
                $result = $this->likeCardService->syncProducts($category->api_id);
                $duration = round(microtime(true) - $startTime, 2);

                if ($result) {
                    $synced++;
                    $productCount = $category->products()->count();

                    if ($this->option('verbose')) {
                        $this->info("  ✓ {$category->name}: {$productCount} products ({$duration}s)");
                    }

                    // Mark category as synced
                    $category->update(['last_synced_at' => now()]);
                } else {
                    $failed++;
                    if ($this->option('verbose')) {
                        $this->error("  ✗ {$category->name}: Failed");
                    }
                }

                // Small delay to avoid rate limiting
                usleep(250000); // 0.25 seconds

            } catch (\Exception $e) {
                $failed++;
                Log::error("Failed to sync products for category {$category->name}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("  ✓ Products synced: {$synced}/{$total} categories");

        if ($failed > 0) {
            $this->warn("  ⚠ Failed: {$failed} categories");
        }

        // Show product statistics
        if ($this->option('verbose')) {
            $stats = \App\Models\Product::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available
            ')->first();

            $this->info("  📊 Total products: {$stats->total} (Available: {$stats->available})");
        }
    }

    /**
     * Get categories that need product sync
     */
    protected function getCategoriesToSync()
    {
        // Prioritize categories that haven't been synced recently
        return \App\Models\Category::where('is_active', true)
            ->whereNotNull('api_id')
            ->where(function ($query) {
                $query->whereNull('last_synced_at')
                    ->orWhere('last_synced_at', '<', now()->subMinutes(30));
            })
            ->orderBy('last_synced_at', 'asc')
            ->limit(20) // Limit to avoid overwhelming the API
            ->get();
    }
}
