@extends('layouts.app')

@section('title', 'Privacy Policy')
@section('description', 'Privacy policy — how DigitalShop collects, uses, and protects your information.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-1">Privacy policy</h1>
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
                                Your privacy is important to us. This Privacy Policy explains how DigitalShop ("we",
                                "us", "our") collects, uses, and protects your personal information when you visit
                                our website or purchase a product.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">1. Information we collect</h2>
                            <ul class="text-muted">
                                <li><strong>Account info:</strong> name, email address, password (hashed), and verification status.</li>
                                <li><strong>Order info:</strong> products purchased, prices, delivery email, transaction IDs.</li>
                                <li><strong>Contact info:</strong> messages you send via the contact form, including your name, email, IP address, and the message itself.</li>
                                <li><strong>Technical info:</strong> browser type, device info, IP address, and pages visited (used for security and analytics).</li>
                            </ul>

                            <h2 class="h5 fw-bold mt-4 mb-2">2. How we use your information</h2>
                            <ul class="text-muted">
                                <li>To deliver the digital products you purchase.</li>
                                <li>To verify your account via email and prevent fraud.</li>
                                <li>To respond to support requests and feedback.</li>
                                <li>To send transactional emails (receipts, delivery confirmations, account verification).</li>
                                <li>To improve the website and prevent abuse.</li>
                            </ul>

                            <h2 class="h5 fw-bold mt-4 mb-2">3. Information we never share or sell</h2>
                            <p class="text-muted">
                                We do not sell, rent, or trade your personal information to third parties for marketing.
                                We may share data only with: (a) payment processors, strictly to complete a transaction;
                                (b) email/delivery providers, strictly to deliver your purchase; (c) law enforcement, only
                                when legally required.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">4. Cookies</h2>
                            <p class="text-muted">
                                We use cookies and similar technologies to keep you logged in, remember your cart, and
                                analyze traffic. You can disable cookies in your browser settings, but parts of the
                                site may not function correctly.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">5. Data retention</h2>
                            <p class="text-muted">
                                Account and order data is retained for as long as your account is active and for up to
                                3 years afterward to comply with legal and tax obligations. Contact form messages are
                                retained for up to 12 months.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">6. Your rights</h2>
                            <p class="text-muted">
                                You have the right to access, correct, or delete your personal information at any time.
                                To exercise these rights, contact us via the
                                <a href="{{ route('contact') }}">contact form</a>.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">7. Security</h2>
                            <p class="text-muted">
                                We use HTTPS, hashed passwords, and limited internal access controls. While no system
                                is 100% secure, we apply industry-standard practices to keep your data safe.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">8. Changes to this policy</h2>
                            <p class="text-muted">
                                We may update this Privacy Policy from time to time. We will notify you of material
                                changes by email or by posting a prominent notice on the website.
                            </p>

                            <h2 class="h5 fw-bold mt-4 mb-2">9. Contact</h2>
                            <p class="text-muted mb-0">
                                Questions about this policy? Reach us at
                                <a href="mailto:hello@digitalshop.example">hello@digitalshop.example</a>
                                or via our <a href="{{ route('contact') }}">contact page</a>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
