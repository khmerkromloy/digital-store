<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Product;
use App\Support\ColumnRenderer;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryController extends BaseCrudController
{
    protected string $modelClass = BranchInventory::class;

    protected string $routeSlug = 'inventory';

    protected string $singularKey = 'admin.resources.inventory';

    protected string $pluralKey = 'admin.resources.inventory';

    protected array $columns = [
        'branch.name' => 'admin.fields.branch',
        'product.name' => 'admin.fields.product',
        'stock' => 'admin.fields.stock',
        'price_override' => 'admin.fields.price_override',
        'is_active' => 'admin.fields.is_active',
    ];

    protected array $with = ['branch', 'product'];

    protected array $searchable = [];

    protected function fields(): array
    {
        $branches = Branch::orderBy('name')->pluck('name', 'id')->toArray();
        $products = Product::orderBy('name')->pluck('name', 'id')->toArray();

        return [
            CrudField::select('branch_id', 'Branch', $branches, true, 'col-md-6'),
            CrudField::select('product_id', 'Product', $products, true, 'col-md-6'),
            CrudField::number('stock', 'Stock', true, 'col-md-3'),
            CrudField::decimal('price_override', 'Price override', false, 'col-md-3'),
            CrudField::checkbox('is_active', 'Active', 'col-md-3'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $id = $record?->id;

        return [
            'branch_id' => [
                'required',
                'exists:branches,id',
                Rule::unique('branch_product')->where(fn ($q) => $q->where('product_id', request('product_id')))->ignore($id),
            ],
            'product_id' => ['required', 'exists:products,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function applyExtraFilters(Builder $query, Request $request): void
    {
        if ($branch = $request->input('branch_filter')) {
            $query->where('branch_id', $branch);
        }
    }

    protected function columnFormatters(): array
    {
        return [
            'stock' => function (BranchInventory $row) {
                $stock = (int) $row->stock;
                $cls = $stock === 0 ? 'danger' : ($stock < 5 ? 'warning' : 'success');

                return '<span class="badge bg-'.$cls.'">'.$stock.'</span>';
            },
            'is_active' => fn (BranchInventory $row) => $row->is_active
                ? '<span class="badge bg-success" data-i18n="admin.misc.yes">'.e(__('admin.misc.yes')).'</span>'
                : '<span class="badge bg-secondary" data-i18n="admin.misc.no">'.e(__('admin.misc.no')).'</span>',
            'price_override' => fn (BranchInventory $row) => $row->price_override
                ? ColumnRenderer::money($row->price_override)
                : '<span class="text-muted">—</span>',
        ];
    }
}
