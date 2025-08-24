<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $featuredProducts = Product::where('is_active', true)
            ->with('category', 'reviews')
            ->limit(8)
            ->get();

        return view('home', compact('categories', 'featuredProducts'));
    }
}
