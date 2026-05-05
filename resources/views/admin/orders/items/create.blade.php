@extends('admin.layouts.admin_layout')

@section('page_title', __('admin.order.add_item'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.index') }}" data-i18n="admin.resources.orders">{{ __('admin.resources.orders') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a>
    </li>
    <li class="breadcrumb-item active" data-i18n="admin.order.add_item">{{ __('admin.order.add_item') }}</li>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.orders.items.store', $order) }}">
    @csrf
    <div class="card admin-card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" data-i18n="admin.fields.product">{{ __('admin.fields.product') }}</label>
                    <select id="line-product"
                            name="product_id"
                            class="form-select"
                            data-tom-select
                            required>
                        <option value="">— {{ __('admin.misc.optional') }} —</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->price }}">
                                {{ $p->name }} ({{ $p->sku }}) — {{ number_format((float) $p->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" data-i18n="admin.fields.quantity">{{ __('admin.fields.quantity') }}</label>
                    <input type="number" min="1" name="quantity" value="1" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label" data-i18n="admin.fields.unit_price">{{ __('admin.fields.unit_price') }}</label>
                    <input id="line-price" type="number" step="0.01" min="0" name="unit_price" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label" data-i18n="admin.fields.discount">{{ __('admin.fields.discount') }}</label>
                    <input type="number" step="0.01" min="0" name="discount" value="0" class="form-control">
                </div>
            </div>
        </div>
        <div class="card-footer d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary">
                <span data-i18n="admin.actions.cancel">{{ __('admin.actions.cancel') }}</span>
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>
                <span data-i18n="admin.order.add_item">{{ __('admin.order.add_item') }}</span>
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // When product changes, autofill the unit price field with the product's catalog price.
    const selectEl = document.getElementById('line-product');
    const priceEl = document.getElementById('line-price');
    if (!selectEl || !priceEl) return;
    selectEl.addEventListener('change', function () {
        const opt = selectEl.options[selectEl.selectedIndex];
        const price = opt?.dataset?.price;
        if (price && (!priceEl.value || priceEl.value === '0' || priceEl.value === '')) {
            priceEl.value = parseFloat(price).toFixed(2);
        }
    });
});
</script>
@endpush
@endsection
