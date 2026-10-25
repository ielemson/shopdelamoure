@php
    $catalogOpen = request()->routeIs(
        'admin.products.*',
        'admin.categories.*'
    );

    $inventoryOpen = request()->routeIs('admin.inventory.*');

    $deliveryOpen = request()->routeIs(
        'admin.delivery.settings*',
        'admin.shipping-rates.*',
        'admin.shipping-packages.*',
        'admin.terminal-checkout.*'
    );

    $catalogLinks = [
        [
            'label' => 'All Products',
            'route' => 'admin.products.index',
            'active' => request()->routeIs('admin.products.*')
                && !request()->routeIs('admin.products.create'),
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
                && !request()->routeIs('admin.categories.create'),
        ],
        [
            'label' => 'Add Category',
            'route' => 'admin.categories.create',
            'active' => request()->routeIs('admin.categories.create'),
        ],
    ];

    $inventoryLinks = [
        [
            'label' => 'Stock List',
            'route' => 'admin.inventory.index',
        ],
        [
            'label' => 'Low Stock',
            'route' => 'admin.inventory.low-stock',
        ],
        [
            'label' => 'Stock Movements',
            'route' => 'admin.inventory.movements',
        ],
    ];

    $deliveryLinks = [
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

            {{-- Dashboard --}}
            <li>
                <a
                    href="{{ route('home') }}"
                    class="m-link {{ request()->routeIs('home') ? 'active' : '' }}"
                >
                    <i class="icofont-home fs-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Orders --}}
            <li>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="m-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                >
                    <i class="icofont-notepad fs-5"></i>
                    <span>Orders</span>
                </a>
            </li>

            {{-- Customers --}}
            <li>
                <a
                    href="{{ route('admin.customers.index') }}"
                    class="m-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                >
                    <i class="icofont-users-alt-5 fs-5"></i>
                    <span>Customers</span>
                </a>
            </li>

            {{-- Catalog --}}
            <li>
                <a
                    href="#menu-catalog"
                    class="m-link {{ $catalogOpen ? 'active' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    role="button"
                    aria-expanded="{{ $catalogOpen ? 'true' : 'false' }}"
                    aria-controls="menu-catalog"
                >
                    <i class="icofont-box fs-5"></i>
                    <span>Catalog</span>
                    <span class="arrow icofont-rounded-down ms-auto fs-5"></span>
                </a>

                <ul
                    id="menu-catalog"
                    class="sub-menu collapse {{ $catalogOpen ? 'show' : '' }}"
                >
                    @foreach ($catalogLinks as $link)
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
            </li>

            {{-- Inventory --}}
            <li>
                <a
                    href="#menu-inventory"
                    class="m-link {{ $inventoryOpen ? 'active' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    role="button"
                    aria-expanded="{{ $inventoryOpen ? 'true' : 'false' }}"
                    aria-controls="menu-inventory"
                >
                    <i class="icofont-chart-histogram fs-5"></i>
                    <span>Inventory</span>
                    <span class="arrow icofont-rounded-down ms-auto fs-5"></span>
                </a>

                <ul
                    id="menu-inventory"
                    class="sub-menu collapse {{ $inventoryOpen ? 'show' : '' }}"
                >
                    @foreach ($inventoryLinks as $link)
                        <li>
                            <a
                                href="{{ route($link['route']) }}"
                                class="ms-link {{ request()->routeIs($link['route']) ? 'active' : '' }}"
                            >
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>

            {{-- Delivery --}}
            <li>
                <a
                    href="#menu-delivery"
                    class="m-link {{ $deliveryOpen ? 'active' : 'collapsed' }}"
                    data-bs-toggle="collapse"
                    role="button"
                    aria-expanded="{{ $deliveryOpen ? 'true' : 'false' }}"
                    aria-controls="menu-delivery"
                >
                    <i class="icofont-truck-alt fs-5"></i>
                    <span>Delivery</span>
                    <span class="arrow icofont-rounded-down ms-auto fs-5"></span>
                </a>

                <ul
                    id="menu-delivery"
                    class="sub-menu collapse {{ $deliveryOpen ? 'show' : '' }}"
                >
                    @foreach ($deliveryLinks as $link)
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
            </li>

            {{-- Coupons --}}
            <li>
                <a
                    href="{{ route('admin.coupons.index') }}"
                    class="m-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
                >
                    <i class="icofont-tag fs-5"></i>
                    <span>Coupons</span>
                </a>
            </li>

            {{-- Website Settings --}}
            <li>
                <a
                    href="{{ route('admin.website.settings') }}"
                    class="m-link {{ request()->routeIs('admin.website.settings*') ? 'active' : '' }}"
                >
                    <i class="icofont-gear fs-5"></i>
                    <span>Website Settings</span>
                </a>
            </li>

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