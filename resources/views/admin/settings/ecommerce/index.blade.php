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

                                <div class="row">
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
