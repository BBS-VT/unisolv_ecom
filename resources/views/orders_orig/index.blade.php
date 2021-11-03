@extends('layouts.app')

@push('style')
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/select.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ trans('cruds.order.title_singular') }} {{ trans('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">
                            @can('order_create')
                                <a href="{{ route("orders.create.step.one") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.new_order') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatable-orders" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th width="10"></th>
                                <th>{{ trans('cruds.order.fields.number') }}</th>
                                <th>{{ trans('cruds.order.fields.customer_name') }}</th>
                                <th>{{ trans('cruds.order.fields.salesrep') }}</th>
                                <th>{{ trans('cruds.order.fields.ponumber') }}</th>
                                <th>{{ trans('cruds.order.fields.created_at') }}</th>
                                <th>{{ trans('cruds.order.fields.status') }}</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $key => $order)
                                <tr data-entry-id="{{ $order->id }}">
                                    <td></td>
                                    <td> {{ $order->OrderNumber ?? '' }} </td>
                                    <td> {{ $order->customer->CustomerName ?? '' }} </td>
                                    <td> {{ $order->salesperson->FullName ?? '' }} </td>
                                    <td> {{ $order->CustomerPurchaseOrderNumber ?? '' }}</td>
                                    <td> {{ $order->OrderDate ?? '' }} </td>
                                    <td>
                                        <span class=" badge <?php if ( $order->OrderStatusID == 1 ) { echo "badge-danger"; }
                                            elseif ( $order->OrderStatusID == 2) { echo "badge-warning"; }
                                            elseif ( $order->OrderStatusID == 3) { echo "badge-info"; }
                                            elseif ( $order->OrderStatusID == 4) { echo "badge-success"; }
                                            ?>"> {{ $order->orderstatus->name ?? '' }} </span>

                                            {{--@if($order->Authorisation == 0 )
                                                <span class="badge badge-success">Authorised </span>
                                            @elseif($order->Authorisation == 1)
                                                <span class="badge badge-warning">On Hold</span>
                                            @elseif($order->Authorisation == 2)
                                                <span class="badge badge-info">Released</span>
                                            @endif--}}

                                    <td>
                                        @can('order_show')
                                            <a href="{{ route('orders.show', $order->id) }}" target="_blank" data-toggle="tooltip"
                                               title="{{ trans('global.view') }} {{ trans('cruds.order.title_singular') }}"
                                               data-placement="top">
                                                <i class="las dripicons-preview text-info font-18"></i>
                                            </a>
                                        @endcan
                                        &nbsp;
                                        {{--@can('order_edit')
                                            <a href="{{ route('orders.edit', $order->id) }}" data-toggle="tooltip"
                                               title="{{ trans('global.edit') }} {{ trans('cruds.order.title_singular') }}"
                                               data-placement="top">
                                                <i class="las dripicons-document-edit text-info font-18"></i>
                                            </a>
                                        @endcan--}}
                                        &nbsp;
                                        @can('order_delete')
                                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST"
                                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="{{ trans('global.delete') }} {{ trans('cruds.order.title_singular') }}">
                                                    <i class="dripicons-trash"></i>
                                                </button>
                                            </form>
                                        @endcan
                                            &nbsp;
                                        @can('order_download')
                                            <a href="{{ route('orders.download', $order->id) }}"  onClick="history.go(0)"
                                            data-toggle="tooltip" title="{{ trans('global.downloadFile') }} {{ trans('cruds.order.title_singular') }}"
                                               data-placement="top"> <i class="las dripicons-download text-info font-18"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

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

    <script>

    </script>
@endpush
