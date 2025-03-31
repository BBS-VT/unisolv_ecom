@extends('layouts.app')

@section('style')
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
    <style>
        #progressIndicator {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
        }
    </style>
@endsection

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
                                        <a class="dropdown-item" data-toggle="modal" data-target="#importCustomermaster" href="#">
                                            <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                            {{ __('global.import') }} {{ __('cruds.customer.title') }}
                                        </a>
                                        <a class="dropdown-item" data-toggle="modal" data-target="#importBalance" href="#">
                                            <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                            {{ __('global.import') }} {{ __('cruds.customer.fields.balance') }}
                                        </a>

                                    </div>
                                </li>
                            </ul>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="customers-table" class="table table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>{{ trans('cruds.customer.fields.account_code') }}</th>
                            <th>{{ trans('cruds.customer.fields.name') }}</th>
                            <th>{{ trans('cruds.customer.fields.main_contact') }}</th>
                            <th>{{ trans('cruds.customer.fields.phone') }}</th>
                            <th>{{ trans('cruds.customer.fields.vat_nr') }}</th>
                            <th>{{ trans('cruds.customer.fields.status') }}</th>
                            <th></th>
                        </tr>
                    </thead>
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
                            <form action="{{ route('admin.customer-balances.import') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="import_file">{{ __('Select CSV File') }}</label>
                                        <input type="file" id="input_file" name="import_file" class="dropify" required>
                                    </div>
                                    <div class="alert alert-info">
                                        <h6>Instructions:</h6>
                                        <p>Upload a CSV file with the following columns:</p>
                                        <ul>
                                            <li>AccMain (column E)</li>
                                            <li>AccSub (column F)</li>
                                            <li>AgedBalance1 (column H)</li>
                                            <li>AgedBalance2 (column I)</li>
                                            <li>AgedBalance3 (column J)</li>
                                            <li>AgedBalance4 (column K)</li>
                                            <li>AgedBalance5 (column L)</li>
                                            <li>AgedBalance6 (column M)</li>
                                        </ul>
                                        <p>The import will process in the background and can handle large files.</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                    <button class="btn btn-gradient-danger">{{ __('Upload & Import') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="importCustomermaster" tabindex="-1" role="dialog" aria-labelledby="importCustomerLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger">
                                <h6 class="modal-title m-0 text-white" id="importCustomerLabel">{{ __('global.import') }}
                                    {{ __('cruds.customer.title') }}
                                </h6>
                                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                </button>
                            </div>
{{--                            <form action="{{ route('importCustomermaster') }}" class="form-horizontal" method="post" enctype="multipart/form-data">--}}
                            <form id="uploadForm" class="form-horizontal"  enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="modal-body">
                                    <div class="row">
                                        <input type="file" id="fileUpload" name="import_file" class="dropify">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="importButton" class="btn btn-gradient-danger">Import File</button>
                                </div>
                            </form>
                            <div id="progressIndicator" style="display:none;">
                                <p>Uploading file... Please wait.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script>
        $(function () {
            let customersTable = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 30,
                order:[[ 1, 'desc' ]],
                ajax: {
                    url: '{{ route('customers.index') }}',
                    type: "GET",
                },
                columns: [
                    { data: 'account_code', name: 'account_code' },
                    { data: 'name_with_address', name: 'name_with_address' },
                    { data: 'contact_info', name: 'contact_info' },
                    { data: 'PhoneNumber', name: 'PhoneNumber' },
                    { data: 'VatNr', name: 'VatNr' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                searchDelay: 500,
                responsive: true,
                deferRender: true,
            });

            $('#customers-table').on('draw.dt', function () {
                $('.updateCustomerStatus').on ('click', function() {
                    var customerId = $(this).attr('customer_id');

                    $.ajax({
                        type: 'post',
                        url: '/customers/update-status',
                        data: {
                            customer_id: customerId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(resp) {
                            if (resp.status) {
                                customersTable.ajax.reload();
                            }
                        },
                        error: function() {
                            alert('Error updating customer status');
                        }
                    });
                });

                $('[data-toggle="tooltip"]').tooltip();
            });
        });
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
{{--    <script src="{{ asset('pages/jquery.form-upload.init.js') }}"></script>--}}

    <script>
        $(document).ready(function() {
            $('#fileUpload').dropify();

            $('#fileUpload').on('change', function() {
                let fileInput = this;
                let formData = new FormData();
                formData.append('file', fileInput.files[0]);

                // Show progress indicator and disable Import button
                $('#progressIndicator').show();
                $('#importButton').prop('disabled', true);

                $.ajax({
                    url: "{{ route('file.upload') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        $('#progressIndicator').hide();
                        $('#importButton').prop('disabled', false);

                        uploadedFilePath = response.filePath;

                        console.log(response.message, uploadedFilePath);
                    },
                    error: function(error) {
                        console.error('Error:', error.responseJSON.message);

                        $('#progressIndicator').hide();
                        $('#importButton').prop('disabled', true);
                    }
                });
            });

            $('#importButton').on('click', function () {
                if (!uploadedFilePath) {
                    alert('Please upload a file before importing');
                    return;
                }


                $.ajax({
                    url: "{{ route('importCustomermaster') }}",
                    type: "POST",
                    data: {
                        file_path: uploadedFilePath,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        alert('File imported successfully');
                    },
                    error: function (error) {
                        /*console.error('Error importing file:', error.responseJSON.message || 'Unexpected error occurred');
                        alert('Error: ' + (error.responseJSON.message || 'Please check your input and try again'));*/
                        alert('Error starting import process');
                    }
                });
            });

        });
    </script>

@endsection
