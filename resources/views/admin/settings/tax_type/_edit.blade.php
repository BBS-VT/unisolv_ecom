<form action="{{ route('settings.tax_types.update', $tax_type->id) }}" method="POST" id="editTaxTypeForm">
    @csrf
    @method('PUT')

    <div class="modal-header bg-light">
        <h5 class="modal-title">
            <i class="bx bx-edit-alt me-2"></i>
            {{ __('global.edit') }} {{ __('cruds.taxType.title_singular') }}: {{ $tax_type->name }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="edit_name" class="form-label">
                        {{ __('cruds.taxType.fields.name') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="edit_name" name="name"
                           value="{{ old('name', $tax_type->name) }}"
                           placeholder="{{ __('cruds.taxType.fields.name') }}"
                           required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="edit_percent" class="form-label">
                        {{ __('cruds.taxType.fields.percent') }}
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control @error('percent') is-invalid @enderror"
                               id="edit_percent" name="percent"
                               value="{{ old('percent', $tax_type->percent) }}"
                               step="0.01" min="0" max="100"
                               placeholder="0.00"
                               required>
                        <span class="input-group-text">%</span>
                        @error('percent')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="form-text text-muted">{{ __('messages.tax_percent_help') }}</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group mb-0">
                    <label for="edit_description" class="form-label">
                        {{ __('cruds.taxType.fields.description') }}
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="edit_description" name="description"
                              rows="4"
                              placeholder="{{ __('cruds.taxType.fields.description') }}">{{ old('description', $tax_type->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bx bx-x me-1"></i>
            {{ __('global.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i>
            {{ __('global.update') }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editTaxTypeForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                // Disable submit button and show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __("global.updating") }}...';

                // Submit form via fetch
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editTaxTypeModal'));
                            modal.hide();
                            location.reload();
                        } else {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Validation failed');
                            });
                        }
                    })
                    .catch(error => {
                        // Re-enable submit button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;

                        console.error('Error:', error);
                        alert('{{ __("global.error_occurred") }}');
                    });
            });
        }
    });
</script>
