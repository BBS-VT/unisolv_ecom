
{{-- Create Customer Category Modal --}}
<div class="modal fade" id="createCustomerCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.customer-category.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="bx bx-plus-circle me-2"></i>
                        {{ __('global.add') }} {{ __('global.customer_category') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="AccountType" class="form-label">
                            {{ __('cruds.customerCategory.fields.account_type') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('AccountType') is-invalid @enderror"
                               id="AccountType" name="AccountType"
                               value="{{ old('AccountType') }}"
                               required>
                        @error('AccountType')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label for="CustomerCategoryName" class="form-label">
                            {{ __('cruds.customerCategory.fields.category_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('CustomerCategoryName') is-invalid @enderror"
                               id="CustomerCategoryName" name="CustomerCategoryName"
                               value="{{ old('CustomerCategoryName') }}"
                               required>
                        @error('CustomerCategoryName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>
                        {{ __('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>
                        {{ __('global.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Customer Category Modal --}}
<div class="modal fade" id="editCustomerCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="editCustomerCategoryModalContent">
                {{-- Content will be loaded dynamically --}}
            </div>
        </div>
    </div>
</div>
