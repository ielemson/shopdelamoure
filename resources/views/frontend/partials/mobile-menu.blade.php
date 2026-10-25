@once('delamoure-mobile-navigation')
    @php
        if (!isset($delamoureMenu)) {
            $headerCategories =
                $headerCategories ?? \App\Models\Category::query()->where('status', 1)->orderBy('name')->get();
            $findMenuCategory = fn(array $names) => $headerCategories->first(
                fn($category) => in_array(strtolower(trim($category->name)), $names, true),
            );
            $diffusersCategory = $findMenuCategory(['diffusers', 'diffuser', 'reed diffusers', 'reed diffuser']);
            $toiletCategory = $findMenuCategory(['toilet care']);
            $homeFragranceCategories = $headerCategories->filter(
                fn($category) => in_array(
                    strtolower(trim($category->name)),
                    [
                        'air fresheners',
                        'air freshener',
                        'room sprays',
                        'room spray',
                        'scented candles',
                        'scented candle',
                        'candles',
                        'candle',
                        'essential oils',
                        'essential oil',
                        'scented crystal stones',
                        'scented crystal stone',
                        'scented stones',
                    ],
                    true,
                ),
            );
            $categoryIsActive = fn($category) => request()->routeIs('shop.category') &&
                (string) request()->route('slug') === (string) $category->slug;
            $wholesaleActive = request()->routeIs('wholesale.*') || request()->is('shop/wholesale', 'shop/wholesale/*');
            $menuCategoryLinks = fn($categories) => $categories
                ->map(
                    fn($category) => [
                        'label' => $category->name,
                        'url' => route('shop.category', $category->slug),
                        'active' => $categoryIsActive($category),
                    ],
                )
                ->values()
                ->all();

            // One menu definition, used by both desktop and mobile.
            $delamoureMenu = [
                ['label' => 'Home', 'url' => url('/'), 'active' => request()->is('/'), 'children' => []],
                [
                    'label' => 'Shop',
                    'url' => route('shop'),
                    'active' => !$wholesaleActive && request()->routeIs('shop', 'shop.category'),
                    'children' => array_merge(
                        [
                            [
                                'label' => 'Shop All Products',
                                'url' => route('shop'),
                                'active' => request()->routeIs('shop') && !$wholesaleActive,
                            ],
                        ],
                        $menuCategoryLinks($headerCategories),
                    ),
                ],
            ];
            if ($diffusersCategory) {
                $delamoureMenu[] = [
                    'label' => 'Diffusers',
                    'url' => route('shop.category', $diffusersCategory->slug),
                    'active' => $categoryIsActive($diffusersCategory),
                    'children' => [],
                ];
            }
            if ($homeFragranceCategories->isNotEmpty()) {
                $delamoureMenu[] = [
                    'label' => 'Home Fragrance',
                    'url' => '#',
                    'active' => $homeFragranceCategories->contains($categoryIsActive),
                    'children' => $menuCategoryLinks($homeFragranceCategories),
                ];
            }
            if ($toiletCategory) {
                $delamoureMenu[] = [
                    'label' => 'Toilet Care',
                    'url' => route('shop.category', $toiletCategory->slug),
                    'active' => $categoryIsActive($toiletCategory),
                    'children' => [],
                ];
            }
            $delamoureMenu[] = [
                'label' => 'Wholesale',
                'url' => url('shop/wholesale'),
                'active' => $wholesaleActive,
                'children' => [],
            ];
            $delamoureMenu[] = [
                'label' => 'About Us',
                'url' => route('about'),
                'active' => request()->routeIs('about'),
                'children' => [],
            ];
            $delamoureMenu[] = [
                'label' => 'Contact',
                'url' => route('contact'),
                'active' => request()->routeIs('contact'),
                'children' => [],
            ];
        }
        $mobileWishlistCount = auth()->check()
            ? \App\Models\Wishlist::where('user_id', auth()->id())->count()
            : collect(session()->get('wishlist', []))
                ->unique()
                ->count();
    @endphp

    <div class="navbar">
        <div id="offCanvasNavBar" class="offcanvas offcanvas-start" tabindex="-1" aria-labelledby="mobile-menu-title"
            style="--bs-offcanvas-width: min(340px, 90vw)">
            <div class="offcanvas-header bg-body-tertiary">
                <h3 id="mobile-menu-title" class="offcanvas-title text-uppercase fs-5">De Lamoure</h3>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close navigation"></button>
            </div>
            <hr class="mt-0">
            <div class="offcanvas-body pt-0 mb-2">
                <nav aria-label="Mobile navigation">
                    <ul class="navbar-nav">
                        @foreach ($delamoureMenu as $menuItem)
                            <li
                                class="nav-item transition-all-xl-1 py-0 {{ !empty($menuItem['children']) ? 'dropdown' : '' }}">
                                <a class="nav-link d-flex justify-content-between position-relative text-uppercase fw-semibold ls-1 fs-15px {{ $menuItem['active'] ? 'active' : '' }} {{ !empty($menuItem['children']) ? 'dropdown-toggle' : '' }}"
                                    href="{{ $menuItem['url'] }}"
                                    @if (!empty($menuItem['children'])) data-bs-toggle="dropdown" id="dl-mobile-menu-{{ $loop->index }}"
                                    aria-haspopup="true" aria-expanded="false"
                                @elseif ($menuItem['active'])
                                    aria-current="page" @endif>
                                    {{ $menuItem['label'] }}
                                </a>
                                @if (!empty($menuItem['children']))
                                    <ul class="dropdown-menu py-6" aria-labelledby="dl-mobile-menu-{{ $loop->index }}">
                                        @foreach ($menuItem['children'] as $child)
                                            <li><a class="dropdown-item pe-6 border-hover {{ $child['active'] ? 'active' : '' }}"
                                                    href="{{ $child['url'] }}">
                                                    <span class="border-hover-target">{{ $child['label'] }}</span>
                                                </a></li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>
                <hr class="my-6">
                <a class="nav-link py-3" href="{{ auth()->check() ? route('home') : route('login') }}">
                    {{ auth()->check() ? 'My Account' : 'Sign In' }}
                </a>
                <a class="nav-link d-flex justify-content-between py-3" href="{{ route('wishlist.index') }}">
                    Wishlist <span
                        class="wishlist-count {{ $mobileWishlistCount < 1 ? 'd-none' : '' }}">{{ $mobileWishlistCount }}</span>
                </a>
            </div>
            <hr class="mb-0">
            <div class="offcanvas-footer bg-body-tertiary p-6 fs-13px">
                © {{ now()->year }} De Lamoure.<br>All rights reserved.
            </div>
        </div>
    </div>
@endonce
