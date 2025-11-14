@extends('layouts.master')

@section('title', trans('cruds.user.title'))

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        /* Modern Card Styling */
        .users-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .users-card .card-header {
            background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 2px solid #e9ecef;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .users-card .card-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
        }

        .users-card .card-title i {
            color: #667eea;
        }

        /* Table Enhancements */
        #datatable thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 0.75rem;
        }

        #datatable tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        #datatable tbody tr {
            transition: all 0.2s ease;
        }

        #datatable tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }

        /* Role Badge Styling */
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            margin: 0.125rem;
        }

        .role-badge.admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .role-badge.user {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .role-badge.manager {
            background-color: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        /* Action Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            background: white;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            background-color: #f8f9fa;
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 1.125rem;
        }

        .action-btn.delete-btn:hover {
            background-color: #fff5f5;
            border-color: #dc3545;
        }

        /* Add User Button */
        .btn-add-user {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .btn-add-user:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }

        /* User Name Styling */
        .user-name {
            font-weight: 600;
            color: #212529;
        }

        .user-email {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .rep-code {
            font-family: 'Courier New', monospace;
            background-color: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
            border: 1px solid #e9ecef;
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 0.75rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #e9ecef;
            padding: 0.5rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Page Title */
        .page-title-box {
            margin-bottom: 1.5rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .empty-state-text {
            color: #6c757d;
            margin-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .users-card .card-header {
                padding: 1rem;
            }

            .users-card .card-title {
                font-size: 1.125rem;
            }

            #datatable tbody td {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Modal Form Styles */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #667eea;
            border: none;
            color: white;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
        }

        #createUserForm .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        #createUserForm .input-group .form-control {
            border-left: none;
        }

        #createUserForm .input-group .form-control:focus {
            border-color: #ced4da;
            box-shadow: none;
        }

        #createUserForm .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }

        #createUserForm .input-group:focus-within .form-control {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        #togglePassword {
            border-left: none;
        }

        #togglePassword:hover {
            background-color: #f8f9fa;
        }

        #repCodeGroup, #customerGroup {
            transition: all 0.3s ease;
        }
    </style>
@endpush

