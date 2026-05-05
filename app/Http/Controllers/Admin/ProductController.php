<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends BaseCrudController
{
    protected string $modelClass = Product::class;

    protected string $routeSlug = 'products';

    protected string $singularKey = 'admin.resources.product';

    protected string $pluralKey = 'admin.resources.products';

    protected array $columns = [
        'sku' => 'admin.fields.sku',
        'name' => 'admin.fields.name',
        'category.name' => 'admin.fields.category',
        'product_type' => 'admin.fields.product_type',
        'price' => 'admin.fields.price',
        'stock' => 'admin.fields.stock',
        'is_active' => 'admin.fields.is_active',
    ];

    protected array $with = ['category'];

    protected array $searchable = ['name', 'name_kh', 'sku', 'slug', 'short_description'];

    protected function fields(): array
    {
        $categories = Category::orderBy('name')->pluck('name', 'id')->toArray();
        $types = [
            'license_key' => 'admin.product_types.license_key',
            'account' => 'admin.product_types.account',
            'subscription' => 'admin.product_types.subscription',
            'gift_card' => 'admin.product_types.gift_card',
            'other' => 'admin.product_types.other',
        ];

        return [
            CrudField::select('category_id', 'Category', $categories, true, 'col-md-4'),
            CrudField::select('product_type', 'Type', $types, true, 'col-md-4'),
            CrudField::text('sku', 'SKU', false, 'col-md-4'),
            CrudField::text('name', 'Name', true, 'col-md-6'),
            CrudField::text('name_kh', 'Name (KH)', false, 'col-md-6'),
            CrudField::text('slug', 'Slug', false, 'col-md-6'),
            CrudField::decimal('price', 'Price', true, 'col-md-3'),
            CrudField::decimal('original_price', 'Original price', false, 'col-md-3'),
            CrudField::number('stock', 'Stock', false, 'col-md-3'),
            CrudField::text('currency', 'Currency', false, 'col-md-3'),
            CrudField::checkbox('is_active', 'Active', 'col-md-3'),
            CrudField::checkbox('is_featured', 'Featured', 'col-md-3'),
            CrudField::checkbox('auto_deliver', 'Auto deliver', 'col-md-3'),
            CrudField::textarea('short_description', 'Short description', false, 'col-md-12', '2'),
            CrudField::textarea('short_description_kh', 'Short description (KH)', false, 'col-md-12', '2'),
            CrudField::textarea('description', 'Description', false, 'col-md-12', '4'),
            CrudField::textarea('description_kh', 'Description (KH)', false, 'col-md-12', '4'),
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record = null): array
    {
        $data['stock'] = (int) ($data['stock'] ?? 0);
        $data['currency'] = $data['currency'] ?: 'USD';
        foreach (['is_active', 'is_featured', 'auto_deliver'] as $bool) {
            $data[$bool] = (bool) ($data[$bool] ?? false);
        }

        return $data;
    }

    protected function rules(?Model $record = null): array
    {
        $id = $record?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'product_type' => ['required', Rule::in(['license_key', 'account', 'subscription', 'gift_card', 'other'])],
            'sku' => ['nullable', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:160'],
            'name_kh' => ['nullable', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($id)->whereNull('deleted_at')],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'auto_deliver' => ['nullable', 'boolean'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'short_description_kh' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'description_kh' => ['nullable', 'string'],
        ];
    }
}
