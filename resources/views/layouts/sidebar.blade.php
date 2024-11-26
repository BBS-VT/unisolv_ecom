<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('home') }}" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="" alt="" height="22">
                </span>
            <span class="logo-lg">
                    <img src="" alt="" height="22">
                </span>
        </a>
        <a href="{{ route('home') }}" class="logo logo-light">
                <span class="logo-sm">
                    <img src="" alt="" height="22">
                </span>
            <span class="logo-lg">
                    <img src="" alt="" height="22">
                </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
                id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">

                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link menu-link"> <i class="ph-gauge"></i> <span
                            data-key="t-dashboard">@lang('global.dashboard')</span> </a>
                </li>

                <li class="nav-item">
                    <a href="apps-chat" class="nav-link menu-link"> <i class="ph-chats"></i> <span
                            data-key="t-chat">@lang('translation.chat')</span> </a>
                </li>

                <li class="nav-item">
                    <a href="apps-email" class="nav-link menu-link"> <i class="ph-envelope"></i> <span
                            data-key="t-email">@lang('translation.email')</span> </a>
                </li>

                <li class="nav-item">
                    <a href="#sidebarEcommerce" class="nav-link menu-link collapsed" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarEcommerce">
                        <i class="ph-storefront"></i> <span data-key="t-ecommerce">@lang('translation.ecommerce')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarEcommerce">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="apps-ecommerce-products" class="nav-link"
                                   data-key="t-products">@lang('translation.products')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-product-grid" class="nav-link"
                                   data-key="t-products-grid">@lang('translation.product-grid')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-product-details" class="nav-link"
                                   data-key="t-product-Details">@lang('translation.product-Details')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-add-product" class="nav-link"
                                   data-key="t-create-product">@lang('translation.create-product')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-orders" class="nav-link"
                                   data-key="t-orders">@lang('translation.orders')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-order-overview" class="nav-link"
                                   data-key="t-order-overview">@lang('translation.order-overview')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-customers" class="nav-link"
                                   data-key="t-customers">@lang('translation.customers')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-cart" class="nav-link"
                                   data-key="t-shopping-cart">@lang('translation.shopping-cart')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-checkout" class="nav-link"
                                   data-key="t-checkout">@lang('translation.checkout')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-sellers" class="nav-link"
                                   data-key="t-sellers">@lang('translation.sellers')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-ecommerce-seller-overview" class="nav-link"
                                   data-key="t-seller-overview">@lang('translation.sellers-overview')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="apps-file-manager" class="nav-link menu-link"> <i class="ph-folder-open"></i> <span
                            data-key="t-file-manager">@lang('translation.filemanager')</span> </a>
                </li>

                <li class="nav-item">
                    <a href="#sidebarLearning" class="nav-link menu-link  collapsed" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarLearning">
                        <i class="ph-graduation-cap"></i> <span data-key="t-learning">@lang('translation.learning')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarLearning">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#sidebarCourses" class="nav-link" data-bs-toggle="collapse"
                                   role="button" aria-expanded="false" aria-controls="sidebarCourses"
                                   data-key="t-courses"> @lang('translation.cources') </a>
                                <div class="collapse menu-dropdown" id="sidebarCourses">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-learning-list" class="nav-link"
                                               data-key="t-list-view">@lang('translation.list-view')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-learning-grid" class="nav-link"
                                               data-key="t-grid-view">@lang('translation.grid-view')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-learning-category" class="nav-link"
                                               data-key="t-category">@lang('translation.category')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-learning-overview" class="nav-link"
                                               data-key="t-overview">@lang('translation.overview')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-learning-create" class="nav-link"
                                               data-key="t-create-course">@lang('translation.create-course')</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarStudent" class="nav-link" data-bs-toggle="collapse"
                                   role="button" aria-expanded="false" aria-controls="sidebarStudent"
                                   data-key="t-students">@lang('translation.students') </a>
                                <div class="collapse menu-dropdown" id="sidebarStudent">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-student-subscriptions" class="nav-link"
                                               data-key="t-my-subscriptions">@lang('translation.subscriptions')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-student-courses" class="nav-link"
                                               data-key="t-my-courses">@lang('translation.mycourses')</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarInstructors" class="nav-link" data-bs-toggle="collapse"
                                   role="button" aria-expanded="false" aria-controls="sidebarInstructors"
                                   data-key="t-instructors"> @lang('translation.instructors')</a>
                                <div class="collapse menu-dropdown" id="sidebarInstructors">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-instructors-list" class="nav-link"
                                               data-key="t-list-view">@lang('translation.list-view')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-instructors-grid" class="nav-link"
                                               data-key="t-grid-view">@lang('translation.grid-view')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-instructors-overview" class="nav-link"
                                               data-key="t-overview">@lang('translation.overview')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-instructors-create" class="nav-link"
                                               data-key="t-create-instructors">@lang('translation.create-instructor')</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="#sidebarInvoices" class="nav-link menu-link collapsed" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarInvoices">
                        <i class="ph-file-text"></i> <span data-key="t-invoices">@lang('translation.invoices')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarInvoices">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="apps-invoices-list" class="nav-link"
                                   data-key="t-list-view">@lang('translation.list-view')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-invoices-overview" class="nav-link"
                                   data-key="t-overview">@lang('translation.overview')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-invoices-create" class="nav-link"
                                   data-key="t-create-invoice">@lang('translation.create-invoice')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="#sidebarTickets" class="nav-link menu-link collapsed" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarTickets">
                        <i class="ph-ticket"></i> <span data-key="t-support-tickets">@lang('translation.supprt-tickets')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarTickets">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="apps-tickets-list" class="nav-link"
                                   data-key="t-list-view">@lang('translation.list-view')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-tickets-overview" class="nav-link"
                                   data-key="t-overview">@lang('translation.overview')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="#sidebarRealeEstate" class="nav-link menu-link collapsed" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarRealeEstate">
                        <i class="ph-buildings"></i> <span data-key="t-real-estate">@lang('translation.realestate')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarRealeEstate">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="apps-real-estate-grid" class="nav-link"
                                   data-key="t-listing-grid">@lang('translation.listing-grid')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-real-estate-list" class="nav-link"
                                   data-key="t-listing-list">@lang('translation.listing-list')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-real-estate-map" class="nav-link"
                                   data-key="t-listing-map">@lang('translation.listing-map')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-real-estate-property-overview" class="nav-link"
                                   data-key="t-property-overview">@lang('translation.property-overview')</a>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarAgent" class="nav-link" data-bs-toggle="collapse"
                                   role="button" aria-expanded="false" aria-controls="sidebarAgent"
                                   data-key="t-agent"> @lang('translation.agent') </a>
                                <div class="collapse menu-dropdown" id="sidebarAgent">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-real-estate-agent-list" class="nav-link"
                                               data-key="t-list-view">@lang('translation.list-view')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-real-estate-agent-grid" class="nav-link"
                                               data-key="t-grid-view">@lang('translation.grid-view')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-real-estate-agent-overview" class="nav-link"
                                               data-key="t-overview">@lang('translation.overview')</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarAgencies" class="nav-link" data-bs-toggle="collapse"
                                   role="button" aria-expanded="false" aria-controls="sidebarAgencies"
                                   data-key="t-agencies">@lang('translation.agencies')</a>
                                <div class="collapse menu-dropdown" id="sidebarAgencies">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="apps-real-estate-agencies-list" class="nav-link"
                                               data-key="t-list-view">@lang('translation.list-view')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="apps-real-estate-agencies-overview" class="nav-link"
                                               data-key="t-overview">@lang('translation.overview')</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="apps-real-estate-add-properties" class="nav-link"
                                   data-key="t-add-property">@lang('translation.add-property')</a>
                            </li>
                            <li class="nav-item">
                                <a href="apps-real-estate-earnings" class="nav-link"
                                   data-key="t-earnings">@lang('translation.earnings')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span
                        data-key="t-pages">@lang('translation.pages')</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarAuth" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <i class="ph-user-circle"></i> <span>@lang('translation.authentication')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAuth">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="auth-signin" class="nav-link" role="button"
                                   data-key="t-signin">@lang('translation.signin') </a>
                            </li>
                            <li class="nav-item">
                                <a href="auth-signup" class="nav-link" role="button"
                                   data-key="t-signup">@lang('translation.signup')</a>
                            </li>

                            <li class="nav-item">
                                <a href="auth-pass-reset" class="nav-link" role="button"
                                   data-key="t-password-reset">
                                    @lang('translation.password-reset')
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="auth-pass-change" class="nav-link" role="button"
                                   data-key="t-password-create">
                                    @lang('translation.password-create')
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="auth-lockscreen" class="nav-link" role="button"
                                   data-key="t-lock-screen">
                                    @lang('translation.lock-screen')
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="auth-logout" class="nav-link" role="button" data-key="t-logout">
                                    @lang('translation.logout') </a>
                            </li>
                            <li class="nav-item">
                                <a href="auth-success-msg" class="nav-link" role="button"
                                   data-key="t-success-message">@lang('translation.success-message') </a>
                            </li>
                            <li class="nav-item">
                                <a href="auth-twostep" class="nav-link" role="button"
                                   data-key="t-two-step-verification"> @lang('translation.two-step-verification') </a>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarErrors" class="nav-link" data-bs-toggle="collapse"
                                   role="button" aria-expanded="false" aria-controls="sidebarErrors"
                                   data-key="t-errors"> @lang('translation.errors')</a>
                                <div class="collapse menu-dropdown" id="sidebarErrors">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="auth-404" class="nav-link"
                                               data-key="t-404-error">@lang('translation.404')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-500" class="nav-link" data-key="t-500">
                                                @lang('translation.500') </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-503" class="nav-link"
                                               data-key="t-503">@lang('translation.503')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="auth-offline" class="nav-link" data-key="t-offline-page">
                                                @lang('translation.offline-page')</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarPages" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarPages">
                        <i class="ph-address-book"></i> <span data-key="t-pages">@lang('translation.pages')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPages">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="pages-starter" class="nav-link" data-key="t-starter">@lang('translation.starter')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-profile" class="nav-link" data-key="t-profile"> @lang('translation.profile')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-profile-settings" class="nav-link"
                                   data-key="t-profile-setting">@lang('translation.settings')</a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-contacts" class="nav-link"
                                   data-key="t-contacts">@lang('translation.contacts')</a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-timeline" class="nav-link" data-key="t-timeline">
                                    @lang('translation.timeline') </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-faqs" class="nav-link" data-key="t-faqs"> @lang('translation.faqs') </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-pricing" class="nav-link" data-key="t-pricing"> @lang('translation.pricing')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-maintenance" class="nav-link"
                                   data-key="t-maintenance">@lang('translation.maintenance') </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-coming-soon" class="nav-link"
                                   data-key="t-coming-soon">@lang('translation.coming-soon') </a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-privacy-policy" class="nav-link" data-key="t-privacy-policy">
                                    @lang('translation.privacy-policy')</a>
                            </li>
                            <li class="nav-item">
                                <a href="pages-term-conditions" class="nav-link"
                                   data-key="t-term-conditions">@lang('translation.term-condition')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title"><i class="ri-more-fill"></i> <span
                        data-key="t-components">@lang('translation.components')</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarUI" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarUI">
                        <i class="ph-bandaids"></i> <span data-key="t-bootstrap-ui">@lang('translation.base-ui')</span>
                    </a>
                    <div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarUI">
                        <div class="row">
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="ui-alerts" class="nav-link">@lang('translation.alerts')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-badges" class="nav-link">@lang('translation.badges')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-buttons" class="nav-link">@lang('translation.buttons')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-colors" class="nav-link">@lang('translation.colors')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-cards" class="nav-link">@lang('translation.cards')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-carousel" class="nav-link">@lang('translation.carousel')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-dropdowns" class="nav-link">@lang('translation.dropdowns')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-grid" class="nav-link">@lang('translation.grid')</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="ui-images" class="nav-link">@lang('translation.images')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-tabs" class="nav-link">@lang('translation.tabs')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-accordions" class="nav-link">@lang('translation.accordion-collapse')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-modals" class="nav-link">@lang('translation.modals')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-offcanvas" class="nav-link">@lang('translation.offcanvas')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-placeholders" class="nav-link">@lang('translation.placeholders')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-progress" class="nav-link">@lang('translation.progress')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-notifications" class="nav-link">@lang('translation.notifications')</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="ui-media" class="nav-link">@lang('translation.media-object')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-embed-video" class="nav-link">@lang('translation.embed-video')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-typography" class="nav-link">@lang('translation.typography')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-lists" class="nav-link">@lang('translation.lists')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-general" class="nav-link">@lang('translation.general')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-ribbons" class="nav-link">@lang('translation.ribbons')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-utilities" class="nav-link">@lang('translation.utilities')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarAdvanceUI" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarAdvanceUI">
                        <i class="ph-stack-simple"></i> <span data-key="t-advance-ui">@lang('translation.advance-ui')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAdvanceUI">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="advance-ui-sweetalerts" class="nav-link">@lang('translation.sweet-alerts')</a>
                            </li>
                            <li class="nav-item">
                                <a href="advance-ui-nestable" class="nav-link">@lang('translation.nestable-list')</a>
                            </li>
                            <li class="nav-item">
                                <a href="advance-ui-scrollbar" class="nav-link">@lang('translation.scrollbar')</a>
                            </li>
                            <li class="nav-item">
                                <a href="advance-ui-swiper" class="nav-link"
                                   data-key="t-swiper-slider">@lang('translation.swiper-slider')</a>
                            </li>
                            <li class="nav-item">
                                <a href="advance-ui-ratings" class="nav-link">@lang('translation.ratings')</a>
                            </li>
                            <li class="nav-item">
                                <a href="advance-ui-highlight" class="nav-link">@lang('translation.highlight')</a>
                            </li>
                            <li class="nav-item">
                                <a href="advance-ui-scrollspy" class="nav-link">@lang('translation.scrollSpy')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#customUI" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="customUI">
                        <i class="ph-wrench"></i> <span data-key="t-custom-ui">@lang('translation.custom-ui')</span> <span
                            class="badge badge-pill bg-danger" data-key="t-custom">@lang('translation.custom')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="customUI">
                        <div class="row">
                            <div class="col-lg-4">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="ui-ribbons" class="nav-link"
                                           data-key="t-ribbons">@lang('translation.ribbons')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-profile" class="nav-link"
                                           data-key="t-profile">@lang('translation.profile')</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ui-counter" class="nav-link"
                                           data-key="t-counter">@lang('translation.counter')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="widgets">
                        <i class="ph-paint-brush-broad"></i> <span data-key="t-widgets">@lang('translation.widgets')</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarForms" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarForms">
                        <i class="ri-file-list-3-line"></i> <span data-key="t-forms">@lang('translation.forms')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarForms">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="forms-elements" class="nav-link">@lang('translation.basic-elements')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-select" class="nav-link">@lang('translation.form-select')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-checkboxs-radios" class="nav-link">@lang('translation.checkboxs-radios')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-pickers" class="nav-link">@lang('translation.pickers')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-masks" class="nav-link">@lang('translation.input-masks')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-advanced" class="nav-link">@lang('translation.advanced')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-range-sliders" class="nav-link">@lang('translation.range-slider')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-validation" class="nav-link">@lang('translation.validation')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-wizard" class="nav-link">@lang('translation.wizard')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-editors" class="nav-link">@lang('translation.editors')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-file-uploads" class="nav-link">@lang('translation.file-uploads')</a>
                            </li>
                            <li class="nav-item">
                                <a href="forms-layouts" class="nav-link">@lang('translation.form-layouts')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarTables" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarTables">
                        <i class="ph-table"></i> <span data-key="t-tables">@lang('translation.tables')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarTables">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="tables-basic" class="nav-link">@lang('translation.basic-tables')</a>
                            </li>
                            <li class="nav-item">
                                <a href="tables-gridjs" class="nav-link">@lang('translation.grid-js')</a>
                            </li>
                            <li class="nav-item">
                                <a href="tables-listjs" class="nav-link">@lang('translation.list-js')</a>
                            </li>
                            <li class="nav-item">
                                <a href="tables-datatables" class="nav-link">@lang('translation.datatables')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link collapsed" href="#sidebarCharts" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarCharts">
                        <i class="ph-chart-pie-slice"></i> <span
                            data-key="t-apexcharts">@lang('translation.apexcharts')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCharts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="charts-apex-line" class="nav-link">@lang('translation.line')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-area" class="nav-link">@lang('translation.area')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-column" class="nav-link">@lang('translation.column')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-bar" class="nav-link">@lang('translation.bar')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-mixed" class="nav-link">@lang('translation.mixed')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-timeline" class="nav-link">@lang('translation.timeline')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-range-area" class="nav-link">@lang('translation.range-area')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-funnel" class="nav-link">@lang('translation.funnel')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-candlestick" class="nav-link">@lang('translation.candlstick')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-boxplot" class="nav-link">@lang('translation.boxplot')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-bubble" class="nav-link">@lang('translation.bubble')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-scatter" class="nav-link">@lang('translation.scatter')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-heatmap" class="nav-link">@lang('translation.heatmap')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-treemap" class="nav-link">@lang('translation.treemap')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-pie" class="nav-link">@lang('translation.pie')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-radialbar" class="nav-link">@lang('translation.radialbar')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-radar" class="nav-link">@lang('translation.radar')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-polar" class="nav-link">@lang('translation.polar-area')</a>
                            </li>
                            <li class="nav-item">
                                <a href="charts-apex-slope" class="nav-link">@lang('translation.slope-area')</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarIcons" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarIcons">
                        <i class="ri-compasses-2-line"></i> <span>@lang('translation.icons')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarIcons">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="icons-remix" class="nav-link">@lang('translation.remix')</a>
                            </li>
                            <li class="nav-item">
                                <a href="icons-boxicons" class="nav-link">@lang('translation.boxicons')</a>
                            </li>
                            <li class="nav-item">
                                <a href="icons-materialdesign" class="nav-link">@lang('translation.material-design')</a>
                            </li>
                            <li class="nav-item">
                                <a href="icons-bootstrap" class="nav-link">@lang('translation.bootstrap')</a>
                            </li>
                            <li class="nav-item">
                                <a href="icons-phosphor" class="nav-link">@lang('translation.phosphor')</a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarMaps" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarMaps">
                        <i class="ri-map-pin-line"></i> <span>@lang('translation.maps')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarMaps">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="maps-google" class="nav-link">
                                    @lang('translation.google')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="maps-vector" class="nav-link">
                                    @lang('translation.vector')
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="maps-leaflet" class="nav-link">
                                    @lang('translation.leaflet')
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarMultilevel" data-bs-toggle="collapse"
                       role="button" aria-expanded="false" aria-controls="sidebarMultilevel">
                        <i class="ri-share-line"></i> <span>@lang('translation.multi-level')</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarMultilevel">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link">@lang('translation.level-1.1')</a>
                            </li>
                            <li class="nav-item">
                                <a href="#sidebarAccount" class="nav-link" data-bs-toggle="collapse"
                                   role="button" aria-expanded="false"
                                   aria-controls="sidebarAccount">@lang('translation.level-1.2')
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarAccount">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">@lang('translation.level-2.1')</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#sidebarCrm" class="nav-link" data-bs-toggle="collapse"
                                               role="button" aria-expanded="false"
                                               aria-controls="sidebarCrm">@lang('translation.level-2.2')
                                            </a>
                                            <div class="collapse menu-dropdown" id="sidebarCrm">
                                                <ul class="nav nav-sm flex-column">
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link">@lang('translation.level-3.1')</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link">@lang('translation.level-3.2')</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
