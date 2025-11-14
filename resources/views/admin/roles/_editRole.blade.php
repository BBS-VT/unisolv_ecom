<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">
                    <i class="mdi mdi-shield-edit me-2"></i>
                    {{ __('Edit Role') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editRoleForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Role Title -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="edit_title" class="form-label fw-semibold">
                                {{ trans('cruds.role.fields.title') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-shield-account"></i>
                                </span>
                                <input type="text"
                                       class="form-control"
                                       id="edit_title"
                                       name="title"
                                       placeholder="{{ __('global.enter_role_name') }}"
                                       required>
                            </div>
                            <small class="form-text text-muted">{{ trans('cruds.role.fields.title_helper') }}</small>
                        </div>
                        <div class="col-md-6">
                            <!-- Quick Actions -->
                            <label class="form-label fw-semibold">{{ __('global.quick_actions') }}</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-primary select-all-permissions-edit">
                                    <i class="mdi mdi-check-all me-1"></i> {{ trans('global.select_all') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-permissions-edit">
                                    <i class="mdi mdi-close-box-multiple me-1"></i> {{ trans('global.deselect_all') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info collapse-all-groups-edit">
                                    <i class="mdi mdi-unfold-less-horizontal me-1"></i> {{ __('global.collapse_all') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info expand-all-groups-edit">
                                    <i class="mdi mdi-unfold-more-horizontal me-1"></i> {{ __('global.expand_all') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search Permissions -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="mdi mdi-magnify"></i>
                            </span>
                            <input type="text"
                                   class="form-control"
                                   id="searchPermissionsEdit"
                                   placeholder="{{ __('global.search_permissions') }}">
                        </div>
                    </div>

                    <!-- Grouped Permissions -->
                    <div class="permissions-container-edit">
                        @php
                            // Group permissions by module
                            $groupedPermissions = [];
                            foreach($permissions as $id => $permission) {
                                // Extract module name (e.g., "user_create" -> "user")
                                $parts = explode('_', $permission);
                                $module = $parts[0] ?? 'other';

                                if (!isset($groupedPermissions[$module])) {
                                    $groupedPermissions[$module] = [];
                                }
                                $groupedPermissions[$module][$id] = $permission;
                            }
                            ksort($groupedPermissions);
                        @endphp

                        <div class="accordion" id="permissionsAccordionEdit">
                            @foreach($groupedPermissions as $module => $modulePermissions)
                                <div class="accordion-item permission-group-edit" data-module="{{ $module }}">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapseEdit{{ ucfirst($module) }}"
                                                aria-expanded="true">
                                            <div class="d-flex align-items-center w-100">
                                                <div class="form-check me-3" onclick="event.stopPropagation();">
                                                    <input class="form-check-input module-checkbox-edit"
                                                           type="checkbox"
                                                           id="module_edit_{{ $module }}"
                                                           data-module="{{ $module }}">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <i class="mdi mdi-{{ $module === 'user' ? 'account-group' : ($module === 'customer' ? 'account-multiple' : ($module === 'order' ? 'cart' : ($module === 'product' ? 'package-variant' : 'cog'))) }} me-2"></i>
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $module)) }}</strong>
                                                    <span class="badge bg-light text-dark ms-2">{{ count($modulePermissions) }}</span>
                                                </div>
                                                <small class="text-muted me-3 selected-count-edit" data-module="{{ $module }}">
                                                    <span class="selected">0</span> / {{ count($modulePermissions) }} {{ __('global.selected') }}
                                                </small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseEdit{{ ucfirst($module) }}"
                                         class="accordion-collapse collapse show"
                                         data-bs-parent="#permissionsAccordionEdit">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @foreach($modulePermissions as $id => $permission)
                                                    <div class="col-md-6 col-lg-4 permission-item-edit" data-permission-name="{{ strtolower($permission) }}">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input permission-checkbox-edit"
                                                                   type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $id }}"
                                                                   id="permission_edit_{{ $id }}"
                                                                   data-module="{{ $module }}">
                                                            <label class="form-check-label" for="permission_edit_{{ $id }}">
                                                                {{ $permission }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- No Results Message -->
                        <div id="noResultsMessageEdit" class="alert alert-info text-center mt-3" style="display: none;">
                            <i class="mdi mdi-information-outline me-2"></i>
                            {{ __('global.no_permissions_found') }}
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info d-flex align-items-start mt-3" role="alert">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-information-outline fs-4 me-2"></i>
                        </div>
                        <div>
                            <strong>{{ __('global.note') }}:</strong> {{ __('global.role_update_note') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <span class="text-muted">
                            <strong id="totalSelectedCountEdit">0</strong> {{ __('global.permissions_selected') }}
                        </span>
                    </div>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>
                        {{ __('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i>
                        {{ __('global.update_role') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Permission Groups Styling for Edit Modal */
    .permissions-container-edit {
        max-height: 500px;
        overflow-y: auto;
    }

    .permission-item-edit {
        transition: background-color 0.2s ease;
        padding: 0.25rem;
        border-radius: 4px;
    }

    .permission-item-edit:hover {
        background-color: #f8f9fa;
    }

    .selected-count-edit {
        font-size: 0.875rem;
    }

    /* Search highlight */
    .permission-item-edit.highlight {
        background-color: #fff3cd;
        animation: highlight-fade 2s ease;
    }

    /* Hidden groups during search */
    .permission-group-edit.hidden {
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModalElement = document.getElementById('editRoleModal');

        editModalElement.addEventListener('shown.bs.modal', function() {
            initializeEditPermissionHandlers();
        });

        // Handle edit button clicks - delegated event
        $(document).on('click', '.edit-role-btn', function() {
            const roleId = $(this).data('role-id');
            const roleName = $(this).data('role-name');
            const rolePermissions = $(this).data('role-permissions') || [];

            loadRoleData(roleId, roleName, rolePermissions);
        });
    });

    function loadRoleData(roleId, roleName, rolePermissions) {
        // Set form action
        const formAction = `/admin/roles/${roleId}`;
        $('#editRoleForm').attr('action', formAction);

        // Set role title
        $('#edit_title').val(roleName);

        // Reset all checkboxes first
        $('.permission-checkbox-edit').prop('checked', false);

        // Check the permissions this role has
        if (Array.isArray(rolePermissions)) {
            rolePermissions.forEach(permissionId => {
                $(`#permission_edit_${permissionId}`).prop('checked', true);
            });
        }

        // Update counts
        updateEditModuleCounts();
        updateEditTotalCount();

        // Show the modal
        const editModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
        editModal.show();
    }

    function initializeEditPermissionHandlers() {
        // Update counts on checkbox change
        $(document).on('change', '.permission-checkbox-edit', function() {
            updateEditModuleCounts();
            updateEditTotalCount();
        });

        // Module checkbox - select/deselect all in module
        $(document).on('change', '.module-checkbox-edit', function(e) {
            e.stopPropagation();
            const module = $(this).data('module');
            const isChecked = $(this).prop('checked');

            $(`.permission-checkbox-edit[data-module="${module}"]`).prop('checked', isChecked);
            updateEditModuleCounts();
            updateEditTotalCount();
        });

        // Select all permissions
        $('.select-all-permissions-edit').on('click', function() {
            $('.permission-checkbox-edit').prop('checked', true);
            $('.module-checkbox-edit').prop('checked', true);
            updateEditModuleCounts();
            updateEditTotalCount();
        });

        // Deselect all permissions
        $('.deselect-all-permissions-edit').on('click', function() {
            $('.permission-checkbox-edit').prop('checked', false);
            $('.module-checkbox-edit').prop('checked', false);
            updateEditModuleCounts();
            updateEditTotalCount();
        });

        // Collapse all groups
        $('.collapse-all-groups-edit').on('click', function() {
            $('#permissionsAccordionEdit .accordion-collapse').collapse('hide');
        });

        // Expand all groups
        $('.expand-all-groups-edit').on('click', function() {
            $('#permissionsAccordionEdit .accordion-collapse').collapse('show');
        });

        // Search permissions
        let searchTimeout;
        $('#searchPermissionsEdit').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = $(this).val().toLowerCase();
                searchEditPermissions(searchTerm);
            }, 300);
        });

        // Initialize counts
        updateEditModuleCounts();
        updateEditTotalCount();
    }

    function updateEditModuleCounts() {
        $('.module-checkbox-edit').each(function() {
            const module = $(this).data('module');
            const total = $(`.permission-checkbox-edit[data-module="${module}"]`).length;
            const selected = $(`.permission-checkbox-edit[data-module="${module}"]:checked`).length;

            // Update the selected count display
            $(`.selected-count-edit[data-module="${module}"] .selected`).text(selected);

            // Update module checkbox state
            if (selected === 0) {
                $(this).prop('checked', false).prop('indeterminate', false);
            } else if (selected === total) {
                $(this).prop('checked', true).prop('indeterminate', false);
            } else {
                $(this).prop('checked', false).prop('indeterminate', true);
            }
        });
    }

    function updateEditTotalCount() {
        const total = $('.permission-checkbox-edit:checked').length;
        $('#totalSelectedCountEdit').text(total);
    }

    function searchEditPermissions(searchTerm) {
        if (!searchTerm) {
            // Show all
            $('.permission-group-edit').removeClass('hidden').show();
            $('.permission-item-edit').show();
            $('#permissionsAccordionEdit .accordion-collapse').collapse('show');
            $('#noResultsMessageEdit').hide();
            return;
        }

        let hasResults = false;

        $('.permission-group-edit').each(function() {
            const $group = $(this);
            let groupHasMatch = false;

            $group.find('.permission-item-edit').each(function() {
                const permissionName = $(this).data('permission-name');

                if (permissionName.includes(searchTerm)) {
                    $(this).show().addClass('highlight');
                    setTimeout(() => $(this).removeClass('highlight'), 2000);
                    groupHasMatch = true;
                    hasResults = true;
                } else {
                    $(this).hide();
                }
            });

            if (groupHasMatch) {
                $group.removeClass('hidden').show();
                $group.find('.accordion-collapse').collapse('show');
            } else {
                $group.addClass('hidden').hide();
            }
        });

        if (!hasResults) {
            $('#noResultsMessageEdit').show();
        } else {
            $('#noResultsMessageEdit').hide();
        }
    }
</script>
