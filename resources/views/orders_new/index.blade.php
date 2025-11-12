@extends('layouts.app', ['page' => 'orders'])

@section('title', __('global.orders'))

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
                            <h4 class="card-title">{{ __('cruds.order.title_singular') }} {{ __('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center float-end">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'new' ? 'active' : '' }}">
                                    {{ __('global.new_orders') }}
                                </a>
                                <a href="{{ route('orders.index', 'processed') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'processed' ? 'active' : '' }}">
                                    {{ __('global.processed_orders') }}
                                </a>
                                <a href="{{ route('orders.index', 'all') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'all' ? 'active' : '' }}">
                                    {{ __('global.all_orders') }}
                                </a>
                            </div>
                            @can('order_create')
                                <a href="{{ route("orders.create") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ __('global.new_order') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
{{--                @include('orders._filters')--}}
                <div class="card-body pb-1">
                    @include('orders._table')
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

@endpush
