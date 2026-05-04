@props(['product'])

<div class="product-card">
    <a href="{{ route('products.show', $product->slug) }}" class="product-thumb text-decoration-none">
        <i class="bi {{ $product->category?->icon ?? 'bi-box-seam' }}"></i>
    </a>
    <div class="product-body">
        @if($product->category)
            <span class="badge badge-soft-primary align-self-start small">
                {{ $product->category->name }}
            </span>
        @endif
        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
            <h3 class="product-title">{{ $product->name }}</h3>
        </a>
        @if($product->short_description)
            <p class="product-meta small mb-1">{{ \Illuminate\Support\Str::limit($product->short_description, 70) }}</p>
        @endif
        <div class="d-flex align-items-baseline justify-content-between mt-auto pt-2">
            <div>
                <span class="product-price">${{ number_format((float) $product->price, 2) }}</span>
                @if($product->original_price && $product->original_price > $product->price)
                    <small class="text-muted text-decoration-line-through ms-1">${{ number_format((float) $product->original_price, 2) }}</small>
                @endif
            </div>
            @if($product->discount_percent)
                <span class="badge bg-danger-subtle text-danger small">-{{ $product->discount_percent }}%</span>
            @endif
        </div>
        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-primary mt-2">
            View details <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
