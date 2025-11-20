<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductGroupingService
{
    /**
     * Get all base product groups (e.g., "Apple Gift Card", "Google Play")
     */
    public function getProductGroups()
    {
        return Cache::remember('product_groups', 3600, function () {
            $groups = [];

            // Get all categories
            $categories = Category::where('is_active', true)
                ->whereNotNull('parent_id') // Only child categories
                ->get();

            foreach ($categories as $category) {
                // Extract base name and country
                $baseName = $this->extractBaseName($category->name);
                $country = $this->extractCountry($category->name);

                if (!isset($groups[$baseName])) {
                    $groups[$baseName] = [
                        'name' => $baseName,
                        'slug' => Str::slug($baseName),
                        'countries' => [],
                        'image' => $category->image
                    ];
                }

                if ($country) {
                    $groups[$baseName]['countries'][] = [
                        'name' => $country,
                        'category_id' => $category->id,
                        'category_name' => $category->name,
                        'slug' => $category->slug
                    ];
                }
            }

            return array_values($groups);
        });
    }

    /**
     * Get countries for a specific product (e.g., all countries for "Apple Gift Card")
     */
    public function getCountriesForProduct($productSlug)
    {
        // Map slugs to actual category names in the database
        $slugToCategoryMap = [
            'google-play' => 'Google Play',
            'itunes' => 'Apple Gift Card',
            'playstation' => 'PlayStation',
            'xbox' => 'XBOX',
            'steam' => 'Steam',
            'razer-gold' => 'Razer Gold',
        ];

        $baseName = $slugToCategoryMap[$productSlug] ?? ucwords(str_replace('-', ' ', $productSlug));

        // Find all categories that match this base name
        $categories = Category::where('is_active', true)
            ->where(function($query) use ($baseName) {
                $query->where('name', '=', $baseName) // Exact match for parent
                      ->orWhere('name', 'LIKE', $baseName . ' -%') // Match children with 1 space and hyphen
                      ->orWhere('name', 'LIKE', $baseName . '  -%') // Match children with 2 spaces and hyphen
                      ->orWhere('name', 'LIKE', $baseName . '-%') // Match children without space
                      ->orWhere('name', 'LIKE', '% ' . $baseName) // Match "COUNTRY Product" format (e.g., "KSA Google play")
                      ->orWhere('name', 'LIKE', '% ' . strtolower($baseName)); // Match lowercase version
            })
            ->get();

        $countries = [];
        $baseCategory = null;

        foreach ($categories as $category) {
            $country = $this->extractCountry($category->name);

            // If no country found, it's the base category
            if (!$country) {
                $baseCategory = $category;
                continue;
            }

            $countries[] = [
                'name' => $country,
                'full_name' => $this->getCountryFullName($country),
                'flag' => $this->getCountryFlag($country),
                'category_id' => $category->id,
                'category_name' => $category->name,
                'category_slug' => $category->slug,
                'products_count' => Product::where('category_id', $category->id)
                    ->where('is_active', true)
                    ->where('is_available', true)
                    ->count()
            ];
        }

        return [
            'base_name' => $baseName,
            'base_category' => $baseCategory,
            'countries' => $countries
        ];
    }

    /**
     * Get products (amounts) for a specific country
     */
    public function getAmountsForCountry($categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            return [];
        }

        $products = Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->orderBy('selling_price', 'asc')
            ->get();

        return [
            'category' => $category,
            'base_name' => $this->extractBaseName($category->name),
            'country' => $this->extractCountry($category->name),
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'amount' => $this->extractAmount($product->name),
                    'price' => $product->selling_price,
                    'currency' => $product->currency,
                    'image' => $product->image,
                    'formatted_price' => $this->formatPrice($product->selling_price, $product->currency)
                ];
            })
        ];
    }

    /**
     * Extract base name from category name
     * Example: "Apple Gift Card - USA" -> "Apple Gift Card"
     */
    protected function extractBaseName($name)
    {
        // Remove country suffixes
        $patterns = [
            ' - USA',
            ' - KSA',
            ' - UAE',
            ' - British',
            ' - Canadian',
            ' - Japanese',
            ' - Turkey',
            ' - Germany',
            ' - France',
            ' - Italy',
            ' - Spain',
            ' - Australia',
            ' - Brazil',
            ' - Mexico',
            ' - India',
            ' - China',
            ' - Korea',
            ' - Taiwan',
            ' - Hong Kong',
            ' - Singapore',
            ' - Malaysia',
            ' - Indonesia',
            ' - Thailand',
            ' - Vietnam',
            ' - Philippines',
            ' - Egypt',
            ' - Saudi Arabia',
            ' - United Arab Emirates',
            ' - United Kingdom',
            ' - United States',
        ];

        foreach ($patterns as $pattern) {
            if (Str::contains($name, $pattern)) {
                return trim(str_replace($pattern, '', $name));
            }
        }

        return $name;
    }

    /**
     * Extract country from category name
     */
    protected function extractCountry($name)
    {
        $countryPatterns = [
            'USA' => 'USA',
            'KSA' => 'KSA',
            'UAE' => 'UAE',
            'British' => 'UK',
            'Canadian' => 'Canada',
            'Japanese' => 'Japan',
            'Turkey' => 'Turkey',
            'Germany' => 'Germany',
            'France' => 'France',
            'Italy' => 'Italy',
            'Spain' => 'Spain',
            'Australia' => 'Australia',
            'Brazil' => 'Brazil',
            'Mexico' => 'Mexico',
            'India' => 'India',
            'China' => 'China',
            'Korea' => 'Korea',
            'Taiwan' => 'Taiwan',
            'Hong Kong' => 'Hong Kong',
            'Singapore' => 'Singapore',
            'Malaysia' => 'Malaysia',
            'Indonesia' => 'Indonesia',
            'Thailand' => 'Thailand',
            'Vietnam' => 'Vietnam',
            'Philippines' => 'Philippines',
            'Egypt' => 'Egypt',
        ];

        foreach ($countryPatterns as $pattern => $country) {
            // Check if pattern exists in name (handles both "Country Product" and "Product - Country")
            if (Str::contains($name, $pattern)) {
                return $country;
            }
        }

        return null;
    }

    /**
     * Get country full name
     */
    protected function getCountryFullName($country)
    {
        $fullNames = [
            'USA' => 'United States',
            'KSA' => 'Saudi Arabia',
            'UAE' => 'United Arab Emirates',
            'UK' => 'United Kingdom',
            'Canada' => 'Canada',
            'Japan' => 'Japan',
            'Turkey' => 'Turkey',
            'Germany' => 'Germany',
            'France' => 'France',
            'Italy' => 'Italy',
            'Spain' => 'Spain',
            'Australia' => 'Australia',
            'Brazil' => 'Brazil',
            'Mexico' => 'Mexico',
            'India' => 'India',
            'China' => 'China',
            'Korea' => 'South Korea',
            'Taiwan' => 'Taiwan',
            'Hong Kong' => 'Hong Kong',
            'Singapore' => 'Singapore',
            'Malaysia' => 'Malaysia',
            'Indonesia' => 'Indonesia',
            'Thailand' => 'Thailand',
            'Vietnam' => 'Vietnam',
            'Philippines' => 'Philippines',
            'Egypt' => 'Egypt',
        ];

        return $fullNames[$country] ?? $country;
    }

    /**
     * Get country flag emoji
     */
    protected function getCountryFlag($country)
    {
        $flags = [
            'USA' => '🇺🇸',
            'KSA' => '🇸🇦',
            'UAE' => '🇦🇪',
            'UK' => '🇬🇧',
            'Canada' => '🇨🇦',
            'Japan' => '🇯🇵',
            'Turkey' => '🇹🇷',
            'Germany' => '🇩🇪',
            'France' => '🇫🇷',
            'Italy' => '🇮🇹',
            'Spain' => '🇪🇸',
            'Australia' => '🇦🇺',
            'Brazil' => '🇧🇷',
            'Mexico' => '🇲🇽',
            'India' => '🇮🇳',
            'China' => '🇨🇳',
            'Korea' => '🇰🇷',
            'Taiwan' => '🇹🇼',
            'Hong Kong' => '🇭🇰',
            'Singapore' => '🇸🇬',
            'Malaysia' => '🇲🇾',
            'Indonesia' => '🇮🇩',
            'Thailand' => '🇹🇭',
            'Vietnam' => '🇻🇳',
            'Philippines' => '🇵🇭',
            'Egypt' => '🇪🇬',
        ];

        return $flags[$country] ?? '🌍';
    }

    /**
     * Extract amount from product name
     * Example: "Apple Gift Card $10" -> "$10"
     */
    protected function extractAmount($name)
    {
        // Try to match various amount patterns
        if (preg_match('/\$(\d+)/', $name, $matches)) {
            return '$' . $matches[1];
        }

        if (preg_match('/(\d+)\s*(SAR|AED|USD|EUR|GBP)/', $name, $matches)) {
            return $matches[1] . ' ' . $matches[2];
        }

        if (preg_match('/(\d+\.\d+)\s*(SAR|AED|USD|EUR|GBP)/', $name, $matches)) {
            return $matches[1] . ' ' . $matches[2];
        }

        return $name;
    }

    /**
     * Format price with currency
     */
    protected function formatPrice($price, $currency)
    {
        $symbols = [
            'USD' => '$',
            'SAR' => 'SAR ',
            'AED' => 'AED ',
            'EUR' => '€',
            'GBP' => '£'
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';

        if ($currency === 'SAR' || $currency === 'AED') {
            return $symbol . number_format($price, 2);
        }

        return $symbol . number_format($price, 2);
    }
}
