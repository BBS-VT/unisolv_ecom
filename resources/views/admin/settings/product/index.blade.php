@extends('layouts.master', ['page' => 'settings'])

@section('title', __('global.product_settings'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('Settings') }}</h4>

                <div class="page-title-right">

                </div>
            </div>
        </div>
    </div>

    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="card filemanager-sidebar me-md-2">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-4">
                                @include('admin.settings._aside', ['tab' => 'product'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100">
                    <div class="card">

                        <div class="card-body">
                            <!-- Nav tabs -->
                            <div>
                                <ul class="nav nav-tabs nav-tabs-custom nav-justified" id="productSettingsTabs"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general"
                                           role="tab"
                                           aria-controls="general" aria-selected="true">
                                            <i data-feather="settings" class="align-self-center icon-xs me-1"></i>
                                            {{ __('global.general_settings') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="units-tab" data-bs-toggle="tab" href="#units" role="tab"
                                           aria-controls="units" aria-selected="false">
                                            <i data-feather="package" class="align-self-center icon-xs me-1"></i>
                                            {{ __('global.product_units') }}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="locations-tab" data-bs-toggle="tab" href="#locations"
                                           role="tab"
                                           aria-controls="locations" aria-selected="false">
                                            <i data-feather="map-pin" class="align-self-center icon-xs me-1"></i>
                                            {{ __('global.sales_locations') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content" id="productSettingsTabsContent">
                                <!-- General Settings Tab -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel"
                                     aria-labelledby="general-tab">
                                    <div class="p-3">
                                        <form action="{{ route('settings.product.update') }}" method="POST">
                                            @include('layouts._form_errors')
                                            @csrf

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" name="discount_per_item"
                                                                   id="discount_per_item"
                                                                   {{ $currentCompany->getSetting('discount_per_item') ? 'checked' : '' }}
                                                                   class="form-check-input">
                                                            <label class="form-check-label" for="discount_per_item">
                                                                <strong>{{ __('global.discount_per_item') }}</strong>
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            {{ __('messages.discount_per_item') }}
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" name="tax_per_item" id="tax_per_item"
                                                                   {{ $currentCompany->getSetting('tax_per_item') ? 'checked' : '' }}
                                                                   class="form-check-input">
                                                            <label class="form-check-label" for="tax_per_item">
                                                                <strong>{{ __('global.tax_per_item') }}</strong>
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            {{ __('messages.tax_per_item') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" name="sales_locations"
                                                                   id="sales_locations"
                                                                   {{ $currentCompany->getSetting('sales_locations') ? 'checked' : '' }}
                                                                   class="form-check-input"
                                                                   onchange="toggleLocationsTab()">
                                                            <label class="form-check-label" for="sales_locations">
                                                                <strong>{{ __('global.enable_sales_locations') }}</strong>
                                                            </label>
                                                        </div>
                                                        <small class="form-text text-muted">
                                                            {{ __('messages.sales_locations') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">
                                                    <i data-feather="save" class="align-self-center icon-xs me-1"></i>
                                                    {{ __('global.update') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Package Units Tab -->
                                <div class="tab-pane fade" id="units" role="tabpanel" aria-labelledby="units-tab">
                                    <div class="p-3">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <h5 class="card-title mb-0">{{ __('global.product_units') }}</h5>
                                            </div>
                                            <div class="col-auto">
                                                @can('settings_create')
                                                    <a href="{{ route('admin.packagetype.create') }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i data-feather="plus-circle"
                                                           class="align-self-center icon-xs me-1"></i>
                                                        {{ __('global.add') }} {{ __('cruds.packageType.title') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>

                                        @include('admin.settings.product.unit._table')
                                    </div>
                                </div>

                                <!-- Sales Locations Tab -->
                                <div class="tab-pane fade" id="locations" role="tabpanel"
                                     aria-labelledby="locations-tab">
                                    <div class="p-3">
                                        <div id="locations-content">
                                            @if($currentCompany->getSetting('sales_locations'))
                                                <div class="row mb-3">
                                                    <div class="col">
                                                        <h5 class="card-title mb-0">{{ __('global.sales_locations') }}</h5>
                                                        <small
                                                            class="text-muted">{{ __('messages.manage_sales_locations') }}</small>
                                                    </div>
                                                    <div class="col-auto">
                                                        @can('settings_create')
                                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createLocationModal">
                                                                <i data-feather="plus-circle" class="align-self-center icon-xs me-1"></i>
                                                                {{ __('global.add') }} {{ __('global.location') }}
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>

                                                @include('admin.settings.product.locations._table')
                                                @include('admin.settings.product.locations._modals')
                                            @else
                                                <div class="text-center py-5">
                                                    <div class="mb-3">
                                                        <i data-feather="map-pin" class="align-self-center"
                                                           style="width: 48px; height: 48px; color: #ccc;"></i>
                                                    </div>
                                                    <h5 class="text-muted">{{ __('global.locations_disabled') }}</h5>
                                                    <p class="text-muted">{{ __('messages.enable_locations_to_manage') }}</p>
                                                    <button type="button" class="btn btn-outline-primary"
                                                            onclick="enableLocations()">
                                                        <i data-feather="toggle-right"
                                                           class="align-self-center icon-xs me-1"></i>
                                                        {{ __('global.enable_locations') }}
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleLocationsTab() {
                const checkbox = document.getElementById('sales_locations');
                const locationsTab = document.getElementById('locations-tab');

                if (checkbox.checked) {
                    locationsTab.classList.remove('disabled');
                    locationsTab.style.opacity = '1';
                } else {
                    locationsTab.classList.add('disabled');
                    locationsTab.style.opacity = '0.5';
                }
            }

            function enableLocations() {
                const checkbox = document.getElementById('sales_locations');
                checkbox.checked = true;

                // Submit the form to save the setting
                const form = checkbox.closest('form');
                form.submit();
            }

            // Initialize tab state on page load
            document.addEventListener('DOMContentLoaded', function() {
                toggleLocationsTab();

                // Handle tab switching with URL hash
                if (location.hash) {
                    const tabId = location.hash.substring(1);
                    const tabElement = document.querySelector(`a[href="#${tabId}"]`);
                    if (tabElement) {
                        $(tabElement).tab('show');
                    }
                }

                // Update URL when tab changes
                $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                    history.pushState(null, null, e.target.hash);
                });
            });
        </script>
    @endpush

@endsection
