@extends('admin.layouts.admin_layout')

@section('page_title', __('admin.resources.order') . ' ' . $record->order_number)

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.index') }}" data-i18n="admin.resources.orders">{{ __('admin.resources.orders') }}</a>
    </li>
    <li class="breadcrumb-item active">{{ $record->order_number }}</li>
@endsection

@section('page_actions')
    <a href="{{ route('admin.orders.edit', $record) }}" class="btn btn-warning">
        <i class="bi bi-pencil me-1"></i>
        <span data-i18n="admin.actions.edit">{{ __('admin.actions.edit') }}</span>
    </a>
    <form method="POST" action="{{ route('admin.orders.recalculate', $record) }}" class="d-inline ms-1">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i>
            <span data-i18n="admin.order.recalc">{{ __('admin.order.recalc') }}</span>
        </button>
    </form>
    @if($record->payment_status !== 'paid')
        <form method="POST" action="{{ route('admin.orders.mark-paid', $record) }}" class="d-inline ms-1">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-cash-coin me-1"></i>
                <span data-i18n="admin.order.mark_paid">{{ __('admin.order.mark_paid') }}</span>
            </button>
        </form>
    @endif
@endsection

@section('content')
<div class="row g-3">
    <div class="col-md-7">
        <div class="card admin-card mb-3">
            <div class="card-header">
                <h6 class="mb-0" data-i18n="admin.order.header">{{ __('admin.order.header') }}</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4" data-i18n="admin.fields.order_number">{{ __('admin.fields.order_number') }}</dt>
                    <dd class="col-sm-8">{{ $record->order_number }}</dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.customer">{{ __('admin.fields.customer') }}</dt>
                    <dd class="col-sm-8">
                        @if($record->customer)
                            <a href="{{ route('admin.customers.show', $record->customer) }}">{{ $record->customer->full_name }}</a>
                            <span class="text-muted small d-block">{{ $record->customer->email }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.branch">{{ __('admin.fields.branch') }}</dt>
                    <dd class="col-sm-8">{{ $record->branch?->name ?? '—' }}</dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.status">{{ __('admin.fields.status') }}</dt>
                    <dd class="col-sm-8"><span class="badge bg-secondary" data-i18n="admin.statuses.{{ $record->status }}">{{ __('admin.statuses.'.$record->status) }}</span></dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.payment_status">{{ __('admin.fields.payment_status') }}</dt>
                    <dd class="col-sm-8"><span class="badge bg-secondary" data-i18n="admin.statuses.{{ $record->payment_status }}">{{ __('admin.statuses.'.$record->payment_status) }}</span></dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.delivery_status">{{ __('admin.fields.delivery_status') }}</dt>
                    <dd class="col-sm-8"><span class="badge bg-secondary" data-i18n="admin.statuses.{{ $record->delivery_status }}">{{ __('admin.statuses.'.$record->delivery_status) }}</span></dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.payment_method">{{ __('admin.fields.payment_method') }}</dt>
                    <dd class="col-sm-8">{{ $record->payment_method ? __('admin.payment_methods.'.$record->payment_method) : '—' }}</dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.placed_at">{{ __('admin.fields.placed_at') }}</dt>
                    <dd class="col-sm-8">{{ optional($record->placed_at)->format('Y-m-d H:i') ?? '—' }}</dd>

                    <dt class="col-sm-4" data-i18n="admin.fields.paid_at">{{ __('admin.fields.paid_at') }}</dt>
                    <dd class="col-sm-8">{{ optional($record->paid_at)->format('Y-m-d H:i') ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0" data-i18n="admin.fields.items">{{ __('admin.fields.items') }}</h6>
                <a href="{{ route('admin.orders.items.create', $record) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    <span data-i18n="admin.order.add_item">{{ __('admin.order.add_item') }}</span>
                </a>
            </div>
            <div class="card-body p-0">
                @if($record->items->isEmpty())
                    <p class="p-3 mb-0 text-muted" data-i18n="admin.order.no_items">{{ __('admin.order.no_items') }}</p>
                @else
                    <table class="table table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th data-i18n="admin.fields.product">{{ __('admin.fields.product') }}</th>
                                <th class="text-end" data-i18n="admin.fields.unit_price">{{ __('admin.fields.unit_price') }}</th>
                                <th class="text-end" data-i18n="admin.fields.quantity">{{ __('admin.fields.quantity') }}</th>
                                <th class="text-end" data-i18n="admin.fields.discount">{{ __('admin.fields.discount') }}</th>
                                <th class="text-end" data-i18n="admin.fields.line_total">{{ __('admin.fields.line_total') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($record->items as $item)
                                <tr>
                                    <td>
                                        <div>{{ $item->product_name }}</div>
                                        @if($item->product_sku)
                                            <small class="text-muted">{{ $item->product_sku }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ (int) $item->quantity }}</td>
                                    <td class="text-end">{{ number_format((float) $item->discount, 2) }}</td>
                                    <td class="text-end fw-medium">{{ number_format((float) $item->line_total, 2) }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.orders.items.destroy', [$record, $item]) }}" data-confirm-delete class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card admin-card mb-3">
            <div class="card-header">
                <h6 class="mb-0" data-i18n="admin.order.totals">{{ __('admin.order.totals') }}</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-7" data-i18n="admin.fields.subtotal">{{ __('admin.fields.subtotal') }}</dt>
                    <dd class="col-5 text-end">{{ number_format((float) $record->subtotal, 2) }} {{ $record->currency }}</dd>

                    <dt class="col-7" data-i18n="admin.fields.discount_total">{{ __('admin.fields.discount_total') }}</dt>
                    <dd class="col-5 text-end">- {{ number_format((float) $record->discount_total, 2) }} {{ $record->currency }}</dd>

                    <dt class="col-7" data-i18n="admin.fields.tax_total">{{ __('admin.fields.tax_total') }}</dt>
                    <dd class="col-5 text-end">{{ number_format((float) $record->tax_total, 2) }} {{ $record->currency }}</dd>

                    <dt class="col-7 fw-bold" data-i18n="admin.fields.grand_total">{{ __('admin.fields.grand_total') }}</dt>
                    <dd class="col-5 text-end fw-bold h5 mb-0">{{ number_format((float) $record->grand_total, 2) }} {{ $record->currency }}</dd>
                </dl>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header">
                <h6 class="mb-0" data-i18n="admin.resources.payments">{{ __('admin.resources.payments') }}</h6>
            </div>
            <div class="card-body p-0">
                @if($record->payments->isEmpty())
                    <p class="p-3 mb-0 text-muted" data-i18n="admin.misc.no_records">{{ __('admin.misc.no_records') }}</p>
                @else
                    <table class="table table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th data-i18n="admin.fields.payment_number">{{ __('admin.fields.payment_number') }}</th>
                                <th data-i18n="admin.fields.method">{{ __('admin.fields.method') }}</th>
                                <th class="text-end" data-i18n="admin.fields.amount">{{ __('admin.fields.amount') }}</th>
                                <th data-i18n="admin.fields.status">{{ __('admin.fields.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($record->payments as $p)
                                <tr>
                                    <td><a href="{{ route('admin.payments.show', $p) }}">{{ $p->payment_number }}</a></td>
                                    <td>{{ __('admin.payment_methods.'.$p->method) }}</td>
                                    <td class="text-end">{{ number_format((float) $p->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-secondary" data-i18n="admin.statuses.{{ $p->status }}">
                                            {{ __('admin.statuses.'.$p->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
