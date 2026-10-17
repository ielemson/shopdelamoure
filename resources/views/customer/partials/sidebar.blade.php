@php
    $customer = auth()->user();

    $accountLinks = [
        [
            'route' => 'home',
            'pattern' => 'home',
            'label' => 'Dashboard',
            'icon' => 'fa-home',
        ],
        [
            'route' => 'customer.orders.index',
            'pattern' => 'customer.orders.*',
            'label' => 'My Orders',
            'icon' => 'fa-box',
        ],
        [
            'route' => 'customer.addresses.index',
            'pattern' => 'customer.addresses.*',
            'label' => 'Delivery Addresses',
            'icon' => 'fa-map-marker-alt',
        ],
        [
            'route' => 'customer.profile.index',
            'pattern' => 'customer.profile.*',
            'label' => 'My Profile',
            'icon' => 'fa-user',
        ],
    ];
@endphp

<aside class="customer-sidebar navbar navbar-expand-xl navbar-light d-block px-0 header-sticky dashboard-nav py-0"
    aria-label="Customer account navigation">

    <div class="sticky-area border-end bg-body">

        {{-- Logo and mobile menu --}}
        <div class="d-flex align-items-center justify-content-between gap-3 px-6 px-xl-8 py-5 border-bottom">
            <a href="{{ route('shop') }}" class="navbar-brand m-0 py-2">
                <img src="{{ asset('assets/images/others/logo-trans.png') }}" width="160" height="45" alt="Dela Moure"
                    class="img-fluid">
            </a>

            <button class="navbar-toggler d-xl-none" type="button" data-bs-toggle="collapse"
                data-bs-target="#customerSidebar" aria-controls="customerSidebar" aria-expanded="false"
                aria-label="Toggle account menu">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse position-relative z-index-5" id="customerSidebar">

            <div class="w-100">

                {{-- Customer details --}}
                <div class="customer-sidebar-profile d-flex align-items-center gap-3 mx-6 mt-5 mb-4 p-4">
                    <img src="{{ $customer?->profile_photo ?: asset('assets/images/dashboard/avatar.png') }}"
                        alt="" class="customer-sidebar-avatar rounded-circle flex-shrink-0" width="48"
                        height="48">

                    <div class="customer-sidebar-identity">
                        <span class="d-block small text-muted">Welcome back</span>

                        <strong class="d-block text-break">
                            {{ $customer?->name ?: 'Customer' }}
                        </strong>
                    </div>
                </div>

                {{-- Account links --}}
                <ul class="list-unstyled px-6 pb-4 mb-0">
                    <li class="px-4 pt-2 pb-3">
                        <span class="customer-sidebar-label text-muted fw-semibold">
                            My Account
                        </span>
                    </li>

                    @foreach ($accountLinks as $link)
                        @if (Route::has($link['route']))
                            @php
                                $isActive = request()->routeIs($link['pattern']);
                            @endphp

                            <li class="mb-2">
                                <a href="{{ route($link['route']) }}"
                                    class="customer-account-link {{ $isActive ? 'active' : '' }}"
                                    @if ($isActive) aria-current="page" @endif>

                                    <i class="fas {{ $link['icon'] }} customer-account-icon" aria-hidden="true"></i>

                                    <span>{{ $link['label'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach

                    <li class="mt-4 pt-4 border-top">
                        <a href="{{ route('shop') }}" class="customer-account-link">
                            <i class="fas fa-shopping-bag customer-account-icon" aria-hidden="true"></i>
                            <span>Continue Shopping</span>
                        </a>
                    </li>

                    <li class="mt-2">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf

                            <button type="submit"
                                class="customer-account-link customer-account-logout w-100 text-start">
                                <i class="fas fa-sign-out-alt customer-account-icon" aria-hidden="true"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>
                </ul>

            </div>
        </div>
    </div>
</aside>

<style>
    .customer-sidebar .navbar-brand img {
        display: block;
        object-fit: contain;
    }

    .customer-sidebar .navbar-toggler {
        padding: .5rem;
        border: 1px solid var(--bs-border-color);
        border-radius: .5rem;
    }

    .customer-sidebar .customer-sidebar-profile {
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: .75rem;
    }

    .customer-sidebar .customer-sidebar-avatar {
        object-fit: cover;
        border: 2px solid #EBC584;
    }

    .customer-sidebar .customer-sidebar-identity {
        min-width: 0;
    }

    .customer-sidebar .customer-sidebar-identity strong {
        color: var(--bs-heading-color);
        line-height: 1.4;
    }

    .customer-sidebar .customer-sidebar-label {
        font-size: .7rem;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .customer-sidebar .customer-account-link {
        display: flex;
        align-items: center;
        gap: .875rem;
        min-height: 48px;
        padding: .875rem 1rem;
        border: 1px solid transparent;
        border-radius: .625rem;
        background: transparent;
        color: var(--bs-body-color);
        font: inherit;
        font-size: .9rem;
        font-weight: 600;
        line-height: 1.4;
        text-decoration: none;
        transition: background-color .2s ease, color .2s ease;
    }

    .customer-sidebar .customer-account-icon {
        width: 1.25rem;
        flex-shrink: 0;
        text-align: center;
    }

    .customer-sidebar .customer-account-link:hover {
        background: var(--bs-secondary-bg);
        color: var(--bs-primary);
    }

    .customer-sidebar .customer-account-link.active {
        background: #F6EAD6;
        border-color: #DEC79F;
        color: #654621;
    }

    .customer-sidebar .customer-account-logout {
        color: var(--bs-danger);
    }

    .customer-sidebar .customer-account-logout:hover {
        background: var(--bs-danger-bg-subtle);
        color: var(--bs-danger-text);
    }

    .customer-sidebar .customer-account-link:focus-visible,
    .customer-sidebar .navbar-brand:focus-visible,
    .customer-sidebar .navbar-toggler:focus-visible {
        outline: 2px solid var(--bs-primary);
        outline-offset: 3px;
        box-shadow: none;
    }

    @media (min-width: 1200px) {
        .customer-sidebar .sticky-area {
            min-height: 100vh;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .customer-sidebar .customer-account-link {
            transition: none;
        }
    }
</style>
