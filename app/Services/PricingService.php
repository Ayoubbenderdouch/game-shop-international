<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\PricingRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingService
{
    /**
     * Apply pricing rule to products
     */
    public function applyPricingRule(PricingRule $rule)
    {
        DB::beginTransaction();

        try {
            $query = Product::query();

            // Filter products based on rule application
            switch ($rule->apply_to) {
                case 'all':
                    // Apply to all products
                    break;
                case 'category':
                    $query->where('category_id', $rule->category_id);
                    break;
                case 'product':
                    $query->where('id', $rule->product_id);
                    break;
            }

            $products = $query->get();
            $count = 0;

            foreach ($products as $product) {
                $this->applyMarginToProduct($product, $rule->type, $rule->value);
                $count++;
            }

            DB::commit();

            Log::info("Pricing rule {$rule->id} applied to {$count} products");

            return [
                'success' => true,
                'affected_products' => $count,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pricing rule application error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Apply margin to a single product
     */
    public function applyMarginToProduct(Product $product, $type, $value)
    {
        if ($type === 'percentage') {
            $product->margin_percentage = $value;
            $product->margin_type = 'percentage';
        } else {
            $product->margin_amount = $value;
            $product->margin_type = 'fixed';
        }

        $product->calculateSellingPrice();
        $product->save();

        return $product;
    }

    /**
     * Apply bulk margin to products
     */
    public function applyBulkMargin(array $productIds, $type, $value)
    {
        DB::beginTransaction();

        try {
            $products = Product::whereIn('id', $productIds)->get();
            $count = 0;

            foreach ($products as $product) {
                $this->applyMarginToProduct($product, $type, $value);
                $count++;
            }

            DB::commit();

            return [
                'success' => true,
                'affected_products' => $count,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk margin application error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Apply margin to category products
     */
    public function applyCategoryMargin($categoryId, $type, $value, $includeSubcategories = false)
    {
        DB::beginTransaction();

        try {
            $query = Product::where('category_id', $categoryId);

            if ($includeSubcategories) {
                $category = Category::find($categoryId);
                if ($category) {
                    $childCategoryIds = $category->children()->pluck('id')->toArray();
                    $query->orWhereIn('category_id', $childCategoryIds);
                }
            }

            $products = $query->get();
            $count = 0;

            foreach ($products as $product) {
                $this->applyMarginToProduct($product, $type, $value);
                $count++;
            }

            DB::commit();

            return [
                'success' => true,
                'affected_products' => $count,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category margin application error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate recommended selling price based on market analysis
     */
    public function calculateRecommendedPrice(Product $product)
    {
        try {
            // Base calculation: cost + default margin
            $defaultMarginPercentage = 30; // 30% default margin
            $recommendedPrice = $product->cost_price * (1 + $defaultMarginPercentage / 100);

            // Adjust based on category average
            $categoryAvg = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->avg('selling_price');

            if ($categoryAvg) {
                // Adjust to be competitive within category
                $recommendedPrice = ($recommendedPrice + $categoryAvg) / 2;
            }

            // Ensure minimum margin
            $minimumMargin = 10; // 10% minimum margin
            $minimumPrice = $product->cost_price * (1 + $minimumMargin / 100);
            $recommendedPrice = max($recommendedPrice, $minimumPrice);

            // Round to 2 decimal places
            $recommendedPrice = round($recommendedPrice, 2);

            return [
                'recommended_price' => $recommendedPrice,
                'margin_amount' => $recommendedPrice - $product->cost_price,
                'margin_percentage' => (($recommendedPrice - $product->cost_price) / $product->cost_price) * 100,
                'category_average' => $categoryAvg,
            ];
        } catch (\Exception $e) {
            Log::error('Price calculation error: ' . $e->getMessage());

            return [
                'recommended_price' => $product->cost_price * 1.3, // Fallback to 30% margin
                'margin_amount' => $product->cost_price * 0.3,
                'margin_percentage' => 30,
                'category_average' => null,
            ];
        }
    }

    /**
     * Get pricing analytics for a product or category
     */
    public function getPricingAnalytics($type = 'all', $id = null)
    {
        try {
            $query = Product::query();

            if ($type === 'product' && $id) {
                $query->where('id', $id);
            } elseif ($type === 'category' && $id) {
                $query->where('category_id', $id);
            }

            $products = $query->get();

            $analytics = [
                'total_products' => $products->count(),
                'average_cost' => $products->avg('cost_price'),
                'average_selling_price' => $products->avg('selling_price'),
                'average_margin_amount' => $products->avg('margin_amount'),
                'average_margin_percentage' => $products->avg('margin_percentage'),
                'total_potential_revenue' => $products->sum('selling_price'),
                'total_cost' => $products->sum('cost_price'),
                'total_margin' => $products->sum('margin_amount'),
            ];

            // Calculate margin distribution
            $marginRanges = [
                '0-10%' => 0,
                '10-20%' => 0,
                '20-30%' => 0,
                '30-50%' => 0,
                '50%+' => 0,
            ];

            foreach ($products as $product) {
                if ($product->margin_percentage <= 10) {
                    $marginRanges['0-10%']++;
                } elseif ($product->margin_percentage <= 20) {
                    $marginRanges['10-20%']++;
                } elseif ($product->margin_percentage <= 30) {
                    $marginRanges['20-30%']++;
                } elseif ($product->margin_percentage <= 50) {
                    $marginRanges['30-50%']++;
                } else {
                    $marginRanges['50%+']++;
                }
            }

            $analytics['margin_distribution'] = $marginRanges;

            return $analytics;
        } catch (\Exception $e) {
            Log::error('Pricing analytics error: ' . $e->getMessage());

            return [
                'error' => 'Failed to generate pricing analytics',
            ];
        }
    }

    /**
     * Update product prices based on cost changes
     */
    public function updatePricesAfterCostChange(Product $product, $oldCost)
    {
        try {
            if ($product->margin_type === 'percentage') {
                // Maintain the same percentage margin
                $product->calculateSellingPrice();
            } else {
                // For fixed margin, check if adjustment is needed
                if ($product->selling_price < $product->cost_price) {
                    // Selling price is now below cost, apply minimum margin
                    $minimumMarginPercentage = 10;
                    $product->margin_percentage = $minimumMarginPercentage;
                    $product->margin_type = 'percentage';
                    $product->calculateSellingPrice();
                }
            }

            $product->save();

            Log::info("Product {$product->id} price updated after cost change from {$oldCost} to {$product->cost_price}");

            return $product;
        } catch (\Exception $e) {
            Log::error('Price update error: ' . $e->getMessage());
            throw $e;
        }
    }
}
