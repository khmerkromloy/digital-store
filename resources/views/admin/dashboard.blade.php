@extends('admin.layouts.admin_layout')

@section('page_title', __('admin.resources.dashboard'))
@section('page_title_key', 'admin.resources.dashboard')

@section('content')
@php
    $cards = [
        ['label_key' => 'admin.resources.branches',    'value' => $stats['branches'],   'icon' => 'bi-shop',         'color' => 'primary'],
        ['label_key' => 'admin.resources.categories',  'value' => $stats['categories'], 'icon' => 'bi-tags',         'color' => 'info'],
        ['label_key' => 'admin.resources.products',    'value' => $stats['products'],   'icon' => 'bi-box',          'color' => 'warning'],
        ['label_key' => 'admin.resources.product_keys','value' => $stats['keys'],       'icon' => 'bi-key',          'color' => 'success'],
        ['label_key' => 'admin.resources.orders',      'value' => $stats['orders'],     'icon' => 'bi-receipt',      'color' => 'danger'],
        ['label_key' => 'admin.resources.customers',   'value' => $stats['customers'],  'icon' => 'bi-people',       'color' => 'secondary'],
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
        </div>
    </div>
</div>
@endsection
