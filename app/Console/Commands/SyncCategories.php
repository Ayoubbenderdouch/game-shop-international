<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LikeCardApiService;

class SyncCategories extends Command
{
    protected $signature = 'app:sync-categories';
    protected $description = 'Sync categories from LikeCard API';

    protected $likeCardService;

    public function __construct(LikeCardApiService $likeCardService)
    {
        parent::__construct();
        $this->likeCardService = $likeCardService;
    }

    public function handle()
    {
        $this->info('Starting category synchronization...');

        try {
            $result = $this->likeCardService->syncCategories();

            if ($result) {
                $this->info('Categories synchronized successfully!');
                return Command::SUCCESS;
            } else {
                $this->error('Failed to sync categories. Check logs for details.');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
