<div class="sidebar px-4 py-4 py-md-4 me-0">

    <div class="d-flex flex-column h-100">

        {{-- =========================================================
            BRAND
        ========================================================== --}}

        <a href="{{ route('home') }}" class="mb-0 brand-icon text-decoration-none">

            <span class="logo-icon">
                <i class="bi bi-bag-check-fill fs-4"></i>
            </span>

            <span class="logo-text">
                {{ $setting?->website_name ?? 'Dela Moure' }}
            </span>

        </a>


        {{-- =========================================================
            ADMIN MENU
        ========================================================== --}}

        <ul class="menu-list flex-grow-1 mt-3">


            {{-- =====================================================
                DASHBOARD
            ====================================================== --}}

            <li>

                <a href="{{ route('home') }}"
                    class="m-link
                    {{ request()->routeIs('home') ? 'active' : '' }}">

                    <i class="icofont-home fs-5"></i>

                    <span>
                        Dashboard
                    </span>

                </a>

            </li>


            {{-- =====================================================
                CATALOG
            ====================================================== --}}

            <li class="collapsed">

                <a href="#"
                    class="m-link
                    {{ request()->routeIs(['admin.products.*', 'admin.categories.*']) ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-catalog"
                    aria-expanded="{{ request()->routeIs(['admin.products.*', 'admin.categories.*']) ? 'true' : 'false' }}">

                    <i class="icofont-box fs-5"></i>

                    <span>
                        Catalog
                    </span>

                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5">
                    </span>

                </a>


                <ul id="menu-catalog"
                    class="sub-menu collapse
                    {{ request()->routeIs(['admin.products.*', 'admin.categories.*']) ? 'show' : '' }}">


                    {{-- Products --}}
                    <li>

                        <a href="{{ route('admin.products.index') }}"
                            class="ms-link
                            {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">

                            All Products

                        </a>

                    </li>


                    <li>

                        <a href="{{ route('admin.products.create') }}"
                            class="ms-link
                            {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">

                            Add Product

                        </a>

                    </li>


                    {{-- Categories --}}
                    <li>

                        <a href="{{ route('admin.categories.index') }}"
                            class="ms-link
                            {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">

                            Categories

                        </a>

                    </li>


                    <li>

                        <a href="{{ route('admin.categories.create') }}"
                            class="ms-link
                            {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">

                            Add Category

                        </a>

                    </li>

                </ul>

            </li>

            {{-- Inventory --}}
            <li class="collapsed">

                <a class="m-link
        {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-inventory" href="#">

                    <i class="icofont-chart-histogram fs-5"></i>

                    <span>
                        Inventory
                    </span>

                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5">
                    </span>

                </a>


                <ul id="menu-inventory"
                    class="sub-menu collapse
        {{ request()->routeIs('admin.inventory.*') ? 'show' : '' }}">

                    <li>

                        <a href="{{ route('admin.inventory.index') }}"
                            class="ms-link
                {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">

                            Stock List

                        </a>

                    </li>


                    <li>

                        <a href="{{ route('admin.inventory.low-stock') }}"
                            class="ms-link
                {{ request()->routeIs('admin.inventory.low-stock') ? 'active' : '' }}">

                            Low Stock

                        </a>

                    </li>


                    <li>

                        <a href="{{ route('admin.inventory.movements') }}"
                            class="ms-link
                {{ request()->routeIs('admin.inventory.movements') ? 'active' : '' }}">

                            Stock Movements

                        </a>

                    </li>

                </ul>

            </li>
            {{-- =====================================================
                ORDERS
            ====================================================== --}}

            <li>

                <a href="{{ route('admin.orders.index') }}"
                    class="m-link
                    {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">

                    <i class="icofont-notepad fs-5"></i>

                    <span>
                        Orders
                    </span>

                </a>

            </li>


            {{-- =====================================================
                SHIPPING
            ====================================================== --}}

            <li class="collapsed">

                <a href="#"
                    class="m-link
                    {{ request()->routeIs('admin.shipping-rates.*') ? 'active' : '' }}"
                    data-bs-toggle="collapse" data-bs-target="#menu-shipping"
                    aria-expanded="{{ request()->routeIs('admin.shipping-rates.*') ? 'true' : 'false' }}">

                    <i class="icofont-truck-alt fs-5"></i>

                    <span>
                        Shipping
                    </span>

                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5">
                    </span>

                </a>


                <ul id="menu-shipping"
                    class="sub-menu collapse
                    {{ request()->routeIs('admin.shipping-rates.*') ? 'show' : '' }}">

                    <li>

                        <a href="{{ route('admin.shipping-rates.index') }}"
                            class="ms-link
                            {{ request()->routeIs('admin.shipping-rates.index') ? 'active' : '' }}">

                            Shipping Rates

                        </a>

                    </li>

                </ul>

            </li>


            {{-- =====================================================
                CUSTOMERS
            ====================================================== --}}

            <li>

                <a href="{{ route('admin.customers.index') }}"
                    class="m-link
                    {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">

                    <i class="icofont-users-alt-5 fs-5"></i>

                    <span>
                        Customers
                    </span>

                </a>

            </li>


            {{-- =====================================================
                WEBSITE SETTINGS
            ====================================================== --}}

            <li>

                <a href="{{ route('admin.website.settings') }}"
                    class="m-link
                    {{ request()->routeIs('admin.website.settings') ? 'active' : '' }}">

                    <i class="icofont-gear fs-5"></i>

                    <span>
                        Website Settings
                    </span>

                </a>

            </li>


            {{-- =====================================================
                VIEW STOREFRONT
            ====================================================== --}}

            <li>

                <a href="{{ url('/') }}" target="_blank" class="m-link">

                    <i class="icofont-external-link fs-5"></i>

                    <span>
                        View Store
                    </span>

                </a>

            </li>


            {{-- =====================================================
                LOGOUT
            ====================================================== --}}

            @auth

                <li class="mt-3">

                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button type="submit" class="m-link border-0 bg-transparent w-100 text-start">

                            <i class="icofont-logout fs-5"></i>

                            <span>
                                Logout
                            </span>

                        </button>

                    </form>

                </li>

            @endauth

        </ul>


        {{-- =========================================================
            COLLAPSE BUTTON
        ========================================================== --}}

        <button type="button" class="btn btn-link sidebar-mini-btn text-light">

            <span class="ms-2">

                <i class="icofont-bubble-right"></i>

            </span>

        </button>

    </div>

</div>
