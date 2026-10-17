<header class="main-header bg-body border-bottom position-relative d-none d-xl-block px-10 py-4">
    <div class="container-fluid">
        <nav class="d-flex align-items-center justify-content-between gap-4" aria-label="Customer navigation">

            <div>
                <h6 class="mb-1 fw-semibold">My Account</h6>
                <small class="text-muted">
                    Manage your profile and orders.
                </small>
            </div>

            <div class="d-flex align-items-center gap-4">
                <a href="{{ route('shop') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-shopping-bag me-2" aria-hidden="true"></i>
                    Visit Store
                </a>

                <div class="dropdown">
                    <button type="button" id="customerAccountMenu"
                        class="btn btn-link dropdown-toggle d-flex align-items-center gap-3 p-0 text-body text-decoration-none"
                        data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open customer account menu">

                        <img src="{{ asset('assets/images/dashboard/avatar.png') }}" alt=""
                            class="rounded-circle" width="40" height="40">

                        <span class="fw-semibold">
                            {{ auth()->user()->name ?? 'Customer' }}
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="customerAccountMenu">

                        <div class="px-4 py-3 border-bottom">
                            <strong class="d-block">
                                {{ auth()->user()->name ?? 'Customer' }}
                            </strong>
                            <small class="text-muted text-break">
                                {{ auth()->user()->email ?? '' }}
                            </small>
                        </div>

                        <a class="dropdown-item py-2" href="{{ route('home') }}">
                            <i class="fas fa-home me-2" aria-hidden="true"></i>
                            Dashboard
                        </a>

                        <a class="dropdown-item py-2" href="{{ route('customer.profile.index') }}">
                            <i class="fas fa-user me-2" aria-hidden="true"></i>
                            My Profile
                        </a>

                        <a class="dropdown-item py-2" href="{{ route('customer.orders.index') }}">
                            <i class="fas fa-box me-2" aria-hidden="true"></i>
                            My Orders
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
