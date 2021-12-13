@extends('layouts.app')

@push('style')
    <link href="{{ asset('/plugins/dropzone/basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <h4 class="card-title">{{ trans('global.view') }} {{ trans('cruds.product.title_singular') }} -
                                <span class="text-danger">{{ $product->StockItemName }} </span>
                            </h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <a href="{{ URL::previous() }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form >
                        <div class="row">
                            <input type="hidden" name="LastEditedBy" id="LastEditedby" value="{{ auth()->user()->id }}">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="required" for="StockCode">{{ trans('cruds.product.fields.sku') }}</label>
                                            <input class="form-control" type="text" name="StockCode" id="StockCode" value="{{ $product->StockCode }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="required control-label" for="StockItemName">{{ trans('cruds.product.fields.name') }}</label>
                                            <input class="form-control " type="text" name="StockItemName" id="StockItemName"
                                                   value="{{ $product->StockItemName }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="categories">{{ trans('cruds.product.fields.category') }}</label>
                                            <select class="form-control mb-3 select2 {{ $errors->has('categories') ? 'is-invalid' : '' }}"
                                                    name="categories[]" id="categories" readonly>
                                                @foreach($categories as $id => $category)
                                                    <option value="{{ $id }}"
                                                            @if($product->categories->contains('id', $id)) selected @endif>
                                                        {{ $category }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('categories'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('categories') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.category_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="brands">{{ trans('cruds.product.fields.brand') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="tags">{{ trans('cruds.product.fields.tag') }}</label>
                                            <input class="form-control " type="text" name="tags" id="tags"
                                                   value="" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="photo">{{ trans('cruds.product.fields.photo') }}</label>
                                    <img class="card-img-top" src="{{ $product->photo ? $product->photo->thumbnail : 'https://via.placeholder.com/500x280&text=Image' }}" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="Size">{{ trans('cruds.product.fields.size') }}</label>
                                    <input class="form-control " type="text" name="Size" id="Size"
                                           value="{{ $product->Size }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="salesunits">{{ trans('cruds.product.fields.units') }}</label>
                                    <input class="form-control " type="text" name="salesunits" id="salesunit"
                                           value="{{ $product->packageType->PackageTypeName ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="Packsize">{{ trans('cruds.product.fields.packsize') }}</label>
                                    <input class="form-control" type="text" name="Packsize" id="Packsize"
                                           value="{{ $product->Packsize }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="Barcode">{{ trans('cruds.product.fields.barcode') }}</label>
                                    <input class="form-control " type="text" name="Barcode" id="Barcode"
                                           value="{{ $product->Barcode }}" readonly>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label for="BinLocation">{{ trans('cruds.product.fields.bin') }}</label>
                                    <input class="form-control" type="text" name="BinLocation" id="BinLocation"
                                           value="{{ !empty($product->stockHolding->BinLocation) ? $product->stockHolding->BinLocation : '' }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="TaxRateID">{{ trans('cruds.product.fields.vatid') }}</label>
                                            <input class="form-control " type="text" name="TaxRateID"
                                                   id="TaxRateID" value="{{ $product->TaxRateID }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="required" for="CostPrice">{{ trans('cruds.product.fields.cost') }}</label>
                                            <input class="form-control text-center " type="number" name="CostPrice" id="CostPrice"
                                                   value="{{ number_format((float)$product->CostPrice, 2, '.', '') }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="DiscountPercentage">{{ trans('cruds.product.fields.discount') }}</label>
                                            <input id="DiscountPercentage" class="form-control text-center" type="text"
                                                   name="DiscountPercentage" value="{{ $product->DiscountPercentage }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="QuantityOnHand">{{ trans('cruds.product.fields.quantity') }}</label>
                                            <input id="QuantityOnHand" class="form-control text-center{{ $errors->has('QuantityOnHand') ? 'is-invalid' : '' }}" type="text"
                                                   name="QuantityOnHand" readonly
                                                   value="{{ !empty($product->stockHolding->QuantityOnHand) ? $product->stockHolding->QuantityOnHand : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="required" for="SellingPrice">{{ trans('cruds.product.fields.price') }}</label>
                                            <input class="form-control " type="number" name="SellingPrice" id="SellingPrice"
                                                   value="{{ number_format((float)$product->SellingPrice, 2, '.', '') }}" readonly>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="MarketingComments">{{ trans('cruds.product.fields.description') }}</label>
                                    <textarea class="form-control {{ $errors->has('MarketingComments') ? 'is-invalid' : '' }}" name="MarketingComments"
                                              id="MarketingComments" placeholder="Catalog and eCommerce Product Description">{{ $product->MarketingComments }}</textarea>
                                    @if($errors->has('MarketingComments'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('MarketingComments') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.product.fields.description_helper') }}</span>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ URL::asset('/pages/jquery.form-upload.init.js') }}"></script>
    <script src="{{ URL::asset('/plugins/dropzone/dropzone.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>


    <script src="{{ URL::asset('pages/jquery.forms-advanced.js') }}"></script>

@endpush

