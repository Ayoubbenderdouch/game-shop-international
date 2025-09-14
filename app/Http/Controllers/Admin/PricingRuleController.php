<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PricingRule::with(['category', 'product']);

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('apply_to', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $pricingRules = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $activeRules = PricingRule::where('is_active', true)->count();
        $categoryRules = PricingRule::where('apply_to', 'category')->count();
        $productRules = PricingRule::where('apply_to', 'product')->count();
        $globalRules = PricingRule::where('apply_to', 'all')->count();

        $categories = Category::all();
        $products = Product::all();

        return view('admin.pricing-rules.index', compact(
            'pricingRules',
            'activeRules',
            'categoryRules',
            'productRules',
            'globalRules',
            'categories',
            'products'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $products = Product::all();

        return view('admin.pricing-rules.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'apply_to' => 'required|in:all,category,product',
            'category_id' => 'nullable|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        // Validate category_id or product_id based on apply_to
        if ($validated['apply_to'] === 'category' && !$validated['category_id']) {
            return back()->withErrors(['category_id' => 'Category is required when applying to category']);
        }
        if ($validated['apply_to'] === 'product' && !$validated['product_id']) {
            return back()->withErrors(['product_id' => 'Product is required when applying to product']);
        }

        $pricingRule = PricingRule::create($validated);

        // Apply the rule to products if active
        if ($pricingRule->is_active) {
            $this->applyPricingRule($pricingRule);
        }

        return redirect()->route('admin.pricing-rules.index')
            ->with('success', 'Pricing rule created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(PricingRule $pricingRule)
    {
        $pricingRule->load(['category', 'product']);
        return response()->json($pricingRule);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricingRule $pricingRule)
    {
        $categories = Category::all();
        $products = Product::all();

        return view('admin.pricing-rules.edit', compact('pricingRule', 'categories', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PricingRule $pricingRule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'apply_to' => 'required|in:all,category,product',
            'category_id' => 'nullable|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $pricingRule->update($validated);

        // Reapply pricing rules if this rule is active
        if ($pricingRule->is_active) {
            $this->applyPricingRule($pricingRule);
        }

        return redirect()->route('admin.pricing-rules.index')
            ->with('success', 'Pricing rule updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricingRule $pricingRule)
    {
        $pricingRule->delete();

        // Recalculate prices for affected products
        $this->recalculatePrices($pricingRule);

        return redirect()->route('admin.pricing-rules.index')
            ->with('success', 'Pricing rule deleted successfully');
    }

    /**
     * Apply pricing rule to products
     */
    private function applyPricingRule(PricingRule $rule)
    {
        $query = Product::query();

        if ($rule->apply_to === 'category') {
            $query->where('category_id', $rule->category_id);
        } elseif ($rule->apply_to === 'product') {
            $query->where('id', $rule->product_id);
        }

        $products = $query->get();

        foreach ($products as $product) {
            if ($rule->type === 'percentage') {
                $product->margin_percentage = $rule->value;
                $product->margin_type = 'percentage';
            } else {
                $product->margin_amount = $rule->value;
                $product->margin_type = 'fixed';
            }

            $product->calculateSellingPrice();
            $product->save();
        }
    }

    /**
     * Recalculate prices after rule deletion
     */
    private function recalculatePrices(PricingRule $deletedRule)
    {
        // Get next priority rule or default pricing
        $query = Product::query();

        if ($deletedRule->apply_to === 'category') {
            $query->where('category_id', $deletedRule->category_id);
        } elseif ($deletedRule->apply_to === 'product') {
            $query->where('id', $deletedRule->product_id);
        }

        $products = $query->get();

        foreach ($products as $product) {
            // Find next applicable rule
            $nextRule = PricingRule::where('is_active', true)
                ->where(function ($q) use ($product) {
                    $q->where('apply_to', 'all')
                      ->orWhere(function ($q) use ($product) {
                          $q->where('apply_to', 'category')
                            ->where('category_id', $product->category_id);
                      })
                      ->orWhere(function ($q) use ($product) {
                          $q->where('apply_to', 'product')
                            ->where('product_id', $product->id);
                      });
                })
                ->orderBy('priority', 'desc')
                ->first();

            if ($nextRule) {
                if ($nextRule->type === 'percentage') {
                    $product->margin_percentage = $nextRule->value;
                    $product->margin_type = 'percentage';
                } else {
                    $product->margin_amount = $nextRule->value;
                    $product->margin_type = 'fixed';
                }
            }

            $product->calculateSellingPrice();
            $product->save();
        }
    }
}
