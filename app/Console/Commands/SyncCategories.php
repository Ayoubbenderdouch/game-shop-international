<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LikeCardApiService;
use Illuminate\Support\Facades\Log;

class SyncCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'likecard:sync-categories {--force : Force refresh cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync categories from LikeCard API and cache them for 5 hours';

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
        $this->info('Starting LikeCard categories synchronization...');

        try {
            // Validate credentials first
            $this->likeCardService->validateCredentials();
            $this->info('✓ API credentials validated');

            // Check API health
            if (!$this->likeCardService->isApiHealthy()) {
                $this->error('✗ LikeCard API is not responding. Please check your credentials and network connection.');
                return Command::FAILURE;
            }
            $this->info('✓ API connection successful');

            // Get current balance
            $balance = $this->likeCardService->getBalance();
            if ($balance['success']) {
                $this->info("✓ Current balance: {$balance['currency']} {$balance['balance']}");
            }

            // Force refresh if flag is set
            if ($this->option('force')) {
                $this->likeCardService->clearCaches();
                $this->info('✓ Cache cleared');
            }

            // Sync categories
            $startTime = microtime(true);
            $result = $this->likeCardService->syncCategories();
            $duration = round(microtime(true) - $startTime, 2);

            if ($result) {
                $this->info("✓ Categories synchronized successfully in {$duration} seconds!");

                // Show statistics
                $stats = \App\Models\Category::selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN parent_id IS NULL THEN 1 ELSE 0 END) as parent_categories,
                    SUM(CASE WHEN parent_id IS NOT NULL THEN 1 ELSE 0 END) as subcategories
                ')->first();

                $this->table(
                    ['Metric', 'Count'],
                    [
                        ['Total Categories', $stats->total],
                        ['Parent Categories', $stats->parent_categories],
                        ['Subcategories', $stats->subcategories],
                    ]
                );

                // Show sample categories
                $this->info("\nSample categories:");
                $categories = \App\Models\Category::whereNull('parent_id')->limit(5)->get();
                foreach ($categories as $category) {
                    $childCount = $category->children()->count();
                    $this->line("  - {$category->name} ({$childCount} subcategories)");
                }

                return Command::SUCCESS;
            } else {
                $this->error('✗ Failed to sync categories. Check logs for details.');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            Log::error('Category sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
