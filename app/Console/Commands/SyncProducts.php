<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LikeCardApiService;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class SyncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'likecard:sync-products
                            {--category= : Sync products for specific category API ID}
                            {--all : Sync products for all categories}
                            {--force : Force refresh cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products from LikeCard API (cached for 30 minutes)';

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
        $this->info('Starting LikeCard products synchronization...');

        try {
            // Validate credentials first
            $this->likeCardService->validateCredentials();
            $this->info('✓ API credentials validated');

            // Force refresh if flag is set
            if ($this->option('force')) {
                $this->likeCardService->clearCaches();
                $this->info('✓ Cache cleared');
            }

            $startTime = microtime(true);

            if ($this->option('all')) {
                // Sync all products for all categories
                $this->syncAllProducts();
            } elseif ($categoryId = $this->option('category')) {
                // Sync products for specific category
                $this->syncCategoryProducts($categoryId);
            } else {
                // Interactive mode - let user choose
                $this->interactiveSync();
            }

            $duration = round(microtime(true) - $startTime, 2);
            $this->info("✓ Synchronization completed in {$duration} seconds!");

            // Show statistics
            $this->showStatistics();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            Log::error('Product sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Sync all products for all categories
     */
    protected function syncAllProducts()
    {
        $this->info('Syncing products for all categories...');

        $categories = Category::where('is_active', true)->get();
        $total = $categories->count();
        $current = 0;

        $this->output->progressStart($total);

        foreach ($categories as $category) {
            if ($category->api_id) {
                $this->likeCardService->syncProducts($category->api_id);
                $current++;
                $this->output->progressAdvance();

                // Small delay to avoid rate limiting
                usleep(500000); // 0.5 seconds
            }
        }

        $this->output->progressFinish();
        $this->info("✓ Synced products for {$current} categories");
    }

    /**
     * Sync products for a specific category
     */
    protected function syncCategoryProducts($categoryApiId)
    {
        $category = Category::where('api_id', $categoryApiId)->first();

        if (!$category) {
            $this->error("Category with API ID {$categoryApiId} not found.");
            $this->info("Tip: Run 'php artisan likecard:sync-categories' first to sync categories.");
            return;
        }

        $this->info("Syncing products for category: {$category->name}");

        $result = $this->likeCardService->syncProducts($categoryApiId);

        if ($result) {
            $productCount = $category->products()->count();
            $this->info("✓ Synced {$productCount} products for {$category->name}");
        } else {
            $this->error("✗ Failed to sync products for {$category->name}");
        }
    }

    /**
     * Interactive sync mode
     */
    protected function interactiveSync()
    {
        $choice = $this->choice(
            'What would you like to sync?',
            [
                'all' => 'All products for all categories',
                'category' => 'Products for a specific category',
                'popular' => 'Popular categories only (iTunes, Gaming, etc.)',
                'exit' => 'Exit'
            ],
            'popular'
        );

        switch ($choice) {
            case 'all':
                $this->syncAllProducts();
                break;

            case 'category':
                $this->selectCategoryToSync();
                break;

            case 'popular':
                $this->syncPopularCategories();
                break;

            case 'exit':
                $this->info('Exiting...');
                break;
        }
    }

    /**
     * Let user select a category to sync
     */
    protected function selectCategoryToSync()
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($categories->isEmpty()) {
            $this->error('No categories found. Run "php artisan likecard:sync-categories" first.');
            return;
        }

        $choices = $categories->mapWithKeys(function ($cat) {
            $childCount = $cat->children()->count();
            return [$cat->api_id => "{$cat->name} ({$childCount} subcategories)"];
        })->toArray();

        $selectedId = $this->choice('Select a category to sync products:', $choices);

        $this->syncCategoryProducts($selectedId);

        // Ask if they want to sync subcategories too
        if ($this->confirm('Do you want to sync products for subcategories as well?')) {
            $category = Category::where('api_id', $selectedId)->first();
            $subcategories = $category->children;

            foreach ($subcategories as $subcat) {
                if ($subcat->api_id) {
                    $this->info("Syncing: {$subcat->name}");
                    $this->likeCardService->syncProducts($subcat->api_id);
                    usleep(500000); // 0.5 seconds delay
                }
            }
        }
    }

    /**
     * Sync popular categories
     */
    protected function syncPopularCategories()
    {
        $this->info('Syncing products for popular categories...');

        $popularNames = [
            'iTunes', 'Google Play', 'PlayStation', 'Xbox', 'Steam',
            'PUBG', 'Free Fire', 'Fortnite', 'Netflix', 'Spotify'
        ];

        $categories = Category::whereIn('name', $popularNames)
            ->orWhere('name', 'LIKE', '%iTunes%')
            ->orWhere('name', 'LIKE', '%PlayStation%')
            ->orWhere('name', 'LIKE', '%Xbox%')
            ->orWhere('name', 'LIKE', '%PUBG%')
            ->get();

        $this->output->progressStart($categories->count());

        foreach ($categories as $category) {
            if ($category->api_id) {
                $this->likeCardService->syncProducts($category->api_id);
                $this->output->progressAdvance();
                usleep(500000); // 0.5 seconds delay
            }
        }

        $this->output->progressFinish();
        $this->info('✓ Popular categories synced');
    }

    /**
     * Show sync statistics
     */
    protected function showStatistics()
    {
        $stats = \App\Models\Product::selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available,
            SUM(CASE WHEN is_available = 0 THEN 1 ELSE 0 END) as unavailable,
            COUNT(DISTINCT category_id) as categories_with_products,
            AVG(selling_price) as avg_price,
            MIN(selling_price) as min_price,
            MAX(selling_price) as max_price
        ')->first();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Products', number_format($stats->total)],
                ['Available Products', number_format($stats->available)],
                ['Unavailable Products', number_format($stats->unavailable)],
                ['Categories with Products', number_format($stats->categories_with_products)],
                ['Average Price', '$' . number_format($stats->avg_price, 2)],
                ['Min Price', '$' . number_format($stats->min_price, 2)],
                ['Max Price', '$' . number_format($stats->max_price, 2)],
            ]
        );

        // Show top categories by product count
        $this->info("\nTop categories by product count:");
        $topCategories = \App\Models\Category::withCount('products')
            ->having('products_count', '>', 0)
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();

        foreach ($topCategories as $category) {
            $this->line("  - {$category->name}: {$category->products_count} products");
        }
    }
}
