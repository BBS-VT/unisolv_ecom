@extends('layouts.master', ['page' => 'orders'])

@section('title', __('global.orders'))

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .column-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table-card {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
        }
        .product-barcode {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            background-color: #f8f9fa;
            padding: 2px 4px;
            border-radius: 2px;
        }
        .price-list {
            font-size: 0.9rem;
            line-height: 1.3;
        }
        .btn-group-actions {
            white-space: nowrap;
        }
        .btn-sm-custom {
            padding: 0.25rem 0.5rem;
            font-size: 0.775rem;
            margin: 0 1px;
        }
        .stock-badge {
            display: inline-block;
            padding: 0.25em 0.5em;
            font-size: 0.75em;
            font-weight: 500;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .stock-high { background-color: #d4edda; color: #155724; }
        .stock-medium { background-color: #fff3cd; color: #856404; }
        .stock-low { background-color: #f8d7da; color: #721c24; }
        .dt-buttons {
            margin-bottom: 1rem;
        }
    </style>
@endpush

@section('content')
    <div class="mx-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="mb1">{{ __('cruds.order.title_singular') }} {{ __('global.list') }}</h2>
                <p class="text-muted mb-0">{{ __('List of all sales orders') }}</p>
            </div>
            <div class="col-auto align-self-center float-end">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'new' ? 'active' : '' }}">
                        {{ __('global.new_orders') }}
                    </a>
                    <a href="{{ route('orders.index', 'processed') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'processed' ? 'active' : '' }}">
                        {{ __('global.processed_orders') }}
                    </a>
                    <a href="{{ route('orders.index', 'onhold') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'onhold' ? 'active' : '' }}">
                        {{ __('global.on_hold') }}
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
        <!-- Success Message -->
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <strong>{{ __('global.success') }}!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="column-content">
            <div class="table-card">
                <div class="card-body p-2">
                    @include('orders._table')
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

    <script>
        $('#datatable-orders').DataTable({
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all']
            ],
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
            ],
            order: [[ 5, "desc" ]],
            processing: true,
            responsive: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
                search: "_INPUT_",
                searchPlaceholder: "Search orders...",
            },
        });
    </script>

@endpush
