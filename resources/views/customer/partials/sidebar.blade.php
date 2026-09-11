<aside class="navbar navbar-expand-xl navbar-light d-block px-0 header-sticky dashboard-nav py-0">

    <div class="sticky-area border-end bg-body">


        {{-- ==========================================================
            SIDEBAR HEADER
        ========================================================== --}}

        <div
            class="d-flex px-6 px-xl-8 w-100 border-bottom py-7
                   justify-content-between align-items-center">

            <a href="{{ route('home') }}" class="navbar-brand py-2 mb-0">

                <img src="{{ asset('assets/images/others/logo.png') }}" width="160" height="45" class="img-fluid"
                    alt="Dela Moure">

            </a>


            {{-- Mobile Toggle --}}
            <button class="navbar-toggler border-0 px-0 shadow-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#customerSidebar" aria-controls="customerSidebar" aria-expanded="false"
                aria-label="Toggle account navigation">

                <span class="navbar-toggler-icon"></span>

            </button>

        </div>


        {{-- ==========================================================
            CUSTOMER PROFILE
        ========================================================== --}}

        <div class="px-6 px-xl-8 py-7 border-bottom text-center">

            <div class="mb-4">

                <img src="{{ auth()->user()->profile_photo ?: asset('assets/images/dashboard/avatar.png') }}"
                    alt="{{ auth()->user()->name ?: 'Customer' }}" class="rounded-circle object-fit-cover border"
                    width="72" height="72">

            </div>


            <h5 class="fs-16px fw-semibold mb-1">

                {{ auth()->user()->name ?: 'Customer' }}

            </h5>


            @if (auth()->user()->email)
                <p class="fs-13px text-muted mb-1 text-break">

                    {{ auth()->user()->email }}

                </p>
            @endif


            <span class="badge bg-body-tertiary text-body-emphasis px-3 py-2 fs-12px">

                Customer Account

            </span>

        </div>


        {{-- ==========================================================
            SIDEBAR NAVIGATION
        ========================================================== --}}

        <div class="collapse navbar-collapse bg-body position-relative z-index-5" id="customerSidebar">

            <ul class="list-group list-group-flush list-group-no-border w-100 p-6">


                {{-- Account Label --}}
                <li class="list-group-item border-0 bg-transparent px-4 pt-2 pb-3">

                    <span class="fs-12px text-uppercase text-muted fw-semibold letter-spacing-01">

                        My Account

                    </span>

                </li>


                {{-- ==================================================
                    DASHBOARD
                ================================================== --}}

                <li class="list-group-item px-0 py-0 sidebar-item mb-2 border-0">

                    <a href="{{ Route::has('home') ? route('home') : route('home') }}"
                        class="text-heading text-decoration-none lh-1 sidebar-link
                               py-4 px-5 d-flex align-items-center rounded
                               {{ request()->routeIs('home') ? 'active' : '' }}"
                        title="Dashboard">

                        <span
                            class="sidebar-item-icon w-40px d-inline-flex
                                   align-items-center text-muted">

                            <i class="fas fa-house"></i>

                        </span>

                        <span class="sidebar-item-text fs-14px fw-semibold">

                            Dashboard

                        </span>

                    </a>

                </li>


                {{-- ==================================================
                    MY ORDERS
                ================================================== --}}

                <li class="list-group-item px-0 py-0 sidebar-item mb-2 border-0">

                    <a href="{{ route('customer.orders.index') }}"
                        class="text-heading text-decoration-none lh-1 sidebar-link
                               py-4 px-5 d-flex align-items-center rounded
                               {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"
                        title="My Orders">

                        <span
                            class="sidebar-item-icon w-40px d-inline-flex
                                   align-items-center text-muted">

                            <i class="fas fa-bag-shopping"></i>

                        </span>

                        <span class="sidebar-item-text fs-14px fw-semibold">

                            My Orders

                        </span>

                    </a>

                </li>


                {{-- ==================================================
                    DELIVERY ADDRESSES
                ================================================== --}}

                @if (Route::has('customer.addresses.index'))
                    <li class="list-group-item px-0 py-0 sidebar-item mb-2 border-0">

                        <a href="{{ route('customer.addresses.index') }}"
                            class="text-heading text-decoration-none lh-1 sidebar-link
                                   py-4 px-5 d-flex align-items-center rounded
                                   {{ request()->routeIs('customer.addresses.*') ? 'active' : '' }}"
                            title="Delivery Addresses">

                            <span
                                class="sidebar-item-icon w-40px d-inline-flex
                                       align-items-center text-muted">

                                <i class="fas fa-location-dot"></i>

                            </span>

                            <span class="sidebar-item-text fs-14px fw-semibold">

                                Delivery Addresses

                            </span>

                        </a>

                    </li>
                @endif


                {{-- ==================================================
                    PROFILE
                ================================================== --}}

                @if (Route::has('customer.profile.index'))
                    <li class="list-group-item px-0 py-0 sidebar-item mb-2 border-0">

                        <a href="{{ route('customer.profile.index') }}"
                            class="text-heading text-decoration-none lh-1 sidebar-link
                                   py-4 px-5 d-flex align-items-center rounded
                                   {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}"
                            title="My Profile">

                            <span
                                class="sidebar-item-icon w-40px d-inline-flex
                                       align-items-center text-muted">

                                <i class="fas fa-user"></i>

                            </span>

                            <span class="sidebar-item-text fs-14px fw-semibold">

                                My Profile

                            </span>

                        </a>

                    </li>
                @endif


                {{-- ==================================================
                    SHOP
                ================================================== --}}

                <li class="list-group-item px-0 py-0 sidebar-item mb-2 border-0">

                    <a href="{{ route('shop') }}"
                        class="text-heading text-decoration-none lh-1 sidebar-link
                               py-4 px-5 d-flex align-items-center rounded"
                        title="Continue Shopping">

                        <span
                            class="sidebar-item-icon w-40px d-inline-flex
                                   align-items-center text-muted">

                            <i class="fas fa-store"></i>

                        </span>

                        <span class="sidebar-item-text fs-14px fw-semibold">

                            Continue Shopping

                        </span>

                    </a>

                </li>


                {{-- ==================================================
                    DIVIDER
                ================================================== --}}

                <li class="border-top my-4"></li>


                {{-- ==================================================
                    LOGOUT
                ================================================== --}}

                <li class="list-group-item px-0 py-0 sidebar-item border-0">

                    <form method="POST" action="{{ route('logout') }}">

                        @csrf


                        <button type="submit"
                            class="text-danger text-decoration-none lh-1 sidebar-link
                                   py-4 px-5 d-flex align-items-center rounded
                                   border-0 bg-transparent w-100 text-start">

                            <span
                                class="sidebar-item-icon w-40px d-inline-flex
                                       align-items-center">

                                <i class="fas fa-right-from-bracket"></i>

                            </span>

                            <span class="sidebar-item-text fs-14px fw-semibold">

                                Logout

                            </span>

                        </button>

                    </form>

                </li>

            </ul>

        </div>

    </div>

</aside>


<style>
    /*
    |--------------------------------------------------------------------------
    | Customer Sidebar
    |--------------------------------------------------------------------------
    */

    .dashboard-nav .sidebar-link {
        transition:
            background-color .2s ease,
            color .2s ease;
    }


    .dashboard-nav .sidebar-link:hover {
        background: var(--bs-tertiary-bg);
    }


    .dashboard-nav .sidebar-link.active {
        background: var(--bs-dark);
        color: #fff !important;
    }


    .dashboard-nav .sidebar-link.active .sidebar-item-icon {
        color: #fff !important;
    }


    .dashboard-nav .sidebar-item-icon {
        transition: color .2s ease;
    }


    .dashboard-nav .navbar-brand img {
        object-fit: contain;
    }


    @media (min-width: 1200px) {

        .dashboard-nav .sticky-area {
            min-height: 100vh;
        }

    }
</style>
