@extends('layouts.app', ['page' => 'settings'])

@section('title', __('global.order_settings'))

@section('content')
    <div class="row">
        <div class="col-sm-12 mb-2">
            <div class="page-title-box">
                <div class="row">
                    <div class="col">
                        <h4 class="page-title">Settings</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-2 col-sm-3">
            @include('admin.settings._aside', ['tab' => 'orders'])
        </div>
        <div class="col-xl-10 col-sm-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ __('global.order_settings') }}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col card-body bg-white">
                            <form action="{{ route('settings.order.update') }}" method="POST">
                                @include('layouts._form_errors')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6">
                                        <fieldset>
                                            <legend class="h6">{{ __('Display Options')}}</legend>
                                            <div class="form-check mb-3">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="displaySellingPrices"
                                                    name="display_selling_prices"
                                                    {{ $currentCompany->getSetting('display_selling_prices') ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="displaySellingPrices">
                                                    {{ __('Display Additional Selling Prices') }}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="displayCostPrices"
                                                    name="display_cost_prices"
                                                    {{ $currentCompany->getSetting('display_cost_prices') ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="displayCostPrices">
                                                    {{ __('Display Additional Cost Prices') }}
                                                </label>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <div class="col-lg-6">
                                        <fieldset>
                                            <legend class="h6">{{ __('Notification Options') }}</legend>
                                            <div class="form-check mb-3">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="orderCustomerConfirmation"
                                                    name="order_customer_confirmation"
                                                    {{ $currentCompany->getSetting('order_customer_confirmation') ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="orderCustomerConfirmation">
                                                    {{ __('Send Order Confirmation to Customer') }}
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="orderFulfillmentNotification"
                                                    name="order_fulfillment_notification"
                                                    {{ $currentCompany->getSetting('order_fulfillment_notification') ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="orderFulfillmentNotification">
                                                    {{ __('Notify Fulfillment Team') }}
                                                </label>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="form-group">
                                        <label for="fulfillment_mailbox">{{ __('global.fulfillment_mailbox') }}</label>
                                        <input type="email" name="fulfillment_mailbox" id="fulfillment_mailbox" class="form-control"
                                               value="{{ old('fulfillment_mailbox', $currentCompany->getSetting('fulfillment_mailbox') ?? '') }}">
                                        <small class="form-text text-muted">
                                            {{ __('messages.fulfillment_mailbox') }}
                                        </small>
                                    </div>
                                </div>




                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-danger">{{ __('global.update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
