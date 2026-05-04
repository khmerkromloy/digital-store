@extends('layouts.app')

@section('title', $product->name)
@section('description', $product->short_description ?? 'Buy '.$product->name.' at DigitalShop with instant delivery.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                    @if($product->category)
                        <li class="breadcrumb-item">
                            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($product->name, 40) }}</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="product-thumb" style="aspect-ratio: 1 / 1; font-size: 8rem;">
                            <i class="bi {{ $product->category?->icon ?? 'bi-box-seam' }}"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    @if($product->category)
                        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                           class="badge badge-soft-primary text-decoration-none mb-2">
                            <i class="bi {{ $product->category->icon }}"></i> {{ $product->category->name }}
                        </a>
                    @endif

                    <h1 class="h2 fw-bold mb-2">{{ $product->name }}</h1>

                    @if($product->short_description)
                        <p class="text-muted">{{ $product->short_description }}</p>
                    @endif

                    <div class="d-flex align-items-baseline gap-3 mb-3">
                        <span class="fs-2 fw-bold text-primary">${{ number_format((float) $product->price, 2) }}</span>
                        @if($product->original_price && $product->original_price > $product->price)
                            <span class="text-muted text-decoration-line-through">${{ number_format((float) $product->original_price, 2) }}</span>
                            <span class="badge bg-danger-subtle text-danger fs-6">-{{ $product->discount_percent }}%</span>
                        @endif
                    </div>

                    <div class="d-flex gap-3 small text-muted mb-4 flex-wrap">
                        <span><i class="bi bi-box-seam me-1"></i> {{ $product->stock }} in stock</span>
                        <span><i class="bi bi-eye me-1"></i> {{ number_format($product->views) }} views</span>
                        <span><i class="bi bi-cart-check me-1"></i> {{ number_format($product->sales_count) }} sold</span>
                    </div>

                    <div class="alert alert-info d-flex gap-2 align-items-start small">
                        <i class="bi bi-info-circle-fill mt-1"></i>
                        <div>
                            <strong>Login required to purchase.</strong> Sign-up + email verification will be enabled in
                            the next phase. For now, this is a preview of the visitor catalog.
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary btn-lg disabled" tabindex="-1" aria-disabled="true">
                            <i class="bi bi-cart-plus me-1"></i> Add to cart
                        </button>
                        <button class="btn btn-outline-primary btn-lg disabled" tabindex="-1" aria-disabled="true">
                            <i class="bi bi-lightning-charge me-1"></i> Buy now
                        </button>
                        <a href="{{ route('contact') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-chat-dots me-1"></i> Ask a question
                        </a>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-semibold mb-3">Description</h5>
                    <div class="text-muted" style="white-space: pre-line;">{{ $product->description }}</div>
                </div>
            </div>
        </div>
    </section>

    @if($related->isNotEmpty())
    <section class="py-5 bg-white border-top">
        <div class="container">
            <h3 class="section-title h4 mb-4">Related products</h3>
            <div class="row g-3">
                @foreach($related as $r)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('partials.product-card', ['product' => $r])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
