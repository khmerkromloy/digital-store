<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductKey;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Model;

class ProductKeyController extends BaseCrudController
{
    protected string $modelClass = ProductKey::class;

    protected string $routeSlug = 'product-keys';

    protected string $singularKey = 'admin.resources.product_key';

    protected string $pluralKey = 'admin.resources.product_keys';

    protected array $columns = [
        'product.name' => 'admin.fields.product',
        'branch.name' => 'admin.fields.branch',
        'key_value' => 'admin.fields.key_value',
        'status' => 'admin.fields.status',
        'expires_at' => 'admin.fields.expires_at',
    ];

    protected array $with = ['product', 'branch'];

    protected array $searchable = ['key_value', 'extra_info'];

    protected function fields(): array
    {
        $products = Product::orderBy('name')->pluck('name', 'id')->toArray();
        $branches = Branch::orderBy('name')->pluck('name', 'id')->toArray();
        $statuses = [
            'available' => 'admin.statuses.available',
            'reserved' => 'admin.statuses.reserved',
            'sold' => 'admin.statuses.sold',
            'expired' => 'admin.statuses.expired',
            'invalid' => 'admin.statuses.invalid',
        ];

        return [
            CrudField::select('product_id', 'Product', $products, true, 'col-md-6'),
            CrudField::select('branch_id', 'Branch', $branches, false, 'col-md-3'),
            CrudField::select('status', 'Status', $statuses, true, 'col-md-3'),
            CrudField::text('key_value', 'Key value', true, 'col-md-9'),
            CrudField::datetime('expires_at', 'Expires at', false, 'col-md-3'),
            CrudField::textarea('extra_info', 'Extra info', false, 'col-md-12', '2'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status' => ['required', 'in:available,reserved,sold,expired,invalid'],
            'key_value' => ['required', 'string', 'max:500'],
            'expires_at' => ['nullable', 'date'],
            'extra_info' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
