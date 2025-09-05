<ul class="list-unstyled categories-list">
    <li class="pt-1">
        <a href="{{ route('settings.company') }}" class="text-body d-flex align-items-center {{ $tab == 'company' ? 'text-primary' : 'text-secondary' }}">
            <i class="bx bxs-buildings font-size-16 text-muted me-2"></i>
            <span class="me-auto pl-2">{{ __('global.company_settings') }}</span>
        </a>
    </li>
    <li class="pt-1">
        <a href="{{ route('settings.preferences') }}" class="sidebar-menu-button {{ $tab == 'preferences' ? 'text-primary' : 'text-secondary' }}">
            <i class="bx bx-slider-alt font-size-16 text-muted me-2"></i>
            <span class="me-auto pl-2">{{ __('global.preferences') }}</span>
        </a>
    </li>
    <li class="pt-1">
        <a href="{{ route('settings.customer') }}" class="sidebar-menu-button {{ $tab == 'customers' ? 'text-primary' : 'text-secondary' }}">
            <i class="bx bxs-user-detail font-size-16 text-muted me-2" ></i>
            <span class="me-auto pl-2">{{ __('global.customer_settings') }}</span>
        </a>
    </li>
    <li class="pt-1">
        <a href="{{ route('settings.tax_types') }}" class="sidebar-menu-button {{ $tab == 'tax_types' ? 'text-primary' : 'text-secondary' }}">
            <i class="bx bx-receipt font-size-16 text-muted me-2" ></i>
            <span class="me-auto pl-2">{{ __('global.tax_types') }}</span>
        </a>
    </li>
    <li class="pt-1">
        <a href="{{ route('settings.product') }}" class="sidebar-menu-button {{ $tab == 'product' ? 'text-primary' : 'text-secondary' }}">
            <i class="bx bxs-box font-size-16 text-muted me-2" ></i>
            <span class="me-auto pl-2">{{ __('global.product_settings') }}</span>
        </a>
    </li>
    <li class="pt-1">
        <a href="{{ route('settings.order') }}" class="sidebar-menu-button {{ $tab == 'order' ? 'text-primary' : 'text-secondary' }}">
            <i class="bx bx-book-open font-size-16 text-muted me-2" ></i>
            <span class="me-auto pl-2">{{ __('global.order_settings') }}</span>
        </a>
    </li>
    <li class="pt-1">
        <a href="{{ route('settings.ecommerce') }}" class="sidebar-menu-button {{ $tab == 'ecommerce' ? 'text-primary' : 'text-secondary' }}">
            <i class="bx bx-cart font-size-16 text-muted me-2" ></i>
            <span class="me-auto pl-2">{{ __('global.ecommerce_settings') }}</span>
        </a>
    </li>
</ul>
