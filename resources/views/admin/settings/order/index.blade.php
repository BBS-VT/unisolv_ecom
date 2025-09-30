@extends('layouts.master', ['page' => 'settings'])

@section('title', __('global.order_settings'))

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
                                @include('admin.settings._aside', ['tab' => 'order'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('settings.order.update') }}" method="POST">
                                @include('layouts._form_errors')
                                @csrf

                                <div class="row">
                                    {{-- Display Options --}}
                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bx-show me-1"></i>
                                                    {{ __('global.display_options') }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="displaySellingPrices" name="display_selling_prices"
                                                            {{ $currentCompany->getSetting('display_selling_prices') ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="displaySellingPrices">
                                                            <strong>{{ __('global.display_selling_prices') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.display_selling_prices') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="displayCostPrices" name="display_cost_prices"
                                                            {{ $currentCompany->getSetting('display_cost_prices') ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="displayCostPrices">
                                                            <strong>{{ __('global.display_cost_prices') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.display_cost_prices') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notification Options --}}
                                    <div class="col-lg-6">
                                        <div class="card border h-100">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bx-bell me-1"></i>
                                                    {{ __('global.notification_options') }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-3">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="orderCustomerConfirmation" name="order_customer_confirmation"
                                                            {{ $currentCompany->getSetting('order_customer_confirmation') ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="orderCustomerConfirmation">
                                                            <strong>{{ __('global.order_customer_confirmation') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.order_customer_confirmation') }}
                                                    </small>
                                                </div>

                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input"
                                                               id="orderFulfillmentNotification" name="order_fulfillment_notification"
                                                            {{ $currentCompany->getSetting('order_fulfillment_notification') ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="orderFulfillmentNotification">
                                                            <strong>{{ __('global.order_fulfillment_notification') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.order_fulfillment_notification') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Fulfillment Settings --}}
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bx-package me-1"></i>
                                                    {{ __('global.fulfillment_settings') }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-0">
                                                            <label for="fulfillment_mailbox">
                                                                {{ __('global.fulfillment_mailbox') }}
                                                                <i class="bx bx-info-circle text-muted"
                                                                   data-bs-toggle="tooltip"
                                                                   title="{{ __('messages.fulfillment_mailbox') }}"></i>
                                                            </label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">
                                                                    <i class="bx bx-envelope"></i>
                                                                </span>
                                                                <input type="email" name="fulfillment_mailbox"
                                                                       id="fulfillment_mailbox"
                                                                       class="form-control @error('fulfillment_mailbox') is-invalid @enderror"
                                                                       value="{{ old('fulfillment_mailbox', $currentCompany->getSetting('fulfillment_mailbox') ?? '') }}"
                                                                       placeholder="fulfillment@example.com">
                                                                @error('fulfillment_mailbox')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <small class="form-text text-muted">
                                                                {{ __('messages.fulfillment_mailbox_help') }}
                                                            </small>
                                                        </div>
                                                    </div>
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
