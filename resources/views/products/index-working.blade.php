@extends('layouts.app')

@section('style')
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
    <style>
        .dt-buttons {
            margin: 0 auto;
        }

        .dataTables_length {
            float: left;
        }

        .dataTables_filter {
            float: right;
        }
    </style>

@endsection

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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <h4>Products</h4>
                            </div>
                            <div class="col-auto">
                                <div class="card-header-action">
                                    @can('product_create')
                                        <a href="{{ route('products.create') }}" class="btn btn-primary mr-1">
                                            <i class="fas fa-plus"></i> Add Product
                                        </a>
                                    @endcan

                                    @can('product_import')
                                        <ul class="list-unstyled float-right mb-0">
                                            <li class="dropdown">
                                                <a href="#"
                                                   class="btn btn-outline-danger dropdown-toggle arrow-none waves-light waves-effect"
                                                   data-toggle="dropdown" role="button" aria-haspopup="false"
                                                   aria-expanded="false">
                                                    <i class="fas fa-file-import align-self-center icon-xs"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" data-toggle="modal"
                                                       data-target="#importStockmaster" href="#">
                                                        <i data-feather="upload-cloud"
                                                           class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                                        {{ trans('global.import') }} {{ trans('cruds.product.title') }}
                                                    </a>
                                                    <a class="dropdown-item" data-toggle="modal"
                                                       data-target="#importQuantities" href="#">
                                                        <i data-feather="upload-cloud"
                                                           class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                                        {{ trans('global.import') }} {{ trans('cruds.product.fields.quantity') }}
                                                    </a>
                                                </div>
                                            </li>
                                        </ul>
                                    @endcan
                                </div>
                            </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="products-table">
                                <thead>
                                <tr>
                                    <th>Stock Code</th>
                                    <th>Name</th>
                                    <th>Barcode</th>
                                    <th>Selling Prices</th>
                                    <th>Cost Prices</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <!-- Table body is loaded via AJAX -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    @include('products.partials.importStockmaster')
    @include('products.partials.importStockquantities')

@endsection

@section('script')
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

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            });
            // Initialize DataTable with server-side processing
            let productsTable = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('products.index') }}",
                    type: "GET"
                },
                columns: [
                    { data: 'StockCode', name: 'StockCode' },
                    { data: 'StockItemName', name: 'StockItemName' },
                    { data: 'barcodes', name: 'Barcode', searchable: true },
                    { data: 'prices', name: 'prices', searchable: false },
                    { data: 'costPrices', name: 'costPrices', searchable: false },
                    { data: 'quantity_on_hand', name: 'quantity_on_hand', searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                order: [[0, 'asc']],
                // Enable search delay to reduce server load
                searchDelay: 500,
                // Add responsive features
                responsive: true,
                // Improve rendering performance
                deferRender: true
            });

            // Delete product functionality
            $(document).on('click', '.delete-product', function() {
                let productId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/products/${productId}`,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                productsTable.ajax.reload();
                                Swal.fire('Deleted!', 'Product has been deleted.', 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection


