<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\CurrencyService;

class InternationalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencyService = app(CurrencyService::class);
        
        $this->command->info('Initializing currencies and countries...');
        
        $currencyService->initializeDefaults();
        
        $this->command->info('✅ Currencies and countries initialized successfully!');
        
        // Optionally update exchange rates
        $this->command->info('Updating exchange rates...');
        
        if ($currencyService->updateExchangeRates()) {
            $this->command->info('✅ Exchange rates updated successfully!');
        } else {
            $this->command->warn('⚠️  Could not update exchange rates. Using default rates.');
        }
    }
}
