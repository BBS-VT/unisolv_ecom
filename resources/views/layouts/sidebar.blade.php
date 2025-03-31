
<div class="app-menu navbar-menu pt-3">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('home') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
                </span>
            <span class="logo-lg">
                    <img src="{{ URL::asset('build/images/logo-dark.png') }}" alt="" height="22">
                </span>
        </a>
        <a href="{{ route('home') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="{{ URL::asset('build/images/logo-sm.png') }}" alt="" height="22">
                </span>
            <span class="logo-lg">
                    <img src="{{ URL::asset('build/images/logo-light.png') }}" alt="" height="22">
                </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
                id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div id="scrollbar ">
        <div class="container-fluid pt-3">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link menu-link"> <i class="ph-gauge"></i> <span
                            data-key="t-crm">@lang('global.dashboard')</span> </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarDashboards" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                        <i class="ph-gauge"></i> <span>@lang('translation.dashboards')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="dashboard-analytics" class="nav-link" data-key="t-analytics">
                                    @lang('translation.analytics') </a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard-crm" class="nav-link" data-key="t-crm"> @lang('translation.crm')</a>
                            </li>
                            <li class="nav-item">
                                <a href="index" class="nav-link" data-key="t-ecommerce"> @lang('translation.ecommerce')</a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard-learning" class="nav-link" data-key="t-learning">
                                    @lang('translation.learning')</a>
                            </li>
                            <li class="nav-item">
                                <a href="dashboard-real-estate" class="nav-link" data-key="t-real-estate">
                                    @lang('translation.real-estate') </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
<div class="vertical-overlay"></div>
