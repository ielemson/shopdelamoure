@php
    $menus = [
        [
            'label' => 'Dashboard',
            'icon' => 'icofont-home',
            'permission' => 'manage store',
            'route' => 'home',
            'patterns' => ['home'],
        ],
        [
            'label' => 'Orders',
            'icon' => 'icofont-notepad',
            'permission' => 'manage store',
            'route' => 'admin.orders.index',
            'patterns' => ['admin.orders.*'],
        ],
        [
            'label' => 'Customers',
            'icon' => 'icofont-users-alt-5',
            'permission' => 'manage store',
            'route' => 'admin.customers.index',
            'patterns' => ['admin.customers.*'],
        ],
        [
            'label' => 'Catalog',
            'icon' => 'icofont-box',
            'permission' => 'manage store',
            'id' => 'menu-catalog',
            'patterns' => ['admin.products.*', 'admin.categories.*'],
            'children' => [
                [
                    'label' => 'All Products',
                    'route' => 'admin.products.index',
                    'active' => request()->routeIs('admin.products.*')
                        && ! request()->routeIs('admin.products.create'),
                ],
                [
                    'label' => 'Add Product',
                    'route' => 'admin.products.create',
                    'active' => request()->routeIs('admin.products.create'),
                ],
                [
                    'label' => 'Categories',
                    'route' => 'admin.categories.index',
                    'active' => request()->routeIs('admin.categories.*')
                        && ! request()->routeIs('admin.categories.create'),
                ],
                [
                    'label' => 'Add Category',
                    'route' => 'admin.categories.create',
                    'active' => request()->routeIs('admin.categories.create'),
                ],
            ],
        ],
        [
            'label' => 'Inventory',
            'icon' => 'icofont-chart-histogram',
            'permission' => 'manage store',
            'id' => 'menu-inventory',
            'patterns' => ['admin.inventory.*'],
            'children' => [
                [
                    'label' => 'Stock List',
                    'route' => 'admin.inventory.index',
                    'active' => request()->routeIs('admin.inventory.index'),
                ],
                [
                    'label' => 'Low Stock',
                    'route' => 'admin.inventory.low-stock',
                    'active' => request()->routeIs('admin.inventory.low-stock'),
                ],
                [
                    'label' => 'Stock Movements',
                    'route' => 'admin.inventory.movements',
                    'active' => request()->routeIs('admin.inventory.movements'),
                ],
            ],
        ],
        [
            'label' => 'Delivery',
            'icon' => 'icofont-truck-alt',
            'permission' => 'manage delivery settings',
            'id' => 'menu-delivery',
            'patterns' => [
                'admin.delivery.settings*',
                'admin.shipping-rates.*',
                'admin.shipping-packages.*',
                'admin.terminal-checkout.*',
            ],
            'children' => [
                [
                    'label' => 'Delivery Settings',
                    'route' => 'admin.delivery.settings',
                    'active' => request()->routeIs('admin.delivery.settings*'),
                ],
                [
                    'label' => 'Local Delivery Rates',
                    'route' => 'admin.shipping-rates.index',
                    'active' => request()->routeIs('admin.shipping-rates.*'),
                ],
                [
                    'label' => 'Packaging & Quote Tests',
                    'route' => 'admin.shipping-packages.index',
                    'active' => request()->routeIs('admin.shipping-packages.*'),
                ],
                [
                    'label' => 'Terminal Checkout',
                    'route' => 'admin.terminal-checkout.edit',
                    'active' => request()->routeIs('admin.terminal-checkout.*'),
                ],
            ],
        ],
        [
            'label' => 'Coupons',
            'icon' => 'icofont-tag',
            'permission' => 'manage store',
            'route' => 'admin.coupons.index',
            'patterns' => ['admin.coupons.*'],
        ],
        [
            'label' => 'Admin Management',
            'icon' => 'icofont-users-alt-5',
            'permission' => 'manage admins',
            'route' => 'admin.admins.index',
            'patterns' => ['admin.admins.*'],
        ],
        [
            'label' => 'Role Management',
            'icon' => 'icofont-lock',
            'permission' => 'manage admins',
            'route' => 'admin.roles.index',
            'patterns' => ['admin.roles.*'],
        ],
        [
            'label' => 'Website Settings',
            'icon' => 'icofont-gear',
            'permission' => 'manage website settings',
            'route' => 'admin.website.settings',
            'patterns' => ['admin.website.settings*'],
        ],
    ];
@endphp

<div class="sidebar px-4 py-4 py-md-4 me-0">
    <div class="d-flex flex-column h-100">

        {{-- Brand --}}
        <a
            href="{{ route('home') }}"
            class="mb-0 brand-icon text-decoration-none"
        >
            <span class="logo-icon">
                <i class="bi bi-bag-check-fill fs-4"></i>
            </span>

            <span class="logo-text">
                {{ ($setting ?? null)?->website_name ?? 'De Lamoure' }}
            </span>
        </a>

        <ul class="menu-list flex-grow-1 mt-3">

            {{-- Permission-controlled menus --}}
            @foreach ($menus as $menu)
                @can($menu['permission'])
                    @php
                        $menuActive = request()->routeIs(...$menu['patterns']);
                    @endphp

                    <li>
                        @if (isset($menu['children']))
                            <a
                                href="#{{ $menu['id'] }}"
                                class="m-link {{ $menuActive ? 'active' : 'collapsed' }}"
                                data-bs-toggle="collapse"
                                role="button"
                                aria-expanded="{{ $menuActive ? 'true' : 'false' }}"
                                aria-controls="{{ $menu['id'] }}"
                            >
                                <i class="{{ $menu['icon'] }} fs-5"></i>
                                <span>{{ $menu['label'] }}</span>
                                <span class="arrow icofont-rounded-down ms-auto fs-5"></span>
                            </a>

                            <ul
                                id="{{ $menu['id'] }}"
                                class="sub-menu collapse {{ $menuActive ? 'show' : '' }}"
                            >
                                @foreach ($menu['children'] as $link)
                                    <li>
                                        <a
                                            href="{{ route($link['route']) }}"
                                            class="ms-link {{ $link['active'] ? 'active' : '' }}"
                                        >
                                            {{ $link['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <a
                                href="{{ route($menu['route']) }}"
                                class="m-link {{ $menuActive ? 'active' : '' }}"
                            >
                                <i class="{{ $menu['icon'] }} fs-5"></i>
                                <span>{{ $menu['label'] }}</span>
                            </a>
                        @endif
                    </li>
                @endcan
            @endforeach

            {{-- Storefront --}}
            <li>
                <a
                    href="{{ route('index') }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="m-link"
                >
                    <i class="icofont-external-link fs-5"></i>
                    <span>View Store</span>
                </a>
            </li>

            {{-- Logout --}}
            @auth
                <li class="mt-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="m-link border-0 bg-transparent w-100 text-start"
                        >
                            <i class="icofont-logout fs-5"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            @endauth

        </ul>

        {{-- Collapse Sidebar --}}
        <button
            type="button"
            class="btn btn-link sidebar-mini-btn text-light"
            aria-label="Toggle sidebar size"
        >
            <span class="ms-2">
                <i class="icofont-bubble-right"></i>
            </span>
        </button>

    </div>
</div>