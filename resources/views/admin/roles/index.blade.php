@extends('layouts.master')

@section('title', trans('cruds.role.title'))

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        /* Modern Card Styling */
        .roles-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .roles-card .card-header {
            background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 2px solid #e9ecef;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .roles-card .card-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
        }

        .roles-card .card-title i {
            color: #1C75BC;
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

        /* Permission Badge Styling */
        .permission-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 20px;
            margin: 0.125rem;
            background: linear-gradient(135deg, rgba(28, 117, 188, 0.15) 0%, rgba(42, 48, 66, 0.15) 100%);
            color: #1C75BC;
            border: 1px solid rgba(28, 117, 188, 0.3);
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
            border-color: #1C75BC;
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 1.125rem;
        }

        .action-btn.delete-btn:hover {
            background-color: #fff5f5;
            border-color: var(--bs-danger);
        }

        /* Add Role Button */
        .btn-add-role {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            border: none;
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(28, 117, 188, 0.3);
            transition: all 0.3s ease;
        }

        .btn-add-role:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(28, 117, 188, 0.4);
        }

        /* Role Name Styling */
        .role-name {
            font-weight: 600;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .role-name i {
            color: #1C75BC;
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

        /* Modal Styling */
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 150px;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #1C75BC;
            border: none;
            color: white;
        }

        .permissions-helper {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .permissions-helper .btn {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .roles-card .card-header {
                padding: 1rem;
            }

            .roles-card .card-title {
                font-size: 1.125rem;
            }

            #datatable tbody td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
@endpush

@section('content')

    @include('flash-message')

    <!-- Roles Table Card -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card roles-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">
                                <i class="mdi mdi-shield-check"></i>
                                {{ trans('cruds.role.title_singular') }} {{ trans('global.list') }}
                            </h4>
                        </div>
                        <div class="col-auto">
                            @can('role_create')
                                <button type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createRoleModal"
                                        class="btn btn-primary btn-add-role">
                                    <i class="mdi mdi-plus me-1"></i> {{ __('Add Role') }}
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table id="datatable" class="table table-hover align-middle">
                                <thead>
                                <tr>
                                    <th style="width: 10px;"></th>
                                    <th>{{ trans('cruds.role.fields.id') }}</th>
                                    <th>{{ trans('cruds.role.fields.title') }}</th>
                                    <th>{{ trans('cruds.role.fields.permissions') }}</th>
                                    <th class="text-end">{{ trans('global.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($roles as $key => $role)
                                    <tr data-entry-id="{{ $role->id }}">
                                        <td></td>
                                        <td>
                                            <span class="text-muted">#{{ $role->id }}</span>
                                        </td>
                                        <td>
                                            <div class="role-name">
                                                <i class="mdi mdi-shield"></i>
                                                <strong>{{ $role->title }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-primary">
                                                        {{ $role->permissions->count() }} {{ __('global.permissions') }}
                                                    </span>
                                                @if($role->permissions->count() > 0)
                                                    <button type="button"
                                                            class="btn btn-sm btn-link p-0 text-decoration-none preview-permissions"
                                                            data-role-id="{{ $role->id }}"
                                                            data-role-name="{{ $role->title }}"
                                                            data-permissions="{{ $role->permissions->pluck('title')->implode(', ') }}">
                                                        <i class="mdi mdi-eye-outline"></i> {{ __('global.preview') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-end">

                                                @can('role_edit')
                                                    <button type="button"
                                                            class="btn btn-sm btn-primary edit-role-btn"
                                                            data-role-id="{{ $role->id }}"
                                                            data-role-name="{{ $role->title }}"
                                                            data-role-permissions='@json($role->permissions->pluck("id"))'>
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                @endcan

                                                @can('role_delete')
                                                    <button type="button"
                                                            class="action-btn delete-btn delete-role-btn"
                                                            data-role-id="{{ $role->id }}"
                                                            data-role-name="{{ $role->title }}"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="{{ trans('global.delete') }} {{ trans('cruds.role.title_singular') }}">
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
                                <i class="mdi mdi-shield-outline"></i>
                            </div>
                            <p class="empty-state-text">{{ trans('global.no_roles_yet') }}</p>
                            @can('role_create')
                                <button type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createRoleModal"
                                        class="btn btn-primary btn-add-role">
                                    <i class="mdi mdi-plus me-1"></i> {{ __('Add First Role') }}
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Include Create Role Modal -->
    @include('admin.roles._newRole')

    <!-- Include Edit Role Modal -->
    @include('admin.roles._editRole')

    <!-- Permissions Preview Modal -->
    <div class="modal fade" id="previewPermissionsModal" tabindex="-1" aria-labelledby="previewPermissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewPermissionsModalLabel">
                        <i class="mdi mdi-shield-check me-2"></i>
                        <span id="roleNameDisplay"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center mb-3">
                        <i class="mdi mdi-information-outline fs-4 me-2"></i>
                        <div>
                            <strong id="permissionCount"></strong> {{ __('global.permissions_assigned') }}
                        </div>
                    </div>
                    <div id="permissionsGrid" class="row g-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>
                        {{ __('global.close') }}
                    </button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            @if($roles->count() > 0)
            $('#datatable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[1, 'asc']], // Sort by ID
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
                    { orderable: false, targets: [0, 4] } // Disable sorting on first and last columns
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

            // ===== CREATE MODAL SCRIPTS =====

            // Initialize Select2 for permissions
            $('#permissions').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#createRoleModal'),
                placeholder: '{{ __("global.select_permissions") }}',
                allowClear: true
            });

            // Select all permissions
            $('.select-all').on('click', function() {
                $('#permissions option').prop('selected', true);
                $('#permissions').trigger('change');
            });

            // Deselect all permissions
            $('.deselect-all').on('click', function() {
                $('#permissions option').prop('selected', false);
                $('#permissions').trigger('change');
            });

            // Reset form when modal is closed
            $('#createRoleModal').on('hidden.bs.modal', function() {
                $('#createRoleForm')[0].reset();
                $('#permissions').val(null).trigger('change');

                // Clear validation states
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            });

            // Form submission loading state
            $('#createRoleForm').on('submit', function() {
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<span class="spinner-border spinner-border-sm me-1"></span> {{ __("global.creating") }}...');
            });

            // ===== PREVIEW PERMISSIONS MODAL =====

            $(document).on('click', '.preview-permissions', function() {
                const roleName = $(this).data('role-name');
                const permissionsString = $(this).data('permissions');
                const permissions = permissionsString.split(', ');

                // Set modal title
                $('#roleNameDisplay').text(roleName + ' - ' + '{{ __("global.permissions") }}');
                $('#permissionCount').text(permissions.length);

                // Group permissions by module
                const grouped = {};
                permissions.forEach(permission => {
                    const parts = permission.split('_');
                    const module = parts[0] || 'other';

                    if (!grouped[module]) {
                        grouped[module] = [];
                    }
                    grouped[module].push(permission);
                });

                // Build permissions grid
                let html = '';
                Object.keys(grouped).sort().forEach(module => {
                    html += `
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-${getModuleIcon(module)} me-2"></i>
                                        ${capitalizeFirst(module.replace('_', ' '))}
                                        <span class="badge bg-primary ms-2">${grouped[module].length}</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    <div class="row g-2">
                                        ${grouped[module].map(perm => `
                                            <div class="col-md-6 col-lg-4">
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-check-circle text-success me-2"></i>
                                                    <small>${perm}</small>
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $('#permissionsGrid').html(html);

                // Show modal
                const previewModal = new bootstrap.Modal(document.getElementById('previewPermissionsModal'));
                previewModal.show();
            });

            function getModuleIcon(module) {
                const icons = {
                    'user': 'account-group',
                    'customer': 'account-multiple',
                    'order': 'cart',
                    'product': 'package-variant',
                    'role': 'shield-account',
                    'permission': 'key',
                    'setting': 'cog'
                };
                return icons[module] || 'cog';
            }

            function capitalizeFirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            // ===== DELETE ROLE WITH SWEETALERT2 =====

            $(document).on('click', '.delete-role-btn', function() {
                const roleId = $(this).data('role-id');
                const roleName = $(this).data('role-name');

                Swal.fire({
                    title: '{{ __("global.delete_confirmation") }}',
                    html: '{{ __("global.delete_role_warning") }}<br><strong>' + roleName + '</strong>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--bs-danger)',
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
                            url: '{{ route("admin.roles.index") }}/' + roleId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: '{{ __("global.deleted") }}!',
                                    text: '{{ __("global.role_deleted_successfully") }}',
                                    icon: 'success',
                                    confirmButtonColor: '#1C75BC',
                                    confirmButtonText: '{{ __("global.ok") }}',
                                    customClass: {
                                        confirmButton: 'btn btn-primary px-4'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    // Reload page to refresh role list
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: '{{ __("global.error") }}!',
                                    text: xhr.responseJSON?.message || '{{ __("global.error_deleting_role") }}',
                                    icon: 'error',
                                    confirmButtonColor: '#1C75BC',
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
