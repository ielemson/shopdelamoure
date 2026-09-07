<aside class="navbar navbar-expand-xl navbar-light d-block px-0 header-sticky dashboard-nav py-0">
    <div class="sticky-area border-end">

        {{-- Sidebar Header --}}
        <div class="d-flex px-6 px-xl-8 w-100 border-bottom py-7 justify-content-between align-items-center">

            <a href="{{ route('home') }}" class="navbar-brand py-3">
                <img class="light-mode-img" src="{{ asset('assets/images/others/logo.png') }}" width="179" height="26"
                    alt="Delamoure">

                <img class="dark-mode-img" src="{{ asset('assets/images/others/logo-white.png') }}" width="179"
                    height="26" alt="Delamoure">
            </a>

            {{-- Mobile Toggle --}}
            <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#customerSidebar" aria-controls="customerSidebar" aria-expanded="false"
                aria-label="Toggle account navigation">

                <span class="navbar-toggler-icon"></span>
            </button>

        </div>


        {{-- Customer Profile --}}
        <div class="px-6 px-xl-8 py-7 border-bottom text-center">

            <div class="mb-4">
                <img src="{{ auth()->user()->profile_photo ?? asset('assets/images/user/default-user.jpg') }}"
                    alt="{{ auth()->user()->name ?? 'Customer' }}" class="rounded-circle object-fit-cover"
                    width="72" height="72">
            </div>

            <h5 class="fs-16px fw-semibold mb-1">
                {{ auth()->user()->name ?? 'Customer' }}
            </h5>

            <p class="fs-13px text-muted mb-0">
                Welcome to your account
            </p>

        </div>


        {{-- Sidebar Navigation --}}
        <div class="collapse navbar-collapse bg-body position-relative z-index-5" id="customerSidebar">

            <ul class="list-group list-group-flush list-group-no-border w-100 p-6">


                {{-- Dashboard --}}
                <li class="list-group-item px-0 py-0 sidebar-item mb-3 border-0">

                    <a href="{{ route('home') }}"
                        class="text-heading text-decoration-none lh-1 sidebar-link
                               py-5 px-6 d-flex align-items-center
                               {{ request()->routeIs('home') ? 'active' : '' }}"
                        title="Dashboard">

                        <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                            <i class="fas fa-home-lg-alt"></i>
                        </span>

                        <span class="sidebar-item-text fs-14px fw-semibold">
                            Dashboard
                        </span>

                    </a>

                </li>


                {{-- Orders --}}
                <li class="list-group-item px-0 py-0 sidebar-item mb-3 border-0">

                    <a href="{{ route('customer.orders.index') }}"
                        class="text-heading text-decoration-none lh-1 sidebar-link
                               py-5 px-6 d-flex align-items-center
                               {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"
                        title="My Orders">

                        <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                            <i class="fas fa-shopping-bag"></i>
                        </span>

                        <span class="sidebar-item-text fs-14px fw-semibold">
                            My Orders
                        </span>

                    </a>

                </li>


                {{-- Delivery Addresses --}}
                <li class="list-group-item px-0 py-0 sidebar-item mb-3 border-0">

                    <a href="{{ route('customer.addresses.index') }}"
                        class="text-heading text-decoration-none lh-1 sidebar-link
                               py-5 px-6 d-flex align-items-center
                               {{ request()->routeIs('customer.addresses.*') ? 'active' : '' }}"
                        title="Delivery Addresses">

                        <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>

                        <span class="sidebar-item-text fs-14px fw-semibold">
                            Delivery Addresses
                        </span>

                    </a>

                </li>


                {{-- Profile --}}
                <li class="list-group-item px-0 py-0 sidebar-item mb-3 border-0">

                    <a href=""
                        class="text-heading text-decoration-none lh-1 sidebar-link
                               py-5 px-6 d-flex align-items-center
                               {{ request()->routeIs('customer.profile.*') ? 'active' : '' }}"
                        title="My Profile">

                        <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                            <i class="fas fa-user"></i>
                        </span>

                        <span class="sidebar-item-text fs-14px fw-semibold">
                            My Profile
                        </span>

                    </a>

                </li>


                {{-- Divider --}}
                <li class="border-top my-4"></li>


                {{-- Logout --}}
                <li class="list-group-item px-0 py-0 sidebar-item border-0">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                            class="text-heading text-decoration-none lh-1 sidebar-link
                                   py-5 px-6 d-flex align-items-center
                                   border-0 bg-transparent w-100 text-start">

                            <span class="sidebar-item-icon w-40px d-inline-block text-muted">
                                <i class="fas fa-sign-out-alt"></i>
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
