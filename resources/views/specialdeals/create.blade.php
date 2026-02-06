@extends('layouts.master')

@section('title', 'Contract Discount')

@push('style')
    <link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/bootstrap-timepicker/timepicker/css/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('build/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <style>
        /* Add transition effect for visual feedback */
        .card {
            transition: border-color 0.3s ease;
        }

        .card.border-warning {
            border-width: 2px !important;
        }
    </style
@endpush


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ trans('global.add') }} {{ trans('cruds.deal.title_singular') }}</h4>

                <div class="page-title-right">
                    <a href="{{ route('deals.index') }}" class="btn btn-sm btn-outline-primary">
                        <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <h4 class="card-title">Contract Details</h4>
                    <p class="card-title-desc">Required fields indicated with a *</p>

                    <form action="{{ route("deals.store") }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <!-- Deal Description -->
                            <div class="col-12 mb-3">
                                <label for="DealDescription" class="form-label required">{{ trans('cruds.deal.fields.description') }}</label>
                                <input class="form-control @error('DealDescription') is-invalid @enderror"
                                       type="text"
                                       value="{{ old('DealDescription', '') }}"
                                       id="DealDescription"
                                       name="DealDescription"
                                       required>
                                @error('DealDescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ trans('cruds.deal.fields.description_helper') }}</div>
                            </div>

                            <!-- Product Selection Box -->
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ trans('cruds.deal.fields.product_selection') }} <small class="text-muted">(Select one)</small></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="StockItemID" class="form-label">{{ trans('cruds.deal.fields.product') }}</label>
                                                <select class="select2 form-control @error('StockItemID') is-invalid @enderror"
                                                        id="StockItemID"
                                                        name="StockItemID"
                                                        data-exclusive-group="product">
                                                    <option value="">-- select a product --</option>
                                                    @foreach($products as $id => $product)
                                                        <option value="{{ $id }}" {{ old('StockItemID') == $id ? 'selected' : '' }}>
                                                            {{ intval(ltrim($product->StockCode, '0')) }} &nbsp;
                                                            {{ $product->StockItemName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('StockItemID')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="StockGroupID" class="form-label">{{ trans('cruds.deal.fields.department') }}</label>
                                                <select class="select2 form-control @error('StockGroupID') is-invalid @enderror"
                                                        id="StockGroupID"
                                                        name="StockGroupID"
                                                        data-exclusive-group="product">
                                                    <option value="">-- select a department --</option>
                                                    @foreach($categories as $id => $category)
                                                        <option value="{{ $id }}" {{ old('StockGroupID') == $id ? 'selected' : '' }}>
                                                            {{ $category }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('StockGroupID')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Selection Box -->
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ trans('cruds.deal.fields.customer_selection') }} <small class="text-muted">(Select one)</small></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="BuyingGroupID" class="form-label">{{ trans('cruds.deal.fields.buygroup') }}</label>
                                                <select class="select2 form-control @error('BuyingGroupID') is-invalid @enderror"
                                                        id="BuyingGroupID"
                                                        name="BuyingGroupID"
                                                        data-exclusive-group="customer">
                                                    <option value="">-- select a buying group --</option>
                                                    @foreach($buyinggroups as $id => $buyingroup)
                                                        <option value="{{ $id }}" {{ old('BuyingGroupID') == $id ? 'selected' : '' }}>
                                                            {{ $buyingroup }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('BuyingGroupID')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="CustomerCategoryID" class="form-label">{{ trans('cruds.deal.fields.customergroup') }}</label>
                                                <select class="select2 form-control @error('CustomerCategoryID') is-invalid @enderror"
                                                        id="CustomerCategoryID"
                                                        name="CustomerCategoryID"
                                                        data-exclusive-group="customer">
                                                    <option value="">-- select a customer group --</option>
                                                    @foreach($customergroups as $id => $customergroup)
                                                        <option value="{{ $id }}" {{ old('CustomerCategoryID') == $id ? 'selected' : '' }}>
                                                            {{ $customergroup }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('CustomerCategoryID')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label for="CustomerID" class="form-label">{{ trans('cruds.deal.fields.customer') }}</label>
                                                <select class="select2 form-control @error('CustomerID') is-invalid @enderror"
                                                        id="CustomerID"
                                                        name="CustomerID"
                                                        data-exclusive-group="customer">
                                                    <option value="">-- select a customer --</option>
                                                    @foreach($customers as $id => $customer)
                                                        <option value="{{ $id }}" {{ old('CustomerID') == $id ? 'selected' : '' }}>
                                                            {{ $customer->acc_main }} &nbsp;
                                                            {{ $customer->CustomerName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('CustomerID')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Discount Box -->
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ trans('cruds.deal.fields.pricing_section') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="DiscountAmount" class="form-label">{{ trans('cruds.deal.fields.discount') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">R</span>
                                                    <input class="form-control @error('DiscountAmount') is-invalid @enderror"
                                                           type="text"
                                                           value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                                           id="DiscountAmount"
                                                           name="DiscountAmount"
                                                           placeholder="0.00">
                                                    @error('DiscountAmount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="DiscountPercentage" class="form-label">{{ trans('cruds.deal.fields.discountperc') }}</label>
                                                <div class="input-group">
                                                    <input type="text"
                                                           id="DiscountPercentage"
                                                           name="DiscountPercentage"
                                                           class="form-control @error('DiscountPercentage') is-invalid @enderror"
                                                           placeholder="0.00"
                                                           value="{{ old('DiscountPercentage') }}">
                                                    <span class="input-group-text">%</span>
                                                    @error('DiscountPercentage')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="UnitPrice" class="form-label">{{ trans('cruds.deal.fields.unitprice') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">R</span>
                                                    <input class="form-control @error('UnitPrice') is-invalid @enderror"
                                                           type="text"
                                                           value="{{ old('UnitPrice', isset($deal) ? $deal->UnitPrice : '') }}"
                                                           id="UnitPrice"
                                                           name="UnitPrice"
                                                           placeholder="0.00">
                                                    @error('UnitPrice')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Range Box -->
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">{{ trans('cruds.deal.fields.validity_period') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="StartDate" class="form-label">{{ trans('cruds.deal.fields.startdate') }}</label>
                                                <input class="form-control @error('StartDate') is-invalid @enderror"
                                                       type="date"
                                                       value="{{ old('StartDate', date('Y-m-d')) }}"
                                                       id="StartDate"
                                                       name="StartDate">
                                                @error('StartDate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="EndDate" class="form-label">{{ trans('cruds.deal.fields.enddate') }}</label>
                                                <input class="form-control @error('EndDate') is-invalid @enderror"
                                                       type="date"
                                                       value="{{ old('EndDate', date('Y-m-d')) }}"
                                                       id="EndDate"
                                                       name="EndDate">
                                                <input type="hidden" name="LastEditedBy" id="LastEditedBy" value="{{ auth()->user()->id }}"/>
                                                @error('EndDate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a class="btn btn-danger" href="{{ route('deals.index') }}">
                                <i class="fas fa-times me-1"></i>{{ trans('global.cancel') }}
                            </a>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save me-1"></i>{{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/bootstrap-timepicker/timepicker/js/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 on all select elements
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            /**
             * Handle mutual exclusivity for grouped fields
             * When one field in a group is selected, clear all others in the same group
             */
            function handleExclusiveFields() {
                $('select[data-exclusive-group]').on('change', function() {
                    const selectedValue = $(this).val();
                    const groupName = $(this).data('exclusive-group');

                    // If a value was selected (not the empty option)
                    if (selectedValue && selectedValue !== '') {
                        // Find all other selects in the same exclusive group
                        $('select[data-exclusive-group="' + groupName + '"]').not(this).each(function() {
                            // Clear the select2 value
                            $(this).val('').trigger('change');
                        });
                    }
                });
            }

            // Initialize the exclusive field handler
            handleExclusiveFields();

            /**
             * Optional: Visual feedback when fields are cleared
             * Add a subtle animation to show the field was reset
             */
            $('select[data-exclusive-group]').on('select2:unselecting', function(e) {
                const $card = $(this).closest('.card');
                $card.addClass('border-warning');
                setTimeout(function() {
                    $card.removeClass('border-warning');
                }, 1000);
            });

            /**
             * Optional: Form validation before submit
             * Ensure at least one field in each exclusive group is selected
             */
            $('form').on('submit', function(e) {
                let productSelected = false;
                let customerSelected = false;
                let validationErrors = [];

                // Check product selection group
                $('select[data-exclusive-group="product"]').each(function() {
                    if ($(this).val() && $(this).val() !== '') {
                        productSelected = true;
                    }
                });

                // Check customer selection group
                $('select[data-exclusive-group="customer"]').each(function() {
                    if ($(this).val() && $(this).val() !== '') {
                        customerSelected = true;
                    }
                });

                // Validate that at least one product option is selected
                if (!productSelected) {
                    validationErrors.push('Please select either a Product or a Department.');
                }

                // Validate that at least one customer option is selected
                if (!customerSelected) {
                    validationErrors.push('Please select a Customer, Customer Group, or Buying Group.');
                }

                // If there are validation errors, prevent submit and show errors
                if (validationErrors.length > 0) {
                    e.preventDefault();

                    // Show validation errors using SweetAlert2
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Selection',
                        html: validationErrors.join('<br>'),
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });

                    return false;
                }
            });
        });
    </script>

@endpush
