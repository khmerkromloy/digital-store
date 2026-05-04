@extends('layouts.app')

@section('title', 'Categories')
@section('description', 'Browse all digital product categories on DigitalShop.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-1">Browse categories</h1>
            <p class="text-muted mb-0">{{ $categories->count() }} categories — find exactly what you need.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-3">
                @foreach($categories as $category)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('categories.show', $category->slug) }}" class="category-card h-100 align-items-start flex-column">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <div class="category-icon">
                                    <i class="bi {{ $category->icon ?? 'bi-box-seam' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="category-name mb-0">{{ $category->name }}</h5>
                                    <span class="category-count">
                                        {{ $category->products_count }} {{ \Illuminate\Support\Str::plural('product', $category->products_count) }}
                                    </span>
                                </div>
                                <i class="bi bi-arrow-right text-muted"></i>
                            </div>
                            @if($category->description)
                                <p class="text-muted small mb-0 mt-3">{{ $category->description }}</p>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
