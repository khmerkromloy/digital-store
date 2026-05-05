<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Support\ColumnRenderer;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends BaseCrudController
{
    protected string $modelClass = Customer::class;

    protected string $routeSlug = 'customers';

    protected string $singularKey = 'admin.resources.customer';

    protected string $pluralKey = 'admin.resources.customers';

    protected array $columns = [
        'full_name' => 'admin.fields.full_name',
        'email' => 'admin.fields.email',
        'phone' => 'admin.fields.phone',
        'country' => 'admin.fields.country',
        'orders_count' => 'admin.fields.orders_count',
        'total_spent' => 'admin.fields.total_spent',
        'status' => 'admin.fields.status',
    ];

    protected array $searchable = ['full_name', 'first_name', 'last_name', 'email', 'phone', 'telegram_handle'];

    protected function fields(): array
    {
        $statuses = [
            'active' => __('admin.statuses.active'),
            'inactive' => __('admin.statuses.inactive'),
            'blocked' => __('admin.statuses.blocked'),
        ];

        $locales = ['en' => 'English', 'km' => 'ខ្មែរ'];

        return [
            CrudField::text('first_name', 'First name', false, 'col-md-4'),
            CrudField::text('last_name', 'Last name', false, 'col-md-4'),
            CrudField::text('full_name', 'Full name', true, 'col-md-4'),
            CrudField::email('email', 'Email', true, 'col-md-6'),
            CrudField::text('phone', 'Phone', false, 'col-md-3'),
            CrudField::text('telegram_handle', 'Telegram', false, 'col-md-3'),
            CrudField::text('country', 'Country', false, 'col-md-4'),
            CrudField::select('locale', 'Locale', $locales, false, 'col-md-2'),
            CrudField::select('status', 'Status', $statuses, true, 'col-md-3'),
            CrudField::textarea('note', 'Note', false, 'col-md-12', '2'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $id = $record?->id;

        return [
            'first_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['nullable', 'string', 'max:80'],
            'full_name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', Rule::unique('customers', 'email')->ignore($id)->whereNull('deleted_at')],
            'phone' => ['nullable', 'string', 'max:32'],
            'telegram_handle' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:80'],
            'locale' => ['nullable', 'in:en,km'],
            'status' => ['required', 'in:active,inactive,blocked'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function applyExtraFilters(Builder $query, Request $request): void
    {
        if ($status = $request->input('status_filter')) {
            $query->where('status', $status);
        }
    }

    protected function columnFormatters(): array
    {
        return [
            'status' => fn ($row) => ColumnRenderer::badge($row->status, 'admin.statuses'),
            'total_spent' => fn ($row) => ColumnRenderer::money($row->total_spent),
        ];
    }
}
