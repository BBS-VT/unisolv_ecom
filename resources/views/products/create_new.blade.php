@extends('layouts.master')
@section('title') @lang('global.add_product') @endsection

@section('css')
    <link href="{{ URL::asset('build/libs/dropzone/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('css/addProduct.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .column-content {
            background-color: #fff; /* Set background to white */
            padding: 20px; /* Add some padding for spacing */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: Add a subtle shadow for depth */
        }
    </style>

@section('content')
    {{--@component('components.breadcrumb')
        @slot('title') Add Product @endslot
    @endcomponent--}}
    <div class="mx-4">
        <div class="d-flex align-items-center mb-4">
            <div class="me-3">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            <div>
                <small class="text-muted fs-6">Back to product list</small>
                <h2 class="mb-0">Add New Product</h2>
            </div>
        </div>
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <input type="hidden" name="LastEditedBy" id="LastEditedby" value="{{ auth()->user()->id }}">

                <div class="col-md-6">
                    <div class="column-content">
                        <h4>{{ __('Description') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-4 col-md-6">
                                        <div>
                                            <label class="form-label required" for="StockCode">{{ __('cruds.product.fields.sku') }}</label>
                                            <input
                                                class="form-control {{ $errors->has('StockCode') ? 'is-invalid' : '' }}"
                                                type="text" name="StockCode" id="StockCode"
                                                value="{{ old('StockCode', '') }}" required>
                                            @if($errors->has('StockCode'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('StockCode') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('cruds.product.fields.sku_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-8 col-md-12">
                                        <div>
                                            <label class="required form-label"
                                                  for="StockItemName">{{ __('cruds.product.fields.name') }}</label>
                                            <input
                                                class="form-control {{ $errors->has('StockItemName') ? 'is-invalid' : '' }}"
                                                type="text" name="StockItemName" id="StockItemName"
                                                value="{{ old('StockItemName', '') }}" required>
                                            @if($errors->has('StockItemName'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('StockItemName') }}
                                                </div>
                                            @endif
                                            <span
                                                class="help-block">{{ __('cruds.product.fields.name_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-12 ">
                                        <div>
                                            <small class="text-muted  fs-6">{{ __('Catalog and eCommerce Product Description') }}</small>
                                            <textarea class="form-control {{ $errors->has('MarketingComments') ? 'is-invalid' : '' }}" name="MarketingComments"
                                                      id="MarketingComments" placeholder="">{{ old('MarketingComments') }}</textarea>
                                            @if($errors->has('MarketingComments'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('MarketingComments') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('cruds.product.fields.description_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Department') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-6 col-md-12">
                                        <div class="mb-3">
                                            <label for="choices-single-default" class="form-label">{{ __('Main Department') }}</label>
                                            <select class="form-control {{ $errors->has('categories') ? 'is-invalid' : '' }}" data-choices name="categories[]" id="categories" required>
                                                @foreach($categories as $id => $category)
                                                    <option value="{{ $id }}" {{ in_array($id, old('categories', [])) ? 'selected' : '' }}>{{ $category }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('categories'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('categories') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-md-12">
                                        <div class="mb-3">
                                            <label for="choices-single-default" class="form-label">{{ __('Sub Department (Optional)') }}</label>
                                            <select class="form-control" data-choices name="choices-single-default" id="choices-single-default">
                                                <option value="">This is a placeholder</option>
                                                <option value="Choice 1">Choice 1</option>
                                                <option value="Choice 2">Choice 2</option>
                                                <option value="Choice 3">Choice 3</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Stock Details') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="Quantity">{{ __('cruds.product.fields.quantity') }}</label>
                                            <input class="form-control" type="text" name="Quantity" id="Quantity" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-5 col-md-6">
                                        <div>
                                            <label class="form-label" for="Barcode">{{ __('cruds.product.fields.barcode') }}</label>
                                            <div class="input-group">
                                                <input
                                                    class="form-control {{ $errors->has('Barcode') ? 'is-invalid' : '' }}"
                                                    type="text" name="Barcode" id="Barcode"
                                                    placeholder="07-817-181-435-16" value="{{ old('Barcode', '') }}">
                                                <button type="button" id="generate-barcode" class="input-group-text btn btn-outline-secondary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('Click to generate barcode') }}">
                                                    <i class="ph-barcode"></i>
                                                </button>

                                                @if($errors->has('Barcode'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('Barcode') }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="help-block">{{ __('cruds.product.fields.barcode_helper') }}</span></div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-5 col-md-6">
                                        <div>
                                            <label class="form-label" for="AltBarcode">{{ __('cruds.product.fields.altbarcode') }}</label>
                                            <div class="input-group">
                                                <input
                                                    class="form-control {{ $errors->has('AltBarcode') ? 'is-invalid' : '' }}"
                                                    type="text" name="AltBarcode" id="AltBarcode"
                                                    value="{{ old('AltBarcode', '') }}" placeholder="07-817-181-435-16">
                                                <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                                                @if($errors->has('AltBarcode'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('AltBarcode') }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="help-block">{{ __('cruds.product.fields.altbarcode_helper') }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Selling Type') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                    <input type="checkbox" class="btn-check" id="btncheck1" autocomplete="off" checked="">
                                    <label class="btn btn-primary" for="btncheck1">In-store selling only</label>

                                    <input type="checkbox" class="btn-check" id="btncheck2" autocomplete="off">
                                    <label class="btn btn-primary" for="btncheck2">Online selling only</label>

                                    <input type="checkbox" class="btn-check" id="btncheck3" autocomplete="off">
                                    <label class="btn btn-primary" for="btncheck3">Available both in-store & online</label>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Variant') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly placeholder="Product variant" aria-label="Product variant" aria-describedby="basic-addon2">
                                        <a class="input-group-text" id="basic-addon2">@example.com</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="column-content">

                        <h4>{{ __('Product Images') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group">
                                        <label for="photo">{{ trans('cruds.product.fields.photo') }}</label>
                                        <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" id="photo-dropzone">
                                        </div>
                                        @if($errors->has('photo'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('photo') }}
                                            </div>
                                        @endif
                                        <span class="help-block">{{ trans('cruds.product.fields.photo_helper') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Pricing') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="TaxRateID">{{ __('cruds.product.fields.vatid') }}</label>
                                            <input class="form-control {{ $errors->has('TaxRateID') ? 'is-invalid' : '' }}" type="text" name="TaxRateID"
                                                   id="TaxRateID" value="{{ old('TaxRateID', '1') }}">
                                            @if($errors->has('TaxRateID'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('TaxRateID') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('cruds.product.fields.vatid_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="AverageCostPrice">{{ __('cruds.product.fields.ave_cost') }}</label>
                                            <input class="form-control {{ $errors->has('AverageCostPrice') ? 'is-invalid' : '' }}" type="number"
                                                   name="AverageCostPrice" id="AverageCostPrice" value="{{ old('AverageCostPrice', '') }}" step="0.01">
                                            @if($errors->has('AverageCostPrice'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('AverageCostPrice') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('cruds.product.fields.ave_cost_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="LastCostPrice">{{ __('cruds.product.fields.last_cost') }}</label>
                                            <input class="form-control {{ $errors->has('LastCostPrice') ? 'is-invalid' : '' }}" type="number"
                                                   name="LastCostPrice" id="LastCostPrice" value="{{ old('LastCostPrice', '') }}" step="0.01">
                                            @if($errors->has('LastCostPrice'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('LastCostPrice') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('cruds.product.fields.last_cost_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="DiscountPercentage">{{ __('cruds.product.fields.discount') }}</label>
                                            <input class="form-control {{ $errors->has('DiscountPercentage') ? 'is-invalid' : '' }}" type="number"
                                                   name="DiscountPercentage" id="DiscountPercentage" value="{{ old('DiscountPercentage', '') }}" step="0.01">
                                            @if($errors->has('DiscountPercentage'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('DiscountPercentage') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('cruds.product.fields.discount_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="SellingPrice">{{ __('cruds.product.fields.price') }}</label>
                                            <input class="form-control" type="text" name="SellingPrice" id="SellingPrice" >
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="Quantity">{{ __('cruds.product.fields.quantity') }}</label>
                                            <input class="form-control" type="text" name="Quantity" id="Quantity" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="SellingPrice2">{{ __('cruds.product.fields.price2') }}</label>
                                            <input class="form-control" type="text" name="SellingPrice2" id="SellingPrice" >
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="Quantity">{{ __('cruds.product.fields.quantity') }}</label>
                                            <input class="form-control" type="text" name="Quantity" id="Quantity" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="SellingPrice3">{{ __('cruds.product.fields.price3') }}</label>
                                            <input class="form-control" type="text" name="SellingPrice3" id="SellingPrice3" >
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-6">
                                        <div>
                                            <label class="form-label" for="Quantity">{{ __('cruds.product.fields.quantity') }}</label>
                                            <input class="form-control" type="text" name="Quantity" id="Quantity" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Packaging') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>



@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/inputmask.min.js"></script>

    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Barcode input mask
            Inputmask("99-999-999-999-9").mask(document.getElementById('Barcode'));

            // Generate barcode
            document.getElementById('generate-barcode').addEventListener('click', function () {
                const sku = document.getElementById('StockCode').value;

                let baseBarcode = '60';

                if (sku.length === 10) {
                    baseBarcode+= sku;
                } else {
                    let validSku = sku.padStart(10, '');
                    validSku = validSku.slice(0, 10);
                    while (validSku.length < 10) {
                        validSku += Math.floor(Math.random() * 10);
                    }
                    baseBarcode += validSku;
                }

                let checksum = calculateEAN13Checksum(baseBarcode);

                let fullBarcode = baseBarcode + checksum;

                let formattedBarcode = `${fullBarcode.slice(0, 2)}-${fullBarcode.slice(2, 5)}-${fullBarcode.slice(5, 8)}-${fullBarcode.slice(8, 12)}-${fullBarcode.slice(12)}`;
                document.getElementById('barcode').value = formattedBarcode;
            });

            function calculateEAN13Checksum(barcode) {
                let sum = 0;
                for (let i = 0; i < barcode.length; i++) {
                    sum += i % 2 === 0 ? parseInt(barcode[i]) : parseInt(barcode[i]) * 3;
                }
                return (10 - (sum % 10)) % 10; // Checksum calculation
            }
        });
    </script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
