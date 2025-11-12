@extends('layouts.master')

@section('title', __('global.customer_management'))

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/dropify/css/dropify.min.css') }}" rel="stylesheet">
    <style>
        .column-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .table-card {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
        }

        .customer-code {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
        }

        .customer-info {
            line-height: 1.6;
        }

        .customer-name {
            font-weight: 600;
            color: #495057;
            display: block;
            margin-bottom: 2px;
        }

        .customer-address {
            font-size: 0.875rem;
            color: #6c757d;
            display: block;
        }

        .contact-info {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .contact-name {
            font-weight: 500;
            display: block;
        }

        .contact-detail {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
            border-radius: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-suspended {
            background-color: #fff3cd;
            color: #856404;
        }

        .btn-group-actions {
            white-space: nowrap;
        }

        .btn-sm-custom {
            padding: 0.25rem 0.5rem;
            font-size: 0.775rem;
            margin: 0 1px;
        }

        .filter-buttons .btn {
            border-radius: 20px;
            padding: 0.375rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-buttons .btn:not(.active) {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }

        .filter-buttons .btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .filter-buttons .btn:hover:not(.active) {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .page-header-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .page-header-box h2 {
            color: white;
            margin-bottom: 0.25rem;
        }

        .page-header-box p {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
        }

        .page-header-box .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
        }

        .page-header-box .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .vat-number {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="mx-4">
        <!-- Page Header -->
        <div class="page-header-box">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="mb-1">
                        <i class="mdi mdi-account-group me-2"></i>{{ __('cruds.customer.title') }}
                    </h2>
                    <p class="mb-0">{{ __('Manage your customer accounts') }}</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @can('customer_create')
                        <a href="{{ route('customers.create') }}" class="btn">
                            <i class="mdi mdi-plus me-1"></i> {{ __('global.add') }} {{ __('cruds.customer.title_singular') }}
                        </a>
                    @endcan

                    @can('customer_balance_import')
                        <div class="btn-group">
                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-upload me-1"></i> {{ __('global.import') }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importCustomermaster" href="#">
                                        <i class="mdi mdi-upload-outline me-2"></i>{{ __('global.import') }} {{ __('cruds.customer.title') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#importBalance" href="#">
                                        <i class="mdi mdi-upload-outline me-2"></i>{{ __('global.import') }} {{ __('cruds.customer.fields.balance') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="mdi mdi-check-circle me-2"></i>
                <strong>{{ __('global.success') }}!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Buttons -->
        <div class="mb-4 filter-buttons">
            <div class="btn-group" role="group" aria-label="Customer Status Filter">
                <button type="button" class="btn active" data-filter="all">
                    <i class="mdi mdi-view-list me-1"></i>{{ __('global.all') }}
                </button>
                <button type="button" class="btn" data-filter="active">
                    <i class="mdi mdi-check-circle me-1"></i>{{ __('global.active') }}
                </button>
                <button type="button" class="btn" data-filter="inactive">
                    <i class="mdi mdi-close-circle me-1"></i>{{ __('global.inactive') }}
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="column-content">
            <div class="table-card">
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="customers-table">
                            <thead class="table-light">
                            <tr>
                                <th class="border-0">{{ __('cruds.customer.fields.account_code') }}</th>
                                <th class="border-0">{{ __('cruds.customer.fields.name') }}</th>
                                <th class="border-0">{{ __('cruds.customer.fields.main_contact') }}</th>
                                <th class="border-0">{{ __('cruds.customer.fields.phone') }}</th>
                                <th class="border-0">{{ __('cruds.customer.fields.vat_nr') }}</th>
                                <th class="border-0">{{ __('cruds.customer.fields.status') }}</th>
                                <th class="border-0 text-center">{{ __('global.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Data loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Customers Modal -->
    <div class="modal fade" id="importCustomermaster" tabindex="-1" aria-labelledby="importCustomermasterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importCustomermasterLabel">
                        <i class="mdi mdi-upload me-2"></i>{{ __('global.import') }} {{ __('cruds.customer.title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('importCustomermaster') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileUpload" class="form-label">{{ __('Select CSV File') }}</label>
                            <input type="file" id="fileUpload" name="import_file" class="dropify" required>
                        </div>
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="mdi mdi-information-outline me-1"></i>Instructions
                            </h6>
                            <p class="mb-0 small">Upload a CSV file with customer data. The import will process in the background and can handle large files.</p>
                        </div>
                        <div id="progressIndicator" style="display:none;" class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0">Uploading file... Please wait.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i>{{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-upload me-1"></i>{{ __('Upload & Import') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Balance Modal -->
    <div class="modal fade" id="importBalance" tabindex="-1" aria-labelledby="importBalanceLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importBalanceLabel">
                        <i class="mdi mdi-upload me-2"></i>{{ __('global.import') }} {{ __('cruds.customer.fields.balance') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.customer-balances.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="input_file" class="form-label">{{ __('Select CSV File') }}</label>
                            <input type="file" id="input_file" name="import_file" class="dropify" required>
                        </div>
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="mdi mdi-information-outline me-1"></i>Instructions
                            </h6>
                            <p class="mb-2">Upload a CSV file with the following columns:</p>
                            <ul class="small mb-0">
                                <li>AccMain (column E)</li>
                                <li>AccSub (column F)</li>
                                <li>AgedBalance1 (column H)</li>
                                <li>AgedBalance2 (column I)</li>
                                <li>AgedBalance3 (column J)</li>
                                <li>AgedBalance4 (column K)</li>
                                <li>AgedBalance5 (column L)</li>
                                <li>AgedBalance6 (column M)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i>{{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-upload me-1"></i>{{ __('Upload & Import') }}
                        </button>
                    </div>
                </form>
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
    <script src="{{ URL::asset('build/libs/dropify/js/dropify.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            });

            // Initialize Dropify
            $('.dropify').dropify();

            let currentFilter = 'all';

            // Initialize DataTable with modern styling
            let customersTable = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 15,
                ajax: {
                    url: "{{ route('customers.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.filter = currentFilter;
                    }
                },
                columns: [
                    {
                        data: 'account_code',
                        name: 'account_code',
                        render: function(data, type, row) {
                            return '<span class="customer-code">' + data + '</span>';
                        }
                    },
                    {
                        data: 'name_with_address',
                        name: 'name_with_address',
                        className: 'customer-info'
                    },
                    {
                        data: 'contact_info',
                        name: 'contact_info',
                        className: 'contact-info'
                    },
                    {
                        data: 'PhoneNumber',
                        name: 'PhoneNumber'
                    },
                    {
                        data: 'VatNr',
                        name: 'VatNr',
                        render: function(data, type, row) {
                            return data ? '<span class="vat-number">' + data + '</span>' : '-';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                order: [[1, 'asc']],
                searchDelay: 500,
                responsive: true,
                deferRender: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search customers...",
                    lengthMenu: "Show _MENU_ customers",
                    info: "Showing _START_ to _END_ of _TOTAL_ customers",
                    paginate: {
                        previous: '<i class="mdi mdi-chevron-left"></i>',
                        next: '<i class="mdi mdi-chevron-right"></i>'
                    },
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
                },
                drawCallback: function() {
                    // Apply Bootstrap styling to pagination
                    $('.dataTables_paginate .paginate_button').addClass('page-link');
                    $('.dataTables_paginate .paginate_button.current').addClass('active');

                    // Reinitialize tooltips
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Filter button functionality
            $('.filter-buttons .btn').on('click', function() {
                $('.filter-buttons .btn').removeClass('active');
                $(this).addClass('active');

                currentFilter = $(this).data('filter');
                customersTable.ajax.reload();
            });

            // Update customer status functionality
            $(document).on('click', '.updateCustomerStatus', function(e) {
                e.preventDefault();

                let customerId = $(this).attr('customer_id');
                let customerName = $(this).data('name') || 'this customer';
                let currentStatus = $(this).data('status');
                let newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                Swal.fire({
                    title: 'Update Customer Status?',
                    html: `Change status of <strong>${customerName}</strong> to <strong>${newStatus}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="mdi mdi-check me-1"></i>Yes, update it!',
                    cancelButtonText: '<i class="mdi mdi-close me-1"></i>Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary me-2',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/customers/update-status',
                            type: 'POST',
                            data: {
                                customer_id: customerId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                customersTable.ajax.reload(null, false);
                                Swal.fire({
                                    title: 'Updated!',
                                    text: 'Customer status has been updated successfully.',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'Something went wrong while updating the status.';
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

            // Delete customer functionality
            $(document).on('click', '.delete-customer', function(e) {
                e.preventDefault();

                let customerId = $(this).data('id');
                let customerName = $(this).data('name') || 'this customer';

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete <strong>${customerName}</strong>. This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="mdi mdi-delete me-1"></i>Yes, delete it!',
                    cancelButtonText: '<i class="mdi mdi-close me-1"></i>Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait while we delete the customer.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('customers.destroy', ':id') }}".replace(':id', customerId),
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                customersTable.ajax.reload(null, false);
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Customer has been deleted successfully.',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = 'Something went wrong while deleting the customer.';
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

            // Refresh table functionality
            $(document).on('click', '.refresh-table', function() {
                customersTable.ajax.reload();
                $(this).find('i').addClass('mdi-spin');
                setTimeout(() => {
                    $(this).find('i').removeClass('mdi-spin');
                }, 1000);
            });
        });
    </script>
@endpush

@push('script-bottom')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush
