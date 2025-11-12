@extends('layouts.app')

@push('style')
    <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
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
                            <h4 class="card-title">{{ trans('global.add') }} {{ trans('cruds.deal.title_singular') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <a href="{{ route('deals.index') }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route("deals.store") }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label for="DealDescription" class="col-sm-4 col-form-label text-start required">{{ trans('cruds.deal.fields.description') }}</label>
                                        <div class="col-lg-8">
                                            <input class="form-control" type="text" value="{{ old('DealDescription', '')  }}" id="DealDescription" name="DealDescription" required>
                                            @if($errors->has('DealDescription'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('DealDescription') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.deal.fields.description_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group bootstrap-select-1">
                                                <label>{{ trans('cruds.deal.fields.product') }}</label>
                                                <select class="select2 form-control mb-3 form-select {{ $errors->has('StockItemID') ? 'is-invalid' : '' }}"
                                                        id="StockItemID" name="StockItemID" style="width: 100%; height:36px;">
                                                    <option disabled selected value> -- select an option -- </option>
                                                    @foreach($products as $id => $product )
                                                        <option value="{{ $id }}" >
                                                            {{ intval( ltrim( $product->StockCode, '0')) }} &nbsp;
                                                            {{ $product->StockItemName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group bootstrap-select-1">
                                                <label >{{ trans('cruds.deal.fields.department') }}</label>
                                                <select class="select2 form-control mb-3 form-select {{ $errors->has('StockGroupID') ? 'is-invalid' : '' }}"
                                                        id="StockGroupID" name="StockGroupID" style="width: 100%; height:36px;">
                                                    <option disabled selected value> -- select an option -- </option>
                                                    @foreach($categories as $id => $category )
                                                        <option value="{{ $id }}" >{{ $category }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group bootstrap-select-1">
                                                <label >{{ trans('cruds.deal.fields.buygroup') }}</label>
                                                <select class="select2 form-control mb-3 form-select {{ $errors->has('BuyingGroupID') ? 'is-invalid' : '' }}"
                                                        id="BuyingGroupID" name="BuyingGroupID" style="width: 100%; height:36px;">
                                                    <option disabled selected value> -- select an option -- </option>
                                                    @foreach($buyinggroups as $id => $buyingroup )
                                                        <option value="{{ $id }}" >{{ $buyingroup }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group bootstrap-select-1">
                                                <label >{{ trans('cruds.deal.fields.customergroup') }}</label>
                                                <select class="select2 form-control mb-3 form-select {{ $errors->has('CustomerCategoryID') ? 'is-invalid' : '' }}"
                                                        id="CustomerCategoryID" name="CustomerCategoryID" style="width: 100%; height:36px;">
                                                    <option disabled selected value> -- select an option -- </option>
                                                    @foreach($customergroups as $id => $customergroup )
                                                        <option value="{{ $id }}" >{{ $customergroup }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group bootstrap-select-1">
                                                <label >{{ trans('cruds.deal.fields.customer') }}</label>
                                                <select class="select2 form-control mb-3 form-select {{ $errors->has('CustomerID') ? 'is-invalid' : '' }}"
                                                        id="CustomerID" name="CustomerID" style="width: 100%; height:36px;">
                                                    <option disabled selected value> -- select an option -- </option>
                                                    @foreach($customers as $id => $customer )
                                                        <option value="{{ $id }}" >
                                                            {{ $customer->acc_main }} &nbsp;
                                                            {{ $customer->CustomerName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="DiscountAmount" class="col-sm-4 col-form-label text-start">{{ trans('cruds.deal.fields.discount') }}</label>
                                        <div class="col-lg-8">
                                            <input class="form-control" type="text" value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                                   id="DiscountAmount" name="DiscountAmount">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="DiscountPercentage" class="col-sm-4 col-form-label text-start">{{ trans('cruds.deal.fields.discountperc') }}</label>
                                        <div class="input-group col-lg-8">
                                            <input type="text" id="DiscountPercentage" name="DiscountPercentage" class="form-control" placeholder="">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-percentage"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="UnitPrice" class="col-sm-4 col-form-label text-start">{{ trans('cruds.deal.fields.unitprice') }}</label>
                                        <div class="col-lg-8">
                                            <input class="form-control" type="text" value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                                   id="UnitPrice" name="UnitPrice">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="StartDate" class="col-sm-6 text-start col-form-label">{{ trans('cruds.deal.fields.startdate') }}</label>
                                                <div class="col-sm-12">
                                                    <input class="form-control" type="date" value="{{ date('Y-m-d') }}" id="StartDate" name="StartDate">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="EndDate" class="col-sm-6 col-form-label text-start">{{ trans('cruds.deal.fields.enddate') }}</label>
                                                <div class="col-sm-12">
                                                    <input class="form-control" type="date" value="{{ date('Y-m-d') }}" id="EndDate" name="EndDate">
                                                    <input type="hidden" name="LastEditedBy" id="LastEditedBy" value="{{ auth()->user()->id }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<!--                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="submit" value="submit" class="btn btn-primary btn-sm">Save</button>
                        </div>-->

                        <div class="float-end">
                            <a class="btn btn-default btn-danger" href="{{ route('deals.index') }}">{{ trans('global.cancel') }}</a>
                            <input class="btn btn-primary" type="submit" value="{{ trans('global.save') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    <script src="{{ asset('pages/jquery.forms-advanced.js') }}"></script>

@endpush
