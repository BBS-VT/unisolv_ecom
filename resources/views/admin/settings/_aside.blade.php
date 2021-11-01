<ul class="sidebar-menu">
    <li class="sidebar-menu-item">
        <a href="{{ route('settings.company') }}" class="sidebar-menu-button {{ $tab == 'company' ? 'text-primary' : 'text-secondary' }}">
            <i class="feahter-cpu"></i>
            <span class="sidebar-menu-text">{{ __('global.company_settings') }}</span>
        </a>
    </li>
    <li class="sidebar-menu-item">
        <a href="{{ route('settings.tax_types') }}" class="sidebar-menu-button {{ $tab == 'tax_types' ? 'text-primary' : 'text-secondary' }}">
            <i class="feather-command"></i>
            <span class="sidebar-menu-text">{{ __('global.tax_types') }}</span>
        </a>
    </li>
</ul>
