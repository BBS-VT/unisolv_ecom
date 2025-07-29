<div class="left-sidenav">
    <!-- LOGO -->
    <div class="brand">
        <a href="{{ route('home') }}" class="logo">
            <span>
                <img src="{{ asset('images/unisolv_light.png') }}" alt="logo-small" class="logo-sm">
            </span>

        </a>
    </div>
    <!--end logo-->
    <div class="menu-content h-100" data-simplebar>
        <ul class="metismenu left-sidenav-menu">
{{--            <li class="menu-label mt-0">Main</li>--}}
            <li>
                <a href="{{ route('home') }}"><i data-feather="home" class="align-self-center menu-icon"></i><span>Dashboard</span></a>
            </li>

            <li>
                <a href="javascript: void(0);"><i data-feather="grid" class="align-self-center menu-icon"></i><span>Sales</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}" class="nav-link"><i class="ti-control-record"></i>{{ __('Orders') }} </a>
                        {{--<a href="{{ route('orders.create') }}" class="sidebar-menu-action">
                            <i class="fas fa-plus mr-2 icon-plus"></i>
                        </a>--}}
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}" class="nav-link"><i class="ti-control-record"></i>{{ __('Customers') }} </a>
                        {{--<a href="{{ route('customers.create') }}" class="sidebar-menu-action">
                            <i class="fas fa-plus mr-2 icon-plus"></i>
                        </a>--}}
                    </li>
                    @can('specialdeal_access')
                    <li class="nav-item">
                        <a href="{{ route('deals.index') }}" class="nav-link"><i class="ti-control-record"></i>{{ __('Contract Discounts') }} </a>
                        {{--<a href="{{ route('deals.create') }}" class="sidebar-menu-action">
                            <i class="fas fa-plus mr-2 icon-plus"></i>
                        </a>--}}

                    </li>
                    @endcan
                    {{--<li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>New <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            --}}{{--<li><a href="apps-email-inbox.html">Quote</a></li> --}}{{--
                            --}}{{--<li><a href="{{ route('orders.create.step.one') }}">Order</a></li>--}}{{--
                            <li><a href="{{ route('orders.create') }}">Order</a></li>
                            --}}{{-- <li><a href="apps-email-inbox.html">Invoice</a></li> --}}{{--
                            <li><a href="{{ route('customers.create') }}">Customer</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>Maintain <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('orders.index') }}">Orders</a></li>
                            <li><a href="{{ route('customers.index') }}">Customers</a></li>
                            @can('specialdeal_access')
                                <li><a href="{{ route('deals.index') }}">Contract Discounts</a></li>
                            @endcan
                            --}}{{--<li><a href="apps-project-projects.html">Projects</a></li>
                            <li><a href="apps-project-board.html">Board</a></li>
                            <li><a href="apps-project-teams.html">Teams</a></li>
                            <li><a href="apps-project-files.html">Files</a></li>
                            <li><a href="apps-new-project.html">New Project</a></li>--}}{{--
                        </ul>
                    </li> --}}

                </ul>
            </li>
            @can('purchase_management_access')
            <li>
                <a href="javascript: void(0);"><i data-feather="grid" class="align-self-center menu-icon"></i><span>Purchasing</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>New <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="apps-email-inbox.html">Purchase Order</a></li>
                            <li><a href="apps-email-inbox.html">Supplier</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>Maintain <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="#">Purchases</a></li>
                            <li><a href="#">Supplier</a></li>
                            {{--<li><a href="apps-project-projects.html">Projects</a></li>
                            <li><a href="apps-project-board.html">Board</a></li>
                            <li><a href="apps-project-teams.html">Teams</a></li>
                            <li><a href="apps-project-files.html">Files</a></li>
                            <li><a href="apps-new-project.html">New Project</a></li>--}}
                        </ul>
                    </li>

                </ul>
            </li>
            @endcan
            @can('product_management_access')
            <li>
                <a href="javascript: void(0);"><i data-feather="grid" class="align-self-center menu-icon"></i><span>Stock</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>New <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('products.create') }}">Items</a></li>
                            <li><a href="{{ route('product-categories.create') }}">Department</a></li>
                            {{--<li><a href="apps-email-inbox.html">Stock Adjustment</a></li>
                            <li><a href="#">Stocktake</a></li>
                            <li><a href="#">Transfer</a></li>--}}
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>Maintain <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('products.index') }}">Items</a></li>
                            <li><a href="{{ route('product-categories.index') }}">Departments</a></li>
                            {{--<li><a href="apps-project-projects.html">Availability</a></li>
                            <li><a href="apps-project-board.html">Stocktake</a></li>
                            <li><a href="apps-project-teams.html">Transfers</a></li>--}}

                        </ul>
                    </li>

                </ul>
            </li>
            @endcan
            {{-- <hr class="hr-dashed hr-menu">
           <li class="menu-label my-2">Components & Extra</li>--}}

            <li>
                <a href="{{ route('admin.imports.status') }}"><i data-feather="layers" class="align-self-center menu-icon"></i><span>Reports</span><span class="badge badge-soft-success menu-arrow">New</span></a>
            </li>

            @can('user_management_access')
            <li>
                <a href="javascript: void(0);"><i data-feather="file-plus" class="align-self-center menu-icon"></i><span>Settings</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                <ul class="nav-second-level" aria-expanded="false">
                    <li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>General Settings <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('settings.company') }}">Company Settings</a></li>
                            <li><a href="{{ route('admin.buying-group.index') }}">Buying Groups</a></li>
                            <li><a href="{{ route('admin.orderstatus.index') }}">Order Statuses</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="ti-control-record"></i>Users & Roles <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('admin.users.index') }}">Users</a></li>
                            <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                        </ul>
                    </li>

                    {{--<li class="nav-item"><a class="nav-link" href="pages-pricing.html"><i class="ti-control-record"></i>Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-profile.html"><i class="ti-control-record"></i>Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-starter.html"><i class="ti-control-record"></i>Starter Page</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-timeline.html"><i class="ti-control-record"></i>Timeline</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-treeview.html"><i class="ti-control-record"></i>Treeview</a></li>--}}
                </ul>
            </li>
            @endcan
            <li>
                <a href="#"><i data-feather="book-open" class="align-self-center menu-icon"></i><span>Documentation</span>
                    <span class="badge badge-soft-success menu-arrow">New</span></a>
            </li>
        </ul>
    </div>
</div>
