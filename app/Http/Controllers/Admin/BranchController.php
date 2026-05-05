<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class BranchController extends BaseCrudController
{
    protected string $modelClass = Branch::class;

    protected string $routeSlug = 'branches';

    protected string $singularKey = 'admin.resources.branch';

    protected string $pluralKey = 'admin.resources.branches';

    protected array $columns = [
        'code' => 'admin.fields.code',
        'name' => 'admin.fields.name',
        'name_kh' => 'admin.fields.name_kh',
        'email' => 'admin.fields.email',
        'currency' => 'admin.fields.currency',
        'is_active' => 'admin.fields.is_active',
        'is_default' => 'admin.fields.is_default',
    ];

    protected array $searchable = ['code', 'name', 'name_kh', 'email', 'phone', 'city'];

    protected function fields(): array
    {
        return [
            CrudField::text('code', 'Code', false, 'col-md-3'),
            CrudField::text('name', 'Name', true, 'col-md-5'),
            CrudField::text('name_kh', 'Name (KH)', false, 'col-md-4'),
            CrudField::text('slug', 'Slug', false, 'col-md-6'),
            CrudField::email('email', 'Email', false, 'col-md-6'),
            CrudField::text('phone', 'Phone', false, 'col-md-4'),
            CrudField::text('city', 'City', false, 'col-md-4'),
            CrudField::text('country', 'Country', false, 'col-md-4'),
            CrudField::text('timezone', 'Timezone', false, 'col-md-4'),
            CrudField::text('currency', 'Currency', false, 'col-md-2'),
            CrudField::number('sort_order', 'Sort order', false, 'col-md-2'),
            CrudField::checkbox('is_active', 'Active', 'col-md-2'),
            CrudField::checkbox('is_default', 'Default', 'col-md-2'),
            CrudField::textarea('address', 'Address', false, 'col-md-6'),
            CrudField::textarea('description', 'Description', false, 'col-md-12'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $id = $record?->id;

        return [
            'code' => ['nullable', 'string', 'max:32', Rule::unique('branches', 'code')->ignore($id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:120'],
            'name_kh' => ['nullable', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('branches', 'slug')->ignore($id)->whereNull('deleted_at')],
            'email' => ['nullable', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:80'],
            'country' => ['nullable', 'string', 'max:80'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'currency' => ['nullable', 'string', 'size:3'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'address' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
