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
                            <h4 class="card-title">{{ trans('global.add') }} {{ trans('cruds.product.title_singular') }}</h4>
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
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="LastEditedBy" id="LastEditedby" value="{{ auth()->user()->id }}">
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="required" for="StockCode">{{ trans('cruds.product.fields.sku') }}</label>
                                            <input class="form-control {{ $errors->has('StockCode') ? 'is-invalid' : '' }}" type="text" name="StockCode" id="StockCode" value="{{ old('StockCode', '') }}" required>
                                            @if($errors->has('StockCode'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('StockCode') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.sku_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label class="required control-label" for="StockItemName">{{ trans('cruds.product.fields.name') }}</label>
                                            <input class="form-control {{ $errors->has('StockItemName') ? 'is-invalid' : '' }}" type="text" name="StockItemName" id="StockItemName"
                                                   value="{{ old('StockItemName', '') }}" required>
                                            @if($errors->has('StockItemName'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('StockItemName') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.name_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="categories">{{ trans('cruds.product.fields.category') }}</label>
                                            <select class="form-control mb-3 select2 {{ $errors->has('categories') ? 'is-invalid' : '' }}" name="categories[]" id="categories">
                                                @foreach($categories as $id => $category)
                                                    <option value="{{ $id }}" {{ in_array($id, old('categories', [])) ? 'selected' : '' }}>{{ $category }}</option>
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
                                            <select class="form-control select2 mb-3 select2-multiple {{ $errors->has('tags') ? 'is-invalid' : '' }}" name="tags[]"
                                                    id="tags" multiple="multiple" data-placeholder="Please Choose">
                                                @foreach($tags as $id => $tag)
                                                    <option value="{{ $id }}" {{ in_array($id, old('tags', [])) ? 'selected' : '' }}>{{ $tag }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('tags'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('tags') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.tag_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="Size">{{ trans('cruds.product.fields.size') }}</label>
                                    <input class="form-control {{ $errors->has('Size') ? 'is-invalid' : '' }}" type="text" name="Size" id="Size"
                                           value="{{ old('Size', '') }}" >
                                    @if($errors->has('Size'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('Size') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.product.fields.size_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="salesunits">{{ trans('cruds.product.fields.units') }}</label>
                                    <select class="form-control mb-3 select2 {{ $errors->has('salesunits') ? 'is-invalid' : '' }}" name="salesunits[]" id="salesunits">
                                        @foreach($salesunits as $id => $salesunit)
                                            <option value="{{ $id }}" {{ in_array($id, old('salesunits', [])) ? 'selected' : '' }}>{{ $salesunit }}</option>
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
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="Packsize">{{ trans('cruds.product.fields.packsize') }}</label>
                                    <input class="form-control {{ $errors->has('Packsize') ? 'is-invalid' : '' }}" type="text" name="Packsize" id="Packsize"
                                           value="{{ old('Packsize', '') }}" >
                                    @if($errors->has('Packsize'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('Packsize') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.product.fields.packsize_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="Barcode">{{ trans('cruds.product.fields.barcode') }}</label>
                                    <input class="form-control {{ $errors->has('Barcode') ? 'is-invalid' : '' }}" type="text" name="Barcode" id="Barcode"
                                           value="{{ old('Barcode', '') }}" >
                                    @if($errors->has('Barcode'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('Barcode') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.product.fields.barcode_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label for="AltBarcode">{{ trans('cruds.product.fields.altbarcode') }}</label>
                                    <input class="form-control {{ $errors->has('AltBarcode') ? 'is-invalid' : '' }}" type="text" name="AltBarcode" id="AltBarcode"
                                           value="{{ old('AltBarcode', '') }}" >
                                    @if($errors->has('AltBarcode'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('AltBarcode') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.product.fields.altbarcode_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="TaxRateID">{{ trans('cruds.product.fields.vatid') }}</label>
                                            <input class="form-control {{ $errors->has('TaxRateID') ? 'is-invalid' : '' }}" type="text" name="TaxRateID"
                                                   id="TaxRateID" value="{{ old('TaxRateID', '1') }}" >
                                            @if($errors->has('TaxRateID'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('TaxRateID') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.vatid_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label class="required" for="CostPrice">{{ trans('cruds.product.fields.cost') }}</label>
                                            <input class="form-control {{ $errors->has('CostPrice') ? 'is-invalid' : '' }}" type="number" name="CostPrice" id="CostPrice"
                                                   value="{{ old('CostPrice', '') }}" step="0.01">
                                            @if($errors->has('CostPrice'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('CostPrice') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.cost_helper') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="DiscountPercentage">{{ trans('cruds.product.fields.discount') }}</label>
                                            <input id="DiscountPercentage" class="form-control {{ $errors->has('DiscountPercentage') ? 'is-invalid' : '' }}" type="text"
                                                   name="DiscountPercentage" value="{{ old('DiscountPercentage', '') }}">
                                            @if($errors->has('DiscountPercentage'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('DiscountPercentage') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.discount_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="required" for="SellingPrice">{{ trans('cruds.product.fields.price') }}</label>
                                            <input class="form-control {{ $errors->has('SellingPrice') ? 'is-invalid' : '' }}" type="number" name="SellingPrice" id="SellingPrice"
                                                   value="{{ old('SellingPrice', '') }}" step="0.01" required>
                                            @if($errors->has('SellingPrice'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('SellingPrice') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.product.fields.price_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="MarketingComments">{{ trans('cruds.product.fields.description') }}</label>
                                    <textarea class="form-control {{ $errors->has('MarketingComments') ? 'is-invalid' : '' }}" name="MarketingComments"
                                              id="MarketingComments" placeholder="Catalog and eCommerce Product Description">{{ old('MarketingComments') }}</textarea>
                                    @if($errors->has('MarketingComments'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('MarketingComments') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.product.fields.description_helper') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">{{ trans('global.save') }}</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ trans('global.cancel') }}</a>
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

    <script>
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

    <script src="{{ URL::asset('pages/jquery.forms-advanced.js') }}"></script>

@endpush

