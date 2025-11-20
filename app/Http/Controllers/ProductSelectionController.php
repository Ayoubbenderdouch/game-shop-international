<?php

namespace App\Http\Controllers;

use App\Services\ProductGroupingService;
use Illuminate\Http\Request;

class ProductSelectionController extends Controller
{
    protected $groupingService;

    public function __construct(ProductGroupingService $groupingService)
    {
        $this->groupingService = $groupingService;
    }

    /**
     * Show country selection for a product
     */
    public function selectCountry($productSlug)
    {
        $data = $this->groupingService->getCountriesForProduct($productSlug);

        if (empty($data['countries'])) {
            abort(404, 'No countries available for this product');
        }

        return view('product-selection.countries', [
            'baseName' => $data['base_name'],
            'countries' => $data['countries'],
            'productSlug' => $productSlug
        ]);
    }

    /**
     * Show amount selection for a specific country
     */
    public function selectAmount($productSlug, $countrySlug)
    {
        // Find category by slug
        $category = \App\Models\Category::where('slug', $countrySlug)->first();

        if (!$category) {
            abort(404, 'Category not found');
        }

        $data = $this->groupingService->getAmountsForCountry($category->id);

        if (empty($data['products']) || $data['products']->isEmpty()) {
            abort(404, 'No products available for this country');
        }

        return view('product-selection.amounts', [
            'baseName' => $data['base_name'],
            'country' => $data['country'],
            'category' => $data['category'],
            'products' => $data['products'],
            'productSlug' => $productSlug
        ]);
    }
}
