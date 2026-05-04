@extends('layouts.app')

@section('title', 'Contact Us')
@section('description', 'Get in touch with the DigitalShop team — questions, feedback, or support requests.')

@section('content')
    <section class="py-4 bg-white border-bottom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold mb-1">Contact us</h1>
            <p class="text-muted mb-0">We typically respond within 24 hours. For urgent issues, please use live chat (coming soon).</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="fw-semibold mb-3">Reach us</h5>
                            <ul class="list-unstyled d-grid gap-3 mb-0">
                                <li class="d-flex gap-3">
                                    <i class="bi bi-envelope-fill text-primary fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Email</small>
                                        <a href="mailto:hello@digitalshop.example" class="fw-semibold text-decoration-none">hello@digitalshop.example</a>
                                    </div>
                                </li>
                                <li class="d-flex gap-3">
                                    <i class="bi bi-headset text-primary fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Support</small>
                                        <span class="fw-semibold">support@digitalshop.example</span>
                                    </div>
                                </li>
                                <li class="d-flex gap-3">
                                    <i class="bi bi-telegram text-primary fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Telegram</small>
                                        <span class="fw-semibold">@digitalshop_support</span>
                                    </div>
                                </li>
                                <li class="d-flex gap-3">
                                    <i class="bi bi-clock-fill text-primary fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Hours</small>
                                        <span class="fw-semibold">24/7 — global support</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="fw-semibold mb-3">Send us a message</h5>
                            <div id="contact-alert"></div>
                            <form id="contact-form" novalidate>
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="contact-name" class="form-label">Your name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="contact-name" name="name" required minlength="2" maxlength="120">
                                        <div class="invalid-feedback" data-field="name"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="contact-email" class="form-label">Email address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="contact-email" name="email" required maxlength="180">
                                        <div class="invalid-feedback" data-field="email"></div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contact-subject" class="form-label">Subject</label>
                                        <input type="text" class="form-control" id="contact-subject" name="subject" maxlength="180" placeholder="e.g. Question about a Spotify product">
                                        <div class="invalid-feedback" data-field="subject"></div>
                                    </div>
                                    <div class="col-12">
                                        <label for="contact-message" class="form-label">Message <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="contact-message" name="message" rows="5" required minlength="10" maxlength="4000"></textarea>
                                        <div class="invalid-feedback" data-field="message"></div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3" id="contact-submit">
                                    <span class="submit-text"><i class="bi bi-send me-1"></i> Send message</span>
                                    <span class="submit-spinner d-none">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Sending...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('contact-form');
        const submit = document.getElementById('contact-submit');
        const alertBox = document.getElementById('contact-alert');
        const submitText = submit.querySelector('.submit-text');
        const submitSpinner = submit.querySelector('.submit-spinner');

        function clearErrors() {
            $(form).find('.is-invalid').removeClass('is-invalid');
            $(form).find('.invalid-feedback').text('');
            $(alertBox).empty();
        }

        function showAlert(type, message) {
            $(alertBox).html(
                `<div class="alert alert-${type} d-flex align-items-start gap-2" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} mt-1"></i>
                    <div>${message}</div>
                </div>`
            );
        }

        function setLoading(isLoading) {
            submit.disabled = isLoading;
            submitText.classList.toggle('d-none', isLoading);
            submitSpinner.classList.toggle('d-none', !isLoading);
        }

        $(form).on('submit', function (e) {
            e.preventDefault();
            clearErrors();
            setLoading(true);

            $.ajax({
                url: @json(route('contact.store')),
                method: 'POST',
                data: $(form).serialize(),
                dataType: 'json',
            })
                .done(function (resp) {
                    showAlert('success', resp.message || 'Message sent.');
                    form.reset();
                })
                .fail(function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        Object.entries(xhr.responseJSON.errors).forEach(function ([field, messages]) {
                            const input = form.querySelector(`[name="${field}"]`);
                            const feedback = form.querySelector(`[data-field="${field}"]`);
                            if (input) input.classList.add('is-invalid');
                            if (feedback) feedback.textContent = messages[0];
                        });
                        showAlert('danger', 'Please correct the errors below.');
                    } else {
                        showAlert('danger', 'Something went wrong. Please try again.');
                    }
                })
                .always(function () {
                    setLoading(false);
                });
        });
    });
</script>
@endpush
