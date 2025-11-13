
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="mdi mdi-account-edit me-2"></i>
                    {{ __('Edit User') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-body" id="editUserModalBody">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="edit_PreferredName" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.fullname') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="edit_PreferredName"
                                       name="PreferredName"
                                       required>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="edit_email" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.email') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-email-outline"></i>
                                    </span>
                                    <input type="email"
                                           class="form-control"
                                           id="edit_email"
                                           name="email"
                                           required>
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="edit_PhoneNumber" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.phonenumber') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-phone"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control phone"
                                           id="edit_PhoneNumber"
                                           name="PhoneNumber"
                                           placeholder="012 345 6789">
                                </div>
                            </div>

                            <!-- Password (Optional for Edit) -->
                            <div class="mb-3">
                                <label for="edit_password" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.password') }}
                                    <span class="text-muted small">({{ __('global.leave_blank_to_keep') }})</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-lock-outline"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control"
                                           id="edit_password"
                                           name="password">
                                    <button class="btn btn-outline-secondary" type="button" id="editTogglePassword">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">{{ __('global.password_edit_note') }}</small>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Roles -->
                            <div class="mb-3">
                                <label for="edit_roles" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.roles') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-edit"
                                        id="edit_roles"
                                        name="roles[]"
                                        multiple
                                        required>
                                    @foreach($roles as $id => $role)
                                        <option value="{{ $id }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
                            </div>

                            <!-- Is Salesperson -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('cruds.user.fields.salesrep') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="edit_IsSalesperson"
                                           name="IsSalesperson"
                                           value="1">
                                    <label class="form-check-label" for="edit_IsSalesperson">
                                        {{ __('global.yes') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Rep Code (shown when IsSalesperson is checked) -->
                            <div class="mb-3" id="editRepCodeGroup" style="display: none;">
                                <label for="edit_RepCode" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.repcode') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="edit_RepCode"
                                       name="RepCode"
                                       placeholder="e.g., REP001">
                                <small class="form-text text-muted">Unique sales representative code</small>
                            </div>

                            <!-- Is Customer -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('cruds.user.fields.customer') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="edit_IsCustomer"
                                           name="IsCustomer"
                                           value="1">
                                    <label class="form-check-label" for="edit_IsCustomer">
                                        {{ __('global.yes') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Customer Selection (shown when IsCustomer is checked) -->
                            <div class="mb-3" id="editCustomerGroup" style="display: none;">
                                <label for="edit_customer_id" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.select_customer') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-edit-customer"
                                        id="edit_customer_id"
                                        name="customer_id">
                                    <option value="">{{ __('global.select_customer') }}</option>
                                    @foreach($customers as $id => $customer)
                                        <option value="{{ $id }}">{{ $customer }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Link this user to a customer account</small>
                            </div>
                        </div>
                    </div>

                    <!-- User Info Alert -->
                    <div class="alert alert-info d-flex align-items-start mt-3" role="alert">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-information-outline fs-4 me-2"></i>
                        </div>
                        <div>
                            <strong>{{ __('global.note') }}:</strong> {{ __('global.user_edit_note') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>
                        {{ __('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save me-1"></i>
                        {{ __('global.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Template for Edit Modal Body (used by JavaScript) -->
<template id="editUserModalBodyTemplate">
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
            <!-- Full Name -->
            <div class="mb-3">
                <label for="edit_PreferredName" class="form-label fw-semibold">
                    {{ __('cruds.user.fields.fullname') }}
                    <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="edit_PreferredName"
                       name="PreferredName"
                       required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="edit_email" class="form-label fw-semibold">
                    {{ __('cruds.user.fields.email') }}
                    <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="mdi mdi-email-outline"></i>
                    </span>
                    <input type="email"
                           class="form-control"
                           id="edit_email"
                           name="email"
                           required>
                </div>
            </div>

            <!-- Phone Number -->
            <div class="mb-3">
                <label for="edit_PhoneNumber" class="form-label fw-semibold">
                    {{ __('cruds.user.fields.phonenumber') }}
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="mdi mdi-phone"></i>
                    </span>
                    <input type="text"
                           class="form-control phone"
                           id="edit_PhoneNumber"
                           name="PhoneNumber"
                           placeholder="012 345 6789">
                </div>
            </div>

            <!-- Password (Optional for Edit) -->
            <div class="mb-3">
                <label for="edit_password" class="form-label fw-semibold">
                    {{ __('cruds.user.fields.password') }}
                    <span class="text-muted small">({{ __('global.leave_blank_to_keep') }})</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="mdi mdi-lock-outline"></i>
                    </span>
                    <input type="password"
                           class="form-control"
                           id="edit_password"
                           name="password">
                    <button class="btn btn-outline-secondary" type="button" id="editTogglePassword">
                        <i class="mdi mdi-eye-outline"></i>
                    </button>
                </div>
                <small class="form-text text-muted">{{ __('global.password_edit_note') }}</small>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <!-- Roles -->
            <div class="mb-3">
                <label for="edit_roles" class="form-label fw-semibold">
                    {{ __('cruds.user.fields.roles') }}
                    <span class="text-danger">*</span>
                </label>
                <select class="form-select select2-edit"
                        id="edit_roles"
                        name="roles[]"
                        multiple
                        required>
                    @foreach($roles as $id => $role)
                        <option value="{{ $id }}">{{ $role }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
            </div>

            <!-- Is Salesperson -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('cruds.user.fields.salesrep') }}</label>
                <div class="form-check form-switch">
                    <input class="form-check-input"
                           type="checkbox"
                           id="edit_IsSalesperson"
                           name="IsSalesperson"
                           value="1">
                    <label class="form-check-label" for="edit_IsSalesperson">
                        {{ __('global.yes') }}
                    </label>
                </div>
            </div>

            <!-- Rep Code (shown when IsSalesperson is checked) -->
            <div class="mb-3" id="editRepCodeGroup" style="display: none;">
                <label for="edit_RepCode" class="form-label fw-semibold">
                    {{ __('cruds.user.fields.repcode') }}
                    <span class="text-danger">*</span>
                </label>
                <input type="text"
                       class="form-control"
                       id="edit_RepCode"
                       name="RepCode"
                       placeholder="e.g., REP001">
                <small class="form-text text-muted">Unique sales representative code</small>
            </div>

            <!-- Is Customer -->
            <div class="mb-3">
                <label class="form-label fw-semibold">{{ __('cruds.user.fields.customer') }}</label>
                <div class="form-check form-switch">
                    <input class="form-check-input"
                           type="checkbox"
                           id="edit_IsCustomer"
                           name="IsCustomer"
                           value="1">
                    <label class="form-check-label" for="edit_IsCustomer">
                        {{ __('global.yes') }}
                    </label>
                </div>
            </div>

            <!-- Customer Selection (shown when IsCustomer is checked) -->
            <div class="mb-3" id="editCustomerGroup" style="display: none;">
                <label for="edit_customer_id" class="form-label fw-semibold">
                    {{ __('cruds.user.fields.select_customer') }}
                    <span class="text-danger">*</span>
                </label>
                <select class="form-select select2-edit-customer"
                        id="edit_customer_id"
                        name="customer_id">
                    <option value="">{{ __('global.select_customer') }}</option>
                    @foreach($customers as $id => $customer)
                        <option value="{{ $id }}">{{ $customer }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Link this user to a customer account</small>
            </div>
        </div>
    </div>

    <!-- User Info Alert -->
    <div class="alert alert-info d-flex align-items-start mt-3" role="alert">
        <div class="flex-shrink-0">
            <i class="mdi mdi-information-outline fs-4 me-2"></i>
        </div>
        <div>
            <strong>{{ __('global.note') }}:</strong> {{ __('messages.user_edit_note') }}
        </div>
    </div>
</template>

<script>
    // Store the template content globally for the edit modal
    window.editUserModalBodyTemplate = document.getElementById('editUserModalBodyTemplate').innerHTML;
    document.getElementById('editUserModalBody').innerHTML = window.editUserModalBodyTemplate;
</script>
