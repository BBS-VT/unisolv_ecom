@extends('layouts.master', ['page' => 'settings'])

@section('title', __('global.ecommerce_settings'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('Settings') }}</h4>
                <div class="page-title-right"></div>
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
                                @include('admin.settings._aside', ['tab' => 'ecommerce'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('settings.ecommerce.update') }}" method="POST">
                                @include('layouts._form_errors')
                                @csrf

                                <div class="row mb-4">
                                    {{-- General Settings --}}
                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bx-cog me-1"></i>
                                                    {{ __('global.general_settings') }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="b2b_ecommerce_enabled" value="0">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="b2b_ecommerce_enabled" name="b2b_ecommerce_enabled" value="1"
                                                            {{ $settings['b2b_ecommerce_enabled'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="b2b_ecommerce_enabled">
                                                            <strong>{{ __('global.enable_b2b_ecommerce') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.enable_b2b_ecommerce') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="ecommerce_guest_checkout" value="0">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="ecommerce_guest_checkout" name="ecommerce_guest_checkout" value="1"
                                                            {{ $settings['ecommerce_guest_checkout'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="ecommerce_guest_checkout">
                                                            <strong>{{ __('global.allow_guest_checkout') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.allow_guest_checkout') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="ecommerce_public_prices" value="0">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="ecommerce_public_prices" name="ecommerce_public_prices" value="1"
                                                            {{ $settings['ecommerce_public_prices'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="ecommerce_public_prices">
                                                            <strong>{{ __('global.show_prices_without_login') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.show_prices_without_login') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="ecommerce_require_approval" value="0">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="ecommerce_require_approval" name="ecommerce_require_approval" value="1"
                                                            {{ $settings['ecommerce_require_approval'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="ecommerce_require_approval">
                                                            <strong>{{ __('global.require_order_approval') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.require_order_approval') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-0">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="sales_locations" value="0">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="sales_locations" name="sales_locations" value="1"
                                                            {{ $settings['sales_locations'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="sales_locations">
                                                            <strong>{{ __('global.multiple_sales_locations') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.multiple_sales_locations') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Display Settings --}}
                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bx-desktop me-1"></i>
                                                    {{ __('global.display_settings') }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <label for="ecommerce_min_order_amount">
                                                        {{ __('global.minimum_order_amount') }}
                                                        <i class="bx bx-info-circle text-muted"
                                                           data-bs-toggle="tooltip"
                                                           title="{{ __('messages.minimum_order_amount') }}"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-dollar"></i>
                                                        </span>
                                                        <input type="number" class="form-control"
                                                               id="ecommerce_min_order_amount"
                                                               name="ecommerce_min_order_amount"
                                                               step="0.01" min="0"
                                                               value="{{ $settings['ecommerce_min_order_amount'] }}">
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.minimum_order_amount_help') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="ecommerce_products_per_page">
                                                        {{ __('global.products_per_page') }}
                                                        <i class="bx bx-info-circle text-muted"
                                                           data-bs-toggle="tooltip"
                                                           title="{{ __('messages.products_per_page') }}"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-grid-alt"></i>
                                                        </span>
                                                        <input type="number" class="form-control"
                                                               id="ecommerce_products_per_page"
                                                               name="ecommerce_products_per_page"
                                                               min="1" max="100"
                                                               value="{{ $settings['ecommerce_products_per_page'] }}">
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.products_per_page_help') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="ecommerce_processing_time">
                                                        {{ __('global.order_processing_time') }}
                                                        <i class="bx bx-info-circle text-muted"
                                                            data-bs-toggle="tooltip" title="{{ __('messages.order_processing_time') }}"></i>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-time"></i>
                                                        </span>
                                                        <input type="text" class="form-control"
                                                               id="ecommerce_processing_time"
                                                               name="ecommerce_processing_time"
                                                               placeholder="1-2 business hours"
                                                               value="{{ $settings['ecommerce_processing_time'] ?? '1-2 business hours' }}">
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.order_processing_time_help') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-0">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" name="ecommerce_show_stock" value="0">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="ecommerce_show_stock" name="ecommerce_show_stock" value="1"
                                                            {{ $settings['ecommerce_show_stock'] ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="ecommerce_show_stock">
                                                            <strong>{{ __('global.show_stock_levels') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.show_stock_levels') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    {{--Delivery Settings --}}
                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bxs-truck me-1"></i>
                                                    {{ 'Delivery Settings' }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-0">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="ecommerce_delivery_enabled"
                                                               id="ecommerce_delivery_enabled"
                                                            {{ $settings['ecommerce_delivery_enabled'] ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                               for="ecommerce_delivery_enabled">
                                                            <strong>{{ __('Enable Delivery') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ __('messages.enable_ecommerce_deliveries') }}
                                                    </small></div>
                                            </div>
                                        </div>
                                    </div>

                                    {{--Collection Settings --}}
                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bx-box me-1"></i>
                                                    {{ 'Collection Settings' }}
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#collectionAddressModal">
                                                    <i class="fas fa-edit"></i> {{ __('Edit Collection Address') }}
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                @if($currentCompany->hasAddress('collection'))
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>{{ __('Collection Point Name') }}:</strong></p>
                                                            <p>{{ $currentCompany->collection_address->name ?? 'N/A' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>{{ __('Phone') }}:</strong></p>
                                                            <p>{{ $currentCompany->collection_address->phone ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-12">
                                                            <p class="mb-1"><strong>{{ __('Address') }}:</strong></p>
                                                            <p class="mb-0">{{ $currentCompany->collection_address->address_1 }}</p>
                                                            @if($currentCompany->collection_address->address_2)
                                                                <p class="mb-0">{{ $currentCompany->collection_address->address_2 }}</p>
                                                            @endif
                                                            <p class="mb-0">
                                                                {{ $currentCompany->collection_address->city }}
                                                                @if($currentCompany->collection_address->state), {{ $currentCompany->collection_address->state }}@endif
                                                            </p>
                                                            <p class="mb-0">{{ $currentCompany->collection_address->zip }}</p>
                                                            <p>{{ $currentCompany->collection_address->country->name ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning mb-0">
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                        {{ __('No collection address configured. Customers will not see collection details at checkout.') }}
                                                    </div>
                                                @endif
                                            </div>
                                    </div>
                                </div>
                                </div>

                                <div class="row mb-4">
                                    {{--Future Use --}}
                                    <div class="col-lg-6">

                                    </div>

                                    {{--Collection Hours --}}
                                    <div class="col-lg-6">
                                        <div class="card mb-4">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">{{ __('Collection Hours') }}</h5>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#collectionHoursModal">
                                                    <i class="fas fa-clock"></i> {{ __('Edit Hours') }}
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-2">
                                                        <i class="fas fa-clock text-primary me-2"></i>
                                                        <strong>{{ __('Weekdays') }}:</strong>
                                                        {{ $currentCompany->getSetting('ecommerce_collection_hours_weekday') }}
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-clock text-primary me-2"></i>
                                                        <strong>{{ __('Saturday') }}:</strong>
                                                        {{ $currentCompany->getSetting('ecommerce_collection_hours_saturday') }}
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-clock text-primary me-2"></i>
                                                        <strong>{{ __('Sunday') }}:</strong>
                                                        {{ $currentCompany->getSetting('ecommerce_collection_hours_sunday') }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Action Buttons --}}
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save me-1"></i>
                                                {{ __('global.update') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Collection Address Modal --}}
    <div class="modal fade" id="collectionAddressModal" tabindex="-1" aria-labelledby="collectionAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('settings.company.ecommerce.collection-address') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="collectionAddressModalLabel">{{ __('Collection Address') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="collection_name" class="form-label">{{ __('Location Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="collection_name" name="name"
                                       value="{{ old('name', $company->collection_address->name ?? '') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="collection_phone" class="form-label">{{ __('Phone') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="collection_phone" name="phone"
                                       value="{{ old('phone', $company->collection_address->phone ?? '') }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="collection_address_1" class="form-label">{{ __('Address Line 1') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address_1') is-invalid @enderror"
                                   id="collection_address_1" name="address_1"
                                   value="{{ old('address_1', $company->collection_address->address_1 ?? '') }}" required>
                            @error('address_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="collection_address_2" class="form-label">{{ __('Address Line 2') }}</label>
                            <input type="text" class="form-control @error('address_2') is-invalid @enderror"
                                   id="collection_address_2" name="address_2"
                                   value="{{ old('address_2', $company->collection_address->address_2 ?? '') }}">
                            @error('address_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="collection_city" class="form-label">{{ __('City') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                       id="collection_city" name="city"
                                       value="{{ old('city', $company->collection_address->city ?? '') }}" required>
                                @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="collection_state" class="form-label">{{ __('State/Province') }}</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror"
                                       id="collection_state" name="state"
                                       value="{{ old('state', $company->collection_address->state ?? '') }}">
                                @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="collection_zip" class="form-label">{{ __('Postal Code') }}</label>
                                <input type="text" class="form-control @error('zip') is-invalid @enderror"
                                       id="collection_zip" name="zip"
                                       value="{{ old('zip', $company->collection_address->zip ?? '') }}">
                                @error('zip')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="collection_country_id" class="form-label">{{ __('Country') }} <span class="text-danger">*</span></label>
                                <select class="form-select @error('country_id') is-invalid @enderror"
                                        id="collection_country_id" name="country_id" required>
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach(\App\Models\Country::all() as $country)
                                        <option value="{{ $country->id }}"
                                            {{ old('country_id', $company->collection_address->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Collection Address') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Collection Hours Modal --}}
    <div class="modal fade" id="collectionHoursModal" tabindex="-1" aria-labelledby="collectionHoursModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('settings.ecommerce.update') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="collectionHoursModalLabel">{{ __('Collection Hours') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            {{ __('Enter the collection hours that will be displayed to customers at checkout.') }}
                        </p>

                        <div class="mb-3">
                            <label for="weekday_hours" class="form-label">
                                {{ __('Monday - Friday') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('ecommerce_collection_hours_weekday') is-invalid @enderror"
                                   id="weekday_hours"
                                   name="ecommerce_collection_hours_weekday"
                                   value="{{ old('ecommerce_collection_hours_weekday', $currentCompany->getSetting('ecommerce_collection_hours_weekday')) }}"
                                   placeholder="e.g., Monday - Friday: 8:00 AM - 4:30 PM"
                                   required>
                            <small class="text-muted">Example: Monday - Friday: 8:00 AM - 4:30 PM</small>
                            @error('ecommerce_collection_hours_weekday')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="saturday_hours" class="form-label">
                                {{ __('Saturday') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('ecommerce_collection_hours_saturday') is-invalid @enderror"
                                   id="saturday_hours"
                                   name="ecommerce_collection_hours_saturday"
                                   value="{{ old('ecommerce_collection_hours_saturday', $currentCompany->getSetting('ecommerce_collection_hours_saturday')) }}"
                                   placeholder="e.g., Saturday: 8:00 AM - 12:00 PM"
                                   required>
                            <small class="text-muted">Example: Saturday: 8:00 AM - 12:00 PM or Saturday: Closed</small>
                            @error('ecommerce_collection_hours_saturday')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sunday_hours" class="form-label">
                                {{ __('Sunday') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('ecommerce_collection_hours_sunday') is-invalid @enderror"
                                   id="sunday_hours"
                                   name="ecommerce_collection_hours_sunday"
                                   value="{{ old('ecommerce_collection_hours_sunday', $currentCompany->getSetting('ecommerce_collection_hours_sunday')) }}"
                                   placeholder="e.g., Sunday: Closed"
                                   required>
                            <small class="text-muted">Example: Sunday: 9:00 AM - 1:00 PM or Sunday: Closed</small>
                            @error('ecommerce_collection_hours_sunday')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Hours') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialize Bootstrap tooltips
            document.addEventListener('DOMContentLoaded', function() {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
@endsection
