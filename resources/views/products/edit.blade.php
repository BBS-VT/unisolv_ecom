@extends('layouts.app')

@section('title', __('global.product_management'))

@section('css')
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .column-content {
            background-color: #fff; /* Set background to white */
            padding: 20px; /* Add some padding for spacing */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: Add a subtle shadow for depth */
        }
    </style>
@endsection

@php
    use App\Models\ProductCategory;
@endphp

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
        <form method="POST" action="{{ route('products.update', [$product->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
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
                                            <small class="text-muted fs-6">{{ __('Catalog and eCommerce Product Description') }}</small>
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
                                            <select class="form-control mb-3 select2 {{ $errors->has('categories') ? 'is-invalid' : '' }}" name="categories[]" id="categories">
                                                <option value="">-- Select Category --</option>
                                                @foreach($categories as $id => $category)
                                                    <option value="{{ $id }}" {{ (in_array($id, old('categories', [])) || $product->categories->contains($id)) ? 'selected' : '' }}>
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
                                            <select class="form-control mb-3 select2 {{ $errors->has('subCategories') ? 'is-invalid' : '' }}" name="subCategories[]" id="subCategories">

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
                                            <input class="form-control" type="text" name="Quantity" id="Quantity" value="{{ $product->Quantity }}" readonly>
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
                                                <span class="help-block">{{ trans('cruds.product.fields.barcode_helper') }}</span>
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
                                                <span class="help-block">{{ trans('cruds.product.fields.altbarcode_helper') }}</span>
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
                                    <input type="checkbox" class="btn-check" id="btncheck1" name="InStore" value="1" {{ $product->InStore ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn" for="btncheck1">In-store selling only</label>

                                    <input type="checkbox" class="btn-check" id="btncheck2" name="Online" value="1" {{ $product->Online ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn" for="btncheck2">Online selling only</label>

                                    <input type="checkbox" class="btn-check" id="btncheck3" name="BothStoreOnline" value="1"
                                           {{ ($product->InStore && $product->Online) ? 'checked' : '' }} autocomplete="off">
                                    <label class="btn" for="btncheck3">Available both in-store & online</label>
                                </div>
                            </div>
                        </div>

                        <h4>{{ __('Variant') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    {{--<div class="input-group">
                                        <input type="text" class="form-control" readonly
                                               value="{{ $product->variants->count() ? $product->variants->count() . ' variants' : 'No variants' }}"
                                               placeholder="Product variant" aria-label="Product variant" aria-describedby="basic-addon2">
                                        <a href="{{ route('product-variants.create', ['product_id' => $product->id]) }}" class="input-group-text" id="basic-addon2">{{ __('Add Variant') }}</a>
                                    </div>

                                    @if($product->variants->count())
                                        <div class="mt-3">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th>Variant</th>
                                                        <th>SKU</th>
                                                        <th>Stock</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($product->variants as $variant)
                                                        <tr>
                                                            <td>{{ $variant->name }}</td>
                                                            <td>{{ $variant->sku }}</td>
                                                            <td>{{ $variant->quantity }}</td>
                                                            <td>
                                                                <a href="{{ route('product-variants.edit', $variant->id) }}" class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif--}}
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
                                                   value="{{ old('LastCostPrice', $product->LastCostPrice) }}" step="0.01">
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
                                            <input class="form-control" type="text" name="SellingPriceExcl" id="SellingPriceExcl"
                                                   value="{{ $product->SellingPriceExcl }}" readonly>
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
                                            <input class="form-control" type="text" name="SellingPrice2Excl" id="SellingPrice2Excl"
                                                   value="{{ $product->SellingPrice2Excl }}" readonly>
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
                                            <input class="form-control" type="text" name="SellingPrice3Excl" id="SellingPrice3Excl"
                                                   value="{{ $product->SellingPrice3Excl }}" readonly>
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
                                            <input class="form-control" type="text" name="SellingPrice4Excl" id="SellingPrice4Excl"
                                                   value="{{ $product->SellingPrice4Excl }}" readonly>
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
                    {{ __('global.save') }}
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
            </div>
        </form>
    </div>

@endsection

@section('custom-scripts')
    <script src="{{ URL::asset('build/libs/inputmask/jquery.inputmask.min.js')}}"></script>
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('/plugins/dropzone/dropzone.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('#salesunits').select2();

            $('#btncheck3').change(function() {
                if($(this).is(':checked')) {
                    $('#btncheck1').prop('checked', true);
                    $('#btncheck2').prop('checked', true);
                }
            });

            $('#btncheck1, #btncheck2').change(function() {
                if($('#btncheck1').is(':checked') && $('#btncheck2').is(':checked')) {
                    $('#btncheck3').prop('checked', true);
                } else {
                    $('#btncheck3').prop('checked', false);
                }
            });

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
            };

            // Calculate price excluding VAT when price including VAT changes
            function calculatePriceExcl(priceField, priceExclField) {
                const vatRate = parseFloat($('#TaxRateID').val()) || 15;
                const priceIncl = parseFloat($(priceField).val()) || 0;
                const priceExcl = (priceIncl * 100 / (100 + vatRate)).toFixed(2);
                $(priceExclField).val(priceExcl);
            }

            // Set up price calculation events
            $('#SellingPrice').on('input', function() {
                calculatePriceExcl('#SellingPrice', '#SellingPriceExcl');
            });

            $('#SellingPrice2').on('input', function() {
                calculatePriceExcl('#SellingPrice2', '#SellingPrice2Excl');
            });

            $('#SellingPrice3').on('input', function() {
                calculatePriceExcl('#SellingPrice3', '#SellingPrice3Excl');
            });

            $('#SellingPrice4').on('input', function() {
                calculatePriceExcl('#SellingPrice4', '#SellingPrice4Excl');
            });

            // Recalculate all prices when VAT rate changes
            $('#TaxRateID').on('input', function() {
                calculatePriceExcl('#SellingPrice', '#SellingPriceExcl');
                calculatePriceExcl('#SellingPrice2', '#SellingPrice2Excl');
                calculatePriceExcl('#SellingPrice3', '#SellingPrice3Excl');
                calculatePriceExcl('#SellingPrice4', '#SellingPrice4Excl');
            });

            // Initial calculation on page load
            calculatePriceExcl('#SellingPrice', '#SellingPriceExcl');
            calculatePriceExcl('#SellingPrice2', '#SellingPrice2Excl');
            calculatePriceExcl('#SellingPrice3', '#SellingPrice3Excl');
            calculatePriceExcl('#SellingPrice4', '#SellingPrice4Excl');
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Barcode input mask
            Inputmask("99-999-999-999-9").mask(document.getElementById('Barcode'));
            Inputmask("99-999-999-999-9").mask(document.getElementById('AltBarcode'));

            // Generate barcode
            document.getElementById('generate-barcode').addEventListener('click', function () {
                const sku = document.getElementById('StockCode').value;

                if (!sku || sku === '') {
                    alert('Please provide a valid SKU');
                    return;
                }

                let baseBarcode = '60';

                if (sku.length === 10) {
                    baseBarcode += sku;
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

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });


        $(document).ready(function() {
            // Get all subcategories with their parent relationships
            @php
                $subCategories = App\Models\ProductCategory::where('ParentID', '>', 0)->where('status', 1)->get();
            @endphp
            const allSubCategories = @json($subCategories);

            $('#categories').select2();
            $('#subCategories').select2();

            // Function to update subcategories based on selected category
            function updateSubCategories() {

                const selectedCategoryId = parseInt($('#categories').val());



                if (isNaN(selectedCategoryId)) {

                    $('#subCategories').empty().append(new Option('-- Select Subcategory --', ''));
                    $('#subCategories').trigger('change');
                    return;
                }


                const filteredSubCategories = allSubCategories.filter(
                    subCat => subCat.ParentID === selectedCategoryId
                );


                $('#subCategories').select2('destroy');

                $('#subCategories').empty().append(new Option('-- Select Subcategory --', ''));

                filteredSubCategories.forEach(subCat => {
                    $('#subCategories').append(new Option(subCat.StockGroupName, subCat.id));
                });

                $('#subCategories').select2();

                // If we have preselected subcategories, set them
                if (window.preSelectedSubcats && window.preSelectedSubcats.length) {
                    setTimeout(() => {
                        $('#subCategories').val(window.preSelectedSubcats).trigger('change');
                    }, 100);
                }
            }

            // Add event listener for category change
            $('#categories').on('change', updateSubCategories);

            // Store preselected subcategories for the product


            // Initial call to populate subcategories based on any preselected category
            updateSubCategories();
        });
    </script>
@endsection
