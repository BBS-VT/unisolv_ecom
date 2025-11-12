<div class="vertical-menu">

    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">@lang('Menu')</li>
                <li>
                    @if(Auth::user()->roles('Sales Rep'))
                        <a href="{{ route('sales.dashboard') }}">
                            <i class="bx bx-home-circle"></i><span>{{ __('Sales Dashboard') }}</span>
                        </a>
                    @else
                        <a href="{{ route('home') }}">
                            <i class="bx bx-home-circle"></i><span>{{ __('global.dashboard') }}</span>
                        </a>
                    @endif
                </li>
                @can('product_management_access')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bx-shopping-bag"></i><span key="t-inventory">{{ __('global.inventory') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            {{--<li>
                                <a href="{{ route('inventory.dashboard') }}">{{ __('Inventory Dashboard') }}</a>
                            </li>--}}
                            <li>
                                <a href="javascript: void(0);" class="has-arrow" key="t-products">{{ __('global.products') }} </a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ route('products.create') }}" key="t-products-new">{{ __('global.new') }} {{ __('cruds.product.title_singular') }}</a></li>
                                    <li><a href="{{ url('products') }}" key="t-products-view">{{ __('global.view') }} {{ __('cruds.product.title') }}</a></li>
                                    <li><a href="{{ url('/product-categories') }}" key="t-product-category">{{ __('global.view') }} {{ __('cruds.productCategory.title') }}</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow" key="t-promotions">{{ __('Promotions') }} </a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ route('promotions.create') }}" key="t-promotions-new">{{ __('global.new') }} {{ __('Promotion') }}</a></li>
                                    <li><a href="{{ route('promotions.index') }}" key="t-promotions-view">{{ __('global.view') }} {{ __('Promotions') }}</a></li>
                                    <li><a href="{{ route('promotions.import') }}" key="t-promotions-import">{{ __('Import Promotions') }}</a></li>
                                    {{-- <li><a href="{{ route('promotions.featured') }}" key="t-promotions-featured" target="_blank">{{ __('Featured Deals') }} <i class="bx bx-link-external bx-xs"></i></a></li>--}}
                                </ul>
                            </li>
                            {{--<li>
                                <a href="javascript: void(0);" class="has-arrow">{{ __('Stock Management') }}</a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ route('inventory-adjustments.create') }}">{{ __('New Adjustment') }}</a></li>
                                    <li><a href="{{ route('inventory-adjustments.index') }}">{{ __('Adjustments List') }}</a></li>
                                    <li><a href="{{ route('stock-transfers.create') }}">{{ __('New Transfer') }}</a></li>
                                    <li><a href="{{ route('stock-transfers.index') }}">{{ __('Transfers List') }}</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow">{{ __('Reorder Management') }}</a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ route('reorder.low-stock') }}">{{ __('Low Stock Items') }}</a></li>
                                    <li><a href="{{ route('reorder.bulk-config') }}">{{ __('Bulk Configure') }}</a></li>
                                    <li><a href="{{ route('reorder.report') }}">{{ __('Reorder Report') }}</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow">{{ __('Reports') }}</a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ route('inventory.value-report') }}">{{ __('Inventory Value') }}</a></li>
                                    <li><a href="{{ route('inventory.movement-report') }}">{{ __('Stock Movement') }}</a></li>
                                </ul>
                            </li>--}}
                        </ul>
                    </li>
                @endcan
                @can('manage_customer')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect">
                            <i class="bx bxs-user-detail"></i>
                            <span key="t-customers">{{ __('global.customers') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li ><a href="{{ route('customers.create') }}" key="t-customers-new">{{ __('global.new') }} {{ __('cruds.customer.title_singular') }}</a></li>
                            <li><a href="{{ route('customers.index') }}" key="t-customers-list">{{ __('global.list') }} {{ __('cruds.customer.title') }}</a></li>
                            @can('manage_contractDiscount')
                                <li><a href="{{ route('deals.index') }}" key="t-customers-disc">{{ __('cruds.deal.title') }}</a></li>
                            @endcan
                            @can('import_customer')
                                <li><a href="#" key="t-customers-export">{{ __('global.export') }} {{ __('cruds.customer.title') }}</a></li>
                                {{--<li ><a href="#">{{ __('global.export') }} {{ __('global.address_list') }}</a></li>--}}
                            @endcan
                            @can('manage_license')
                                <li><a href="{{ route('license.index') }}" key="t-customers-lic">{{ __('Unisolv Licenses') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="bx bx-cart-alt"></i>
                        <span key="t-sales">{{ __('global.sales') }}</span>
                    </a>
                    <ul class="nav-second-level" aria-expanded="false">
                        @can('manage_quote')
                            <li>
                                <a href="javascript: void(0);"><i class="ti-control-record"></i>{{ __('global.quotes') }} <span class="menu-arrow left-has-menu">
                                <i class="mdi mdi-chevron-right"></i></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="#">{{ __('global.new') }}</a></li>
                                    <li><a href="#">{{ __('global.list') }}</a></li>
                                </ul>
                            </li>
                        @endcan
                        @can('manage_invoice')
                            <li>
                                <a href="javascript: void(0);"><i class="ti-control-record"></i>{{ __('global.invoices') }} <span class="menu-arrow left-has-menu">
                            <i class="mdi mdi-chevron-right"></i></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="{{ route('invoices.create') }}">{{ __('global.new') }}</a></li>
                                    <li><a href="{{ route('invoices') }}">{{ __('global.list') }}</a></li>
                                </ul>
                            </li>
                        @endcan
                        @can('manage_proposal')
                            <li>
                                <a href="javascript: void(0);"><i class="ti-control-record"></i>{{ __('global.proposal') }} <span class="menu-arrow left-has-menu">
                                    <i class="mdi mdi-chevron-right"></i></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="{{ route('proposals.create') }}">{{ __('global.new') }}</a></li>
                                    <li><a href="{{ route('proposals.index') }}">{{ __('global.list') }}</a></li>
                                </ul>
                            </li>
                        @endcan
                        <li>
                            <a href="javascript: void(0);"><i class="ti-control-record"></i>{{ __('global.orders') }} <span class="menu-arrow left-has-menu">
                                <i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="{{ route('orders.create') }}">{{ __('global.new') }} {{ __('cruds.order.title_singular') }}</a></li>
                                <li><a href="{{ route('orders.index') }}">{{ __('global.list') }} {{ __('cruds.order.title') }}</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

                @can('manage_purchase')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="bx bx-wallet "></i>
                            <span key="t-purchase">{{ __('global.purchases') }}</span>
                        </a>

                        <ul class="sub-menu" aria-expanded="false">
                            @can('manage_supplier')
                                <li>
                                    <a href="javascript: void(0);" class="has-arrow">{{ __('Suppliers') }}</a>
                                    <ul class="sub-menu" aria-expanded="true">
                                        <li><a href="{{ route('suppliers.create') }}">{{ __('New Supplier') }}</a></li>
                                        <li><a href="{{ route('suppliers.index') }}">{{ __('List Suppliers') }}</a></li>
                                    </ul>
                                </li>
                            @endcan
                            <li>
                                <a href="javascript: void(0);" class="has-arrow">{{ __('Purchase Orders') }}</a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ route('purchase-orders.create') }}">{{ __('global.new') }} {{ __('Order') }}</a></li>
                                    <li><a href="{{ route('purchase-orders.index') }}">{{ __('global.list') }} {{ __('Orders') }}</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow">{{ __('Goods Receipt') }}</a>
                                <ul class="sub-menu" aria-expanded="true">
                                    <li><a href="{{ route('goods-receipts.create') }}">{{ __('global.new') }} {{ __('Receipt') }}</a></li>
                                    <li><a href="{{ route('goods-receipts.index') }}">{{ __('global.list') }} {{ __('Receipts') }}</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('manage_ticket')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="bx bx-support "></i>
                            <span key="t-support">{{ __('global.support') }}</span>
                        </a>
                        @can('view_ticket')
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="{{ route('tickets.index') }}">{{ __('All Tickets') }}</a></li>
                                <li><a href="#">{{ __('My Tickets') }}</a></li>
                                <li>
                                    <a href="javascript: void(0);" class="has-arrow waves-effect">{{ __('All Views') }}</a>
                                    <ul class="sub-menu" aria-expanded="false">
                                        <li><a href="{{ route('tickets.index') }}">{{ __('All Tickets') }}</a></li>
                                        <li><a href="#">{{ __('global.list') }} {{ __('Closed Tickets') }}</a></li>
                                        <li><a href="#">{{ __('global.list') }} {{ __('Open Tickets') }}</a></li>
                                        <li><a href="#">{{ __('global.list') }} {{ __('Overdue Tickets') }}</a></li>
                                        <li><a href="#">{{ __('global.list') }} {{ __('On Hold Tickets') }}</a></li>
                                    </ul>
                                </li>
                            </ul>
                        @endcan
                    </li>
                @endcan
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect"><i class="bx bxs-report "></i>
                        <span key="t-reports">{{ __('global.reports') }}</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="#">{{ __('Stock Reports') }}</a></li>
                    </ul>
                </li>

                <li class="menu-title" key="t-settings">{{ __('Settings') }}</li>
                @can('manage_setting')
                    <li>
                        <a href="javascript: void(0);" class="has-arrow waves-effect"> <i class="bx bx-slider-alt "></i>
                            <span key="t-settings-general">{{ __('global.settings') }}</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            <li >
                                <a href="{{ route('settings.company') }}">{{ __('global.general_settings') }}</a>
                            </li>
                            @can('user_access')
                                <li>
                                    <a href="javascript: void(0);" class="has-arrow waves-effect">{{ __('Users & Roles') }}
                                    </a>
                                    <ul class="sub-menu" aria-expanded="true">
                                        <li><a href="{{ route('admin.users.index') }}">Users</a></li>
                                        <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                                    </ul>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                <li>
                    <a href="{{ url('./docs') }}"><i class="bx bx-book"></i>
                        <span key="t-documents">Documentation</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
