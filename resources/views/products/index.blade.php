@extends('layouts.master')

@section('title', __('global.product_management'))

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div>
        <div class="page-bar">
            <h1>{{ __('cruds.product.title') }}</h1>
            <div class="d-flex gap-2">
                @can('product_create')
                    <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> {{ __('global.add') }} {{ __('cruds.product.title_singular') }}
                    </a>
                @endcan

                @can('adjust_stock')
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#adjustStockModalIndex">
                        <i class="mdi mdi-plus-minus-variant me-1"></i> Adjust Stock
                    </button>
                @endcan

                @can('product_import')
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-import me-1"></i> {{ __('global.import') }}
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importStockmaster" href="#">
                                    <i class="fas fa-upload me-2"></i>{{ __('global.import') }} {{ __('cruds.product.title') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importQuantities" href="#">
                                    <i class="fas fa-upload me-2"></i>{{ __('global.import') }} {{ __('cruds.product.fields.quantity') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.imports.status') }}">
                                    <i class="fas fa-info-circle me-2"></i>{{ __('global.import') }} {{ __('global.status') }}
                                </a>
                            </li>
                        </ul>
                    </div>
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

        <!-- Main Content -->
        <div class="list-surface">
            <table class="table table-hover mb-0 w-100" id="products-table">
                <thead>
                <tr>
                    <th>{{ __('cruds.product.fields.sku') }}</th>
                    <th>{{ __('cruds.product.fields.name') }}</th>
                    <th>{{ __('cruds.product.fields.barcode') }}</th>
                    <th class="num">P1</th>
                    <th class="num">P2</th>
                    <th class="num">P3</th>
                    <th class="num">P4</th>
                    <th class="num">Avg cost</th>
                    <th class="num">Last cost</th>
                    <th class="num">{{ __('cruds.product.fields.quantity') }}</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <!-- Data loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Import Modals -->
    @include('products.partials.importStockmaster')
    @include('products.partials.importStockquantities')
    @include('products.partials.adjustStockIndex')

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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            });

            // Initialize DataTable with modern styling
            let productsTable = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: {
                    url: "{{ route('products.index') }}",
                    type: "GET"
                },
                columns: [
                    { data: 'StockCode', name: 'StockCode' },
                    { data: 'StockItemName', name: 'StockItemName' },
                    { data: 'barcodes', name: 'Barcode', searchable: true, orderable: false },
                    { data: 'SellingPrice', name: 'products.SellingPrice', searchable: false, className: 'num' },
                    { data: 'SellingPrice2', name: 'products.SellingPrice2', searchable: false, className: 'num' },
                    { data: 'SellingPrice3', name: 'products.SellingPrice3', searchable: false, className: 'num' },
                    { data: 'SellingPrice4', name: 'products.SellingPrice4', searchable: false, className: 'num' },
                    { data: 'AverageCostPrice', name: 'products.AverageCostPrice', searchable: false, className: 'num' },
                    { data: 'LastCostPrice', name: 'stock_item_holdings.LastCostPrice', searchable: false, className: 'num' },
                    { data: 'quantity_on_hand', name: 'quantity_on_hand', searchable: false, className: 'num' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
                ],
                order: [[0, 'asc']],
                searchDelay: 500,
                responsive: true,
                deferRender: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search products...",
                    lengthMenu: "Show _MENU_ products",
                    info: "Showing _START_ to _END_ of _TOTAL_ products",
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                },
                drawCallback: function() {
                    // Apply Bootstrap styling to pagination
                    $('.dataTables_paginate .paginate_button').addClass('page-link');
                    $('.dataTables_paginate .paginate_button.current').addClass('active');
                }
            });

            // Delete product functionality with SweetAlert2
            $(document).on('click', '.delete-product', function() {
                let productId = $(this).data('id');
                let productName = $(this).data('name') || 'this product';

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete "${productName}". This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--bs-danger)',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i>Yes, delete it!',
                    cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait while we delete the product.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('products.destroy', ':id') }}".replace(':id', productId),
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                productsTable.ajax.reload(null, false); // Keep current page
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Product has been deleted successfully.',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'Something went wrong while deleting the product.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        });
                    }
                });
            });

            // Refresh button functionality
            $(document).on('click', '.refresh-table', function() {
                productsTable.ajax.reload();
                $(this).find('i').addClass('fa-spin');
                setTimeout(() => {
                    $(this).find('i').removeClass('fa-spin');
                }, 1000);
            });
        });
    </script>

    <script>
        // Additional initialization if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush

