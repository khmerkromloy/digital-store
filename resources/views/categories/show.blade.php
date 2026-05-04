@extends('layouts.app')

@section('title', $category->name)
@section('description', $category->description ?? 'Browse all '.$category->name.' on DigitalShop.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <div class="category-icon" style="width:56px;height:56px;font-size:1.6rem;">
                    <i class="bi {{ $category->icon ?? 'bi-box-seam' }}"></i>
                </div>
                <div>
                    <h1 class="h3 fw-bold mb-1">{{ $category->name }}</h1>
                    <p class="text-muted mb-0">{{ $category->description }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            @if($products->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No products in this category yet.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        Browse all products
                    </a>
                </div>
            @else
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <span class="text-muted">
                        Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
                    </span>
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-table me-1"></i> View as searchable table
                    </a>
                </div>

                <div class="row g-3">
                    @foreach($products as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            @include('partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
