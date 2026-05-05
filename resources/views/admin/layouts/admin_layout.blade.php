@include('admin.layouts.partials.head')

<body class="admin-body">
    @include('admin.layouts.partials.left_sidebar')

    <div class="admin-main">
        @include('admin.layouts.partials.header')

        <main class="admin-content">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    @php($__pageTitleKey = trim($__env->yieldContent('page_title_key')))
                    <h1 class="h4 mb-1" @if($__pageTitleKey) data-i18n="{{ $__pageTitleKey }}" @endif>@yield('page_title', __('admin.resources.dashboard'))</h1>
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}" data-i18n="admin.header.home">{{ __('admin.header.home') }}</a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </div>
                @yield('page_actions')
            </div>

            @include('admin.layouts.partials.flash')
            @yield('content')
        </main>

        @include('admin.layouts.partials.footer')
    </div>

    @include('admin.layouts.partials.scripts')
</body>
</html>
