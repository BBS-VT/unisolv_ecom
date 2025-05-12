@extends('layouts.app', ['page' => 'settings'])

@section('title', __('global.ecommerceSettings'))

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-2 col-sm-3">
            @include('admin.settings._aside', ['tab' => 'ecommerce'])
        </div>
        <div class="col-xl-10 col-sm-9">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('E-commerce Settings') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.ecommerce.update') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <h5>General Settings</h5>

                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="b2b_ecommerce_enabled" value="0">
                                    <input class="form-check-input" type="checkbox" id="b2b_ecommerce_enabled"
                                           name="b2b_ecommerce_enabled" value="1"
                                        {{ $settings['b2b_ecommerce_enabled'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="b2b_ecommerce_enabled">
                                        Enable B2B E-commerce
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="ecommerce_guest_checkout" value="0">
                                    <input class="form-check-input" type="checkbox" id="ecommerce_guest_checkout"
                                           name="ecommerce_guest_checkout" value="1"
                                        {{ $settings['ecommerce_guest_checkout'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ecommerce_guest_checkout">
                                        Allow Guest Checkout
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="ecommerce_public_prices" value="0">
                                    <input class="form-check-input" type="checkbox" id="ecommerce_public_prices"
                                           name="ecommerce_public_prices" value="1"
                                        {{ $settings['ecommerce_public_prices'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ecommerce_public_prices">
                                        Show Prices Without Login
                                    </label>
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="ecommerce_require_approval" value="0">
                                    <input class="form-check-input" type="checkbox" id="ecommerce_require_approval"
                                           name="ecommerce_require_approval" value="1"
                                        {{ $settings['ecommerce_require_approval'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ecommerce_require_approval">
                                        Require Order Approval
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5>Display Settings</h5>

                                <div class="mb-3">
                                    <label for="ecommerce_min_order_amount" class="form-label">
                                        Minimum Order Amount
                                    </label>
                                    <input type="number" class="form-control" id="ecommerce_min_order_amount"
                                           name="ecommerce_min_order_amount" step="0.01"
                                           value="{{ $settings['ecommerce_min_order_amount'] }}">
                                </div>

                                <div class="mb-3">
                                    <label for="ecommerce_products_per_page" class="form-label">
                                        Products Per Page
                                    </label>
                                    <input type="number" class="form-control" id="ecommerce_products_per_page"
                                           name="ecommerce_products_per_page" min="1" max="100"
                                           value="{{ $settings['ecommerce_products_per_page'] }}">
                                </div>

                                <div class="form-check form-switch mb-3">
                                    <input type="hidden" name="ecommerce_show_stock" value="0">
                                    <input class="form-check-input" type="checkbox" id="ecommerce_show_stock"
                                           name="ecommerce_show_stock" value="1"
                                        {{ $settings['ecommerce_show_stock'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ecommerce_show_stock">
                                        Show Stock Levels
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
