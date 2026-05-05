@include('admin.layouts.partials.head')

<body class="admin-login-page">
    <div class="login-card">
        <div class="text-center mb-3">
            <i class="bi bi-shop-window text-primary" style="font-size:2.4rem"></i>
            <h1 class="h4 mt-2 mb-1" data-i18n="admin.auth.login_title">{{ __('admin.auth.login_title') }}</h1>
            <p class="text-muted small" data-i18n="admin.auth.login_subtitle">{{ __('admin.auth.login_subtitle') }}</p>
        </div>

        <div class="d-flex justify-content-end mb-2">
            <div data-react-component="language-switcher"></div>
        </div>

        @include('admin.layouts.partials.flash')

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label small fw-medium" data-i18n="admin.auth.email">{{ __('admin.auth.email') }}</label>
                <input id="email" name="email" type="email" required autofocus
                       value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-medium" data-i18n="admin.auth.password">{{ __('admin.auth.password') }}</label>
                <input id="password" name="password" type="password" required
                       class="form-control @error('password') is-invalid @enderror">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="form-check mb-4">
                <input id="remember" name="remember" type="checkbox" value="1" class="form-check-input">
                <label for="remember" class="form-check-label small" data-i18n="admin.auth.remember_me">{{ __('admin.auth.remember_me') }}</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <span data-i18n="admin.auth.sign_in">{{ __('admin.auth.sign_in') }}</span>
            </button>
        </form>
    </div>

    @include('admin.layouts.partials.scripts')
</body>
</html>
