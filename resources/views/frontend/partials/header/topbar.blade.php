  <header class="ec-header">
        <!--Ec Header Top Start -->
        <div class="header-top">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Header Top social Start -->
                    <div class="col text-left header-top-left d-none d-lg-block">
                        <div class="header-top-social">
                            <ul class="mb-0">
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-facebook"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-twitter"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-youtube-play"></i></a>
                                </li>
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-vimeo"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-instagram"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Header Top social End -->
                    <!-- Header Top Message Start -->
                    <div class="col text-center header-top-center">
                        <div class="header-top-message">
                            World's Fastest Online Shopping Destination
                        </div>
                    </div>
                    <!-- Header Top Message End -->
                    <!-- Header Top Language Currency -->
                    <div class="col header-top-right d-none d-lg-block">
                        <div class="header-top-right-inner d-flex justify-content-end">
                            <!-- Language Start -->
                            <div class="header-top-lan-curr header-top-lan dropdown">
                                <button class="dropdown-toggle" data-bs-toggle="dropdown">English <i
                                        class="fas fa-angle-down" aria-hidden="true"></i></button>
                                <ul class="dropdown-menu">
                                    <li class="active"><a class="dropdown-item" href="#">English</a></li>
                                    <li><a class="dropdown-item" href="#">Italiano</a></li>
                                </ul>
                            </div>
                            <!-- Language End -->
                            <!-- Currency Start -->
                            <div class="header-top-lan-curr header-top-curr dropdown">
                                <button class="dropdown-toggle" data-bs-toggle="dropdown">Dollar <i
                                        class="fas fa-angle-down" aria-hidden="true"></i></button>
                                <ul class="dropdown-menu">
                                    <li class="active"><a class="dropdown-item" href="#">USD $</a></li>
                                    <li><a class="dropdown-item" href="#">EUR €</a></li>
                                </ul>
                            </div>
                            <!-- Currency End -->

                        </div>
                    </div>
                    <!-- Header Top Language Currency -->
                    <!-- Header Top responsive Action -->
                    <div class="col header-top-res d-lg-none">
                        <div class="ec-header-bottons">
                            <!-- Header User Start -->
                            <a href="login.html" class="ec-header-btn ec-header-user">
                                <div class="header-icon"><i class="fi-rr-user"></i></div>
                            </a>
                            <!-- Header User End -->
                            <!-- Header Cart Start -->
                            <a href="#" class="ec-header-btn ec-header-wishlist">
                                <div class="header-icon"><i class="fi-rr-heart"></i></div>
                                <span class="ec-header-count ec-wishlist-count">0</span>
                            </a>
                            <!-- Header Cart End -->
                            <!-- Header Cart Start -->
                            <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
                                <div class="header-icon"><i class="fi-rr-shopping-basket"></i></div>
                                <span class="ec-header-count ec-cart-count">0</span>
                            </a>
                            <!-- Header Cart End -->
                            <!-- Header menu Start -->
                            <a href="#ec-mobile-menu" class="ec-header-btn ec-side-toggle d-lg-none">
                                <i class="ecicon eci-bars"></i>
                            </a>
                            <!-- Header menu End -->
                        </div>
                    </div>
                    <!-- Header Top responsive Action -->
                </div>
            </div>
        </div>
        <!-- Ec Header Top  End -->
        <!-- Ec Header Bottom  Start -->
        <div class="ec-header-bottom d-none d-lg-block">
            <div class="container position-relative">
                <div class="row">
                    <div class="ec-flex">
                        <!-- Ec Header Logo Start -->
                        <div class="align-self-center ec-header-logo">
                            <div class="header-logo">
                                <a href="{{route("index")}}"><img src="assets/images/logo/logo-4.png" alt="Site Logo" /><img
                                        class="dark-logo" src="assets/images/logo/dark-logo-4.png" alt="Site Logo"
                                        style="display: none;" /></a>
                            </div>
                        </div>
                        <!-- Ec Header Logo End -->

                        <!-- Ec Header Search Start -->
                        <div class="align-self-center ec-header-search">
                            <div class="header-search">
                                <form class="ec-search-group-form" action="#">
                                    <div class="ec-search-select-inner">
                                        <select name="ec-search-cat" id="ec-search-cat">
                                            <option selected disabled>Category</option>
                                            <option value="cloths">Cloths</option>
                                            <option value="bag">Bag</option>
                                            <option value="shoes">Shoes</option>
                                        </select>
                                    </div>
                                    <input class="form-control" placeholder="Search Your Products..." type="text">
                                    <button class="search_submit" type="submit"><i class="fi-rr-search"></i></button>
                                </form>
                            </div>
                        </div>
                        <!-- Ec Header Search End -->

                        <!-- Ec Header Button Start -->
                        <div class="align-self-center">
                            <div class="ec-header-bottons">
                                <!-- Header User Start -->
                                <a href="login.html" class="ec-header-btn ec-header-user">
                                    <div class="header-icon"><i class="fi-rr-user"></i></div>
                                    <div class="ec-btn-desc">
                                        <span class="ec-btn-title">Account</span>
                                        <span class="ec-btn-stitle">Login</span>
                                    </div>
                                </a>
                                <!-- Header User End -->
                                <!-- Header wishlist Start -->
                                <a href="#" class="ec-header-btn ec-header-wishlist">
                                    <div class="header-icon"><i class="fi-rr-heart"></i></div>
                                    <div class="ec-btn-desc">
                                        <span class="ec-btn-title">Wishlist</span>
                                        <span class="ec-btn-stitle"><b class="ec-wishlist-count">0</b>-items</span>
                                    </div>
                                </a>
                                <!-- Header wishlist End -->
                                <!-- Header Cart Start -->
                                <a href="#ec-side-cart" class="ec-header-btn ec-side-toggle">
                                    <div class="header-icon"><i class="fi-rr-shopping-basket"></i></div>
                                    <div class="ec-btn-desc">
                                        <span class="ec-btn-title">Cart</span>
                                        <span class="ec-btn-stitle"><b class="ec-cart-count">0</b>-items</span>
                                    </div>
                                </a>
                                <!-- Header Cart End -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div id="ec-mobile-menu" class="ec-side-cart ec-mobile-menu">
            <div class="ec-menu-title">
                <span class="menu_title">My Menu</span>
                <button class="ec-close">×</button>
            </div>
            <div class="ec-menu-inner">
                <div class="ec-menu-content">
                   <ul>
    
    <li>
        <a href="/">Home</a>
    </li>

    <li>
        <a href="/shop">Shop</a>

        <ul class="sub-menu">

            <li><a href="/category/electronics">Electronics</a></li>
            <li><a href="/category/fashion">Fashion</a></li>
            <li><a href="/category/home-living">Home & Living</a></li>
            <li><a href="/category/kitchen">Kitchen Essentials</a></li>
            <li><a href="/category/beauty">Beauty & Personal Care</a></li>
            <li><a href="/category/mannequins">Mannequins</a></li>
            <li><a href="/category/health">Health & Wellness</a></li>
            <li><a href="/new-arrivals">New Arrivals</a></li>
            <li><a href="/best-sellers">Best Sellers</a></li>

        </ul>
    </li>

    <li>
        <a href="/offers">Special Offers</a>
    </li>

    <li>
        <a href="/track-order">Track Order</a>
    </li>

    <li>
        <a href="/wishlist">Wishlist</a>
    </li>

    <li>
        <a href="/about-us">About Us</a>
    </li>

    <li>
        <a href="/contact-us">Contact Us</a>
    </li>

    <li>
        <a href="/faq">FAQs</a>
    </li>

    <li>
        <a href="/account">My Account</a>

        <ul class="sub-menu">

            <li><a href="/login">Login</a></li>
            <li><a href="/register">Register</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><a href="/orders">My Orders</a></li>
            <li><a href="/cart">Cart</a></li>
            <li><a href="/checkout">Checkout</a></li>

        </ul>
    </li>

