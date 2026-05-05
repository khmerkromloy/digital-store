<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class CategoryController extends BaseCrudController
{
    protected string $modelClass = Category::class;

    protected string $routeSlug = 'categories';

    protected string $singularKey = 'admin.resources.category';

    protected string $pluralKey = 'admin.resources.categories';

    protected array $columns = [
        'name' => 'admin.fields.name',
        'name_kh' => 'admin.fields.name_kh',
        'slug' => 'admin.fields.slug',
        'icon' => 'admin.fields.icon',
        'sort_order' => 'admin.fields.sort_order',
        'is_active' => 'admin.fields.is_active',
    ];

    protected array $searchable = ['name', 'name_kh', 'slug', 'description'];

    protected function fields(): array
    {
        return [
            CrudField::text('name', 'Name', true, 'col-md-6'),
            CrudField::text('name_kh', 'Name (KH)', false, 'col-md-6'),
            CrudField::text('slug', 'Slug', false, 'col-md-6'),
            CrudField::text('icon', 'Icon (bi-* class)', false, 'col-md-3'),
            CrudField::number('sort_order', 'Sort order', false, 'col-md-3'),
            CrudField::checkbox('is_active', 'Active', 'col-md-3'),
            CrudField::textarea('description', 'Description', false, 'col-md-12'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $id = $record?->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'name_kh' => ['nullable', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('categories', 'slug')->ignore($id)->whereNull('deleted_at')],
            'icon' => ['nullable', 'string', 'max:64'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
