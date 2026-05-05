<header class="admin-topbar">
    <button id="sidebar-toggle" type="button" class="btn btn-link p-0 me-2" aria-label="Toggle sidebar">
        <i class="bi bi-list fs-4"></i>
    </button>

    <a href="{{ url('/') }}" class="nav-link" target="_blank" data-i18n="admin.header.storefront">
        {{ __('admin.header.storefront') }}
    </a>

    <div class="ms-auto d-flex align-items-center gap-2">
        <div data-react-component="language-switcher"></div>

        @auth
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                        <i class="bi bi-person"></i>
                    </span>
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li class="dropdown-header small">{{ auth()->user()->email }}</li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                <span data-i18n="admin.header.logout">{{ __('admin.header.logout') }}</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a class="nav-link" href="{{ route('login') }}" data-i18n="admin.header.login">{{ __('admin.header.login') }}</a>
        @endauth
    </div>
</header>
