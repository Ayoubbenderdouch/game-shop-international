<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->root()
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $featuredProducts = Product::active()
            ->available()
            ->inStock()
            ->orderBy('sales_count', 'desc')
            ->limit(8)
            ->with(['category', 'reviews'])
            ->get();

        return view('home', compact('categories', 'featuredProducts'));
    }

    public function setLocale(Request $request)
    {
        $locale = $request->input('locale', 'en');

        if (in_array($locale, ['en', 'ar'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);

            if (auth()->check()) {
                auth()->user()->update(['locale' => $locale]);
            }
        }

        return redirect()->back();
    }
}
