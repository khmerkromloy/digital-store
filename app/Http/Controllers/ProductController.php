<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('products.index', [
            'categories' => $categories,
            'selectedCategory' => $request->query('category'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with('category:id,name,slug,icon')
            ->where('is_active', true)
            ->select([
                'id', 'category_id', 'name', 'slug', 'short_description',
                'price', 'original_price', 'currency', 'stock', 'is_featured',
                'sales_count', 'created_at',
            ]);

        if ($categorySlug = $request->query('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($priceMin = $request->query('price_min')) {
            $query->where('price', '>=', (float) $priceMin);
        }
        if ($priceMax = $request->query('price_max')) {
            $query->where('price', '<=', (float) $priceMax);
        }

        return DataTables::eloquent($query)
            ->addColumn('category_name', fn (Product $p) => $p->category?->name)
            ->addColumn('price_formatted', fn (Product $p) => '$'.number_format((float) $p->price, 2))
            ->addColumn('original_price_formatted', fn (Product $p) => $p->original_price
                ? '$'.number_format((float) $p->original_price, 2)
                : null)
            ->addColumn('discount_percent', fn (Product $p) => $p->discount_percent)
            ->addColumn('detail_url', fn (Product $p) => route('products.show', $p->slug))
            ->filterColumn('category_name', function ($query, $keyword) {
                $query->whereHas('category', fn ($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->orderColumn('category_name', function ($query, $order) {
                $query->join('categories', 'categories.id', '=', 'products.category_id')
                    ->orderBy('categories.name', $order)
                    ->select('products.*');
            })
            ->toJson();
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->increment('view_count');
        $product->load('category');

        $related = Product::query()
            ->with('category:id,name,slug,icon')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
