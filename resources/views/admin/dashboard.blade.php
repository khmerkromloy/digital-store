@extends('admin.layouts.admin_layout')

@section('page_title', __('admin.resources.dashboard'))
@section('page_title_key', 'admin.resources.dashboard')

@section('content')
@php
    $cards = [
        ['label_key' => 'admin.resources.branches',    'value' => $stats['branches'],    'icon' => 'bi-shop',         'color' => 'primary'],
        ['label_key' => 'admin.resources.categories',  'value' => $stats['categories'],  'icon' => 'bi-tags',         'color' => 'info'],
        ['label_key' => 'admin.resources.products',    'value' => $stats['products'],    'icon' => 'bi-box',          'color' => 'warning'],
        ['label_key' => 'admin.resources.product_keys','value' => $stats['keys'],        'icon' => 'bi-key',          'color' => 'success'],
        ['label_key' => 'admin.resources.orders',      'value' => $stats['orders'],      'icon' => 'bi-receipt',      'color' => 'danger'],
        ['label_key' => 'admin.resources.payments',    'value' => $stats['payments'],    'icon' => 'bi-credit-card',  'color' => 'primary'],
        ['label_key' => 'admin.resources.customers',   'value' => $stats['customers'],   'icon' => 'bi-people',       'color' => 'secondary'],
        ['label_key' => 'admin.resources.contact_messages', 'value' => $stats['messages'], 'icon' => 'bi-envelope', 'color' => 'dark'],
    ];
@endphp

<div class="row g-3">
    @foreach($cards as $c)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card admin-card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small" data-i18n="{{ $c['label_key'] }}">{{ __($c['label_key']) }}</div>
                        <div class="h3 mb-0">{{ number_format($c['value']) }}</div>
                    </div>
                    <i class="bi {{ $c['icon'] }} text-{{ $c['color'] }}" style="font-size:2rem;"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-md-4">
        <div class="card admin-card h-100">
            <div class="card-body">
                <div class="text-muted small" data-i18n="admin.fields.total_spent">{{ __('admin.fields.total_spent') }}</div>
                <div class="h3 mb-0">USD {{ number_format($stats['revenue'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="card admin-card h-100">
            <div class="card-body">
                <h6 class="card-title mb-3" data-i18n="admin.resources.orders">{{ __('admin.resources.orders') }}</h6>
                @if($recentOrders->isEmpty())
                    <p class="text-muted mb-0" data-i18n="admin.misc.no_records">{{ __('admin.misc.no_records') }}</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th data-i18n="admin.fields.order_number">{{ __('admin.fields.order_number') }}</th>
                                    <th data-i18n="admin.fields.customer">{{ __('admin.fields.customer') }}</th>
                                    <th data-i18n="admin.fields.grand_total">{{ __('admin.fields.grand_total') }}</th>
                                    <th data-i18n="admin.fields.status">{{ __('admin.fields.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($recentOrders as $o)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $o) }}">{{ $o->order_number }}</a></td>
                                    <td>{{ $o->customer?->full_name ?? '—' }}</td>
                                    <td class="fw-medium">{{ number_format((float) $o->grand_total, 2) }} {{ $o->currency }}</td>
                                    <td><span class="badge bg-{{ ['paid' => 'success', 'pending' => 'warning', 'cancelled' => 'secondary', 'delivered' => 'success', 'failed' => 'danger', 'refunded' => 'secondary', 'partial' => 'info'][$o->status] ?? 'secondary' }}" data-i18n="admin.statuses.{{ $o->status }}">{{ __('admin.statuses.'.$o->status) }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card admin-card mt-4">
    <div class="card-body">
        <h5 class="card-title" data-i18n="admin.misc.quick_start">{{ __('admin.misc.quick_start') }}</h5>
        <p class="text-muted small mb-3" data-i18n="admin.misc.quick_start_hint">{{ __('admin.misc.quick_start_hint') }}</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.branches.index') }}">
                <i class="bi bi-shop me-1"></i> <span data-i18n="admin.resources.branches">{{ __('admin.resources.branches') }}</span>
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.categories.index') }}">
                <i class="bi bi-tags me-1"></i> <span data-i18n="admin.resources.categories">{{ __('admin.resources.categories') }}</span>
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.products.index') }}">
                <i class="bi bi-box me-1"></i> <span data-i18n="admin.resources.products">{{ __('admin.resources.products') }}</span>
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.product-keys.index') }}">
                <i class="bi bi-key me-1"></i> <span data-i18n="admin.resources.product_keys">{{ __('admin.resources.product_keys') }}</span>
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.customers.index') }}">
                <i class="bi bi-people me-1"></i> <span data-i18n="admin.resources.customers">{{ __('admin.resources.customers') }}</span>
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.orders.index') }}">
                <i class="bi bi-receipt me-1"></i> <span data-i18n="admin.resources.orders">{{ __('admin.resources.orders') }}</span>
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.payments.index') }}">
                <i class="bi bi-credit-card me-1"></i> <span data-i18n="admin.resources.payments">{{ __('admin.resources.payments') }}</span>
            </a>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.inventory.index') }}">
                <i class="bi bi-stack me-1"></i> <span data-i18n="admin.resources.inventory">{{ __('admin.resources.inventory') }}</span>
            </a>
        </div>
    </div>
</div>
@endsection
