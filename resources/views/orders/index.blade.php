@extends('layouts.master', ['page' => 'orders'])

@section('title', __('global.orders'))

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div>
        <div class="page-bar">
            <h1>{{ __('cruds.order.title_singular') }} {{ __('global.list') }}</h1>
            <div class="d-flex align-items-center gap-2">
                <div class="btn-group btn-group-sm" role="group" aria-label="Order status tabs">
                    <a class="btn btn-sm btn-outline-dark {{ $tab == 'pending' ? 'active' : '' }}"
                       href="{{ route('orders.index', ['tab' => 'pending']) }}" >
                        <i class="bx bx-time me-1"></i>
                        {{ __('global.pending') }}
                        @if(isset($pendingCount))
                            <span class="badge bg-warning ms-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <a class="btn btn-sm btn-outline-dark {{ $tab == 'processed' ? 'active' : '' }}"
                       href="{{ route('orders.index', ['tab' => 'processed']) }}">
                        <i class="bx bx-check-circle me-1"></i>
                        {{ __('global.processed') }}
                        @if(isset($processedCount))
                            <span class="badge bg-warning ms-1">{{ $processedCount }}</span>
                        @endif
                    </a>
                    <a class="btn btn-sm btn-outline-dark {{ $tab == 'completed' ? 'active' : '' }}"
                       href="{{ route('orders.index', ['tab' => 'completed']) }}" >
                        <i class="bx bx-package me-1"></i>
                        {{ __('global.completed') }}
                        @if(isset($completedCount))
                            <span class="badge bg-warning ms-1">{{ $completedCount }}</span>
                        @endif
                    </a>
                    <a class="btn btn-sm btn-outline-dark {{ $tab == 'all' ? 'active' : '' }}"
                       href="{{ route('orders.index', ['tab' => 'all']) }}">
                        <i class="bx bx-list-ul me-1"></i>
                        {{ __('global.all_orders') }}
                    </a>
                </div>
                @can('order_create')
                    <a href="{{ route("orders.create") }}" class="btn btn-sm btn-primary">
                        <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                        {{ __('global.new_order') }}
                    </a>
                @endcan
            </div>
        </div>
        <!-- Success Message -->
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert">
                <strong>{{ __('global.success') }}!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="list-surface">
            @include('orders._table')
        </div>

        @include('orders._statusUpdate')

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
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('orders.index', ['tab' => $tab]) }}",
                type: "GET"
            },
            columns: [
                { data: 'OrderNumber', name: 'OrderNumber' },
                { data: 'customer_name', name: 'customer_name', orderable: false },
                { data: 'salesrep', name: 'salesrep', orderable: false, searchable: false, className: 'text-muted' },
                { data: 'CustomerPurchaseOrderNumber', name: 'CustomerPurchaseOrderNumber', className: 'code text-muted' },
                { data: 'OrderDate', name: 'OrderDate', className: 'num text-muted' },
                { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-nowrap' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
            ],
            order: [[ 4, "desc" ]], // newest order date first
            searchDelay: 500,
            dom: "<'d-flex justify-content-between align-items-center gap-2 mb-2'Bf>rt<'d-flex justify-content-between align-items-center'ip>",
            pageLength: 50,
            lengthMenu: [
                [ 25, 50, 100, 500 ],
                [ '25 rows', '50 rows', '100 rows', '500 rows' ]
            ],
            buttons: {
                dom: { button: { className: 'btn btn-sm btn-outline-secondary' } },
                buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength' ],
            },
            responsive: true,
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-fw"></i><span class="sr-only">Loading...</span> ',
                search: "_INPUT_",
                searchPlaceholder: "Search orders...",
            },
        });

        // Rows arrive via AJAX, so custom.js's page-load .delete-confirm binding never reaches them
        $('#datatable-orders').on('click', '.delete-confirm', function (event) {
            event.preventDefault();
            var url = $(this).attr('href');
            Swal.fire({
                title: 'Are you sure?',
                text: 'This record and it`s details will be permanantly deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Delete!',
                focusConfirm: false,
                focusCancel: false,
            }).then((result) => {
                if (result.value) {
                    window.location.href = url;
                }
            });
        });
    </script>

@endpush
