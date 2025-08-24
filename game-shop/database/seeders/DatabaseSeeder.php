<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gameshop.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Create categories
        $categories = [
            ['name' => 'Game Cards', 'slug' => 'game-cards'],
            ['name' => 'Gift Cards', 'slug' => 'gift-cards'],
            ['name' => 'Subscriptions', 'slug' => 'subscriptions'],
            ['name' => 'Game Top-Ups', 'slug' => 'game-topups'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create sample products
        $gameCardsCategory = Category::where('slug', 'game-cards')->first();

        $products = [
            [
                'title' => 'Steam Gift Card $20',
                'type' => 'game_card',
                'price' => 20.00,
                'description' => 'Add $20 to your Steam Wallet',
                'category_id' => $gameCardsCategory->id,
                'tags' => ['steam', 'gaming', 'pc'],
                'country_availability' => ['US', 'CA', 'UK', 'EU'],
            ],
            [
                'title' => 'PlayStation Store $25',
                'type' => 'game_card',
                'price' => 25.00,
                'description' => 'PlayStation Store gift card worth $25',
                'category_id' => $gameCardsCategory->id,
                'tags' => ['playstation', 'ps5', 'ps4'],
                'country_availability' => ['US', 'CA'],
            ],
            [
                'title' => 'Xbox Gift Card $50',
                'type' => 'game_card',
                'price' => 50.00,
                'description' => 'Xbox gift card for games and entertainment',
                'category_id' => $gameCardsCategory->id,
                'tags' => ['xbox', 'microsoft', 'gaming'],
                'country_availability' => ['US', 'CA', 'UK'],
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            // Create sample codes for each product
            for ($i = 1; $i <= 10; $i++) {
                ProductCode::create([
                    'product_id' => $product->id,
                    'code' => strtoupper(bin2hex(random_bytes(8))),
                ]);
            }
        }
    }
}
