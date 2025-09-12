<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ManualHashCalculator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually calculate hash for email verification URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('MANUAL HASH CALCULATOR FOR SIGNATURES');
        $this->info('========================================');

        // Get your current APP_KEY
        $appKey = config('app.key');
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }

        $this->info("\n📋 Your Current Configuration:");
        $this->info('APP_KEY (first 20 chars): ' . substr(config('app.key'), 0, 20) . '...');
        $this->info('APP_URL: ' . config('app.url'));

        // Example URL from your error
        $this->info("\n📝 Enter your verification URL parameters:");

        $userId = $this->ask('User ID', '5');
        $hash = $this->ask('Hash', 'a86e4aba45f3d0ae6c59badb9d043c6b1d210b3d');
        $expires = $this->ask('Expires timestamp', '1757713900');
        $providedSignature = $this->ask('Signature from URL', '22d30d1597351cf0569d2860b7a95aa28e26e0408928d5e721ae155cc4ef3c7b');

        $this->info("\n🔧 Testing different URL formats:");
        $this->info(str_repeat('-', 50));

        // Test different URL combinations
        $urlFormats = [
            'http://127.0.0.1:8000',
            'http://localhost:8000',
            'https://127.0.0.1:8000',
            'https://localhost:8000',
            config('app.url'), // Your actual APP_URL
        ];

        $matchFound = false;

        foreach ($urlFormats as $baseUrl) {
            // Build the complete URL (without signature)
            $urlToSign = $baseUrl . '/verify-email/' . $userId . '/' . $hash . '?expires=' . $expires;

            // Calculate the signature
            $calculatedSignature = hash_hmac('sha256', $urlToSign, $appKey);

            // Check if it matches
            $matches = hash_equals($calculatedSignature, $providedSignature);

            $this->info("\n🔗 URL Format: $baseUrl");
            $this->info("   Full URL: $urlToSign");
            $this->info("   Calculated: $calculatedSignature");
            $this->info("   Provided:   $providedSignature");
            $this->info("   Match: " . ($matches ? '✅ YES!' : '❌ No'));

            if ($matches) {
                $matchFound = true;
                $this->info("\n🎉 FOUND THE MATCH!");
                $this->info("Your APP_URL should be: $baseUrl");
            }
        }

        if (!$matchFound) {
            $this->error("\n❌ No match found. Possible issues:");
            $this->error("1. APP_KEY might have changed since the email was sent");
            $this->error("2. The URL structure might be different");
            $this->error("3. There might be additional parameters");
        }

        // Show the raw calculation
        $this->info("\n📊 RAW CALCULATION EXAMPLE:");
        $this->info(str_repeat('-', 50));

        $exampleUrl = config('app.url') . '/verify-email/' . $userId . '/' . $hash . '?expires=' . $expires;
        $this->info("URL to sign: $exampleUrl");
        $this->info("APP_KEY: " . substr(config('app.key'), 0, 30) . '...');
        $this->info("\nPHP Code to calculate:");
        $this->line('$signature = hash_hmac(\'sha256\', \'' . $exampleUrl . '\', $appKey);');
        $this->info("\nResult: " . hash_hmac('sha256', $exampleUrl, $appKey));

        return 0;
    }
}
