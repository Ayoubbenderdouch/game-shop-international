<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\PricingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('api_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by availability
        if ($request->has('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'api_id' => 'required|unique:products',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'margin_type' => 'required|in:fixed,percentage',
            'margin_value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $product = new Product($validated);

        if ($validated['margin_type'] === 'percentage') {
            $product->margin_percentage = $validated['margin_value'];
        } else {
            $product->margin_amount = $validated['margin_value'];
        }

        $product->calculateSellingPrice();
        $product->save();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'orderItems.order', 'reviews.user']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'margin_type' => 'required|in:fixed,percentage',
            'margin_value' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $product->fill($validated);

        if ($validated['margin_type'] === 'percentage') {
            $product->margin_percentage = $validated['margin_value'];
            $product->margin_type = 'percentage';
        } else {
            $product->margin_amount = $validated['margin_value'];
            $product->margin_type = 'fixed';
        }

        $product->calculateSellingPrice();
        $product->save();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Check if product has orders
        if ($product->orderItems()->exists()) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete product with existing orders');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'action' => 'required|in:activate,deactivate,delete,update_margin',
            'margin_type' => 'required_if:action,update_margin|in:fixed,percentage',
            'margin_value' => 'required_if:action,update_margin|numeric|min:0',
        ]);

        $products = Product::whereIn('id', $validated['product_ids'])->get();

        DB::beginTransaction();
        try {
            switch ($validated['action']) {
                case 'activate':
                    Product::whereIn('id', $validated['product_ids'])
                        ->update(['is_active' => true]);
                    $message = 'Products activated successfully';
                    break;

                case 'deactivate':
                    Product::whereIn('id', $validated['product_ids'])
                        ->update(['is_active' => false]);
                    $message = 'Products deactivated successfully';
                    break;

                case 'delete':
                    // Check if any product has orders
                    $hasOrders = false;
                    foreach ($products as $product) {
                        if ($product->orderItems()->exists()) {
                            $hasOrders = true;
                            break;
                        }
                    }

                    if ($hasOrders) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'Cannot delete products with existing orders');
                    }

                    Product::whereIn('id', $validated['product_ids'])->delete();
                    $message = 'Products deleted successfully';
                    break;

                case 'update_margin':
                    foreach ($products as $product) {
                        if ($validated['margin_type'] === 'percentage') {
                            $product->margin_percentage = $validated['margin_value'];
                            $product->margin_type = 'percentage';
                        } else {
                            $product->margin_amount = $validated['margin_value'];
                            $product->margin_type = 'fixed';
                        }
                        $product->calculateSellingPrice();
                        $product->save();
                    }
                    $message = 'Product margins updated successfully';
                    break;
            }

            DB::commit();
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function applyMargin(Request $request)
    {
        $validated = $request->validate([
            'apply_to' => 'required|in:all,category,selected',
            'category_id' => 'required_if:apply_to,category|exists:categories,id',
            'product_ids' => 'required_if:apply_to,selected|array',
            'product_ids.*' => 'exists:products,id',
            'margin_type' => 'required|in:fixed,percentage',
            'margin_value' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $query = Product::query();

            switch ($validated['apply_to']) {
                case 'all':
                    // Apply to all products
                    break;
                case 'category':
                    $query->where('category_id', $validated['category_id']);
                    break;
                case 'selected':
                    $query->whereIn('id', $validated['product_ids']);
                    break;
            }

            $products = $query->get();
            $count = 0;

            foreach ($products as $product) {
                if ($validated['margin_type'] === 'percentage') {
                    $product->margin_percentage = $validated['margin_value'];
                    $product->margin_type = 'percentage';
                } else {
                    $product->margin_amount = $validated['margin_value'];
                    $product->margin_type = 'fixed';
                }
                $product->calculateSellingPrice();
                $product->save();
                $count++;
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Margin applied successfully to {$count} products");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
