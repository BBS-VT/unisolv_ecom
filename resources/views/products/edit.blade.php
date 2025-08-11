@extends('layouts.master')

@section('title', __('global.product_management'))

@push('styles')
    <link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .column-content {
            background-color: #fff; /* Set background to white */
            padding: 20px; /* Add some padding for spacing */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: Add a subtle shadow for depth */
        }
    </style>
@endpush

@section('content')

    <div class="mx-4">
        <div class="d-flex align-items-center mb-4">
            <div class="me-3">
                <a href="{{ URL::previous() }}" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-between" style="width: 40px; height: 40px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <div>
                <small class="text-muted fs-6">{{ __('global.back_to_list') }}</small>
                <h2 class="mb-0">{{ __('global.edit') }} {{ __('cruds.product.title_singular') }}</h2>
            </div>
        </div>
        <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <input type="hidden" name="LastEditedBy" value="{{ Auth::user()->id }}">
                <div class="col-md-6">
                    <div class="column-content">
                        <h4>{{ __('Description') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-4 col-md-3">
                                        <div>
                                            <label class="required" for="StockCode">{{ __('cruds.product.fields.sku') }}</label>
                                            <input class="form-control {{ $errors->has('StockCode') ? 'is-invalid' : ''  }}"
                                                   type="text" name="StockCode" id="StockCode" value="{{ old('StockCode', $product->StockCode) }}" required>
                                            @if($errors->has('StockCode'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('StockCode') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.sku_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-8 col-md-9">
                                        <div>
                                            <label class="required form-label" for="StockItemName">{{ trans('cruds.product.fields.name') }}</label>
                                            <input class="form-control {{ $errors->has('StockItemName') ? 'is-invalid' : '' }}" type="text" name="StockItemName" id="StockItemName"
                                                   value="{{ old('StockItemName', $product->StockItemName) }}" required>
                                            @if($errors->has('StockItemName'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('StockItemName') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.name_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-12">
                                        <div>
                                            <small class="text-muted  fs-6">{{ __('Catalog and eCommerce Product Description') }}</small>
                                            <textarea class="form-control {{ $errors->has('MarketingComments') ? 'is-invalid' : '' }}" name="MarketingComments"
                                                      id="MarketingComments" placeholder="">{{ old('MarketingComments', $product->MarketingComments) }}</textarea>
                                            @if($errors->has('MarketingComments'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('MarketingComments') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.description_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4>{{ __('Department') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="categories">{{ __('cruds.product.fields.category') }}</label>
                                            <select class="form-control mb-3 select2 {{ $errors->has('categories') ? 'is-invalid' : '' }}"
                                                    name="categories[]" id="categories">
                                                <option value="">-- Select Category --</option>
                                                @foreach($mainCategories as $id => $category)
                                                    <option value="{{ $id }}" {{ in_array($id, old('categories', $product->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $category }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('categories'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('categories') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('cruds.product.fields.category_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="subCategories">{{ __('cruds.product.fields.subCategory') }}</label>
                                            <select class="form-control mb-3 select2 {{ $errors->has('subCategories') ? 'is-invalid' : '' }}"
                                                    name="subCategories[]" id="subCategories">

                                            </select>
                                            @if($errors->has('subCategories'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('subCategories') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.category_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Stock Details') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label" for="Quantity">{{ __('cruds.product.fields.quantity') }}</label>
                                            <input class="form-control" type="text" name="Quantity" id="Quantity"
                                                   value="{{ old('Quantity', $product->stockHolding->QuantityOnHand ?? 0) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-5 col-md-5">
                                        <div>
                                            <label class="form-label" for="Barcode">{{ trans('cruds.product.fields.barcode') }}</label>
                                            <div class="input-group">
                                                <input
                                                    class="form-control {{ $errors->has('Barcode') ? 'is-invalid' : '' }}"
                                                    type="text" name="Barcode" id="Barcode" placeholder="07-817-181-435-16"
                                                    value="{{ old('Barcode', $product->Barcode) }}">
                                                <button type="button" id="generate-barcode" class="input-group-text btn btn-outline-secondary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('Click to generate barcode') }}">
                                                    <i class="bx bx-barcode"></i>
                                                </button>
                                                @if($errors->has('Barcode'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('Barcode') }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="help-block">{{ trans('cruds.product.fields.barcode_helper') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-5 col-md-5">
                                        <div>
                                            <label class="form-label" for="AltBarcode">{{ trans('cruds.product.fields.altbarcode') }}</label>
                                            <div class="input-group">
                                                <input
                                                    class="form-control {{ $errors->has('AltBarcode') ? 'is-invalid' : '' }}"
                                                    type="text" name="AltBarcode" id="AltBarcode"
                                                    value="{{ old('AltBarcode', $product->AltBarcode) }}" placeholder="07-817-181-435-16">
                                                <span class="input-group-text"><i class="bx bx-barcode"></i></span>
                                                @if($errors->has('AltBarcode'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('AltBarcode') }}
                                                    </div>
                                                @endif
                                                <span
                                                    class="help-block">{{ trans('cruds.product.fields.altbarcode_helper') }}</span>
                                            </div>
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
                                    <label class="btn " for="btncheck1">In-store selling only</label>

                                    <input type="checkbox" class="btn-check" id="btncheck2" autocomplete="off">
                                    <label class="btn " for="btncheck2">Online selling only</label>

                                    <input type="checkbox" class="btn-check" id="btncheck3" autocomplete="off">
                                    <label class="btn " for="btncheck3">Available both in-store & online</label>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Variant') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="input-group">
                                        <input type="text" class="form-control" readonly placeholder="Product variant" aria-label="Product variant" aria-describedby="basic-addon2">
                                        <a class="input-group-text" id="basic-addon2">{{ __('Add Variant') }}</a>
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
                                        <label for="file" hidden>{{ trans('cruds.product.fields.photo') }}</label>
                                        <div class="dropzone {{ $errors->has('file') ? 'is-invalid' : '' }}" id="photo-dropzone">
                                            <div class="fallback">
                                                <input name="file" type="file" multiple="multiple">
                                            </div>
                                            <div class="dz-message needsclick">
                                                <div class="mb-3">
                                                    <i class="display-4 text-muted bx bx-cloud-upload"></i>
                                                </div>
                                                <h4>{{ __('Drop  images here or click upload') }}</h4>
                                            </div>
                                        </div>
                                        @if($errors->has('file'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('file') }}
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
                                    <div class="col-xxl-2 col-md-3">
                                        <div>
                                            <label class="form-label" for="TaxRateID">{{ trans('cruds.product.fields.vatid') }}</label>
                                            <input class="form-control {{ $errors->has('TaxRateID') ? 'is-invalid' : '' }}" type="text" name="TaxRateID"
                                                   id="TaxRateID" value="{{ old('TaxRateID', $product->TaxRateID) }}" >
                                            @if($errors->has('TaxRateID'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('TaxRateID') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.vatid_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-3">
                                        <div>
                                            <label class="form-label required" for="AverageCostPrice">{{ trans('cruds.product.fields.ave_cost') }}</label>
                                            <input class="form-control {{ $errors->has('AverageCostPrice') ? 'is-invalid' : '' }}" type="number" name="AverageCostPrice" id="AverageCostPrice"
                                                   value="{{ old('AverageCostPrice', $product->AverageCostPrice) }}" step="0.01">
                                            @if($errors->has('AverageCostPrice'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('AverageCostPrice') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.ave_cost_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-3" >
                                        <div>
                                            <label class="form-label required" for="LastCostPrice">{{ trans('cruds.product.fields.last_cost') }}</label>
                                            <input class="form-control {{ $errors->has('LastCostPrice') ? 'is-invalid' : '' }}" type="number" name="LastCostPrice" id="LastCostPrice"
                                                   value="{{ old('LastCostPrice', $product->stockHolding->LastCostPrice ?? '') }}" step="0.01">
                                            @if($errors->has('LastCostPrice'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('LastCostPrice') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.last_cost_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-3">
                                        <div>
                                            <label class="form-label" for="DiscountPercentage">{{ trans('cruds.product.fields.discount') }}</label>
                                            <input id="DiscountPercentage" class="form-control {{ $errors->has('DiscountPercentage') ? 'is-invalid' : '' }}" type="text"
                                                   name="DiscountPercentage" value="{{ old('DiscountPercentage', $product->DiscountPercentage) }}">
                                            @if($errors->has('DiscountPercentage'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('DiscountPercentage') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.discount_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label" for="SellingPriceExcl">{{ __('Price 1 Excl') }}</label>
                                            <input class="form-control" type="text" name="SellingPriceExcl" id="SellingPriceExcl" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label required" for="SellingPrice">{{ trans('cruds.product.fields.price') }}</label>
                                            <input class="form-control {{ $errors->has('SellingPrice') ? 'is-invalid' : '' }}" type="number" name="SellingPrice" id="SellingPrice"
                                                   value="{{ old('SellingPrice', $product->SellingPrice) }}" step="0.01" required>
                                            @if($errors->has('SellingPrice'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('SellingPrice') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.price_helper') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label" for="SellingPrice2Excl">{{ __('Price 2 Excl') }}</label>
                                            <input class="form-control" type="text" name="SellingPrice2Excl" id="SellingPric2eExcl" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label" for="SellingPrice2">{{ trans('cruds.product.fields.price2') }}</label>
                                            <input class="form-control {{ $errors->has('SellingPrice2') ? 'is-invalid' : '' }}" type="number" name="SellingPrice2" id="SellingPrice2"
                                                   value="{{ old('SellingPrice2', $product->SellingPrice2) }}" step="0.01">
                                            @if($errors->has('SellingPrice2'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('SellingPrice2') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.price_helper') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label" for="SellingPrice3Excl">{{ __('Price 3 Excl') }}</label>
                                            <input class="form-control" type="text" name="SellingPrice3Excl" id="SellingPrice3Excl" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-2">
                                        <div >
                                            <label class="form-label" for="SellingPrice3">{{ trans('cruds.product.fields.price3') }}</label>
                                            <input class="form-control {{ $errors->has('SellingPrice3') ? 'is-invalid' : '' }}" type="number" name="SellingPrice3" id="SellingPrice3"
                                                   value="{{ old('SellingPrice3', $product->SellingPrice3) }}" step="0.01" >
                                            @if($errors->has('SellingPrice3'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('SellingPrice3') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.price_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label" for="SellingPrice4Excl">{{ __('Price 4 Excl') }}</label>
                                            <input class="form-control" type="text" name="SellingPrice4Excl" id="SellingPrice4Excl" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-md-2">
                                        <div>
                                            <label class="form-label" for="SellingPrice4">{{ trans('cruds.product.fields.price4') }}</label>
                                            <input class="form-control {{ $errors->has('SellingPrice4') ? 'is-invalid' : '' }}" type="number" name="SellingPrice4" id="SellingPrice4"
                                                   value="{{ old('SellingPrice4', $product->SellingPrice4) }}" step="0.01" >
                                            @if($errors->has('SellingPrice4'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('SellingPrice4') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.price_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Packaging') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-4 col-md-4">
                                        <div>
                                            <div class="form-group">
                                                <label for="Size">{{ trans('cruds.product.fields.size') }}</label>
                                                <input class="form-control {{ $errors->has('Size') ? 'is-invalid' : '' }}" type="text" name="Size" id="Size"
                                                       value="{{ old('Size', $product->Size) }}" >
                                                @if($errors->has('Size'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('Size') }}
                                                    </div>
                                                @endif
                                                <span class="help-block">{{ trans('cruds.product.fields.size_helper') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-md-4">
                                        <div class="form-group">
                                            <label for="salesunits">{{ trans('cruds.product.fields.units') }}</label>
                                            <select class="form-control mb-3 select2 {{ $errors->has('salesunits') ? 'is-invalid' : '' }}" name="salesunits[]" id="salesunits">
                                                @foreach($packagetypes as $id => $packagetype)
                                                    <option value="{{ $id }}" {{ $product->packageType && $product->packageType->id == $id ? 'selected' : '' }}>{{ $packagetype }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('salesunits'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('salesunits') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.units_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-4 col-md-4">
                                        <div class="form-group">
                                            <label for="Packsize">{{ trans('cruds.product.fields.packsize') }}</label>
                                            <input class="form-control {{ $errors->has('Packsize') ? 'is-invalid' : '' }}" type="text" name="Packsize" id="Packsize"
                                                   value="{{ old('Packsize', $product->Packsize) }}" >
                                            @if($errors->has('Packsize'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('Packsize') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.packsize_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ __('global.update') }}
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ URL::asset('build/libs/inputmask/jquery.inputmask.min.js')}}"></script>
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

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
                    @if($product->photo)
                    // Create a mock file object
                    var mockFile = {
                        name: "{{ $product->photo->file_name ?? 'product-image.jpg' }}",
                        size: {{ $product->photo->size ?? 0 }},
                        accepted: true
                    };

                    // Add the file to dropzone
                    this.options.addedfile.call(this, mockFile);

                    // Add the thumbnail using your product's thumbnail attribute
                    this.options.thumbnail.call(this, mockFile, "{{ $product->photo->thumbnail }}");

                    // Mark as complete
                    mockFile.previewElement.classList.add('dz-complete');
                    mockFile.previewElement.classList.add('dz-success');

                    // Add hidden input with the correct file name
                    $('form').append('<input type="hidden" name="photo" value="{{ $product->photo->file_name ?? basename($product->photo->url) }}">');

                    // Decrease max files
                    this.options.maxFiles = this.options.maxFiles - 1;
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
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Barcode input mask
            Inputmask("9-99999-999999-9").mask(document.getElementById('Barcode'));
            Inputmask("9-99999-999999-9").mask(document.getElementById('AltBarcode'));

            // Generate barcode
            document.getElementById('generate-barcode').addEventListener('click', function () {
                const sku = document.getElementById('StockCode').value;

                if (!sku || sku === '') {
                    alert('Please provide a valid SKU');
                    return;
                }

                let baseBarcode = '60';

                if (sku.length === 10) {
                    baseBarcode+= sku;
                } else {
                    let validSku = sku;
                    while (validSku.length < 10) {
                        validSku += Math.floor(Math.random() * 10);
                    }
                    baseBarcode += validSku;
                }

                let checksum = calculateEAN13Checksum(baseBarcode);

                let fullBarcode = baseBarcode + checksum;

                let formattedBarcode = `${fullBarcode.slice(0, 2)}-${fullBarcode.slice(2, 5)}-${fullBarcode.slice(5, 8)}-${fullBarcode.slice(8, 12)}-${fullBarcode.slice(12)}`;
                document.getElementById('Barcode').value = formattedBarcode;
            });

            function calculateEAN13Checksum(baseBarcode) {
                let sum = 0;
                for (let i = 0; i < baseBarcode.length; i++) {
                    sum += i % 2 === 0 ? parseInt(baseBarcode[i]) : parseInt(baseBarcode[i]) * 3;
                }
                return (10 - (sum % 10)) % 10;
            }
        });

        $(document).ready(function() {
            // Get all subcategories with their parent relationships
            const allSubCategories = @json($subCategories);
            const currentCategories = @json($product->categories->pluck('id')->toArray());
            const mainCategories = @json($mainCategories);

            // Determine current main and sub categories
            let currentMainCategory = null;
            let currentSubCategories = [];

            // Find which categories are main categories and which are subcategories
            currentCategories.forEach(catId => {
                const isSubCategory = allSubCategories.find(sub => sub.id === catId);
                if (isSubCategory) {
                    currentSubCategories.push(catId);
                    // If this is a subcategory, find its parent
                    if (!currentMainCategory) {
                        currentMainCategory = isSubCategory.ParentID;
                    }
                } else {
                    // This is a main category
                    if (!currentMainCategory) {
                        currentMainCategory = catId;
                    }
                }
            });

            // Initialize Select2 on both dropdowns
            $('#categories').select2();
            $('#subCategories').select2();

            // Set the main category if we found one
            if (currentMainCategory) {
                $('#categories').val(currentMainCategory).trigger('change');
            }

            // Function to update subcategories based on selected category
            function updateSubCategories() {
                // Get selected category ID
                const selectedCategoryId = parseInt($('#categories').val());
                //console.log('Selected category ID:', selectedCategoryId);

                // Exit if no category selected or invalid ID
                if (isNaN(selectedCategoryId)) {
                    console.log('No valid category selected');
                    // Clear the subcategory dropdown
                    $('#subCategories').select2('destroy');
                    $('#subCategories').empty().append(new Option('-- Select Subcategory --', ''));
                    $('#subCategories').select2();
                    return;
                }

                // Filter subcategories by selected main category
                const filteredSubCategories = allSubCategories.filter(
                    subCat => subCat.ParentID === selectedCategoryId
                );

                // Important: Destroy and recreate the Select2 to ensure proper rendering
                $('#subCategories').select2('destroy');

                // Clear existing options and add default
                $('#subCategories').empty().append(new Option('-- Select Subcategory --', ''));

                // Add filtered subcategories to select
                filteredSubCategories.forEach(subCat => {
                    const isSelected = currentSubCategories.includes(subCat.id);
                    const option = new Option(subCat.StockGroupName, subCat.id, false, isSelected);
                    $('#subCategories').append(option);
                });

                // Reinitialize Select2
                $('#subCategories').select2();

                // Set selected subcategories after a brief delay to ensure DOM is ready
                setTimeout(() => {
                    if (currentSubCategories.length > 0) {
                        $('#subCategories').val(currentSubCategories).trigger('change');
                    }
                }, 100);
            }

            // Add event listener for category change
            $('#categories').on('change', updateSubCategories);

            // Initial call to populate subcategories based on any preselected category
            updateSubCategories();
        });
    </script>
@endpush

@push('script-bottom')
    <script src="{{ URL::asset('build/js/pages/form-file-upload.init.js') }}"></script>
@endpush
