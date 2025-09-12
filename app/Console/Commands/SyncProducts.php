<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LikeCardApiService;
use App\Models\Category;

class SyncProducts extends Command
{
    protected $signature = 'app:sync-products {--category= : Sync products for specific category}';
    protected $description = 'Sync products from LikeCard API';

    protected $likeCardService;

    public function __construct(LikeCardApiService $likeCardService)
    {
        parent::__construct();
        $this->likeCardService = $likeCardService;
    }

    public function handle()
    {
        $this->info('Starting product synchronization...');

        try {
            $categoryId = $this->option('category');

            if ($categoryId) {
                $category = Category::where('api_id', $categoryId)->first();
                if (!$category) {
                    $this->error('Category not found.');
                    return Command::FAILURE;
                }
                $this->info("Syncing products for category: {$category->name}");
            }

            $result = $this->likeCardService->syncProducts($categoryId);

            if ($result) {
                $this->info('Products synchronized successfully!');
                return Command::SUCCESS;
            } else {
                $this->error('Failed to sync products. Check logs for details.');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
