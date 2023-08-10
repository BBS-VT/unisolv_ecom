@extends('layouts.app')

@push('style')
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                @if($message = Session::get('success'))
                    <div class="alert alert-info alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                        <strong>{{ trans('global.success') }}</strong> {{ $message }}
                    </div>
                @endif
                {!! Session::forget('success') !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ trans('cruds.deal.title_singular') }} {{ trans('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">
                            @can('specialdeal_create')
                               <a href="{{ route("deals.create") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }} {{ trans('cruds.deal.title_singular') }}
                                </a>
                            <!--   <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#createDeal">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }} {{ trans('cruds.deal.title_singular') }}
                                </button>-->
                                <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#importDeal">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.import') }} {{ trans('cruds.deal.title_singular') }}
                                </button>
                            @endcan
                            <a href="{{ route('exportSpecialDeals', 'xlsx') }}"><button class="btn btn-outline-success btn-sm">Export to Excel</button></a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.deal.fields.description') }}</th>
                            <th>{{ trans('cruds.deal.fields.dates') }}</th>
                            <th>{{ trans('cruds.deal.fields.discount') }}</th>
                            <th>{{ trans('cruds.deal.fields.unitprice') }}</th>
                            <th>{{ trans('cruds.deal.fields.applied') }}</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($deals as $key => $deal)
                            <tr data-entry-id="{{ $deal->id }}">
                                <td> </td>
                                <td>
                                    @if(!empty($deal->BuyinGroupID))
                                        @foreach($deal->buyingGroup as $key => $group)
                                            @foreach($group->customer as $entity)
                                                {{ $group->BuyingGroupName }} -
                                                <span class="badge badge-info">{{ $entity->CustomerName }}</span>
                                            @endforeach
                                        @endforeach
                                    @else
                                        {{ $deal->DealDescription ?? '' }}
                                        <span class="badge badge-secondary">{{ $deal->productCategory->StockGroupName ?? '' }}</span>
                                        <span class="badge badge-success">{{ $deal->customer->CustomerName ?? '' }}</span>
                                        <span class="badge badge-danger">{{ $deal->customerGroup->CustomerCategoryName ?? '' }}</span>
                                        <span class="badge badge-warning">{{ $deal->buyingGroup->BuyingGroupName ?? '' }}</span>
                                    @endif

                                </td>
                                <td> {{ $deal->StartDate ?? '' }} - {{ $deal->EndDate ?? '' }}</td>
                                <td> {{ $deal->DiscountPercentage ?? $deal->DiscountAmount }} </td>
                                <td> {{ $deal->UnitPrice ?? '' }} </td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ intval( ltrim($deal->products->StockCode ?? '', '0')) }} -
                                        {{ $deal->products->StockItemName ?? '' }}
                                    </span>

                                </td>
                                <td>
                                    @can('specialdeal_show')
                                        <a href="javascript:void(0)" id="show_deal" data-url="{{ route('deals.show', $deal->id) }}"
                                           data-toggle="tooltip" title="{{ trans('global.view') }} {{ trans('cruds.deal.title_singular') }}"
                                           data-placement="top">
                                            <i class="las dripicons-preview text-info font-18"></i>
                                        </a>
                                    @endcan
                                    &nbsp;
                                    @can('specialdeal_edit')
                                        <a href="{{ route('deals.edit', $deal->id) }}" data-toggle="tooltip"
                                           title="{{ trans('global.edit') }} {{ trans('cruds.deal.title_singular') }}"
                                           data-placement="top">
                                            <i class="las dripicons-document-edit text-info font-18"></i>
                                        </a>
                                    @endcan
                                    &nbsp;
                                    @can('specialdeal_delete')
                                        <form action="{{ route('deals.destroy', $deal->id) }}" method="POST"
                                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                    data-toggle="tooltip" data-placement="top"
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

                    <div class="modal fade" id="createDeal" tabindex="-1" role="dialog" aria-labelledby="createDealLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title m-0" id="createDealLabel">{{ trans('global.create') }} {{ trans('cruds.deal.title_singular') }}</h6>
                                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-times"></i></span>
                                    </button>
                                </div>
                                <form action="{{ route("deals.store") }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group row">
                                                    <label for="DealDescription" class="col-sm-4 col-form-label text-left required">{{ trans('cruds.deal.fields.description') }}</label>
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
                                                            <select class="select2 form-control mb-3 custom-select {{ $errors->has('StockItemID') ? 'is-invalid' : '' }}" style="width: 100%; height:36px;">
                                                                <option disabled selected value> -- select an option -- </option>
                                                                @foreach($products as $id => $product )
                                                                    <option value="{{ $id }}" >{{ $product }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group bootstrap-select-1">
                                                            <label >{{ trans('cruds.deal.fields.department') }}</label>
                                                            <select class="select2 form-control mb-3 custom-select {{ $errors->has('StockGroupID') ? 'is-invalid' : '' }}" style="width: 100%; height:36px;">
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
                                                            <select class="select2 form-control mb-3 custom-select {{ $errors->has('BuyingGroupID') ? 'is-invalid' : '' }}" style="width: 100%; height:36px;">
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
                                                            <select class="select2 form-control mb-3 custom-select {{ $errors->has('CustomerCategoryID') ? 'is-invalid' : '' }}" style="width: 100%; height:36px;">
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
                                                            <select class="select2 form-control mb-3 custom-select {{ $errors->has('CustomerID') ? 'is-invalid' : '' }}" style="width: 100%; height:36px;">
                                                                <option disabled selected value> -- select an option -- </option>
                                                                @foreach($customers as $id => $customer )
                                                                    <option value="{{ $id }}" >{{ $customer }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="DiscountAmount" class="col-sm-4 col-form-label text-left">{{ trans('cruds.deal.fields.discount') }}</label>
                                                    <div class="col-lg-8">
                                                        <input class="form-control" type="text" value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                                               id="DiscountAmount" name="DiscountAmount">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="DiscountPercentage" class="col-sm-4 col-form-label text-left">{{ trans('cruds.deal.fields.discountperc') }}</label>
                                                    <div class="input-group col-lg-8">
                                                        <input type="text" id="DiscountPercentage" name="DiscountPercentage" class="form-control" placeholder="">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text"><i class="far fa-percentage"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="UnitPrice" class="col-sm-4 col-form-label text-left">{{ trans('cruds.deal.fields.unitprice') }}</label>
                                                    <div class="col-lg-8">
                                                        <input class="form-control" type="text" value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                                               id="UnitPrice" name="UnitPrice">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="StartDate" class="col-sm-6 text-left col-form-label">{{ trans('cruds.deal.fields.startdate') }}</label>
                                                            <div class="col-sm-12">
                                                                <input class="form-control" type="date" value="{{ date('Y-m-d') }}" id="StartDate" name="StartDate">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="form-group">
                                                            <label for="EndDate" class="col-sm-6 col-form-label text-left">{{ trans('cruds.deal.fields.enddate') }}</label>
                                                            <div class="col-sm-12">
                                                                <input class="form-control" type="date" value="{{ date('Y-m-d') }}" id="EndDate" name="EndDate">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                        <button type="button" id="submit" value="submit" class="btn btn-primary btn-sm">Save</button>
                                    </div>
                                </form>
                            </div><!--end modal-content-->
                        </div><!--end modal-dialog-->
                    </div>

                    <div class="modal fade" id="importDeal" tabindex="-1" role="dialog" aria-labelledby="importDealLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h6 class="modal-title m-0 text-white" id="importDealLabel">{{ trans('global.import') }} {{ trans('cruds.deal.title_singular') }}</h6>
                                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                    </button>
                                </div>
                                <form action="{{ route('importSpecialDeals') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                        <div class="row">
                                            <input type="file" id="input-file-now" name="import_file" class="dropify">
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
@endsection

@push('custom-scripts')
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/pages/jquery.datatable.init.js') }}"></script>

    <script src="{{ asset('plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.form-upload.init.js') }}"></script>

    <script>
        $(document).ready(function () {

            $('body').on('click', '#show_deal', function () {
                var dealURL = $(this).data('url');
                $.get(dealURL, function (data) {
                    $('#showDealModal').modal('show');
                    $('#deal_id').val(data.id);
                    $('#deal_name').val(data.DealDescription);
                    $('#deal_customer').val(data.CustomerName);
                    $('#deal_product').val(data.StockItemName);
                    $('#deal_amount').val(data.DiscountAmount);
                    $('#deal_percentage').val(data.DiscountPercentage);
                    $('#deal_unit_price').val(data.UnitPrice);
                    $('#deal_start_date').val(data.StartDate);
                    $('#deal_end_date').val(data.EndDate);
                })
            })
        })
    </script>

@endpush
