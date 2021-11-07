<ul class="sidebar-menu">
    <li class="sidebar-menu-item">
        <a href="{{ route('settings.company') }}" class="sidebar-menu-button {{ $tab == 'company' ? 'text-primary' : 'text-secondary' }}">
            <i data-feather="cpu"></i>
            <span class="sidebar-menu-text">{{ __('global.company_settings') }}</span>
        </a>
    </li>
    <li class="sidebar-menu-item pt-1">
        <a href="{{ route('settings.preferences') }}" class="sidebar-menu-button {{ $tab == 'preferences' ? 'text-primary' : 'text-secondary' }}">
            <i data-feather="sliders" ></i>
            <span class="sidebar-menu-text">{{ __('global.preferences') }}</span>
        </a>
    </li>
    <li class="sidebar-menu-item pt-1">
        <a href="{{ route('settings.tax_types') }}" class="sidebar-menu-button {{ $tab == 'tax_types' ? 'text-primary' : 'text-secondary' }}">
            <i data-feather="command" ></i>
            <span class="sidebar-menu-text">{{ __('global.tax_types') }}</span>
        </a>
    </li>
    <li class="sidebar-menu-item pt-1">
        <a href="{{ route('settings.product') }}" class="sidebar-menu-button {{ $tab == 'product' ? 'text-primary' : 'text-secondary' }}">
            <i data-feather="shopping-bag" ></i>
            <span class="sidebar-menu-text">{{ __('global.product_settings') }}</span>
        </a>
    </li>
</ul>
