@extends('layouts.app')

@section('style')
    <link href="{{ asset('/plugins/dropzone/basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        .form-section {
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .form-section-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #495057;
        }
        .required-label:after {
            content: " *";
            color: #dc3545;
        }
        .help-text {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .price-input {
            font-weight: 600;
        }

    </style>
@endsection

@section('content')
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ trans('global.edit') }} {{ trans('cruds.product.title_singular') }} -
                                <span class="text-primary">{{ $product->StockItemName }}</span>
                            </h4>
                        </div>
                        <div class="col-auto align-self-center">
                            @can('product_create')
                                <a href="{{ URL::previous() }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.back_to_list') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('products.update', [$product->id]) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="LastEditedBy" id="LastEditedby" value="{{ auth()->user()->id }}">

                        <!-- Basic Information Section -->
                        <div class="form-section">
                            <h5 class="form-section-title">Basic Information</h5>
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="required-label" for="StockCode">{{ trans('cruds.product.fields.sku') }}</label>
                                                <input class="form-control {{ $errors->has('StockCode') ? 'is-invalid' : '' }}" type="text" name="StockCode" id="StockCode" value="{{ $product->StockCode }}" required>
                                                @if($errors->has('StockCode'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('StockCode') }}
                                                    </div>
                                                @endif
                                                <small class="help-text">{{ trans('cruds.product.fields.sku_helper') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label class="required-label" for="StockItemName">{{ trans('cruds.product.fields.name') }}</label>
                                                <input class="form-control {{ $errors->has('StockItemName') ? 'is-invalid' : '' }}" type="text" name="StockItemName" id="StockItemName"
                                                       value="{{ $product->StockItemName }}" required>
                                                @if($errors->has('StockItemName'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('StockItemName') }}
                                                    </div>
                                                @endif
                                                <small class="help-text">{{ trans('cruds.product.fields.name_helper') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="categories">{{ trans('cruds.product.fields.category') }}</label>
                                        <select class="form-control select2 {{ $errors->has('categories') ? 'is-invalid' : '' }}" name="categories[]" id="categories">
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
                                        <small class="help-text">{{ trans('cruds.product.fields.category_helper') }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-4">

                                </div>
                                <div class="col-sm-4">

                                </div>
                            </div>
                        </div>

                        <!-- Product Specifications Section -->
                        <div class="form-section">
                            <h5 class="form-section-title">Product Specifications</h5>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="BinLocation">{{ trans('cruds.product.fields.bin') }}</label>
                                        <input class="form-control {{ $errors->has('BinLocation') ? 'is-invalid' : '' }}" type="text" name="BinLocation" id="BinLocation"
                                               value="{{ $product->stockHolding->BinLocation ?? '' }}" >
                                        @if($errors->has('BinLocation'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('BinLocation') }}
                                            </div>
                                        @endif
                                        <small class="help-text">{{ trans('cruds.product.fields.bin_helper') }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="Size">{{ trans('cruds.product.fields.size') }}</label>
                                        <input class="form-control {{ $errors->has('Size') ? 'is-invalid' : '' }}" type="text" name="Size" id="Size"
                                               value="{{ $product->Size }}" >
                                        @if($errors->has('Size'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('Size') }}
                                            </div>
                                        @endif
                                        <small class="help-text">{{ trans('cruds.product.fields.size_helper') }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="salesunits">{{ trans('cruds.product.fields.units') }}</label>
                                        <select class="form-control select2 {{ $errors->has('salesunits') ? 'is-invalid' : '' }}" name="salesunits[]" id="salesunits">
                                            @foreach($packagetypes as $id => $packagetype)
                                                <option value="{{ $id }}" @if($product->UnitPackageID == $id) selected @endif>{{ $packagetype }}</option>
                                            @endforeach
                                        </select>
                                        @if($errors->has('salesunits'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('salesunits') }}
                                            </div>
                                        @endif
                                        <small class="help-text">{{ trans('cruds.product.fields.units_helper') }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="Packsize">{{ trans('cruds.product.fields.packsize') }}</label>
                                        <input class="form-control {{ $errors->has('Packsize') ? 'is-invalid' : '' }}" type="text" name="Packsize" id="Packsize"
                                               value="{{ $product->Packsize }}" >
                                        @if($errors->has('Packsize'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('Packsize') }}
                                            </div>
                                        @endif
                                        <small class="help-text">{{ trans('cruds.product.fields.packsize_helper') }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="Barcode">{{ trans('cruds.product.fields.barcode') }}</label>
                                        <input class="form-control {{ $errors->has('Barcode') ? 'is-invalid' : '' }}" type="text" name="Barcode" id="Barcode"
                                               value="{{ $product->Barcode }}" >
                                        @if($errors->has('Barcode'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('Barcode') }}
                                            </div>
                                        @endif
                                        <small class="help-text">{{ trans('cruds.product.fields.barcode_helper') }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="AltBarcode">{{ trans('cruds.product.fields.altbarcode') }}</label>
                                        <input class="form-control {{ $errors->has('AltBarcode') ? 'is-invalid' : '' }}" type="text" name="AltBarcode" id="AltBarcode"
                                               value="{{ $product->AltBarcode ?? '' }}" >
                                        @if($errors->has('AltBarcode'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('AltBarcode') }}
                                            </div>
                                        @endif
                                        <small class="help-text">{{ trans('cruds.product.fields.altbarcode_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing and Inventory Section -->
                        <div class="form-section">
                            <h5 class="form-section-title">Pricing & Inventory</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label for="TaxRateID">{{ trans('cruds.product.fields.vatid') }}</label>
                                                <input class="form-control {{ $errors->has('TaxRateID') ? 'is-invalid' : '' }}" type="text" name="TaxRateID"
                                                       id="TaxRateID" value="{{ $product->TaxRateID }}" >
                                                @if($errors->has('TaxRateID'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('TaxRateID') }}
                                                    </div>
                                                @endif
                                                <small class="help-text">{{ trans('cruds.product.fields.vatid_helper') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="required-label" for="AverageCostPrice">{{ trans('cruds.product.fields.ave_cost') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">R</span>
                                                    </div>
                                                    <input class="form-control text-center price-input {{ $errors->has('AverageCostPrice') ? 'is-invalid' : '' }}" type="number" name="AverageCostPrice" id="AverageCostPrice"
                                                           value="{{ number_format((float)$product->AverageCostPrice, 2, '.', '') }}" step="0.01">
                                                </div>
                                                @if($errors->has('AverageCostPrice'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('AverageCostPrice') }}
                                                    </div>
                                                @endif
                                                <small class="help-text">{{ trans('cruds.product.fields.ave_cost_helper') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group">
                                                <label class="required-label" for="LastCostPrice">{{ trans('cruds.product.fields.last_cost') }}</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">R</span>
                                                    </div>
                                                    <input class="form-control text-center price-input {{ $errors->has('LastCostPrice') ? 'is-invalid' : '' }}" type="number" name="LastCostPrice" id="LastCostPrice"
                                                           value="{{ number_format((float)$product->stockHolding->LastCostPrice, 2, '.', '') }}" step="0.01">
                                                </div>
                                                @if($errors->has('LastCostPrice'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('LastCostPrice') }}
                                                    </div>
                                                @endif
                                                <small class="help-text">{{ trans('cruds.product.fields.last_cost_helper') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="DiscountPercentage">{{ trans('cruds.product.fields.discount') }}</label>
                                                <div class="input-group">
                                                    <input id="DiscountPercentage" class="form-control text-center {{ $errors->has('DiscountPercentage') ? 'is-invalid' : '' }}" type="text"
                                                           name="DiscountPercentage" value="{{ $product->DiscountPercentage }}">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                @if($errors->has('DiscountPercentage'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('DiscountPercentage') }}
                                                    </div>
                                                @endif
                                                <small class="help-text">{{ trans('cruds.product.fields.discount_helper') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="QuantityOnHand">{{ trans('cruds.product.fields.quantity') }}</label>
                                                <input id="QuantityOnHand" class="form-control text-center bg-light {{ $errors->has('QuantityOnHand') ? 'is-invalid' : '' }}" type="text"
                                                       name="QuantityOnHand" readonly value="{{ $product->stockHolding->QuantityOnHand }}">
                                                @if($errors->has('QuantityOnHand'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('QuantityOnHand') }}
                                                    </div>
                                                @endif
                                                <small class="help-text">{{ trans('cruds.product.fields.quantity_helper') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selling Prices -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header py-2">
                                                    <h6 class="mb-0">Selling Prices</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label class="required-label" for="SellingPrice">{{ trans('cruds.product.fields.price') }}</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">R</span>
                                                                    </div>
                                                                    <input class="form-control price-input {{ $errors->has('SellingPrice') ? 'is-invalid' : '' }}"
                                                                           type="number" name="SellingPrice" id="SellingPrice"
                                                                           value="{{ $product->SellingPrice }}" step="0.01" required>
                                                                </div>
                                                                @if($errors->has('SellingPrice'))
                                                                    <div class="invalid-feedback">
                                                                        {{ $errors->first('SellingPrice') }}
                                                                    </div>
                                                                @endif
                                                                <small class="help-text">{{ trans('cruds.product.fields.price_helper') }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label class="required-label" for="SellingPrice2">{{ trans('cruds.product.fields.price2') }}</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">R</span>
                                                                    </div>
                                                                    <input class="form-control price-input {{ $errors->has('SellingPrice2') ? 'is-invalid' : '' }}"
                                                                           type="number" name="SellingPrice2" id="SellingPrice2"
                                                                           value="{{ $product->SellingPrice2 }}" step="0.01" required>
                                                                </div>
                                                                @if($errors->has('SellingPrice2'))
                                                                    <div class="invalid-feedback">
                                                                        {{ $errors->first('SellingPrice2') }}
                                                                    </div>
                                                                @endif
                                                                <small class="help-text">{{ trans('cruds.product.fields.price_helper') }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label class="required-label" for="SellingPrice3">{{ trans('cruds.product.fields.price3') }}</label>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">R</span>
                                                                    </div>
                                                                    <input class="form-control price-input {{ $errors->has('SellingPrice3') ? 'is-invalid' : '' }}"
                                                                           type="number" name="SellingPrice3" id="SellingPrice3"
                                                                           value="{{ $product->SellingPrice3 }}" step="0.01" required>
                                                                </div>
                                                                @if($errors->has('SellingPrice3'))
                                                                    <div class="invalid-feedback">
                                                                        {{ $errors->first('SellingPrice3') }}
                                                                    </div>
                                                                @endif
                                                                <small class="help-text">{{ trans('cruds.product.fields.price_helper') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group h-100">
                                        <label for="MarketingComments">{{ trans('cruds.product.fields.description') }}</label>
                                        <textarea class="form-control {{ $errors->has('MarketingComments') ? 'is-invalid' : '' }}" name="MarketingComments"
                                                  id="MarketingComments" placeholder="Catalog and eCommerce Product Description" rows="8">{{ $product->MarketingComments }}</textarea>
                                        @if($errors->has('MarketingComments'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('MarketingComments') }}
                                            </div>
                                        @endif
                                        <small class="help-text">{{ trans('cruds.product.fields.description_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-section mt-4">
                            <div class="row">
                                <div class="col-12 float-right">
                                    <button class="btn btn-primary px-4" type="submit">
                                        <i data-feather="save" class="icon-sm mr-1"></i> {{ trans('global.save') }}
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-light ml-2">
                                        <i data-feather="x" class="icon-sm mr-1"></i> {{ trans('global.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-scripts')
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ URL::asset('/pages/jquery.form-upload.init.js') }}"></script>
    <script src="{{ URL::asset('/plugins/dropzone/dropzone.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            // Initialize TouchSpin for numeric inputs with increment/decrement controls
            $('#DiscountPercentage').TouchSpin({
                min: 0,
                max: 100,
                step: 1,
                decimals: 2,
                postfix: '%'
            });

            // Format currency inputs
            $('.price-input').TouchSpin({
                min: 0,
                step: 0.01,
                decimals: 2,
                prefix: '
            });

            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        // Dropzone configuration
        Dropzone.options.photoDropzone = {
            url: '{{ route('products.storeMedia') }}',
            maxFilesize: 2, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 2,
                width: 4096,
                height: 4096
            },
            success: function (file, response) {
                $('form').find('input[name="photo"]').remove()
                $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
            },
            removedfile: function (file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="photo"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function () {
                @if(isset($product) && $product->photo)
                var file = {!! json_encode($product->photo) !!}
                this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, '{{ $product->photo->getUrl('thumb') }}')
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
                this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function (file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }
                return _results
            }
        }
    </script>
@endsection
