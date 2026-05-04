@extends('layouts.app')

@section('title', 'Home')
@section('description', 'DigitalShop sells genuine license keys, Spotify, TikTok, Facebook accounts and more — instant delivery, 24/7 support.')

@section('content')
    <section class="hero">
        <div class="container position-relative">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="badge badge-soft-primary bg-light text-primary mb-3 px-3 py-2">
                        <i class="bi bi-lightning-charge-fill me-1"></i> Instant digital delivery
                    </span>
                    <h1 class="display-4 mb-3">Genuine digital products at <u>unbeatable prices</u></h1>
                    <p class="lead mb-4">
                        License keys, Spotify, TikTok, Facebook accounts, streaming subscriptions and more —
                        delivered to your inbox the moment your payment clears.
                    </p>
                    <div class="hero-cta d-flex flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-shop me-1"></i> Browse products
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">
                            View all categories
                        </a>
                    </div>
                    <div class="d-flex flex-wrap gap-4 mt-4 text-white-50">
                        <span><i class="bi bi-shield-check me-1"></i> Replacement guarantee</span>
                        <span><i class="bi bi-headset me-1"></i> 24/7 support</span>
                        <span><i class="bi bi-stars me-1"></i> Trusted by thousands</span>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <div class="position-relative">
                        <i class="bi bi-bag-check-fill text-white" style="font-size: 14rem; opacity: 0.18;"></i>
                        <i class="bi bi-key-fill position-absolute text-white"
                           style="font-size: 5rem; top: 10%; left: 10%; opacity: 0.6;"></i>
                        <i class="bi bi-spotify position-absolute text-white"
                           style="font-size: 4rem; bottom: 18%; left: 8%; opacity: 0.65;"></i>
                        <i class="bi bi-tiktok position-absolute text-white"
                           style="font-size: 3.5rem; top: 18%; right: 14%; opacity: 0.7;"></i>
                        <i class="bi bi-facebook position-absolute text-white"
                           style="font-size: 4rem; bottom: 12%; right: 10%; opacity: 0.65;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                @php
                    $tiles = [
                        ['icon' => 'bi-lightning-charge-fill', 'title' => 'Instant delivery', 'desc' => 'Keys & credentials are sent within seconds of payment.'],
                        ['icon' => 'bi-shield-check', 'title' => 'Genuine & guaranteed', 'desc' => 'Replacement covered if a key fails within the warranty.'],
                        ['icon' => 'bi-cash-coin', 'title' => 'Unbeatable prices', 'desc' => 'Up to 80% off retail on most digital products.'],
                        ['icon' => 'bi-headset', 'title' => '24/7 support', 'desc' => 'Real humans on live chat and email, day or night.'],
                    ];
                @endphp
                @foreach($tiles as $tile)
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary"
                                     style="width:48px;height:48px;font-size:1.4rem;">
                                    <i class="bi {{ $tile['icon'] }}"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-semibold">{{ $tile['title'] }}</h6>
                                <p class="text-muted small mb-0">{{ $tile['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-4 bg-white border-top border-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="section-title h3 mb-1">Browse by category</h2>
                    <p class="section-subtitle mb-0">{{ $stats['products'] }} products across {{ $stats['categories'] }} curated categories.</p>
                </div>
                <a href="{{ route('categories.index') }}" class="btn btn-link">View all <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-3">
                @foreach($categories as $category)
                    <div class="col-sm-6 col-lg-4">
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-card">
                            <div class="category-icon">
                                <i class="bi {{ $category->icon ?? 'bi-box-seam' }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="category-name">{{ $category->name }}</h6>
                                <span class="category-count">{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('product', $category->products_count) }}</span>
                            </div>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if($featured->isNotEmpty())
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="section-title h3 mb-1">Featured products</h2>
                    <p class="section-subtitle mb-0">Hand-picked best deals updated weekly.</p>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-link">See all <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-3">
                @foreach($featured as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($latest->isNotEmpty())
    <section class="py-5 bg-white border-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="section-title h3 mb-1">New arrivals</h2>
                    <p class="section-subtitle mb-0">Fresh stock added recently.</p>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-link">See all <i class="bi bi-arrow-right"></i></a>
            </div>

            <div class="row g-3">
                @foreach($latest as $product)
                    <div class="col-6 col-md-4 col-lg-3">
                        @include('partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="py-5">
        <div class="container">
            <div class="rounded-4 p-4 p-md-5 text-white" style="background: var(--brand-gradient);">
                <div class="row align-items-center g-4">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-2">Ready to shop?</h3>
                        <p class="mb-0 opacity-90">
                            Sign-up & email-verified checkout coming soon. For now, browse the catalog and reach out
                            to us with any questions about a specific product.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('contact') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-chat-dots me-1"></i> Get in touch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
