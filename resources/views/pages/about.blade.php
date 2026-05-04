@extends('layouts.app')

@section('title', 'About Us')
@section('description', 'About DigitalShop — our mission, story, and how we deliver genuine digital products at unbeatable prices.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About Us</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-1">About DigitalShop</h1>
            <p class="text-muted mb-0">Genuine digital products. Instant delivery. Real human support.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-start">
                <div class="col-lg-7">
                    <h2 class="h4 fw-bold mb-3">Our story</h2>
                    <p class="text-muted">
                        DigitalShop started with a simple frustration: software, streaming subscriptions, and digital
                        accounts cost too much, and the experience of buying them felt sketchy and slow. We built this
                        store to be the opposite — clean, transparent, and instant.
                    </p>
                    <p class="text-muted">
                        Today we serve thousands of customers around the world with license keys, premium accounts,
                        streaming subscriptions, and gaming products. Every order is delivered the moment payment
                        clears, and every product is backed by a replacement guarantee.
                    </p>

                    <h2 class="h4 fw-bold mt-5 mb-3">Our values</h2>
                    <div class="row g-3">
                        @php
                            $values = [
                                ['icon' => 'bi-shield-check', 'title' => 'Genuine products only', 'desc' => 'We source from trusted suppliers and verify every batch before listing.'],
                                ['icon' => 'bi-lightning-charge-fill', 'title' => 'Instant delivery', 'desc' => 'Automated fulfillment delivers your purchase in seconds.'],
                                ['icon' => 'bi-headset', 'title' => 'Real support', 'desc' => '24/7 human support — no chatbots, no scripts.'],
                                ['icon' => 'bi-cash-coin', 'title' => 'Fair pricing', 'desc' => 'Save up to 80% off retail without sacrificing legitimacy.'],
                            ];
                        @endphp
                        @foreach($values as $v)
                            <div class="col-md-6">
                                <div class="d-flex gap-3">
                                    <div class="category-icon">
                                        <i class="bi {{ $v['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-semibold mb-1">{{ $v['title'] }}</h6>
                                        <p class="text-muted small mb-0">{{ $v['desc'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-semibold mb-3">By the numbers</h5>
                            <div class="d-grid gap-3">
                                @php
                                    $stats = [
                                        ['label' => 'Happy customers', 'value' => '12,400+'],
                                        ['label' => 'Products delivered', 'value' => '38,000+'],
                                        ['label' => 'Avg. delivery time', 'value' => 'Under 60 seconds'],
                                        ['label' => 'Support response', 'value' => '< 30 minutes'],
                                    ];
                                @endphp
                                @foreach($stats as $s)
                                    <div class="d-flex justify-content-between border-bottom pb-2">
                                        <span class="text-muted">{{ $s['label'] }}</span>
                                        <strong>{{ $s['value'] }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body p-4">
                            <h5 class="fw-semibold mb-3">Need help?</h5>
                            <p class="text-muted small">
                                Have a question about a product, order, or refund?
                                Reach out and we'll respond within 24 hours.
                            </p>
                            <a href="{{ route('contact') }}" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-chat-dots me-1"></i> Contact us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
