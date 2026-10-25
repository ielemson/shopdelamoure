@php
    $admin = auth()->user();

    $fullName = trim(($admin?->first_name ?? '') . ' ' . ($admin?->last_name ?? ''));

    $adminName = trim($admin?->name ?? '') ?: ($fullName ?: 'Administrator');
    $adminEmail = $admin?->email ?? '';

    $initials = collect(preg_split('/\s+/', $adminName))
        ->filter()
        ->take(2)
        ->map(fn($name) => mb_strtoupper(mb_substr($name, 0, 1)))
        ->implode('');

    $headerLinks = [
        [
            'label' => 'Dashboard',
            'route' => 'home',
            'icon' => 'icofont-dashboard',
            'permission' => 'manage store',
        ],
        [
            'label' => 'Admin Management',
            'route' => 'admin.admins.index',
            'icon' => 'icofont-users-alt-5',
            'permission' => 'manage admins',
        ],
        [
            'label' => 'Role Management',
            'route' => 'admin.roles.index',
            'icon' => 'icofont-lock',
            'permission' => 'manage admins',
        ],
        [
            'label' => 'Website Settings',
            'route' => 'admin.website.settings',
            'icon' => 'icofont-gear',
            'permission' => 'manage website settings',
        ],
        [
            'label' => 'Delivery Settings',
            'route' => 'admin.delivery.settings',
            'icon' => 'icofont-truck-alt',
            'permission' => 'manage delivery settings',
        ],
        [
            'label' => 'Local Delivery Rates',
            'route' => 'admin.shipping-rates.index',
            'icon' => 'icofont-location-pin',
            'permission' => 'manage delivery settings',
        ],
        [
            'label' => 'Packaging & Quote Tests',
            'route' => 'admin.shipping-packages.index',
            'icon' => 'icofont-box',
            'permission' => 'manage delivery settings',
        ],
        [
            'label' => 'Terminal Checkout',
            'route' => 'admin.terminal-checkout.edit',
            'icon' => 'icofont-truck-alt',
            'permission' => 'manage delivery settings',
        ],
    ];
@endphp

<div class="header">
    <nav class="navbar py-3" aria-label="Admin header">
        <div class="container-xxl d-flex align-items-center justify-content-between">

            {{-- Sidebar toggle --}}
            <button class="navbar-toggler p-0 border-0 menu-toggle" type="button" aria-label="Toggle sidebar">
                <span class="fa fa-bars" aria-hidden="true"></span>
            </button>

            {{-- Header actions --}}
            <div class="h-right d-flex align-items-center gap-3 ms-auto">

                {{-- Storefront --}}
                <a class="nav-link text-primary p-2" href="{{ route('index') }}" target="_blank"
                    rel="noopener noreferrer" title="View Store" aria-label="View store in a new tab">
                    <i class="icofont-home fs-5" aria-hidden="true"></i>
                </a>

                {{-- User menu --}}
                <div class="dropdown user-profile d-flex align-items-center">

                    <div class="u-info me-3 d-none d-md-block text-end">
                        <p class="mb-0 lh-sm">
                            <span class="fw-semibold">{{ $adminName }}</span>
                        </p>

                        <small class="text-muted">Administrator</small>
                    </div>

                    <button class="nav-link dropdown-toggle border-0 bg-transparent p-0" type="button"
                        id="adminAccountMenu" data-bs-toggle="dropdown" aria-expanded="false"
                        aria-label="Open administrator menu">
                        @if (!empty($admin?->profile_photo))
                            <img class="avatar lg rounded-circle img-thumbnail" src="{{ asset($admin->profile_photo) }}"
                                alt="{{ $adminName }}" style="object-fit: cover;">
                        @else
                            <span
                                class="avatar lg rounded-circle img-thumbnail d-inline-flex align-items-center justify-content-center bg-primary text-white fw-bold"
                                aria-hidden="true">
                                {{ $initials }}
                            </span>
                        @endif
                    </button>

                    <div class="dropdown-menu dropdown-menu-end rounded-lg shadow border-0 p-0 mt-2"
                        aria-labelledby="adminAccountMenu">
                        <div class="card border-0 w280">

                            <div class="card-body border-bottom">
                                <p class="mb-1 fw-semibold">{{ $adminName }}</p>

                                <small class="text-muted text-break">
                                    {{ $adminEmail }}
                                </small>
                            </div>

                            <div class="list-group m-2">

                                @foreach ($headerLinks as $link)
                                    @can($link['permission'])
                                        <a href="{{ route($link['route']) }}"
                                            class="list-group-item list-group-item-action border-0">
                                            <i class="{{ $link['icon'] }} fs-5 me-3" aria-hidden="true"></i>
                                            {{ $link['label'] }}
                                        </a>
                                    @endcan
                                @endforeach

                                @auth
                                    <form action="{{ route('logout') }}" method="POST" class="border-top mt-2 pt-2">
                                        @csrf

                                        <button type="submit"
                                            class="list-group-item list-group-item-action border-0 text-danger w-100 text-start">
                                            <i class="icofont-logout fs-5 me-3" aria-hidden="true"></i>
                                            Sign Out
                                        </button>
                                    </form>
                                @endauth

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </nav>
</div>