</ul>
                </div>
                <div class="header-res-lan-curr">
                    <div class="header-top-lan-curr">
                        <!-- Language Start -->
                        <div class="header-top-lan dropdown">
                            <button class="dropdown-toggle text-upper" data-bs-toggle="dropdown">Language <i
                                    class="ecicon eci-caret-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li class="active"><a class="dropdown-item" href="#">English</a></li>
                                <li><a class="dropdown-item" href="#">Italiano</a></li>
                            </ul>
                        </div>
                        <!-- Language End -->
                        <!-- Currency Start -->
                        <div class="header-top-curr dropdown">
                            <button class="dropdown-toggle text-upper" data-bs-toggle="dropdown">Currency <i
                                    class="ecicon eci-caret-down" aria-hidden="true"></i></button>
                            <ul class="dropdown-menu">
                                <li class="active"><a class="dropdown-item" href="#">USD $</a></li>
                                <li><a class="dropdown-item" href="#">EUR €</a></li>
                            </ul>
                        </div>
                        <!-- Currency End -->
                    </div>
                    <!-- Social Start -->
                    <div class="header-res-social">
                        <div class="header-top-social">
                            <ul class="mb-0">
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-facebook"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-twitter"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-instagram"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="ecicon eci-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Social End -->
                </div>
            </div>
        </div>
       
        <!-- Ec Header Button End -->
        <div class="ec-header-cat d-none d-lg-block">
            <div class="container position-relative">
                <div class="row ">
                    <div class="col ec-category-block">
                        <div class="ec-category-menu">
                            <div class="ec-category-toggle">
                                <i class="ecicon eci-bars"></i>
                                <span class="ec-category-title d-1199">Shop By Category</span>
                                <i class="fas fa-angle-down d-1199" aria-hidden="true"></i>
                            </div>
                            <div class="ec-category-content">
                                <div class="ec-category-dropdown">
                                    <ul class="ec-category-wrapper">
                                        <li><a title="" class="ec-cat-menu-link" href="#">Home & Kitchen</a></li>
                                        <li><a title="" class="ec-cat-menu-link" href="#">Electronics & Digital</a></li>
                                        <li><a title="" class="ec-cat-menu-link" href="#">Fashion</a></li>
                                        <li><a title="" class="ec-cat-menu-link" href="#">Electronics</a></li>
                                        <li><a title="" class="ec-cat-menu-link" href="#">Office Furniture</a></li>
                                        <li><a title="" class="ec-cat-menu-link" href="#">Hotel Furniture</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EC Main Menu Start -->
                    <div id="ec-main-menu-desk" class="d-none d-lg-block sticky-nav">
                        <div class="position-relative nav-desk">
                            <div class="row">
                                <div class="col-md-12 align-self-center">
                                    <div class="ec-main-menu">
                                        <ul>
                                            <li class="dropdown"><a href="{{route("index")}}">Home</a></li>
                                            <li class="dropdown position-static"><a
                                                    href="javascript:void(0)">Categories</a>
                                                <ul class="mega-menu d-block">
                                                    <li class="d-flex">
                                                        <ul class="d-block">
                                                            <li class="menu_title"><a href="javascript:void(0)">Classic
                                                                    Variation</a></li>
                                                            <li><a href="shop-left-sidebar-col-3.html">Left sidebar 3
                                                                    column</a>
                                                            </li>
                                                            <li><a href="shop-left-sidebar-col-4.html">Left sidebar 4
                                                                    column</a>
                                                            </li>
                                                            <li><a href="shop-right-sidebar-col-3.html">Right sidebar 3
                                                                    column</a>
                                                            </li>
                                                            <li><a href="shop-right-sidebar-col-4.html">Right sidebar 4
                                                                    column</a>
                                                            </li>
                                                            <li><a href="shop-full-width.html">Full width 4 column</a>
                                                            </li>
                                                        </ul>
                                                        <ul class="d-block">
                                                            <li class="menu_title"><a href="javascript:void(0)">Banner
                                                                    Variation</a></li>
                                                            <li><a href="shop-banner-left-sidebar-col-3.html">left
                                                                    sidebar 3
                                                                    column</a></li>
                                                            <li><a href="shop-banner-left-sidebar-col-4.html">left
                                                                    sidebar 4
                                                                    column</a></li>
                                                            <li><a href="shop-banner-right-sidebar-col-3.html">right
                                                                    sidebar
                                                                    3 column</a></li>
                                                            <li><a href="shop-banner-right-sidebar-col-4.html">right
                                                                    sidebar
                                                                    4 column</a></li>
                                                            <li><a href="shop-banner-full-width.html">Full width 4
                                                                    column</a>
                                                            </li>
                                                        </ul>
                                                        <ul class="d-block">
                                                            <li class="menu_title"><a href="javascript:void(0)">Columns
                                                                    Variation</a></li>
                                                            <li><a href="shop-full-width-col-3.html">3 Columns full
                                                                    width</a></li>
                                                            <li><a href="shop-full-width-col-4.html">4 Columns full
                                                                    width</a></li>
                                                            <li><a href="shop-full-width-col-5.html">5 Columns full
                                                                    width</a></li>
                                                            <li><a href="shop-full-width-col-6.html">6 Columns full
                                                                    width</a></li>
                                                            <li><a href="shop-banner-full-width-col-3.html">Banner 3
                                                                    Columns</a>
                                                            </li>
                                                        </ul>
                                                        <ul class="d-block">
                                                            <li class="menu_title"><a href="javascript:void(0)">List
                                                                    Variation</a>
                                                            </li>
                                                            <li><a href="shop-list-left-sidebar.html">Shop left
                                                                    sidebar</a></li>
                                                            <li><a href="shop-list-right-sidebar.html">Shop right
                                                                    sidebar</a></li>
                                                            <li><a href="shop-list-banner-left-sidebar.html">Banner left
                                                                    sidebar</a>
                                                            </li>
                                                            <li><a href="shop-list-banner-right-sidebar.html">Banner
                                                                    right
                                                                    sidebar</a></li>
                                                            <li><a href="shop-list-full-col-2.html">Full width 2
                                                                    columns</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="dropdown"><a href="javascript:void(0)">Products</a>
                                                <ul class="sub-menu">
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Product page
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="product-left-sidebar.html">Product left
                                                                    sidebar</a></li>
                                                            <li><a href="product-right-sidebar.html">Product right
                                                                    sidebar</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Product 360
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="product-360-left-sidebar.html">360 left
                                                                    sidebar</a></li>
                                                            <li><a href="product-360-right-sidebar.html">360 right
                                                                    sidebar</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Product video
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="product-video-left-sidebar.html">Video left
                                                                    sidebar</a>
                                                            </li>
                                                            <li><a href="product-video-right-sidebar.html">Video right
                                                                    sidebar</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Product
                                                            gallery
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="product-gallery-left-sidebar.html">Gallery left
                                                                    sidebar</a>
                                                            </li>
                                                            <li><a href="product-gallery-right-sidebar.html">Gallery
                                                                    right
                                                                    sidebar</a></li>
                                                        </ul>
                                                    </li>
                                                    <li><a href="product-full-width.html">Product full width</a></li>
                                                    <li><a href="product-360-full-width.html">360 full width</a></li>
                                                    <li><a href="product-video-full-width.html">Video full width</a>
                                                    </li>
                                                    <li><a href="product-gallery-full-width.html">Gallery full width</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="dropdown"><a href="javascript:void(0)">Pages</a>
                                                <ul class="sub-menu">
                                                    <li><a href="about-us.html">About Us</a></li>
                                                    <li><a href="contact-us.html">Contact Us</a></li>
                                                    <li><a href="cart.html">Cart</a></li>
                                                    <li><a href="checkout.html">Checkout</a></li>
                                                    <li><a href="compare.html">Compare</a></li>
                                                    <li><a href="faq.html">FAQ</a></li>
                                                    <li><a href="login.html">Login</a></li>
                                                    <li><a href="register.html">Register</a></li>
                                                    <li><a href="track-order.html">Track Order</a></li>
                                                    <li><a href="terms-condition.html">Terms Condition</a></li>
                                                    <li><a href="privacy-policy.html">Privacy Policy</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown"><span class="main-label-note-new" data-toggle="tooltip"
                                                    title="NEW"></span><a href="javascript:void(0)">Others</a>
                                                <ul class="sub-menu">
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Mail
                                                            Confirmation
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="email-template-confirm-1.html">Mail
                                                                    Confirmation 1</a></li>
                                                            <li><a href="email-template-confirm-2.html">Mail
                                                                    Confirmation 2</a></li>
                                                            <li><a href="email-template-confirm-3.html">Mail
                                                                    Confirmation 3</a></li>
                                                            <li><a href="email-template-confirm-4.html">Mail
                                                                    Confirmation 4</a></li>
                                                            <li><a href="email-template-confirm-5.html">Mail
                                                                    Confirmation 5</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Mail Reset
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="email-template-forgot-password-1.html">Reset
                                                                    password 1</a>
                                                            </li>
                                                            <li><a href="email-template-forgot-password-2.html">Reset
                                                                    password 2</a>
                                                            </li>
                                                            <li><a href="email-template-forgot-password-3.html">Reset
                                                                    password 3</a>
                                                            </li>
                                                            <li><a href="email-template-forgot-password-4.html">Reset
                                                                    password 4</a>
                                                            </li>
                                                            <li><a href="email-template-forgot-password-5.html">Reset
                                                                    password 5</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Mail
                                                            Promotional
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="email-template-offers-1.html">Offer mail 1</a>
                                                            </li>
                                                            <li><a href="email-template-offers-2.html">Offer mail 2</a>
                                                            </li>
                                                            <li><a href="email-template-offers-3.html">Offer mail 3</a>
                                                            </li>
                                                            <li><a href="email-template-offers-4.html">Offer mail 4</a>
                                                            </li>
                                                            <li><a href="email-template-offers-5.html">Offer mail 5</a>
                                                            </li>
                                                            <li><a href="email-template-offers-6.html">Offer mail 6</a>
                                                            </li>
                                                            <li><a href="email-template-offers-7.html">Offer mail 7</a>
                                                            </li>
                                                            <li><a href="email-template-offers-8.html">Offer mail 8</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static">
                                                        <span class="label-note-hot"></span>
                                                        <a href="javascript:void(0)">Vendor account
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="vendor-dashboard.html">Vendor Dashboard</a>
                                                            </li>
                                                            <li><a href="vendor-profile.html">Vendor Profile</a></li>
                                                            <li><a href="vendor-uploads.html">Vendor Uploads</a></li>
                                                            <li><a href="vendor-settings.html">Vendor Settings</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static">
                                                        <span class="label-note-trending"></span>
                                                        <a href="javascript:void(0)">User account
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="user-profile.html">User Profile</a></li>
                                                            <li><a href="user-history.html">History</a></li>
                                                            <li><a href="wishlist.html">Wishlist</a></li>
                                                            <li><a href="track-order.html">Track Order</a></li>
                                                            <li><a href="user-invoice.html">Invoice</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static"><a
                                                            href="javascript:void(0)">Construction
                                                            pages
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="404-error-page.html">404 error page</a></li>
                                                            <li><a href="under-maintenance.html">maintanence page</a>
                                                            </li>
                                                            <li><a href="coming-soon.html">Coming soon page</a></li>
                                                        </ul>
                                                    </li>
                                                    <li class="dropdown position-static">
                                                        <span class="label-note-new"></span>
                                                        <a href="javascript:void(0)">Vendor Catalog
                                                            <i class="ecicon eci-angle-right"></i></a>
                                                        <ul class="sub-menu sub-menu-child">
                                                            <li><a href="catalog-single-vendor.html">Single Vendor</a>
                                                            </li>
                                                            <li><a href="catalog-multi-vendor.html">Multi Vendor</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="dropdown"><a href="javascript:void(0)">Blog</a>
                                                <ul class="sub-menu">
                                                    <li><a href="blog-left-sidebar.html">left sidebar</a></li>
                                                    <li><a href="blog-right-sidebar.html">right sidebar</a></li>
                                                    <li><a href="blog-detail-left-sidebar.html">detail left sidebar</a>
                                                    </li>
                                                    <li><a href="blog-detail-right-sidebar.html">detail right
                                                            sidebar</a></li>
                                                    <li><a href="blog-full-width.html">full width</a></li>
                                                    <li><a href="blog-detail-full-width.html">detail full width</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown"><a href="javascript:void(0)">Elements</a>
                                                <ul class="sub-menu">
                                                    <li><a href="elemets-products.html">Products</a></li>
                                                    <li><a href="elemets-typography.html">Typography</a></li>
                                                    <li><a href="elemets-title.html">Titles</a></li>
                                                    <li><a href="elemets-categories.html">Categories</a></li>
                                                    <li><a href="elemets-buttons.html">Buttons</a></li>
                                                    <li><a href="elemets-tabs.html">Tabs</a></li>
                                                    <li><a href="elemets-accordions.html">Accordions</a></li>
                                                    <li><a href="elemets-blog.html">Blogs</a></li>
                                                </ul>
                                            </li>
                                            <li class="non-drop"><a href="offer.html">Offers</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ec Main Menu End -->
                </div>
            </div>
        </div>
        <!-- Header responsive Bottom  Start -->
        <div class="ec-header-bottom d-lg-none">
            <div class="container position-relative">
                <div class="row ">
                    <div class="ec-flex">
                        <!-- Ec Header Logo Start -->
                        <div class="col ec-header-logo">
                            <div class="header-logo">
                                <a href="{{route("index")}}"><img src="assets/images/logo/logo-4.png" alt="Site Logo" /><img
                                        class="dark-logo" src="assets/images/logo/dark-logo-4.png" alt="Site Logo"
                                        style="display: none;" /></a>
                            </div>
                        </div>
                        <!-- Ec Header Logo End -->
                        <div class="col ec-category-block">
                            <div class="ec-category-menu">
                                <div class="ec-category-toggle">
                                    <i class="ecicon eci-bars"></i>
                                    <span class="ec-category-title d-479">Category</span>
                                </div>
                                <div class="ec-category-content">
                                    <div class="ec-category-dropdown">
                                        <ul class="ec-category-wrapper">
                                            <li><a title="" class="ec-cat-menu-link" href="#">Home & Kitchen</a></li>
                                            <li><a title="" class="ec-cat-menu-link" href="#">Electronics & Digital</a>
                                            </li>
                                            <li><a title="" class="ec-cat-menu-link" href="#">Home Accessories</a></li>
                                            <li><a title="" class="ec-cat-menu-link" href="#">Electronics</a></li>
                                            <li><a title="" class="ec-cat-menu-link" href="#">Office Furniture</a></li>
                                            <li><a title="" class="ec-cat-menu-link" href="#">Hotel Furniture</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ec Header Search Start -->
                    <div class="col align-self-center ec-header-search">
                        <div class="header-search">
                            <form class="ec-search-group-form" action="#">
                                <div class="ec-search-select-inner">
                                    <select name="ec-search-cat">
                                        <option selected disabled>Category</option>
                                        <option value="cloths">Cloths</option>
                                        <option value="bag">Bag</option>
                                        <option value="shoes">Shoes</option>
                                    </select>
                                </div>
                                <input class="form-control" placeholder="Search Your Products..." type="text">
                                <button class="search_submit" type="submit"><i class="fi-rr-search"></i></button>
                            </form>
                        </div>
                    </div>
                    <!-- Ec Header Search End -->

                </div>
            </div>
        </div>
        <!-- Header responsive Bottom  End -->
    </header>