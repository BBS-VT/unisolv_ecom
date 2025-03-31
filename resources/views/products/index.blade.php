@extends('layouts.app')

@section('style')
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
    <style>
        .dt-buttons {
            margin: 0 auto; /* Center the buttons */
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ trans('cruds.product.title_singular') }} {{ trans('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-sm btn-outline-dark">Active</button>
                                <button type="button" class="btn btn-sm btn-outline-dark">Inactive</button>
                                <button type="button" class="btn btn-sm btn-outline-dark">All</button>
                            </div>
                            @can('product_create')
                                <a href="{{ route("products.create") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }} {{ trans('cruds.product.title_singular') }}
                                </a>
                            @endcan
                            @can('product_import')
                                <ul class="list-unstyled float-right mb-0">
                                    <li class="dropdown">
                                        <a href="#" class="btn btn-sm btn-outline-danger dropdown-toggle arrow-none waves-light waves-effect"
                                           data-toggle="dropdown" role="button" aria-haspopup="false" aria-expanded="false">
                                            <i data-feather="upload" class="align-self-center icon-xs"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" data-toggle="modal" data-target="#importStockmaster" href="#">
                                                <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                                {{ trans('global.import') }} {{ trans('cruds.product.title') }}
                                            </a>
                                            <a class="dropdown-item" data-toggle="modal" data-target="#importQuantities" href="#">
                                                <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
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
                    <table id="productsTable" class="table table-striped table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ trans('cruds.product.fields.sku') }}</th>
                            <th>{{ trans('cruds.product.fields.name') }} </th>
                            <th>{{ trans('cruds.product.fields.barcode') }}</th>
                            <th>{{ __('Selling Price') }}</th>
                            <th>{{ __('Cost') }}</th>
                            <th>{{ trans('cruds.product.fields.quantity') }}</th>
                            <th width="100px">Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <div class="modal fade" id="importQuantities" tabindex="-1" role="dialog" aria-labelledby="importQuantitiesLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h6 class="modal-title m-0 text-white" id="importQuantitiesLabel">{{ trans('global.import') }} {{ trans('cruds.product.fields.quantity') }}</h6>
                                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                    </button>
                                </div>
                                <form action="{{ route('importQuantities') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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

                    <div class="modal fade" id="importStockmaster" tabindex="-1" role="dialog" aria-labelledby="importStockmaster" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h6 class="modal-title m-0 text-white" id="importStockmasterLabel">{{ trans('global.import') }} {{ trans('cruds.product.title') }}</h6>
                                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                    </button>
                                </div>
                                <form action="{{ route('importStockmaster') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
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
    <script src="{{ asset('/pages/jquery.datatable.init.js') }}"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            });

            $('#productsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('products.index') }}",
                columns: [
                    {data:'id', name: 'id', visible: false},
                    {data: 'StockCode', name: 'StockCode'},
                    {data: 'StockItemName', name: 'StockItemName'},
                    {data: null,
                        render: data => data.Barcode + ' | ' + data.AltBarcode
                    },
                    {data:'prices', name: 'prices', orderable: false, searchable:false  },
                    {data:'costPrices', name: 'costPrices', orderable: false, searchable:false },
                    {data:'quantity_on_hand', name: 'quantity_on_hand', orderable: true, searchable:false },

                    {data: 'action', name: 'action', orderable: false, searchable:false},
                ],
                dom: '<"row align-items-center"<"col-md-4"l><"col-md-4 text-center"B><"col-md-4 text-end"f>>rtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i>',
                        className: 'btn btn-success',
                        titleAttr: 'Export to Excel',
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fas fa-file-csv"></i>',
                        className: 'btn btn-primary',
                        titleAttr: 'Export to CSV',
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf"></i>',
                        className: 'btn btn-danger',
                        titleAttr: 'Export to PDF',
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i>',
                        className: 'btn btn-info',
                        titleAttr: 'Print Table',
                    },
                ],
            });

        });


        $(document).on('click', '.delete-product', function () {
            var productId = $(this).data('id');

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
                        type: "DELETE",
                        url: '/products/' + productId,
                        data: {
                            _token: "{{ csrf_token() }}",
                        },
                        success: function (response) {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            $('.product_datatable').DataTable().ajax.reload();
                        },
                        error: function (response) {
                            cSwal.fire(
                                'Error!',
                                'There was a problem deleting the product.',
                                'error'
                            );
                        }
                    });
                }
            })
        })
    </script>

    <script src="{{ asset('plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.form-upload.init.js') }}"></script>
@endsection


