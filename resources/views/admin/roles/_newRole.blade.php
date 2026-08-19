<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoleModalLabel">
                    <i class="mdi mdi-shield-plus me-2"></i>
                    {{ __('Add New Role') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.roles.store') }}" method="POST" id="createRoleForm">
                @csrf
                <div class="modal-body">
                    <!-- Role Title -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="title" class="form-label fw-semibold">
                                {{ trans('cruds.role.fields.title') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="mdi mdi-shield-account"></i>
                                </span>
                                <input type="text"
                                       class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title') }}"
                                       placeholder="{{ __('global.enter_role_name') }}"
                                       required>
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">{{ trans('cruds.role.fields.title_helper') }}</small>
                        </div>
                        <div class="col-md-6">
                            <!-- Quick Actions -->
                            <label class="form-label fw-semibold">{{ __('global.quick_actions') }}</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-primary select-all-permissions">
                                    <i class="mdi mdi-check-all me-1"></i> {{ trans('global.select_all') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-permissions">
                                    <i class="mdi mdi-close-box-multiple me-1"></i> {{ trans('global.deselect_all') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info collapse-all-groups">
                                    <i class="mdi mdi-unfold-less-horizontal me-1"></i> {{ __('global.collapse_all') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info expand-all-groups">
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
                                   id="searchPermissions"
                                   placeholder="{{ __('global.search_permissions') }}">
                        </div>
                    </div>

                    <!-- Grouped Permissions -->
                    <div class="permissions-container">
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

                        <div class="accordion" id="permissionsAccordion">
                            @foreach($groupedPermissions as $module => $modulePermissions)
                                <div class="accordion-item permission-group" data-module="{{ $module }}">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ ucfirst($module) }}"
                                                aria-expanded="true">
                                            <div class="d-flex align-items-center w-100">
                                                <div class="form-check me-3" onclick="event.stopPropagation();">
                                                    <input class="form-check-input module-checkbox"
                                                           type="checkbox"
                                                           id="module_{{ $module }}"
                                                           data-module="{{ $module }}">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <i class="mdi mdi-{{ $module === 'user' ? 'account-group' : ($module === 'customer' ? 'account-multiple' : ($module === 'order' ? 'cart' : ($module === 'product' ? 'package-variant' : 'cog'))) }} me-2"></i>
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $module)) }}</strong>
                                                    <span class="badge bg-light text-dark ms-2">{{ count($modulePermissions) }}</span>
                                                </div>
                                                <small class="text-muted me-3 selected-count" data-module="{{ $module }}">
                                                    <span class="selected">0</span> / {{ count($modulePermissions) }} {{ __('global.selected') }}
                                                </small>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ ucfirst($module) }}"
                                         class="accordion-collapse collapse show"
                                         data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @foreach($modulePermissions as $id => $permission)
                                                    <div class="col-md-6 col-lg-4 permission-item" data-permission-name="{{ strtolower($permission) }}">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input permission-checkbox"
                                                                   type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $id }}"
                                                                   id="permission_{{ $id }}"
                                                                   data-module="{{ $module }}"
                                                                {{ in_array($id, old('permissions', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="permission_{{ $id }}">
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
                        <div id="noResultsMessage" class="alert alert-info text-center mt-3" style="display: none;">
                            <i class="mdi mdi-information-outline me-2"></i>
                            {{ __('global.no_permissions_found') }}
                        </div>
                    </div>

                    @error('permissions')
                    <div class="alert alert-danger mt-3">
                        <i class="mdi mdi-alert-circle-outline me-2"></i>
                        {{ $message }}
                    </div>
                    @enderror

                    <!-- Info Alert -->
                    <div class="alert alert-info d-flex align-items-start mt-3" role="alert">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-information-outline fs-4 me-2"></i>
                        </div>
                        <div>
                            <strong>{{ __('global.note') }}:</strong> {{ __('global.role_creation_note') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <span class="text-muted">
                            <strong id="totalSelectedCount">0</strong> {{ __('global.permissions_selected') }}
                        </span>
                    </div>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>
                        {{ __('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i>
                        {{ __('global.create_role') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Permission Groups Styling */
    .permissions-container {
        max-height: 500px;
        overflow-y: auto;
    }

    .accordion-item {
        border: 1px solid #e9ecef;
        border-radius: 8px !important;
        margin-bottom: 0.5rem;
    }

    .accordion-button {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 500;
        padding: 1rem;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f1ff;
        color: #1C75BC;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: #1C75BC;
    }

    .accordion-button::after {
        margin-left: auto;
    }

    .accordion-body {
        padding: 1rem;
        background-color: #fff;
    }

    .module-checkbox {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .permission-checkbox {
        cursor: pointer;
    }

    .permission-item {
        transition: background-color 0.2s ease;
        padding: 0.25rem;
        border-radius: 4px;
    }

    .permission-item:hover {
        background-color: #f8f9fa;
    }

    .selected-count {
        font-size: 0.875rem;
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
    }

    /* Search highlight */
    .permission-item.highlight {
        background-color: rgba(var(--bs-warning-rgb), 0.15);
        animation: highlight-fade 2s ease;
    }

    @keyframes highlight-fade {
        from { background-color: rgba(var(--bs-warning-rgb), 0.15); }
        to { background-color: transparent; }
    }

    /* Hidden groups during search */
    .permission-group.hidden {
        display: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('createRoleModal');

        modalElement.addEventListener('shown.bs.modal', function() {
            initializePermissionHandlers();
        });
    });

    function initializePermissionHandlers() {
        // Update counts on checkbox change
        $(document).on('change', '.permission-checkbox', function() {
            updateModuleCounts();
            updateTotalCount();
        });

        // Module checkbox - select/deselect all in module
        $(document).on('change', '.module-checkbox', function(e) {
            e.stopPropagation();
            const module = $(this).data('module');
            const isChecked = $(this).prop('checked');

            $(`.permission-checkbox[data-module="${module}"]`).prop('checked', isChecked);
            updateModuleCounts();
            updateTotalCount();
        });

        // Select all permissions
        $('.select-all-permissions').on('click', function() {
            $('.permission-checkbox').prop('checked', true);
            $('.module-checkbox').prop('checked', true);
            updateModuleCounts();
            updateTotalCount();
        });

        // Deselect all permissions
        $('.deselect-all-permissions').on('click', function() {
            $('.permission-checkbox').prop('checked', false);
            $('.module-checkbox').prop('checked', false);
            updateModuleCounts();
            updateTotalCount();
        });

        // Collapse all groups
        $('.collapse-all-groups').on('click', function() {
            $('.accordion-collapse').collapse('hide');
        });

        // Expand all groups
        $('.expand-all-groups').on('click', function() {
            $('.accordion-collapse').collapse('show');
        });

        // Search permissions
        let searchTimeout;
        $('#searchPermissions').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = $(this).val().toLowerCase();
                searchPermissions(searchTerm);
            }, 300);
        });

        // Initialize counts
        updateModuleCounts();
        updateTotalCount();
    }

    function updateModuleCounts() {
        $('.module-checkbox').each(function() {
            const module = $(this).data('module');
            const total = $(`.permission-checkbox[data-module="${module}"]`).length;
            const selected = $(`.permission-checkbox[data-module="${module}"]:checked`).length;

            // Update the selected count display
            $(`.selected-count[data-module="${module}"] .selected`).text(selected);

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

    function updateTotalCount() {
        const total = $('.permission-checkbox:checked').length;
        $('#totalSelectedCount').text(total);
    }

    function searchPermissions(searchTerm) {
        if (!searchTerm) {
            // Show all
            $('.permission-group').removeClass('hidden').show();
            $('.permission-item').show();
            $('.accordion-collapse').collapse('show');
            $('#noResultsMessage').hide();
            return;
        }

        let hasResults = false;

        $('.permission-group').each(function() {
            const $group = $(this);
            let groupHasMatch = false;

            $group.find('.permission-item').each(function() {
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
            $('#noResultsMessage').show();
        } else {
            $('#noResultsMessage').hide();
        }
    }
</script>
