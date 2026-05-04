@extends('layouts.app')

@section('title', 'Terms of Service')
@section('description', 'Terms of service for DigitalShop — your agreement when using our website.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Terms of Service</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-1">Terms of service</h1>
            <p class="text-muted mb-0">Last updated: {{ now()->format('F j, Y') }}</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 mx-auto">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <p class="text-muted">
                                By accessing or using DigitalShop, you agree to these Terms of Service.
                                Please read them carefully.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">1. Eligibility</h2>
                            <p class="text-muted">You must be at least 18 years old (or the age of majority in your jurisdiction) to purchase from DigitalShop.</p>

                            <h2 class="h5 fw-bold mt-4 mb-2">2. Account</h2>
                            <p class="text-muted">You're responsible for keeping your login credentials secure. Notify us immediately if you suspect unauthorized access.</p>

                            <h2 class="h5 fw-bold mt-4 mb-2">3. Digital products</h2>
                            <p class="text-muted">All sales are final once a key/credential has been viewed, except where covered by our replacement guarantee. Replacement guarantee covers verified product defects within the listed warranty window.</p>

                            <h2 class="h5 fw-bold mt-4 mb-2">4. Acceptable use</h2>
                            <p class="text-muted">You agree not to use products for illegal activity, resell them, or attempt to abuse our replacement policy.</p>

                            <h2 class="h5 fw-bold mt-4 mb-2">5. Limitation of liability</h2>
                            <p class="text-muted">DigitalShop is not liable for indirect, incidental, or consequential damages. Our total liability is limited to the amount you paid for the product in question.</p>

                            <h2 class="h5 fw-bold mt-4 mb-2">6. Changes</h2>
                            <p class="text-muted">We may update these Terms at any time. Continued use of the site means you accept the updated Terms.</p>

                            <h2 class="h5 fw-bold mt-4 mb-2">7. Contact</h2>
                            <p class="text-muted mb-0">Questions? Reach us via the <a href="{{ route('contact') }}">contact page</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
