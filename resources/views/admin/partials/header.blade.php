    @php
        $admin = auth()->user();
        $adminName = $admin?->name ?? 'Admin User';
        $adminEmail = $admin?->email ?? 'admin@example.com';

        $initials = collect(explode(' ', $adminName))
            ->map(fn($name) => strtoupper(substr($name, 0, 1)))
            ->take(2)
            ->implode('');
    @endphp

    <div class="header">
        <nav class="navbar py-4">
            <div class="container-xxl">

                <!-- Header Right -->
                <div class="h-right d-flex align-items-center mr-5 mr-lg-0 order-1">

                    <!-- Help -->
                    <div class="d-flex">
                        <a class="nav-link text-primary collapsed" href="{{ route("index") }}" title="Go Home"  target="_blank">
                         <i class="icofont-home fs-5"></i>
                        </a>
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown notifications">
                        <a class="nav-link dropdown-toggle pulse" href="javascript:;" role="button"
                            data-bs-toggle="dropdown">
                            <i class="icofont-alarm fs-5"></i>
                        </a>

                        <div
                            class="dropdown-menu rounded-lg shadow border-0 dropdown-animation dropdown-menu-md-end p-0 m-0 mt-3">
                            <div class="card border-0 w380">
                                <div class="card-header border-0 p-3">
                                    <h5 class="mb-0 font-weight-light d-flex justify-content-between">
                                        <span>Notifications</span>
                                        <span class="badge bg-secondary text-white">0</span>
                                    </h5>
                                </div>

                                <div class="card-body text-center py-4">
                                    <i class="icofont-notification fs-3 text-muted"></i>
                                    <p class="mb-0 mt-2 text-muted">No notifications yet</p>
                                </div>

                                <a class="card-footer text-center border-top-0" href="#">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- User Profile -->
                    <div class="dropdown user-profile ml-2 ml-sm-3 d-flex align-items-center zindex-popover">
                        <div class="u-info me-2">
                            <p class="mb-0 text-end line-height-sm">
                                <span class="font-weight-bold">{{ $adminName }}</span>
                            </p>
                            <small>Administrator</small>
                        </div>

                        <a class="nav-link dropdown-toggle pulse p-0" href="#" role="button"
                            data-bs-toggle="dropdown" data-bs-display="static">

                            @if (!empty($admin?->profile_photo))
                                <img class="avatar lg rounded-circle img-thumbnail"
                                    src="{{ asset($admin->profile_photo) }}" alt="{{ $adminName }}">
                            @else
                                <div
                                    class="avatar lg rounded-circle img-thumbnail d-flex align-items-center justify-content-center bg-primary text-white fw-bold">
                                    {{ $initials }}
                                </div>
                            @endif
                        </a>

                        <div
                            class="dropdown-menu rounded-lg shadow border-0 dropdown-animation dropdown-menu-end p-0 m-0">
                            <div class="card border-0 w280">
                                <div class="card-body pb-0">
                                    <div class="d-flex py-1">
                                        @if (!empty($admin?->profile_photo))
                                            <img class="avatar rounded-circle" src="{{ asset($admin->profile_photo) }}"
                                                alt="{{ $adminName }}">
                                        @else
                                            <div
                                                class="avatar rounded-circle d-flex align-items-center justify-content-center bg-primary text-white fw-bold">
                                                {{ $initials }}
                                            </div>
                                        @endif

                                        <div class="flex-fill ms-3">
                                            <p class="mb-0">
                                                <span class="font-weight-bold">{{ $adminName }}</span>
                                            </p>
                                            <small>{{ $adminEmail }}</small>
                                        </div>
                                    </div>

                                    <hr class="dropdown-divider border-dark">
                                </div>

                                <div class="list-group m-2">
                                    <a href="#" class="list-group-item list-group-item-action border-0">
                                        <i class="icofont-ui-user fs-5 me-3"></i>Profile
                                    </a>

                                    <a href="#" class="list-group-item list-group-item-action border-0">
                                        <i class="icofont-gear fs-5 me-3"></i>Account Settings
                                    </a>

                                    <a href="{{ route('logout') }}"
                                        class="list-group-item list-group-item-action border-0"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="icofont-logout fs-5 me-3"></i>Sign Out
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="setting ms-2">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#Settingmodal">
                            <i class="icofont-gear-alt fs-5"></i>
                        </a>
                    </div>
                </div>

                <!-- Menu Toggler -->
                <button class="navbar-toggler p-0 border-0 menu-toggle order-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainHeader">
                    <span class="fa fa-bars"></span>
                </button>

                <!-- Search -->
                <div class="order-0 col-lg-4 col-md-4 col-sm-12 col-12 mb-3 mb-md-0">
                    <div class="input-group flex-nowrap input-group-lg">
                        <input type="search" class="form-control" placeholder="Search dashboard..."
                            aria-label="search">
                        <button type="button" class="input-group-text">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>

            </div>
        </nav>
    </div>
