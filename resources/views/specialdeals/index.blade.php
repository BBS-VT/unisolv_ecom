@extends('layouts.master')

@section('title', __('Contract Discount'))

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .column-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .table-card {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
        }

        .customer-code {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
        }

        .filter-buttons .btn {
            border-radius: 20px;
            padding: 0.375rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-buttons .btn:not(.active) {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .filter-buttons .btn.active {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            border-color: transparent;
            color: white;
            box-shadow: 0 4px 12px rgba(28, 117, 188, 0.3);
        }

        .filter-buttons .btn:hover:not(.active) {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .page-header-box {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .page-header-box h2 {
            color: white;
            margin-bottom: 0.25rem;
        }

        .page-header-box p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
        }

        .page-header-box .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
        }

        .page-header-box .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

    </style>
@endpush

@section('content')
    <div class="mx-4">
        <!-- Header -->
        <div class="page-header-box">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ __('Contract Discounts') }}</h2>
                    <p class="mb-0">{{ __('Manage contract discounts')}}</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('specialdeal_create')
                        <a href="{{ route("deals.create") }}" class="btn">
                            <i class="mdi mdi-plus me-1"></i> {{ __('Add Contract Discount') }}
                        </a>
                    @endcan
                    {{--                @can('specialdeal_import')--}}
                    <div class="btn-group">
                        <button type="button" class="btn dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-import me-1"></i> {{ __('global.import') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importDeal" href="#">
                                    <i class="fas fa-upload me-2"></i>{{ __('global.import') }} {{ __('cruds.deal.title') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    {{--@endcan--}}
                </div>
            </div>
        </div>

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="mdi mdi-check-circle me-2"></i>
                <strong>{{ __('global.success') }}!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="column-content">
            <div class="table-card">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table id="deals-table" class="table table-hover mb-0"
                               style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead class="table-light">
                            <tr>
                                <th width="10" class="border-0"></th>
                                <th class="border-0">{{ trans('cruds.deal.fields.description') }}</th>
                                <th class="border-0">{{ trans('cruds.deal.fields.dates') }}</th>
                                <th class="border-0">{{ trans('cruds.deal.fields.discount') }}</th>
                                <th class="border-0">{{ trans('cruds.deal.fields.unitprice') }}</th>
                                <th class="border-0">{{ trans('cruds.deal.fields.applied') }}</th>
                                <th class="border-0 text-center">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($deals as $key => $deal)
                                <tr data-entry-id="{{ $deal->id }}">
                                    <td></td>
                                    <td>
                                        @if(!empty($deal->BuyinGroupID))
                                            @foreach($deal->buyingGroup as $key => $group)
                                                @foreach($group->customer as $entity)
                                                    {{ $group->BuyingGroupName }} -
                                                    <span
                                                        class="badge bg-info text-white">{{ $entity->CustomerName }}</span>
                                                @endforeach
                                            @endforeach
                                        @else
                                            {{ $deal->DealDescription ?? '' }}
                                            <span
                                                class="badge bg-secondary text-white">{{ $deal->productCategory->StockGroupName ?? '' }}</span>
                                            <span
                                                class="badge bg-success text-white">{{ $deal->customer->CustomerName ?? '' }}</span>
                                            <span
                                                class="badge bg-danger text-white">{{ $deal->customerGroup->CustomerCategoryName ?? '' }}</span>
                                            <span
                                                class="badge bg-warning text-dark">{{ $deal->buyingGroup->BuyingGroupName ?? '' }}</span>
                                        @endif

                                    </td>
                                    <td> {{ $deal->StartDate ?? '' }} - {{ $deal->EndDate ?? '' }}</td>
                                    <td> {{ $deal->DiscountPercentage ?? $deal->DiscountAmount }} </td>
                                    <td> {{ $deal->UnitPrice ?? '' }} </td>
                                    <td>
                                    <span class="badge bg-primary text-white">
                                        {{ intval( ltrim($deal->products->StockCode ?? '', '0')) }} -
                                        {{ $deal->products->StockItemName ?? '' }}
                                    </span>

                                    </td>
                                    <td>
                                        @can('specialdeal_show')
                                            <a href="javascript:void(0)" class="show_deal"
                                               data-id="{{ $deal->id }}"
                                               data-url="{{ route('deals.show', $deal->id) }}"
                                               data-bs-toggle="tooltip"
                                               title="{{ trans('global.view') }} {{ trans('cruds.deal.title_singular') }}"
                                               data-bs-placement="top">
                                                <i class="dripicons-preview text-info font-18"></i>
                                            </a>
                                        @endcan
                                        &nbsp;
                                        @can('specialdeal_edit')
                                            <a href="javascript:void(0)" class="edit_deal"
                                               data-id="{{ $deal->id }}"
                                               data-url="{{ route('deals.edit', $deal->id) }}"
                                               data-update-url="{{ route('deals.update', $deal->id) }}"
                                               data-bs-toggle="tooltip"
                                               title="{{ trans('global.edit') }} {{ trans('cruds.deal.title_singular') }}"
                                               data-bs-placement="top">
                                                <i class="dripicons-document-edit text-secondary font-18"></i>
                                            </a>
                                        @endcan
                                        &nbsp;
                                        @can('specialdeal_delete')
                                            <form action="{{ route('deals.destroy', $deal->id) }}" method="POST"
                                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                  style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button aria-expanded="false" class="text-danger font-18"
                                                        style="border:none; background: none;" type="submit"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ trans('global.delete') }} {{ trans('cruds.deal.title_singular') }}">
                                                    <i class="dripicons-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="modal fade" id="createDeal" tabindex="-1" role="dialog"
                             aria-labelledby="createDealLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title m-0"
                                            id="createDealLabel">{{ trans('global.create') }} {{ trans('cruds.deal.title_singular') }}</h6>
                                        <button type="button" class="close " data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-times"></i></span>
                                        </button>
                                    </div>
                                    <form action="{{ route("deals.store") }}" method="POST"
                                          enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group row">
                                                        <label for="DealDescription"
                                                               class="col-sm-4 col-form-label text-start required">{{ trans('cruds.deal.fields.description') }}</label>
                                                        <div class="col-lg-8">
                                                            <input class="form-control" type="text"
                                                                   value="{{ old('DealDescription', '')  }}"
                                                                   id="DealDescription" name="DealDescription" required>
                                                            @if($errors->has('DealDescription'))
                                                                <div class="invalid-feedback">
                                                                    {{ $errors->first('DealDescription') }}
                                                                </div>
                                                            @endif
                                                            <span
                                                                class="help-block">{{ trans('cruds.deal.fields.description_helper') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group bootstrap-select-1">
                                                                <label>{{ trans('cruds.deal.fields.product') }}</label>
                                                                <select
                                                                    class="select2 form-control mb-3 form-select {{ $errors->has('StockItemID') ? 'is-invalid' : '' }}"
                                                                    style="width: 100%; height:36px;">
                                                                    <option disabled selected value> -- select an option
                                                                        --
                                                                    </option>
                                                                    @foreach($products as $id => $product )
                                                                        <option
                                                                            value="{{ $id }}">{{ $product }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group bootstrap-select-1">
                                                                <label>{{ trans('cruds.deal.fields.department') }}</label>
                                                                <select
                                                                    class="select2 form-control mb-3 form-select {{ $errors->has('StockGroupID') ? 'is-invalid' : '' }}"
                                                                    style="width: 100%; height:36px;">
                                                                    <option disabled selected value> -- select an option
                                                                        --
                                                                    </option>
                                                                    @foreach($categories as $id => $category )
                                                                        <option
                                                                            value="{{ $id }}">{{ $category }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group bootstrap-select-1">
                                                                <label>{{ trans('cruds.deal.fields.buygroup') }}</label>
                                                                <select
                                                                    class="select2 form-control mb-3 form-select {{ $errors->has('BuyingGroupID') ? 'is-invalid' : '' }}"
                                                                    style="width: 100%; height:36px;">
                                                                    <option disabled selected value> -- select an option
                                                                        --
                                                                    </option>
                                                                    @foreach($buyinggroups as $id => $buyingroup )
                                                                        <option
                                                                            value="{{ $id }}">{{ $buyingroup }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group bootstrap-select-1">
                                                                <label>{{ trans('cruds.deal.fields.customergroup') }}</label>
                                                                <select
                                                                    class="select2 form-control mb-3 form-select {{ $errors->has('CustomerCategoryID') ? 'is-invalid' : '' }}"
                                                                    style="width: 100%; height:36px;">
                                                                    <option disabled selected value> -- select an option
                                                                        --
                                                                    </option>
                                                                    @foreach($customergroups as $id => $customergroup )
                                                                        <option
                                                                            value="{{ $id }}">{{ $customergroup }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="form-group bootstrap-select-1">
                                                                <label>{{ trans('cruds.deal.fields.customer') }}</label>
                                                                <select
                                                                    class="select2 form-control mb-3 form-select {{ $errors->has('CustomerID') ? 'is-invalid' : '' }}"
                                                                    style="width: 100%; height:36px;">
                                                                    <option disabled selected value> -- select an option
                                                                        --
                                                                    </option>
                                                                    @foreach($customers as $id => $customer )
                                                                        <option
                                                                            value="{{ $id }}">{{ $customer }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="DiscountAmount"
                                                               class="col-sm-4 col-form-label text-start">{{ trans('cruds.deal.fields.discount') }}</label>
                                                        <div class="col-lg-8">
                                                            <input class="form-control" type="text"
                                                                   value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                                                   id="DiscountAmount" name="DiscountAmount">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="DiscountPercentage"
                                                               class="col-sm-4 col-form-label text-start">{{ trans('cruds.deal.fields.discountperc') }}</label>
                                                        <div class="input-group col-lg-8">
                                                            <input type="text" id="DiscountPercentage"
                                                                   name="DiscountPercentage" class="form-control"
                                                                   placeholder="">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"><i
                                                                        class="far fa-percentage"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="UnitPrice"
                                                               class="col-sm-4 col-form-label text-start">{{ trans('cruds.deal.fields.unitprice') }}</label>
                                                        <div class="col-lg-8">
                                                            <input class="form-control" type="text"
                                                                   value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                                                   id="UnitPrice" name="UnitPrice">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label for="StartDate"
                                                                       class="col-sm-6 text-start col-form-label">{{ trans('cruds.deal.fields.startdate') }}</label>
                                                                <div class="col-sm-12">
                                                                    <input class="form-control" type="date"
                                                                           value="{{ date('Y-m-d') }}" id="StartDate"
                                                                           name="StartDate">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label for="EndDate"
                                                                       class="col-sm-6 col-form-label text-start">{{ trans('cruds.deal.fields.enddate') }}</label>
                                                                <div class="col-sm-12">
                                                                    <input class="form-control" type="date"
                                                                           value="{{ date('Y-m-d') }}" id="EndDate"
                                                                           name="EndDate">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                    data-bs-dismiss="modal">Close
                                            </button>
                                            <button type="button" id="submit" value="submit"
                                                    class="btn btn-primary btn-sm">Save
                                            </button>
                                        </div>
                                    </form>
                                </div><!--end modal-content-->
                            </div><!--end modal-dialog-->
                        </div>

                        <div class="modal fade" id="importDeal" tabindex="-1" role="dialog"
                             aria-labelledby="importDealLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h6 class="modal-title m-0 text-white"
                                            id="importDealLabel">{{ trans('global.import') }} {{ trans('cruds.deal.title_singular') }}</h6>
                                        <button type="button" class="close " data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                        </button>
                                    </div>
                                    <form action="{{ route('importSpecialDeals') }}" class="form-horizontal"
                                          method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="modal-body">
                                            <div class="row">
                                                <input type="file" id="input-file-now" name="import_file"
                                                       class="dropify">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-gradient-danger">Import File</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @include('specialdeals.partials._show')

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/dropify/js/dropify.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            let modalMode = 'view'; // 'view' or 'edit'

            // Initialize Select2 (will be called when modal opens in edit mode)
            function initializeSelect2() {
                // Regular Select2
                $('.edit-mode-only.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#dealModal')
                });

                // AJAX Select2 for products
                $('#StockItemID').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Search for a product...',
                    allowClear: true,
                    dropdownParent: $('#dealModal'),
                    ajax: {
                        url: $('#StockItemID').data('ajax-url'),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    escapeMarkup: function(markup) { return markup; }
                });

                // AJAX Select2 for customers
                $('#CustomerID').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Search for a customer...',
                    allowClear: true,
                    dropdownParent: $('#dealModal'),
                    ajax: {
                        url: $('#CustomerID').data('ajax-url'),
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    escapeMarkup: function(markup) { return markup; }
                });

                // Handle mutual exclusivity
                $('select[data-exclusive-group]').off('change.exclusive').on('change.exclusive', function() {
                    const selectedValue = $(this).val();
                    const groupName = $(this).data('exclusive-group');

                    if (selectedValue && selectedValue !== '') {
                        $('select[data-exclusive-group="' + groupName + '"]').not(this).each(function() {
                            $(this).val('').trigger('change');
                        });
                    }
                });
            }

            // Set modal to VIEW mode
            function setViewMode() {
                modalMode = 'view';
                $('#modal-icon').removeClass('la-pen').addClass('la-eye');
                $('#modal-title-text').text('{{ trans("global.view") }} {{ trans("cruds.deal.title_singular") }}');

                // Show view elements, hide edit elements
                $('.view-mode-only').show();
                $('.edit-mode-only').hide();

                // Make pricing and date fields readonly
                $('#DiscountAmount, #DiscountPercentage, #UnitPrice, #StartDate, #EndDate').prop('readonly', true);
                $('#deal_name').prop('readonly', true);
            }

            // Set modal to EDIT mode
            function setEditMode() {
                modalMode = 'edit';
                $('#modal-icon').removeClass('la-eye').addClass('la-pen');
                $('#modal-title-text').text('{{ trans("global.edit") }} {{ trans("cruds.deal.title_singular") }}');

                // Hide view elements, show edit elements
                $('.view-mode-only').hide();
                $('.edit-mode-only').show();

                // Make pricing and date fields editable
                $('#DiscountAmount, #DiscountPercentage, #UnitPrice, #StartDate, #EndDate').prop('readonly', false);
                $('#deal_name').prop('readonly', false);

                // Initialize Select2 for edit mode
                initializeSelect2();
            }

            // VIEW button click
            $(document).on('click', '.show_deal', function(e) {
                e.preventDefault();

                const url = $(this).data('url');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        populateModal(response);
                        setViewMode();
                        $('#dealModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not load deal details',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // EDIT button click
            $(document).on('click', '.edit_deal', function(e) {
                e.preventDefault();

                const url = $(this).data('url');
                const updateUrl = $(this).data('update-url');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        populateModal(response);
                        setEditMode();

                        // Set form action for update
                        $('#dealForm').attr('action', updateUrl);

                        $('#dealModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not load deal details',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Populate modal with data
            function populateModal(response) {
                $('#deal_id').val(response.id);
                $('#deal_name').val(response.DealDescription);

                // Product - view mode
                $('#deal_product_display').val(response.product_name || '-');
                $('#deal_department_display').val(response.department_name || '-');

                // Product - edit mode
                if (response.StockItemID) {
                    const productOption = new Option(response.product_name, response.StockItemID, true, true);
                    $('#StockItemID').append(productOption).trigger('change');
                } else {
                    $('#StockItemID').val('').trigger('change');
                }
                $('#StockGroupID').val(response.StockGroupID || '').trigger('change');

                // Customer - view mode
                $('#deal_buygroup_display').val(response.buygroup_name || '-');
                $('#deal_customergroup_display').val(response.customergroup_name || '-');
                $('#deal_customer_display').val(response.customer_name || '-');

                // Customer - edit mode
                $('#BuyingGroupID').val(response.BuyingGroupID || '').trigger('change');
                $('#CustomerCategoryID').val(response.CustomerCategoryID || '').trigger('change');

                if (response.CustomerID) {
                    const customerOption = new Option(response.customer_name, response.CustomerID, true, true);
                    $('#CustomerID').append(customerOption).trigger('change');
                } else {
                    $('#CustomerID').val('').trigger('change');
                }

                // Pricing - view mode (formatted for display)
                $('#deal_amount_display').val(response.DiscountAmount ? parseFloat(response.DiscountAmount).toFixed(2) : '-');
                $('#deal_percentage_display').val(response.DiscountPercentage || '-');
                $('#deal_unit_price_display').val(response.UnitPrice ? parseFloat(response.UnitPrice).toFixed(2) : '-');

                // Pricing - edit mode (raw values)
                $('#DiscountAmount').val(response.DiscountAmount || '');
                $('#DiscountPercentage').val(response.DiscountPercentage || '');
                $('#UnitPrice').val(response.UnitPrice || '');

                // Dates - view mode
                $('#deal_start_date_display').val(response.StartDate || '');
                $('#deal_end_date_display').val(response.EndDate || '');

                // Dates - edit mode
                $('#StartDate').val(response.StartDate || '');
                $('#EndDate').val(response.EndDate || '');
            }

            // Form submission
            $('#dealForm').on('submit', function(e) {
                e.preventDefault();

                if (modalMode !== 'edit') {
                    return false;
                }

                const formData = new FormData(this);
                const url = $(this).attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#dealModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Deal updated successfully',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload(); // Reload to show updated data
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Could not update deal';

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Clean up Select2 when modal closes
            $('#dealModal').on('hidden.bs.modal', function () {
                $('.select2-ajax').select2('destroy');
                $('.edit-mode-only.select2').select2('destroy');
            });

            let dealsTable = $('#deals-table').DataTable({
                processing: true,
                pageLenght: 15,
                order: [[1, 'asc']],
                searchDelay: 500,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search deals",
                    lengthMenu: "Show _MENU_ deals",
                    info: "Showing _START_ to _END_ of _TOTAL_ contract discounts",
                    paginate: {
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>'
                    },
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                },
                drawCallback: function() {
                    // Apply Bootstrap styling to pagination
                    $('.dataTables_paginate .paginate_button').addClass('page-link');
                    $('.dataTables_paginate .paginate_button.current').addClass('active');

                    // Reinitialize tooltips
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });
        });
    </script>

@endpush