@section('content')

        @include('flash-message')

        <!-- Users Table Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card users-card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">
                                    <i class="mdi mdi-account-multiple"></i>
                                    {{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}
                                </h4>
                            </div>
                            <div class="col-auto">
                                @can('user_create')
                                    <button type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#newUserModal"
                                            class="btn btn-primary btn-add-user">
                                        <i class="mdi mdi-plus me-1"></i> {{ __('Add User') }}
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table id="datatable" class="table table-hover align-middle">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px;"></th>
                                        <th>{{ trans('cruds.user.fields.fullname') }}</th>
                                        <th>{{ trans('cruds.user.fields.email') }}</th>
                                        <th>{{ trans('cruds.user.fields.repcode') }}</th>
                                        <th>{{ trans('cruds.user.fields.roles') }}</th>
                                        <th class="text-end">{{ trans('global.actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $key => $user)
                                        <tr data-entry-id="{{ $user->id }}">
                                            <td></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm rounded-circle bg-soft-primary d-flex align-items-center justify-content-center me-2">
                                                        <i class="mdi mdi-account text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="user-name">{{ $user->PreferredName ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                    <span class="user-email">
                                                        <i class="mdi mdi-email-outline me-1"></i>
                                                        {{ $user->email ?? '' }}
                                                    </span>
                                            </td>
                                            <td>
                                                @if($user->RepCode)
                                                    <span class="rep-code">{{ $user->RepCode }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @forelse($user->roles as $role)
                                                        <span class="role-badge {{ strtolower($role->title) }}">
                                                                {{ $role->title }}
                                                            </span>
                                                    @empty
                                                        <span class="text-muted small">No roles</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-end">
                                                    @can('user_show')
                                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                                           class="action-btn"
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="{{ trans('global.view') }} {{ trans('cruds.user.title_singular') }}">
                                                            <i class="mdi mdi-eye text-info"></i>
                                                        </a>
                                                    @endcan

                                                    @can('user_edit')
                                                        <button type="button"
                                                                class="action-btn edit-user-btn"
                                                                data-user-id="{{ $user->id }}"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                title="{{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }}">
                                                            <i class="mdi mdi-pencil text-primary"></i>
                                                        </button>
                                                    @endcan

                                                        @can('user_delete')
                                                            <button type="button"
                                                                    class="action-btn delete-btn delete-user-btn"
                                                                    data-user-id="{{ $user->id }}"
                                                                    data-user-name="{{ $user->PreferredName }}"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="{{ trans('global.delete') }} {{ trans('cruds.user.title_singular') }}">
                                                                <i class="mdi mdi-delete text-danger"></i>
                                                            </button>
                                                        @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="mdi mdi-account-multiple-outline"></i>
                                </div>
                                <p class="empty-state-text">{{ trans('global.no_users_yet') }}</p>
                                @can('user_create')
                                    <button type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#newUserModal"
                                            class="btn btn-primary btn-add-user">
                                        <i class="mdi mdi-plus me-1"></i> {{ __('Add First User') }}
                                    </button>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


    <!-- Include Create User Modal -->
    @include('admin.users._newUser')

    <!-- Include Edit User Modal -->
    @include('admin.users._editUser')
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            @if($users->count() > 0)
            $('#datatable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[1, 'asc']], // Sort by name
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "{{ trans('global.search') }}...",
                    lengthMenu: "{{ trans('global.show') }} _MENU_ {{ trans('global.entries') }}",
                    info: "{{ trans('global.showing') }} _START_ {{ trans('global.to') }} _END_ {{ trans('global.of') }} _TOTAL_ {{ trans('global.entries') }}",
                    infoEmpty: "{{ trans('global.showing') }} 0 {{ trans('global.to') }} 0 {{ trans('global.of') }} 0 {{ trans('global.entries') }}",
                    infoFiltered: "({{ trans('global.filtered_from') }} _MAX_ {{ trans('global.total_entries') }})",
                    paginate: {
                        first: "{{ trans('global.first') }}",
                        last: "{{ trans('global.last') }}",
                        next: "{{ trans('global.next') }}",
                        previous: "{{ trans('global.previous') }}"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [0, 5] } // Disable sorting on first and last columns
                ]
            });
            @endif

            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Re-initialize tooltips after DataTable draws
            $('#datatable').on('draw.dt', function() {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            });

            // Add loading state to action buttons
            $('.action-btn').on('click', function() {
                const $btn = $(this);
                if ($btn.attr('href') && $btn.attr('href') !== 'javascript:void(0);') {
                    $btn.prop('disabled', true);
                    $btn.css('opacity', '0.6');
                }
            });

            // ===== MODAL SCRIPTS =====

            // Initialize Select2 for roles
            $('#roles').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#newUserModal'),
                placeholder: '{{ __("global.select_roles") }}',
                allowClear: true
            });

            // Initialize Select2 for customers
            $('#customer_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#newUserModal'),
                placeholder: '{{ __("global.select_customer") }}',
                allowClear: true
            });

            // Initialize phone input mask
            $('#PhoneNumber').inputmask('999 999 9999');

            // Password visibility toggle
            $('#togglePassword').on('click', function() {
                const passwordField = $('#password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
                }
            });

            // Show/hide Rep Code field based on IsSalesperson checkbox
            $('#IsSalesperson').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#repCodeGroup').slideDown(300);
                    $('#RepCode').prop('required', true);
                } else {
                    $('#repCodeGroup').slideUp(300);
                    $('#RepCode').prop('required', false).val('');
                }
            });

            // Show/hide Customer field based on IsCustomer checkbox
            $('#IsCustomer').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#customerGroup').slideDown(300);
                    $('#customer_id').prop('required', true);
                } else {
                    $('#customerGroup').slideUp(300);
                    $('#customer_id').prop('required', false).val('').trigger('change');
                }
            });

            // Initialize states on modal show (for validation errors)
            $('#newUserModal').on('shown.bs.modal', function() {
                // Check if IsSalesperson is checked (from old input or error state)
                if ($('#IsSalesperson').is(':checked')) {
                    $('#repCodeGroup').show();
                    $('#RepCode').prop('required', true);
                }

                // Check if IsCustomer is checked (from old input or error state)
                if ($('#IsCustomer').is(':checked')) {
                    $('#customerGroup').show();
                    $('#customer_id').prop('required', true);
                }
            });

            // Reset form when modal is closed
            $('#newUserModal').on('hidden.bs.modal', function() {
                $('#createUserForm')[0].reset();
                $('#roles').val(null).trigger('change');
                $('#customer_id').val(null).trigger('change');
                $('#repCodeGroup').hide();
                $('#customerGroup').hide();
                $('#RepCode').prop('required', false);
                $('#customer_id').prop('required', false);

                // Reset password visibility
                $('#password').attr('type', 'password');
                $('#togglePassword i').removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');

                // Clear validation states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Form submission loading state
            $('#createUserForm').on('submit', function() {
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("global.creating") }}...');
            });

            // ===== EDIT MODAL SCRIPTS =====

            // Note: Select2 and InputMask for edit modal are initialized after AJAX loads data

            // Password visibility toggle for edit modal (delegated event)
            $(document).on('click', '#editTogglePassword', function() {
                const passwordField = $('#edit_password');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
                }
            });

            // Show/hide Rep Code field in edit modal (delegated event)
            $(document).on('change', '#edit_IsSalesperson', function() {
                if ($(this).is(':checked')) {
                    $('#editRepCodeGroup').slideDown(300);
                    $('#edit_RepCode').prop('required', true);
                } else {
                    $('#editRepCodeGroup').slideUp(300);
                    $('#edit_RepCode').prop('required', false).val('');
                }
            });

            // Show/hide Customer field in edit modal (delegated event)
            $(document).on('change', '#edit_IsCustomer', function() {
                if ($(this).is(':checked')) {
                    $('#editCustomerGroup').slideDown(300);
                    $('#edit_customer_id').prop('required', true);
                } else {
                    $('#editCustomerGroup').slideUp(300);
                    $('#edit_customer_id').prop('required', false).val('').trigger('change');
                }
            });

            // Handle edit button click
            $(document).on('click', '.edit-user-btn', function() {
                const userId = $(this).data('user-id');

                // Show loading state
                $('#editUserModal .modal-body').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">{{ __('global.loading_user_data') }}</p>
                    </div>
                `);

                // Show modal
                const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editModal.show();

                // Fetch user data
                $.ajax({
                    url: '{{ route("admin.users.index") }}/' + userId + '/edit',
                    type: 'GET',
                    success: function(response) {
                        // Restore the form HTML from global template
                        $('#editUserModalBody').html(window.editUserModalBodyTemplate);

                        // Update form action
                        $('#editUserForm').attr('action', '{{ route("admin.users.index") }}/' + userId);

                        // Populate form fields
                        $('#edit_PreferredName').val(response.PreferredName);
                        $('#edit_email').val(response.email);
                        $('#edit_PhoneNumber').val(response.PhoneNumber);

                        // Set roles (Select2 multiple)
                        const roleIds = response.roles.map(role => role.id);
                        $('#edit_roles').val(roleIds).trigger('change');

                        // Set IsSalesperson
                        if (response.IsSalesperson == 1) {
                            $('#edit_IsSalesperson').prop('checked', true);
                            $('#editRepCodeGroup').show();
                            $('#edit_RepCode').val(response.RepCode).prop('required', true);
                        } else {
                            $('#edit_IsSalesperson').prop('checked', false);
                            $('#editRepCodeGroup').hide();
                            $('#edit_RepCode').val('').prop('required', false);
                        }

                        // Set IsCustomer
                        if (response.IsCustomer == 1) {
                            $('#edit_IsCustomer').prop('checked', true);
                            $('#editCustomerGroup').show();
                            $('#edit_customer_id').val(response.customer_id).trigger('change').prop('required', true);
                        } else {
                            $('#edit_IsCustomer').prop('checked', false);
                            $('#editCustomerGroup').hide();
                            $('#edit_customer_id').val('').trigger('change').prop('required', false);
                        }

                        // Re-initialize Select2 after population
                        $('#edit_roles').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#editUserModal'),
                            placeholder: '{{ __("global.select_roles") }}',
                            allowClear: false
                        });

                        $('#edit_customer_id').select2({
                            theme: 'bootstrap-5',
                            dropdownParent: $('#editUserModal'),
                            placeholder: '{{ __("global.select_customer") }}',
                            allowClear: true
                        });

                        // Re-apply input mask
                        $('#edit_PhoneNumber').inputmask('999 999 9999');
                    },
                    error: function(xhr) {
                        $('#editUserModal .modal-body').html(`
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert-circle-outline me-2"></i>
                                {{ __('global.error_loading_user') }}
                        </div>
`);
                    }
                });
            });

            // Reset edit form when modal is closed
            $('#editUserModal').on('hidden.bs.modal', function() {
                $('#editUserForm')[0].reset();
                $('#edit_roles').val(null).trigger('change');
                $('#edit_customer_id').val(null).trigger('change');
                $('#editRepCodeGroup').hide();
                $('#editCustomerGroup').hide();
                $('#edit_RepCode').prop('required', false);
                $('#edit_customer_id').prop('required', false);

                // Reset password visibility
                $('#edit_password').attr('type', 'password');
                $('#editTogglePassword i').removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');

                // Clear validation states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Form submission loading state for edit
            $('#editUserForm').on('submit', function() {
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("global.saving") }}...');
            });

            $(document).on('click', '.delete-user-btn', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name');

                Swal.fire({
                    title: '{{ __("global.delete_confirmation") }}',
                    html: '{{ __("global.delete_user_warning") }}<br><strong>' + userName + '</strong>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="mdi mdi-delete me-1"></i> {{ __("global.yes_delete") }}',
                    cancelButtonText: '<i class="mdi mdi-close me-1"></i> {{ __("global.cancel") }}',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-danger px-4',
                        cancelButton: 'btn btn-secondary px-4'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: '{{ __("global.deleting") }}...',
                            html: '{{ __("global.please_wait") }}',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit delete request
                        $.ajax({
                            url: '{{ route("admin.users.index") }}/' + userId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '{{ __("global.deleted") }}!',
                                    text: '{{ __("global.user_deleted_successfully") }}',
                                    icon: 'success',
                                    confirmButtonColor: '#667eea',
                                    confirmButtonText: '{{ __("global.ok") }}',
                                    customClass: {
                                        confirmButton: 'btn btn-primary px-4'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    // Reload page to refresh user list
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: '{{ __("global.error") }}!',
                                    text: xhr.responseJSON?.message || '{{ __("global.error_deleting_user") }}',
                                    icon: 'error',
                                    confirmButtonColor: '#667eea',
                                    confirmButtonText: '{{ __("global.ok") }}',
                                    customClass: {
                                        confirmButton: 'btn btn-primary px-4'
                                    },
                                    buttonsStyling: false
                                });
                            }
                        });
                    }
                });
            });
        });


    </script>
@endpush
