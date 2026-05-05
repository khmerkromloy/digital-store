@php
    $nav = [
        ['header' => 'main'],
        ['title' => 'dashboard', 'icon' => 'bi-speedometer2', 'route' => 'admin.dashboard'],

        ['header' => 'catalog'],
        ['title' => 'branches',     'icon' => 'bi-shop',         'route' => 'admin.branches.index'],
        ['title' => 'categories',   'icon' => 'bi-tags',         'route' => 'admin.categories.index'],
        ['title' => 'products',     'icon' => 'bi-box',          'route' => 'admin.products.index'],
        ['title' => 'product_keys', 'icon' => 'bi-key',          'route' => 'admin.product-keys.index'],
        ['title' => 'inventory',    'icon' => 'bi-stack',        'route' => 'admin.inventory.index'],

        ['header' => 'sales'],
        ['title' => 'orders',       'icon' => 'bi-receipt',         'route' => 'admin.orders.index'],
        ['title' => 'payments',     'icon' => 'bi-credit-card',     'route' => 'admin.payments.index'],

        ['header' => 'customers'],
        ['title' => 'customers',    'icon' => 'bi-people',          'route' => 'admin.customers.index'],

        ['header' => 'staff_users'],
        ['title' => 'users',        'icon' => 'bi-person-badge',    'route' => 'admin.users.index'],
        ['title' => 'roles',        'icon' => 'bi-shield-lock',     'route' => 'admin.roles.index'],
        ['title' => 'permissions',  'icon' => 'bi-key-fill',        'route' => 'admin.permissions.index'],

        ['header' => 'marketing'],
        ['title' => 'contact_messages', 'icon' => 'bi-envelope',    'route' => 'admin.contact-messages.index'],

        ['header' => 'system'],
        ['title' => 'settings',     'icon' => 'bi-gear',            'route' => 'admin.settings.index'],
        ['title' => 'audit_logs',   'icon' => 'bi-clock-history',   'route' => 'admin.audit-logs.index'],
    ];
@endphp

<aside class="admin-sidebar" id="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="brand">
        <i class="bi bi-shop-window"></i>
        <span data-i18n="admin.brand_short">{{ __('admin.brand_short') }}</span>
    </a>

    <nav class="mt-2 pb-3">
        <ul class="nav flex-column">
            @foreach($nav as $item)
                @if(isset($item['header']))
                    <li class="nav-header" data-i18n="admin.sidebar_groups.{{ $item['header'] }}">{{ __('admin.sidebar_groups.' . $item['header']) }}</li>
                @else
                    @php
                        $routeBase = str_replace('.index', '', $item['route']) . '.';
                        $active = request()->routeIs($item['route']) || str_starts_with(request()->route()?->getName() ?? '', $routeBase);
                    @endphp
                    <li class="nav-item">
                        <a href="{{ \Route::has($item['route']) ? route($item['route']) : '#' }}" class="nav-link {{ $active ? 'active' : '' }}">
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span data-i18n="admin.resources.{{ $item['title'] }}">{{ __('admin.resources.' . $item['title']) }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</aside>
