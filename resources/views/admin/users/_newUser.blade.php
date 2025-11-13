<!-- New User Modal -->
<div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newUserModalLabel">
                    <i class="mdi mdi-account-plus me-2"></i>
                    {{ __('Add New User') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="PreferredName" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.fullname') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('PreferredName') is-invalid @enderror"
                                       id="PreferredName"
                                       name="PreferredName"
                                       value="{{ old('PreferredName') }}"
                                       required>
                                @error('PreferredName')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.email') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-email-outline"></i>
                                    </span>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-3">
                                <label for="PhoneNumber" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.phonenumber') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-phone"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control phone @error('PhoneNumber') is-invalid @enderror"
                                           id="PhoneNumber"
                                           name="PhoneNumber"
                                           value="{{ old('PhoneNumber') }}"
                                           placeholder="012 345 6789">
                                    @error('PhoneNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.password') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="mdi mdi-lock-outline"></i>
                                    </span>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Minimum 8 characters</small>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Roles -->
                            <div class="mb-3">
                                <label for="roles" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.roles') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2 @error('roles') is-invalid @enderror"
                                        id="roles"
                                        name="roles[]"
                                        multiple
                                        required>
                                    @foreach($roles as $id => $role)
                                        <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('roles')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
                            </div>

                            <!-- Is Salesperson -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('cruds.user.fields.salesrep') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="IsSalesperson"
                                           name="IsSalesperson"
                                           value="1"
                                        {{ old('IsSalesperson') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="IsSalesperson">
                                        {{ __('global.yes') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Rep Code (shown when IsSalesperson is checked) -->
                            <div class="mb-3" id="repCodeGroup" style="display: none;">
                                <label for="RepCode" class="form-label fw-semibold">
                                    {{ __('cruds.user.fields.repcode') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('RepCode') is-invalid @enderror"
                                       id="RepCode"
                                       name="RepCode"
                                       value="{{ old('RepCode') }}"
                                       placeholder="e.g., REP001">
                                @error('RepCode')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Unique sales representative code</small>
                            </div>

                            <!-- Is Customer -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">{{ __('cruds.user.fields.customer') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           id="IsCustomer"
                                           name="IsCustomer"
                                           value="1"
                                        {{ old('IsCustomer') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="IsCustomer">
                                        {{ __('global.yes') }}
                                    </label>
                                </div>
                            </div>

                            <!-- Customer Selection (shown when IsCustomer is checked) -->
                            <div class="mb-3" id="customerGroup" style="display: none;">
                                <label for="customer_id" class="form-label fw-semibold">
                                    {{ __('global.select_customer') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-customer @error('customer_id') is-invalid @enderror"
                                        id="customer_id"
                                        name="customer_id">
                                    <option value="">{{ __('global.select_customer') }}</option>
                                    @foreach($customers as $id => $customer)
                                        <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>
                                            {{ $customer }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Link this user to a customer account</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Alert -->
                    <div class="alert alert-info d-flex align-items-start mt-3" role="alert">
                        <div class="flex-shrink-0">
                            <i class="mdi mdi-information-outline fs-4 me-2"></i>
                        </div>
                        <div>
                            <strong>{{ __('global.note') }}:</strong> {{ __('messages.user_creation_note') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>
                        {{ __('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i>
                        {{ __('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
