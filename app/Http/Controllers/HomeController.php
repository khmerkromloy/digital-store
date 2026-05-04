<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        $latest = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $stats = [
            'products' => Product::where('is_active', true)->count(),
            'categories' => Category::where('is_active', true)->count(),
            'sales' => Product::sum('sales_count'),
        ];

        return view('home', compact('featured', 'latest', 'categories', 'stats'));
    }
}
