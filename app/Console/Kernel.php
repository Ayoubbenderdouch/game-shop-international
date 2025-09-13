<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SyncCategories::class,
        Commands\SyncProducts::class,
        Commands\AutoSyncLikeCard::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sync categories every 5 hours as recommended by API
        $schedule->command('likecard:sync-categories')
            ->everyFiveHours()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/likecard-categories.log'));

        // Sync products every 30 minutes as recommended by API
        $schedule->command('likecard:sync-products --all')
            ->everyThirtyMinutes()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/likecard-products.log'));

        // Alternative: Use the auto-sync command instead of individual commands
        // $schedule->command('likecard:auto-sync')
        //     ->everyMinute()
        //     ->withoutOverlapping()
        //     ->runInBackground()
        //     ->appendOutputTo(storage_path('logs/likecard-sync.log'));

        // Daily cleanup of old logs
        $schedule->call(function () {
            $logFiles = [
                storage_path('logs/likecard-categories.log'),
                storage_path('logs/likecard-products.log'),
            ];

            foreach ($logFiles as $logFile) {
                if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) { // 10MB
                    $backupFile = $logFile . '.' . date('Y-m-d') . '.bak';
                    rename($logFile, $backupFile);
                    touch($logFile);
                }
            }
        })->daily();

        // Health check - ensure API is responsive
        $schedule->call(function () {
            $service = app(\App\Services\LikeCardApiService::class);

            if (!$service->isApiHealthy()) {
                Log::error('LikeCard API health check failed');
                // You can add notification logic here
            }
        })->everyFifteenMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
