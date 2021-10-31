@extends('layouts.app')

@push('style')
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
@endpush

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">

        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h4 class="card-title">{{ trans('cruds.customer.title_singular') }} {{ trans('global.list') }}</h4>
                        <p class="text-muted mb-0"></p>
                    </div>

                    <div class="col-auto align-self-center float-right">
                        <div class="btn-group" role="group" aria-label="Basic example">
                            <button type="button" class="btn btn-sm btn-outline-dark">{{ trans('global.active') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-dark">{{ trans('global.inactive') }}</button>
                            <button type="button" class="btn btn-sm btn-outline-dark">{{ trans('global.all') }}</button>
                        </div>
                        @can('customer_create')
                            <a href="{{ route("customers.create") }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.add') }} {{ trans('cruds.customer.title_singular') }}
                            </a>
                        @endcan
                        @can('customer_balance_import')
                            <ul class="list-unstyled float-right mb-0">
                                <li class="dropdown">
                                    <a href="#" class="btn btn-sm btn-outline-danger dropdown-toggle arrow-none waves-light waves-effect"
                                       data-toggle="dropdown" role="button" aria-haspopup="false" aria-expanded="false">
                                        <i data-feather="upload" class="align-self-center icon-xs"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" data-toggle="modal" data-target="#importBalance" href="#">
                                            <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                            {{ trans('global.import') }} {{ trans('cruds.customer.fields.balance') }}
                                        </a>
<!--                                        <a class="dropdown-item" data-toggle="modal" data-target="#importStockmaster" href="#">
                                            <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                            {{ trans('global.import') }} {{ trans('cruds.product.title') }}
                                        </a>-->
                                    </div>
                                </li>
                            </ul>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="datatable" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th width="5"></th>
                            <th>{{ trans('cruds.customer.fields.account_code') }}</th>
                            <th>{{ trans('cruds.customer.fields.name') }}</th>
                            <th>{{ trans('cruds.customer.fields.main_contact') }}</th>
                            <th>{{ trans('cruds.customer.fields.email') }}</th>
                            <th>{{ trans('cruds.customer.fields.phone') }}</th>
                            <th>{{ trans('cruds.customer.fields.vat_nr') }}</th>
{{--                            <th>{{ trans('cruds.customer.fields.store_ean') }}</th>--}}
                            <th>{{ trans('cruds.customer.fields.status') }}</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($customers as $key => $customer)
                        <tr data-entry-id="{{ $customer->id }}">
                            <td></td>
                            <td>{{ $customer->acc_main ?? '' }} {{ $customer->acc_sub ?? '' }}</td>
                            <td>{{ $customer->CustomerName ?? '' }}</td>
                            <td>{{ $customer->PrimaryContactID ?? '' }}</td>
                            <td>{{ $customer->GeneralEmailAddress ?? '' }}</td>
                            <td>{{ $customer->PhoneNumber ?? '' }}</td>
                            <td>{{ $customer->VatNr ?? '' }}</td>
{{--                            <td>{{ $customer->StoreEAN ?? '' }}</td>--}}
                            <td>
                                @if($customer->CustomerStatus==1)
                                    <a class="updateCustomerStatus" id="customer-{{ $customer->id }}" customer_id="{{ $customer->id }}"
                                       href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to De-activate Customer">
                                        <span class="badge badge-success">{{ trans('global.active') }}</span>
                                    </a>
                                @else
                                    <a class="updateCustomerStatus" id="customer-{{ $customer->id }}" customer_id="{{ $customer->id }}"
                                       href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to Activate Customer">
                                        <span class="badge badge-danger">{{ trans('global.inactive') }}</span>
                                    </a>
                                @endif
                                @if($customer->IsOnCreditHold==1)
                                    <span class="badge badge-warning">{{ trans('global.credit_hold') }}</span>
                                @endif
                            </td>
                            <td>
                                @can('customer_show')
                                    <a href="{{ route('customers.show', $customer->id) }}" data-toggle="tooltip"
                                       title="{{ trans('global.view') }} {{ trans('cruds.customer.title_singular') }}"
                                       data-placement="top">
                                        <i class="las dripicons-preview text-info font-18"></i>
                                    </a>
                                @endcan
                                @can('customer_edit')
                                    <a href="{{ route('customers.edit', $customer->id) }}"data-toggle="tooltip"
                                       title="{{ trans('global.edit') }} {{ trans('cruds.customer.title_singular') }}"
                                       data-placement="top">
                                        <i class="las dripicons-document-edit text-info font-18"></i>
                                    </a>
                                @endcan
                                @can('customer_delete')
                                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                data-toggle="tooltip" data-placement="top"
                                                title="{{ trans('global.delete') }} {{ trans('cruds.customer.title_singular') }}">
                                            <i class="dripicons-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="modal fade" id="importBalance" tabindex="-1" role="dialog" aria-labelledby="importBalanceLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h6 class="modal-title m-0 text-white" id="importBalanceLabel">{{ trans('global.import') }}
                                    {{ trans('cruds.customer.title') }} {{ trans('cruds.customer.fields.balance') }}
                                </h6>
                                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                </button>
                            </div>
                            <form action="{{ route('importBalances') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
            </div>
        </div>
    </div>
</div>

@endsection

@push('custom-scripts')
    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            @can('client_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('customers.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });

                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')

                        return
                    }

                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: { ids: ids, _method: 'DELETE' }})
                            .done(function () { location.reload() })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults,{
                order: [[ 2, 'desc']],
                pageLength: 30,
            });
            $('.datatable-Customer:not(.ajaxTable)').DataTable({ buttons: dtButtons })
                $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                    $($.fn.dataTable.tables(true)).DataTable()
                        .columns.adjust();
                });
        })
    </script>

    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.datatable.init.js') }}"></script>
    <script src="{{ asset('plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.form-upload.init.js') }}"></script>

@endpush
