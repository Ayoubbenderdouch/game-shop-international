<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('sort_order')
            ->get();

        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $productsWithCategory = Product::whereNotNull('category_id')->count();
        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        $lastSync = Category::whereNotNull('last_synced_at')
            ->orderBy('last_synced_at', 'desc')
            ->first();

        return view('admin.categories.index', compact(
            'categories',
            'totalCategories',
            'activeCategories',
            'productsWithCategory',
            'topCategories',
            'lastSync'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category = new Category();
        $category->name = $validated['name'];
        $category->slug = Str::slug($validated['name']);
        $category->api_id = 'manual_' . uniqid();
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->description = $validated['description'] ?? null;
        $category->is_active = $validated['is_active'] ?? true;
        $category->sort_order = $validated['sort_order'] ?? 0;
        $category->save();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('products');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category->name = $validated['name'];
        $category->slug = Str::slug($validated['name']);
        $category->parent_id = $validated['parent_id'] ?? null;
        $category->description = $validated['description'] ?? null;
        $category->is_active = $validated['is_active'] ?? $category->is_active;
        $category->sort_order = $validated['sort_order'] ?? $category->sort_order;
        $category->save();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with products');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
