@extends('layouts.app')

@push('style')
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ __('global.edit') }} {{ __('cruds.customer.title_singular') }} -
                                <span class="text-danger">{{ $customer->CustomerName }}</span>
                            </h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customers.update', [$customer->id]) }}"  enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf
                        <div class="row">
                            <div class="col-md-1">
                                <div class="form-group {{ $errors->has('acc_main') ? 'has-error' : '' }}">
                                    <label for="acc_main">{{ trans('cruds.customer.fields.acc_main') }} <span style="color:darkred";>*</span></label>
                                    <input type="text" id="acc_main" name="acc_main" class="form-control" value="{{ old('acc_main', isset($customer) ? $customer->acc_main : '') }}" required>
                                    @if($errors->has('acc_main'))
                                        <p class="help-block">
                                            {{ $errors->first('acc_main') }}
                                        </p>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.acc_main_helper') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group {{ $errors->has('acc_sub') ? 'has-error' : '' }}">
                                    <label for="acc_sub">{{ trans('cruds.customer.fields.acc_sub') }}</label>
                                    <input type="text" id="acc_sub" name="acc_sub" class="form-control" value="{{ old('acc_sub', isset($customer) ? $customer->sub_acc : '000') }}">
                                    @if($errors->has('acc_sub'))
                                        <p class="help-block">
                                            {{ $errors->first('acc_sub') }}
                                        </p>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.acc_sub_helper') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group {{ $errors->has('CustomerName') ? 'has-error' : '' }}">
                                    <label for="CustomerName">{{ trans('cruds.customer.fields.name') }}</label>
                                    <input type="text" id="CustomerName" name="CustomerName" class="form-control" value="{{ old('CustomerName', isset($customer) ? $customer->CustomerName : '') }}">
                                    @if($errors->has('CustomerName'))
                                        <p class="help-block">
                                            {{ $errors->first('CustomerName') }}
                                        </p>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.name_helper') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group {{ $errors->has('StoreEAN') ? 'has-error' : '' }}">
                                    <label for="StoreEAN">{{ trans('cruds.customer.fields.store_ean') }}</label>
                                    <input type="text" id="StoreEAN" name="StoreEAN" class="form-control"
                                           value="{{ old('StoreEAN', isset($customer) ? $customer->StoreEAN : '') }}">
                                    @if($errors->has('StoreEAN'))
                                        <p class="help-block">
                                            {{ $errors->first('StoreEAN') }}
                                        </p>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.store_ean_helper') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-1 float-right">
                                <div class="form-group">
                                    <label for="AccountOpenedDate" class="float-right">{{ trans('cruds.customer.fields.opened_date') }}</label>
                                    <input type="text" id="AccountOpenedDate" name="AccountOpenedDate" class="form-control text-center"
                                           value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group bootstrap-select-1">
                                    <label for="BillToCustomerID">{{ trans('cruds.customer.fields.billing') }}</label>
                                    <select class="select2 form-control mb-3 {{ $errors->has('BillToCustomerID') ? 'is-invalid' : '' }}" style="width: 100%; height:36px;" name="customer[BillToCustomerID]">
                                        @if( $customer->BillToCustomerID !== 9999 )
                                            @foreach($billingCustomers as $id => $billingCustomer)
                                                <option value="{{ $id }}" {{ ($billingCustomer == $customer->billingCustomer->id) ? 'selected' : '' }}>{{ $billingCustomer }} </option>
                                            @endforeach
                                        @else
                                            <option value="null" selected>{{ __('global.pleaseSelect') }}</option>
                                            @foreach($billingCustomers as $id => $billingCustomer)
                                                <option value="{{ $customer->acc_main }}" >{{ $billingCustomer }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($errors->has('BillToCustomerID'))
                                        <em class="invalid-feedback">
                                            {{ $errors->first('billingCustomer') }}
                                        </em>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.billing_helper') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group bootstrap-select-1">
                                    <label class="required" for="CustomerCategoryID">{{ __('cruds.customer.fields.category') }} </label>
                                    <select class="select2 form-control mb-3 {{$errors->has('CustomerCategoryID') ? 'is-invalid' : '' }}" name="CustomerCategoryID">
                                        @foreach($customerCategories as $id => $customerCategory)
                                            <option value="{{ $id }}" {{ ($customerCategory == $customer->customerCategory->AccountType) ? 'selected' : '' }}>{{ $customerCategory }}</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('CustomerCategoryID'))
                                        <em class="invalid-feedback">
                                            {{ $errors->first('customerCategory') }}
                                        </em>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.category_helper') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="BuyingGroupID">{{ trans('cruds.customer.fields.contract') }}</label>
                                    <select class="select2 form-control mb-3 custom-select {{ $errors->has('BuyingGroupID') ? 'is-invalid' : '' }}" name="BuyingGroupID">
                                        @if( $customer->BuyingGroupID !== 9999 )
                                            <option value="null" selected>{{ __('global.pleaseSelect') }}</option>
                                            @foreach($buyingGroups as $id => $buyingGroup)
                                                <option value="{{ $customer->buyingGroup->BuyingGroupName }}" >{{ $buyingGroup }}</option>
                                            @endforeach
                                        @else
                                            @foreach($buyingGroups as $id => $buyingGroup)
                                                <option value="{{ $id }}" {{ ($buyingGroup == $customer->buyingGroup->BuyingGroupName) ? 'selected' : '' }}> {{ $buyingGroup }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($errors->has('BuyingGroupID'))
                                        <em class="invalid-feedback">
                                            {{ $errors->first('buyingGroup') }}
                                        </em>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.contract_helper') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required" for="SalesRepID">{{ trans('cruds.customer.fields.salerep') }}</label>
                                    <select class="select2 form-control mb-3 {{ $errors->has('SalesRepID') ? 'is-invalid' : '' }}" name="SalesRepID" required>

                                            @foreach($salesreps as $id => $salesrep)
                                                <option value="{{ $id }}" {{ ($id == $customer->salesrep->RepCode) ? 'selected' : '' }}> {{ $salesrep }} </option>
                                            @endforeach


                                    </select>
                                    @if($errors->has('SalesRepID'))
                                        <em class="invalid-feedback">
                                            {{ $errors->first('salesrep') }}
                                        </em>
                                    @endif
                                    <p class="helper-block">
                                        {{ trans('cruds.customer.fields.salerep_helper') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ trans('global.postal_address') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group {{ $errors->has('PostalAddressLine1') ? 'has-error' : '' }}">
                                            <label for="PostalAddressLine1">{{ trans('cruds.customer.fields.address_1') }}</label>
                                            <input type="text" id="PostalAddressLine1" name="PostalAddressLine1" class="form-control" value="{{ old('PostalAddressLine1', isset($customer) ? $customer->PostalAddressLine1 : '') }}">
                                            @if($errors->has('PostalAddressLine1'))
                                                <p class="help-block">
                                                    {{ $errors->first('PostalAddressLine1') }}
                                                </p>
                                            @endif
                                            <p class="helper-block">
                                                {{ trans('cruds.customer.fields.address_1_helper') }}
                                            </p>
                                        </div>
                                        <div class="form-group {{ $errors->has('PostalAddressLine2') ? 'has-error' : '' }}">
                                            <label for="PostalAddressLine2">{{ trans('cruds.customer.fields.address_2') }}</label>
                                            <input type="text" id="PostalAddressLine2" name="PostalAddressLine2" class="form-control" value="{{ old('PostalAddressLine2', isset($customer) ? $customer->PostalAddressLine2 : '') }}">
                                            @if($errors->has('PostalAddressLine2'))
                                                <p class="help-block">
                                                    {{ $errors->first('PostalAddressLine2') }}
                                                </p>
                                            @endif
                                            <p class="helper-block">
                                                {{ trans('cruds.customer.fields.address_2_helper') }}
                                            </p>
                                        </div>
                                        <div class="form-group {{ $errors->has('PostalCity') ? 'has-error' : '' }}">
                                            <label for="PostalC">{{ trans('cruds.customer.fields.city') }}</label>
                                            <input type="text" id="PostalCity" name="PostalCity" class="form-control" value="{{ old('PostalCity', isset($customer) ? $customer->PostalCity : '') }}">
                                            @if($errors->has('PostalCity'))
                                                <p class="help-block">
                                                    {{ $errors->first('PostalCity') }}
                                                </p>
                                            @endif
                                            <p class="helper-block">
                                                {{ trans('cruds.customer.fields.city_helper') }}
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group {{ $errors->has('PostalPostalCode') ? 'has-error' : '' }}">
                                                    <label for="PostalPostalCode">{{ trans('cruds.customer.fields.postal_code') }}</label>
                                                    <input type="text" id="PostalPostalCode" name="PostalPostalCode" class="form-control" value="{{ old('PostalPostalCode', isset($customer) ? $customer->PostalPostalCode : '') }}">
                                                    @if($errors->has('PostalPostalCode'))
                                                        <p class="help-block">
                                                            {{ $errors->first('PostalPostalCode') }}
                                                        </p>
                                                    @endif
                                                    <p class="helper-block">
                                                        {{ trans('cruds.customer.fields.postal_helper') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <input type="hidden" id="LastEditedBy" name="LastEditedBy" value="{{ auth()->id() }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">{{ trans('global.delivery_address') }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group {{ $errors->has('DeliveryAddressLine1') ? 'has-error' : '' }}">
                                            <label for="DeliveryAddressLine1">{{ trans('cruds.customer.fields.address_1') }}</label>
                                            <input type="text" id="DeliveryAddressLine1" name="DeliveryAddressLine1" class="form-control" value="{{ old('DeliveryAddressLine1', isset($customer) ? $customer->DeliveryAddressLine1 : '') }}">
                                            @if($errors->has('DeliveryAddressLine1'))
                                                <p class="help-block">
                                                    {{ $errors->first('DeliveryAddressLine1') }}
                                                </p>
                                            @endif
                                            <p class="helper-block">
                                                {{ trans('cruds.customer.fields.address_1_helper') }}
                                            </p>
                                        </div>
                                        <div class="form-group {{ $errors->has('DeliveryAddressLine2') ? 'has-error' : '' }}">
                                            <label for="DeliveryAddressLine2">{{ trans('cruds.customer.fields.address_2') }}</label>
                                            <input type="text" id="DeliveryAddressLine2" name="DeliveryAddressLine2" class="form-control" value="{{ old('DeliveryAddressLine2', isset($customer) ? $customer->DeliveryAddressLine2 : '') }}">
                                            @if($errors->has('DeliveryAddressLine2'))
                                                <p class="help-block">
                                                    {{ $errors->first('DeliveryAddressLine2') }}
                                                </p>
                                            @endif
                                            <p class="helper-block">
                                                {{ trans('cruds.customer.fields.address_2_helper') }}
                                            </p>
                                        </div>
                                        <div class="form-group {{ $errors->has('DeliveryCity') ? 'has-error' : '' }}">
                                            <label for="DeliveryCity">{{ trans('cruds.customer.fields.city') }}</label>
                                            <input type="text" id="DeliveryCity" name="DeliveryCity" class="form-control" value="{{ old('DeliveryCity', isset($customer) ? $customer->DeliveryCity : '') }}">
                                            @if($errors->has('DeliveryCity'))
                                                <p class="help-block">
                                                    {{ $errors->first('DeliveryCity') }}
                                                </p>
                                            @endif
                                            <p class="helper-block">
                                                {{ trans('cruds.customer.fields.city_helper') }}
                                            </p>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group {{ $errors->has('DeliveryPostalCode') ? 'has-error' : '' }}">
                                                    <label for="DeliveryPostalCode">{{ trans('cruds.customer.fields.postal_code') }}</label>
                                                    <input type="text" id="DeliveryPostalCode" name="DeliveryPostalCode" class="form-control" value="{{ old('DeliveryPostalCode', isset($customer) ? $customer->DeliveryPostalCode : '') }}">
                                                    @if($errors->has('DeliveryPostalCode'))
                                                        <p class="help-block">
                                                            {{ $errors->first('DeliveryPostalCode') }}
                                                        </p>
                                                    @endif
                                                    <p class="helper-block">
                                                        {{ trans('cruds.customer.fields.postal_helper') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-6">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group row {{ $errors->has('VatNr') ? 'has-error' : '' }}">
                                            <label for="VatNr" class="col-sm-2 col-form-label">{{ trans('cruds.customer.fields.vat_nr') }}</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="VatNr" name="VatNr" class="form-control" value="{{ old('VatNr', isset($customer) ? $customer->VatNr : '') }}">
                                                @if($errors->has('VatNr'))
                                                    <p class="help-block">
                                                        {{ $errors->first('VatNr') }}
                                                    </p>
                                                @endif
                                                <p class="helper-block">
                                                    {{ trans('cruds.customer.fields.vat_nr_helper') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-group row {{ $errors->has('PhoneNumber') ? 'has-error' : '' }}">
                                            <label for="PhoneNumber" class="col-sm-2 col-form-label">{{ trans('cruds.customer.fields.phone') }}</label>
                                            <div class="col-sm-10">
                                                <input type="tel" id="PhoneNumber" name="PhoneNumber" class="form-control" value="{{ old('PhoneNumber', isset($customer) ? $customer->PhoneNumber : '') }}">
                                                @if($errors->has('PhoneNumber'))
                                                    <p class="help-block">
                                                        {{ $errors->first('PhoneNumber') }}
                                                    </p>
                                                @endif
                                                <p class="helper-block">
                                                    {{ trans('cruds.customer.fields.phone_helper') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-group row {{ $errors->has('FaxNumber') ? 'has-error' : '' }}">
                                            <label for="FaxNumber" class="col-sm-2 col-form-label">{{ trans('cruds.customer.fields.fax') }}</label>
                                            <div class="col-sm-10">
                                                <input type="tel" id="FaxNumber" name="FaxNumber" class="form-control" value="{{ old('FaxNumber', isset($customer) ? $customer->FaxNumber : '') }}">
                                                @if($errors->has('FaxNumber'))
                                                    <p class="help-block">
                                                        {{ $errors->first('FaxNumber') }}
                                                    </p>
                                                @endif
                                                <p class="helper-block">
                                                    {{ trans('cruds.customer.fields.fax_helper') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-group row {{ $errors->has('website') ? 'has-error' : '' }}">
                                            <label for="WebsiteURL" class="col-sm-2 col-form-label">{{ trans('cruds.customer.fields.website') }}</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="WebsiteURL" name="WebsiteURL" class="form-control" value="{{ old('WebsiteURL', isset($customer) ? $customer->WebsiteURL : '') }}">
                                                @if($errors->has('WebsiteURL'))
                                                    <p class="help-block">
                                                        {{ $errors->first('WebsiteURL') }}
                                                    </p>
                                                @endif
                                                <p class="helper-block">
                                                    {{ trans('cruds.customer.fields.website_helper') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="form-group row {{ $errors->has('GeneralEmailAddress') ? 'has-error' : '' }}">
                                            <label for="GeneralEmailAddress" class="col-sm-2 col-form-label">{{ trans('cruds.customer.fields.email') }}</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="GeneralEmailAddress" name="GeneralEmailAddress" class="form-control"
                                                       value="{{ old('GeneralEmailAddress', isset($customer) ? $customer->GeneralEmailAddress : '') }}">
                                                @if($errors->has('GeneralEmailAddress'))
                                                    <p class="help-block">
                                                        {{ $errors->first('GeneralEmailAddress') }}
                                                    </p>
                                                @endif
                                                <p class="helper-block">
                                                    {{ trans('cruds.customer.fields.email_helper') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group {{ $errors->has('CreditLimit') ? 'has-error' : '' }}">
                                                    <label for="CreditLimit">{{ trans('cruds.customer.fields.creditlimit') }}</label>
                                                    <input type="text" id="CreditLimit" name="CreditLimit" class="form-control" value="{{ old('CreditLimit', isset($customer) ? $customer->CreditLimit : '') }}">
                                                    @if($errors->has('CreditLimit'))
                                                        <p class="help-block">
                                                            {{ $errors->first('CreditLimit') }}
                                                        </p>
                                                    @endif
                                                    <p class="helper-block">
                                                        {{ trans('cruds.customer.fields.creditlimit_helper') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group {{ $errors->has('StandardDiscountPercentage') ? 'has-error' : '' }}">
                                                    <label for="StandardDiscountPercentage">{{ trans('cruds.customer.fields.discount') }}</label>
                                                    <input type="text" id="StandardDiscountPercentage" name="StandardDiscountPercentage" class="form-control" value="{{ old('StandardDiscountPercentage', isset($customer) ? $customer->StandardDiscountPercentage : '') }}">
                                                    @if($errors->has('StandardDiscountPercentage'))
                                                        <p class="help-block">
                                                            {{ $errors->first('StandardDiscountPercentage') }}
                                                        </p>
                                                    @endif
                                                    <p class="helper-block">
                                                        {{ trans('cruds.customer.fields.discount_helper') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group {{ $errors->has('PaymentDays') ? 'has-error' : '' }}">
                                                    <label for="PaymentDays">{{ trans('cruds.customer.fields.paydays') }}</label>
                                                    <input type="text" id="PaymentDays" name="PaymentDays" class="form-control" value="{{ old('PaymentDays', isset($customer) ? $customer->PaymentDays : '') }}">
                                                    @if($errors->has('PaymentDays'))
                                                        <p class="help-block">
                                                            {{ $errors->first('PaymentDays') }}
                                                        </p>
                                                    @endif
                                                    <p class="helper-block">
                                                        {{ trans('cruds.customer.fields.paydays_helper') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group {{ $errors->has('DeliveryRoute') ? 'has-error' : '' }}">
                                                    <label for="DeliveryRoute">{{ trans('cruds.customer.fields.delivery') }}</label>
                                                    <input type="text" id="DeliveryRoute" name="DeliveryRoute" class="form-control" value="{{ old('DeliveryRoute', isset($customer) ? $customer->DeliveryRoute : '') }}">
                                                    @if($errors->has('DeliveryRoute'))
                                                        <p class="help-block">
                                                            {{ $errors->first('DeliveryRoute') }}
                                                        </p>
                                                    @endif
                                                    <p class="helper-block">
                                                        {{ trans('cruds.customer.fields.delivery_helper') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="float-right">
                            <a class="btn btn-default btn-danger" href="{{ route('customers.index') }}">{{ trans('global.cancel') }}</a>
                            <input class="btn btn-primary" type="submit" value="{{ trans('global.save') }}">
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    <script src="{{ URL::asset('pages/jquery.forms-advanced.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#StoreEAN").click(function() {
                $.ajax({
                    type: "GET",
                    url: "/generateStoreEan",
                    success: function (data) {
                        //console.log(data);
                        $('#StoreEAN').val(data);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>

@endpush

