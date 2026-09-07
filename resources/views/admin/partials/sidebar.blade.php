<div class="sidebar px-4 py-4 py-md-4 me-0">
    <div class="d-flex flex-column h-100">

        <!-- Brand -->
        <a href="#" class="mb-0 brand-icon">
            <span class="logo-icon">
                <i class="bi bi-bag-check-fill fs-4"></i>
            </span>
            <span class="logo-text">{{ config('app.name', 'Springcrest') }}</span>
        </a>

        <!-- Admin Menu -->
        <ul class="menu-list flex-grow-1 mt-3">

            <!-- Dashboard -->
            <li>
                <a class="m-link active" href="{{ route("home") }}">
                    <i class="icofont-home fs-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Products -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-products" href="#">
                    <i class="icofont-box fs-5"></i>
                    <span>Products</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-products">
                    <li><a class="ms-link" href="{{ route("admin.products.index") }}">All Products</a></li>
                    <li><a class="ms-link" href="{{ route("admin.products.create") }}">Add Product</a></li>
                    <li><a class="ms-link" href="#">Product Reviews</a></li>
                </ul>
            </li>

            <!-- Categories -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-categories" href="#">
                    <i class="icofont-chart-flow fs-5"></i>
                    <span>Categories</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-categories">
                    <li><a class="ms-link" href="{{ route("admin.categories.index") }}">All Categories</a></li>
                    <li><a class="ms-link" href="{{ route("admin.categories.create") }}">Add Category</a></li>
                    <li><a class="ms-link" href="#">Sub Categories</a></li>
                </ul>
            </li>

            <!-- Brands -->
            <li>
                <a class="m-link" href="#">
                    <i class="icofont-brand-designfloat fs-5"></i>
                    <span>Brands</span>
                </a>
            </li>

            <!-- Orders -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-orders" href="#">
                    <i class="icofont-notepad fs-5"></i>
                    <span>Orders</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-orders">
                    <li><a class="ms-link" href="#">All Orders</a></li>
                    <li><a class="ms-link" href="#">Pending Orders</a></li>
                    <li><a class="ms-link" href="#">Completed Orders</a></li>
                    <li><a class="ms-link" href="#">Cancelled Orders</a></li>
                    <li><a class="ms-link" href="#">Invoices</a></li>
                </ul>
            </li>

            <!-- Customers -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-customers" href="#">
                    <i class="icofont-users-alt-5 fs-5"></i>
                    <span>Customers</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-customers">
                    <li><a class="ms-link" href="{{ route("admin.customers.index") }}">All Customers</a></li>
                    <li><a class="ms-link" href="#">Customer Messages</a></li>
                </ul>
            </li>

            <!-- Inventory -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-inventory" href="#">
                    <i class="icofont-chart-histogram fs-5"></i>
                    <span>Inventory</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-inventory">
                    <li><a class="ms-link" href="#">Stock List</a></li>
                    <li><a class="ms-link" href="#">Low Stock</a></li>
                    <li><a class="ms-link" href="#">Suppliers</a></li>
                    <li><a class="ms-link" href="#">Returns</a></li>
                </ul>
            </li>

            <!-- Sales & Promotions -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-promotions" href="#">
                    <i class="icofont-sale-discount fs-5"></i>
                    <span>Promotions</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-promotions">
                    <li><a class="ms-link" href="#">Coupons</a></li>
                    <li><a class="ms-link" href="#">Add Coupon</a></li>
                    <li><a class="ms-link" href="#">Discount Campaigns</a></li>
                </ul>
            </li>

            <!-- Payments -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-payments" href="#">
                    <i class="icofont-credit-card fs-5"></i>
                    <span>Payments</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-payments">
                    <li><a class="ms-link" href="#">Transactions</a></li>
                    <li><a class="ms-link" href="#">Payment Methods</a></li>
                    <li><a class="ms-link" href="#">Refunds</a></li>
                </ul>
            </li>

            <!-- Reports -->
            <li class="collapsed">
                <a class="m-link" data-bs-toggle="collapse" data-bs-target="#menu-reports" href="#">
                    <i class="icofont-chart-bar-graph fs-5"></i>
                    <span>Reports</span>
                    <span class="arrow icofont-rounded-down ms-auto text-end fs-5"></span>
                </a>

                <ul class="sub-menu collapse" id="menu-reports">
                    <li><a class="ms-link" href="#">Sales Report</a></li>
                    <li><a class="ms-link" href="#">Product Report</a></li>
                    <li><a class="ms-link" href="#">Customer Report</a></li>
                </ul>
            </li>

            <!-- Support -->
            <li>
                <a class="m-link" href="#">
                    <i class="icofont-support fs-5"></i>
                    <span>Support Tickets</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="collapsed">
                <a class="m-link" href="{{ route("admin.website.settings") }}">
                    <i class="icofont-gear fs-5"></i>
                    <span>Settings</span>
                </a>

                
            </li>

        </ul>

        <!-- Sidebar Collapse Button -->
        <button type="button" class="btn btn-link sidebar-mini-btn text-light">
            <span class="ms-2">
                <i class="icofont-bubble-right"></i>
            </span>
        </button>

    </div>
</div>