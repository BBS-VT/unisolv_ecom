@extends('layouts.master')

@section('title', __('global.product_management'))

@push('styles')
    <link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/dropzone/basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .column-content {
            background-color: #fff; /* Set background to white */
            padding: 20px; /* Add some padding for spacing */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Optional: Add a subtle shadow for depth */
        }
        .pack-size-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .pack-size-family-display .current {
            background-color: #e3f2fd;
        }

        .pack-size-member {
            font-size: 0.9rem;
        }

        .pack-size-chain {
            font-size: 0.875rem;
        }

        /* Dropzone thumbnail fixes */
        #photo-dropzone {
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 200px;
        }

        #photo-dropzone .dz-preview {
            margin: 10px;
        }

        #photo-dropzone .dz-preview .dz-image {
            width: 150px;
            height: 150px;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        #photo-dropzone .dz-preview .dz-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #photo-dropzone .dz-preview .dz-details {
            padding: 0.5rem;
        }

        #photo-dropzone .dz-preview .dz-filename {
            font-size: 0.85rem;
        }

        #photo-dropzone .dz-preview .dz-size {
            font-size: 0.75rem;
        }

        /* Make remove link more visible */
        #photo-dropzone .dz-preview .dz-remove {
            font-size: 0.85rem;
            color: #dc3545;
            text-decoration: none;
            display: block;
            margin-top: 0.5rem;
        }

        #photo-dropzone .dz-preview .dz-remove:hover {
            color: #c82333;
            text-decoration: underline;
        }

        /* Success/error states */
        #photo-dropzone .dz-preview.dz-success .dz-success-mark,
        #photo-dropzone .dz-preview.dz-error .dz-error-mark {
            opacity: 1;
        }

        #photo-dropzone .dz-preview.dz-success .dz-image {
            border: 2px solid #28a745;
        }

        #photo-dropzone .dz-preview.dz-error .dz-image {
            border: 2px solid #dc3545;
        }

        /* Progress bar styling */
        #photo-dropzone .dz-preview .dz-progress {
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            background-color: #e9ecef;
            margin-top: 0.5rem;
        }

        #photo-dropzone .dz-preview .dz-progress .dz-upload {
            background-color: #667eea;
            height: 100%;
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
                            @can('adjust_stock')
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-warning float-end" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                                    <i class="mdi mdi-plus-minus-variant"></i> Adjust Stock
                                </button>
                            </div>
                            @endcan
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
                                <div class="btn-group w-100" role="group" aria-label="Selling type selection">
                                    {{-- In-Store Only --}}
                                    <input type="radio" class="btn-check" name="SellingType" id="selling_type_instore" value="instore" autocomplete="off"
                                           {{ old('SellingType', $product->SellingType ?? 'both') === 'instore' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary" for="selling_type_instore">
                                        <i class="bx bx-store me-1"></i>
                                        {{ __('global.instore_only') }}
                                    </label>

                                    {{-- Online Only --}}
                                    <input type="radio" class="btn-check" name="SellingType" id="selling_type_online"
                                           value="online" autocomplete="off"
                                           {{ old('SellingType', $product->SellingType ?? 'both') === 'online' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary" for="selling_type_online">
                                        <i class="bx bx-globe me-1"></i>
                                        {{ __('global.online_only') }}
                                    </label>

                                    {{-- Both In-Store & Online --}}
                                    <input type="radio" class="btn-check" name="SellingType" id="selling_type_both"
                                           value="both" autocomplete="off"
                                           {{ old('SellingType', $product->SellingType ?? 'both') === 'both' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-primary" for="selling_type_both">
                                        <i class="bx bx-infinite me-1"></i>
                                        {{__('global.instore_and_online') }}
                                    </label>
                                </div>

                                <small class="form-text text-muted mt-2 d-block">
                                    {{ __('messages.selling_type_help') }}
                                </small>

                                @error('SellingType')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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
                                            <input class="form-control" type="number" name="SellingPriceExcl" id="SellingPriceExcl" step="0.01">
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
                                            <input class="form-control" type="number" name="SellingPrice2Excl" id="SellingPrice2Excl" step="0.01">
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
                                            <input class="form-control" type="number" name="SellingPrice3Excl" id="SellingPrice3Excl" step="0.01">
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
                                            <input class="form-control" type="number" name="SellingPrice4Excl" id="SellingPrice4Excl" step="0.01">
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

                        <h4>{{ __('Pack Size Configuration') }}</h4>
                        <div class="card border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-6 col-md-6">
                                        <div class="form-group">
                                            <label for="refer_code">{{ __('Link to Pack Size') }}</label>
                                            <select class="form-control select2 {{ $errors->has('refer_code') ? 'is-invalid' : '' }}"
                                                    name="refer_code" id="refer_code">
                                                @foreach($referProducts as $stockCode => $name)
                                                    <option value="{{ $stockCode === '-- No Pack Size Link --' ? '' : $stockCode }}"
                                                        {{ old('refer_code', $product->refer_code) === $stockCode ? 'selected' : '' }}>
                                                        {{ $name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('refer_code'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('refer_code') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('Select another product that this pack size refers to (e.g., singles refer to 6-packs)') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-md-3">
                                        <div class="form-group">
                                            <label for="Packsize" class="required">{{ __('Pack Size (Units)') }}</label>
                                            <input class="form-control {{ $errors->has('Packsize') ? 'is-invalid' : '' }}"
                                                   type="number" name="Packsize" id="Packsize"
                                                   value="{{ old('Packsize', $product->Packsize ?? 1) }}" min="1" required>
                                            @if($errors->has('Packsize'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('Packsize') }}
                                                </div>
                                            @endif
                                            <span class="help-block">{{ __('Number of base units in this pack (e.g., 1 for singles, 6 for 6-pack, 24 for case)') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Pack Type') }}</label>
                                            <div class="mt-2">
                        <span id="pack-type-indicator" class="badge bg-info">
                            {{ $product->refer_code ? __('Child Product') : __('Root Product') }}
                        </span>
                                            </div>
                                            <span class="help-block">{{ __('Automatically determined based on refer code') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Current pack size family display -->
                                @if($product->referredProduct || $product->referringProducts->count() > 0)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <h6>{{ __('Current Pack Size Family') }}</h6>
                                            <div class="pack-size-family-display">
                                                @php
                                                    $family = $product->packSizeFamily()->with('stockHolding')->get()->sortByDesc('pack_size');
                                                @endphp

                                                @foreach($family as $familyMember)
                                                    <div class="pack-size-member {{ $familyMember->StockCode === $product->StockCode ? 'current' : '' }}">
                                                        <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                                                            <div class="d-flex align-items-center">
                                                                @if($familyMember->StockCode === $product->StockCode)
                                                                    <i class="fas fa-arrow-right text-primary me-2"></i>
                                                                    <strong class="text-primary">{{ $familyMember->StockItemName }}</strong>
                                                                @else
                                                                    <span class="me-2">{{ $familyMember->StockItemName }}</span>
                                                                @endif

                                                                <span class="badge bg-{{ $familyMember->StockCode === $product->StockCode ? 'primary' : 'secondary' }} ms-2">
                                        {{ $familyMember->pack_size }} {{ $familyMember->pack_size == 1 ? 'unit' : 'units' }}
                                    </span>
                                                            </div>

                                                            <div class="text-end">
                                                                <small class="text-muted">{{ $familyMember->StockCode }}</small><br>
                                                                <small class="text-muted">
                                                                    Stock: {{ $familyMember->stockHolding?->QuantityOnHand ?? 0 }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h6 class="mb-2"><i class="fas fa-info-circle me-1"></i> Pack Size Configuration Guide</h6>
                                            <ul class="mb-0 small">
                                                <li><strong>Root Product:</strong> Leave "Link to Pack Size" empty for the largest pack size (e.g., case)</li>
                                                <li><strong>Child Product:</strong> Select the larger pack size this refers to</li>
                                                <li><strong>Pack Size:</strong> Enter how many base units are in this pack</li>
                                                <li><strong>Example:</strong> Case (24 units) → 6-Pack (6 units) → Single (1 unit)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3" id="pack-size-preview" style="display: none;">
                                    <div class="col-12">
                                        <h6>{{ __('Updated Pack Size Chain Preview') }}</h6>
                                        <div id="pack-size-chain" class="d-flex align-items-center flex-wrap">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-2">
                <button class="btn btn-danger" type="submit">
                    {{ __('global.update') }}
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
            </div>
        </form>

        @include('products.partials.adjustStock')
    </div>

@endsection

@push('scripts')

    <script src="{{ URL::asset('build/libs/inputmask/jquery.inputmask.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make sure Dropzone is loaded
            if (typeof Dropzone === 'undefined') {
                console.error('Dropzone is not loaded!');
                return;
            }

            console.log('Setting up Dropzone');

            Dropzone.autoDiscover = false;

            var photoDropzone = new Dropzone("#photo-dropzone", {
                url: '{{ route('products.storeMedia') }}',
                maxFilesize: 5,
                acceptedFiles: '.jpeg,.jpg,.png,.gif',
                maxFiles: 5,
                addRemoveLinks: true,
                thumbnailWidth: 150,
                thumbnailHeight: 150,
                thumbnailMethod: 'crop',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                params: {
                    size: 2,
                    width: 4096,
                    height: 4096
                },
                init: function() {
                    console.log('Dropzone initialized');
                    var myDropzone = this;

                    @if($product->hasMedia('photo'))
                    console.log('Loading existing images');

                    @foreach($product->getMedia('photo') as $index => $media)
                    var mockFile{{ $index }} = {
                        name: "{{ $media->file_name }}",
                        size: {{ $media->size }},
                        accepted: true
                    };

                    console.log('Adding file {{ $index }}:', mockFile{{ $index }});

                    // Add the file to dropzone
                    myDropzone.emit("addedfile", mockFile{{ $index }});

                    // Add the thumbnail
                    myDropzone.emit("thumbnail", mockFile{{ $index }}, "{{ $media->getUrl('thumb') }}");

                    // Mark as complete
                    myDropzone.emit("complete", mockFile{{ $index }});

                    // Make file look successful
                    mockFile{{ $index }}.previewElement.classList.add('dz-success');

                    console.log('File {{ $index }} added successfully');
                    @endforeach

                    // Adjust maxFiles count
                    myDropzone.options.maxFiles = myDropzone.options.maxFiles - {{ $product->getMedia('photo')->count() }};
                    console.log('Remaining maxFiles:', myDropzone.options.maxFiles);
                    @endif
                },
                success: function(file, response) {
                    console.log('Upload success:', response);
                    $('form').find('input[name="photo"]').remove();
                    $('form').append('<input type="hidden" name="photo" value="' + response.name + '">');
                },
                removedfile: function(file) {
                    console.log('Removing file:', file.name);

                    // If this is an existing file (not a newly uploaded one)
                    if (file.accepted && !file.xhr) {
                        // Find the media ID by matching the file name
                        @if($product->hasMedia('photo'))
                        var mediaId = null;
                        @foreach($product->getMedia('photo') as $index => $media)
                        if (file.name === "{{ $media->file_name }}") {
                            mediaId = {{ $media->id }};
                        }
                        @endforeach

                        if (mediaId) {
                            // Show confirmation
                            if (!confirm('Are you sure you want to delete this image? This cannot be undone.')) {
                                return false;
                            }

                            // Delete from server
                            $.ajax({
                                url: '/products/media/' + mediaId,
                                type: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    console.log('File deleted from server');
                                    // Remove preview element
                                    if (file.previewElement != null && file.previewElement.parentNode != null) {
                                        file.previewElement.parentNode.removeChild(file.previewElement);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error deleting file:', error);
                                    alert('Failed to delete image. Please try again.');
                                }
                            });
                        }
                        @endif
                    } else {
                        // This is a newly uploaded file that hasn't been saved yet
                        if (file.previewElement != null && file.previewElement.parentNode != null) {
                            file.previewElement.parentNode.removeChild(file.previewElement);
                        }
                    }

                    this.options.maxFiles = this.options.maxFiles + 1;
                    return this._updateMaxFilesReachedClass();
                },
                error: function(file, response) {
                    console.error('Dropzone error:', response);
                    if (typeof response === 'string') {
                        var message = response;
                    } else if (response.errors && response.errors.file) {
                        var message = response.errors.file;
                    } else {
                        var message = 'Upload failed';
                    }

                    if (file.previewElement) {
                        file.previewElement.classList.add('dz-error');
                        var errorNodes = file.previewElement.querySelectorAll('[data-dz-errormessage]');
                        errorNodes.forEach(function(node) {
                            node.textContent = message;
                        });
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            const VAT_RATE = 0.15;  //TODO: link to vat table and rate indicator

            // Function to calculate exclusive price from inclusive
            function calculateExclusivePrice(inclusivePrice) {
                if (!inclusivePrice || inclusivePrice <= 0) return '';
                return (inclusivePrice / (1 + VAT_RATE)).toFixed(2);
            }

            // Function to calculate inclusive price from exclusive
            function calculateInclusivePrice(exclusivePrice) {
                if (!exclusivePrice || exclusivePrice <= 0) return '';
                return (exclusivePrice * (1 + VAT_RATE)).toFixed(2);
            }

            // Price 1 calculations
            $('#SellingPrice').on('input change', function() {
                const inclusivePrice = parseFloat($(this).val());
                const exclusivePrice = calculateExclusivePrice(inclusivePrice);
                $('#SellingPriceExcl').val(exclusivePrice);
            });

            $('#SellingPriceExcl').on('input change', function() {
                const exclusivePrice = parseFloat($(this).val());
                const inclusivePrice = calculateInclusivePrice(exclusivePrice);
                $('#SellingPrice').val(inclusivePrice);
            });

            // Price 2 calculations
            $('#SellingPrice2').on('input change', function() {
                const inclusivePrice = parseFloat($(this).val());
                const exclusivePrice = calculateExclusivePrice(inclusivePrice);
                $('#SellingPrice2Excl').val(exclusivePrice);
            });

            $('#SellingPrice2Excl').on('input change', function() {
                const exclusivePrice = parseFloat($(this).val());
                const inclusivePrice = calculateInclusivePrice(exclusivePrice);
                $('#SellingPrice2').val(inclusivePrice);
            });

            // Price 3 calculations
            $('#SellingPrice3').on('input change', function() {
                const inclusivePrice = parseFloat($(this).val());
                const exclusivePrice = calculateExclusivePrice(inclusivePrice);
                $('#SellingPrice3Excl').val(exclusivePrice);
            });

            $('#SellingPrice3Excl').on('input change', function() {
                const exclusivePrice = parseFloat($(this).val());
                const inclusivePrice = calculateInclusivePrice(exclusivePrice);
                $('#SellingPrice3').val(inclusivePrice);
            });

            // Price 4 calculations
            $('#SellingPrice4').on('input change', function() {
                const inclusivePrice = parseFloat($(this).val());
                const exclusivePrice = calculateExclusivePrice(inclusivePrice);
                $('#SellingPrice4Excl').val(exclusivePrice);
            });

            $('#SellingPrice4Excl').on('input change', function() {
                const exclusivePrice = parseFloat($(this).val());
                const inclusivePrice = calculateInclusivePrice(exclusivePrice);
                $('#SellingPrice4').val(inclusivePrice);
            });

            // Initialize exclusive prices on page load for existing values
            function initializeExclusivePrices() {
                // Price 1
                if ($('#SellingPrice').val()) {
                    $('#SellingPrice').trigger('change');
                }

                // Price 2
                if ($('#SellingPrice2').val()) {
                    $('#SellingPrice2').trigger('change');
                }

                // Price 3
                if ($('#SellingPrice3').val()) {
                    $('#SellingPrice3').trigger('change');
                }

                // Price 4
                if ($('#SellingPrice4').val()) {
                    $('#SellingPrice4').trigger('change');
                }
            }

            // Call initialization
            initializeExclusivePrices();

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
    <script>
        $(document).ready(function() {
            // Initialize pack size functionality
            initializePackSizeFields();

            function initializePackSizeFields() {
                // Update pack type indicator when refer_code changes
                $('#refer_code').on('change', function() {
                    updatePackTypeIndicator();
                    updatePackSizePreview();
                });

                // Update preview when pack_size changes
                $('#pack_size').on('input', function() {
                    updatePackSizePreview();
                });

                // Initial update
                updatePackTypeIndicator();
                updatePackSizePreview();
            }

            function updatePackTypeIndicator() {
                const referCode = $('#refer_code').val();
                const indicator = $('#pack-type-indicator');

                if (referCode && referCode !== '') {
                    indicator.removeClass('bg-info').addClass('bg-success').text('Child Product');
                } else {
                    indicator.removeClass('bg-success').addClass('bg-info').text('Root Product');
                }
            }

            function updatePackSizePreview() {
                const referCode = $('#refer_code').val();
                const packSize = $('#pack_size').val();
                const currentProduct = $('#StockCode').val();
                const currentName = $('#StockItemName').val();

                if (!packSize) return;

                const previewContainer = $('#pack-size-preview');
                const chainContainer = $('#pack-size-chain');

                // Only show preview if values have changed from original
                const originalReferCode = '{{ $product->refer_code ?? "" }}';
                const originalPackSize = '{{ $product->pack_size ?? 1 }}';

                if (referCode !== originalReferCode || packSize != originalPackSize) {
                    if (referCode && referCode !== '') {
                        // This is a child product
                        const referOption = $('#refer_code option:selected');
                        const referName = referOption.text();

                        chainContainer.html(`
                    <div class="pack-size-item me-3">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2">${referName}</span>
                            <i class="fas fa-arrow-right me-2 text-muted"></i>
                            <span class="badge bg-warning">${currentName} (${packSize} units) - UPDATED</span>
                        </div>
                    </div>
                `);
                        previewContainer.show();
                    } else if (packSize && packSize > 1) {
                        // This is a root product
                        chainContainer.html(`
                    <div class="pack-size-item">
                        <span class="badge bg-warning">${currentName} (${packSize} units) - Root Product - UPDATED</span>
                    </div>
                `);
                        previewContainer.show();
                    } else {
                        previewContainer.hide();
                    }
                } else {
                    previewContainer.hide();
                }
            }
        });
    </script>
@endpush

