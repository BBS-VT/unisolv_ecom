{{-- resources/views/admin/settings/product/locations/_edit.blade.php --}}

<form action="{{ route('admin.locations.update', $location->LocationCode) }}" method="POST" id="editLocationForm">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <h5 class="modal-title">
            <i data-feather="edit-2" class="align-self-center icon-sm me-2"></i>
            {{ __('global.edit') }} {{ __('global.location') }}: {{ $location->LocationName }}
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="modal-body">
        <div class="row">
            {{-- Basic Information --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="edit_LocationCode">{{ __('global.location_code') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('LocationCode') is-invalid @enderror"
                           id="edit_LocationCode" name="LocationCode" value="{{ old('LocationCode', $location->LocationCode) }}"
                           maxlength="10" required>
                    @error('LocationCode')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">{{ __('messages.location_code_help') }}</small>
                </div>

                <div class="form-group">
                    <label for="edit_LocationName">{{ __('global.location_name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('LocationName') is-invalid @enderror"
                           id="edit_LocationName" name="LocationName" value="{{ old('LocationName', $location->LocationName) }}"
                           maxlength="100" required>
                    @error('LocationName')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="edit_LocationDescription">{{ __('global.description') }}</label>
                    <textarea class="form-control @error('LocationDescription') is-invalid @enderror"
                              id="edit_LocationDescription" name="LocationDescription" rows="3"
                              maxlength="500">{{ old('LocationDescription', $location->LocationDescription) }}</textarea>
                    @error('LocationDescription')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Address Information --}}
            <div class="col-md-6">
                <h6 class="text-muted mb-3">{{ __('global.address_information') }}</h6>

                <div class="form-group">
                    <label for="edit_Address1">{{ __('global.address_line_1') }}</label>
                    <input type="text" class="form-control @error('Address1') is-invalid @enderror"
                           id="edit_Address1" name="Address1" value="{{ old('Address1', $location->Address1) }}" maxlength="255">
                    @error('Address1')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="edit_Address2">{{ __('global.address_line_2') }}</label>
                    <input type="text" class="form-control @error('Address2') is-invalid @enderror"
                           id="edit_Address2" name="Address2" value="{{ old('Address2', $location->Address2) }}" maxlength="255">
                    @error('Address2')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_City">{{ __('global.city') }}</label>
                            <input type="text" class="form-control @error('City') is-invalid @enderror"
                                   id="edit_City" name="City" value="{{ old('City', $location->City) }}" maxlength="100">
                            @error('City')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_Province">{{ __('global.province') }}</label>
                            <input type="text" class="form-control @error('Province') is-invalid @enderror"
                                   id="edit_Province" name="Province" value="{{ old('Province', $location->Province) }}" maxlength="100">
                            @error('Province')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_PostalCode">{{ __('global.postal_code') }}</label>
                            <input type="text" class="form-control @error('PostalCode') is-invalid @enderror"
                                   id="edit_PostalCode" name="PostalCode" value="{{ old('PostalCode', $location->PostalCode) }}" maxlength="20">
                            @error('PostalCode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edit_Country">{{ __('global.country') }}</label>
                            <input type="text" class="form-control @error('Country') is-invalid @enderror"
                                   id="edit_Country" name="Country" value="{{ old('Country', $location->Country) }}" maxlength="100">
                            @error('Country')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="col-12">
                <hr>
                <h6 class="text-muted mb-3">{{ __('global.contact_information') }}</h6>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edit_ContactPerson">{{ __('global.contact_person') }}</label>
                            <input type="text" class="form-control @error('ContactPerson') is-invalid @enderror"
                                   id="edit_ContactPerson" name="ContactPerson" value="{{ old('ContactPerson', $location->ContactPerson) }}" maxlength="255">
                            @error('ContactPerson')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edit_Phone">{{ __('global.phone') }}</label>
                            <input type="text" class="form-control @error('Phone') is-invalid @enderror"
                                   id="edit_Phone" name="Phone" value="{{ old('Phone', $location->Phone) }}" maxlength="50">
                            @error('Phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edit_Email">{{ __('global.email') }}</label>
                            <input type="email" class="form-control @error('Email') is-invalid @enderror"
                                   id="edit_Email" name="Email" value="{{ old('Email', $location->Email) }}" maxlength="255">
                            @error('Email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="col-12">
                <hr>
                <h6 class="text-muted mb-3">{{ __('global.settings') }}</h6>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="edit_IsActive" name="IsActive"
                                    {{ old('IsActive', $location->IsActive) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="edit_IsActive">{{ __('global.active') }}</label>
                            </div>
                            @if($location->IsDefault)
                                <small class="form-text text-muted text-warning">{{ __('messages.cannot_deactivate_default_location') }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="edit_IsDefault" name="IsDefault"
                                    {{ old('IsDefault', $location->IsDefault) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="edit_IsDefault">{{ __('global.default_location') }}</label>
                            </div>
                            <small class="form-text text-muted">{{ __('messages.default_location_help') }}</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="edit_SortOrder">{{ __('global.sort_order') }}</label>
                            <input type="number" class="form-control @error('SortOrder') is-invalid @enderror"
                                   id="edit_SortOrder" name="SortOrder" value="{{ old('SortOrder', $location->SortOrder) }}"
                                   min="0" max="999">
                            @error('SortOrder')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Holdings Warning --}}
            @if($location->stockHoldings && $location->stockHoldings->count() > 0)
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i data-feather="alert-triangle" class="icon-sm me-2"></i>
                        <strong>{{ __('global.warning') }}:</strong>
                        {{ __('messages.location_has_stock_holdings', ['count' => $location->stockHoldings->count()]) }}
                        {{ __('messages.location_code_change_warning') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('global.cancel') }}</button>
        <button type="submit" class="btn btn-primary">
            <i data-feather="save" class="align-self-center icon-xs me-1"></i>
            {{ __('global.update') }}
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Handle form submission
        $('#editLocationForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Disable submit button and show loading
            submitBtn.prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i> {{ __("global.updating") }}...');

            // Submit form via AJAX
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#editLocationModal').modal('hide');
                    location.reload(); // Reload to show updated data
                },
                error: function(xhr) {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html(originalText);

                    if (xhr.status === 422) {
                        // Validation errors
                        const errors = xhr.responseJSON.errors;

                        // Clear previous errors
                        form.find('.is-invalid').removeClass('is-invalid');
                        form.find('.invalid-feedback').remove();

                        // Show new errors
                        $.each(errors, function(field, messages) {
                            const input = form.find(`[name="${field}"]`);
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback">${messages[0]}</div>`);
                        });
                    } else {
                        alert('{{ __("global.error_occurred") }}');
                    }
                }
            });
        });

        // Handle default location checkbox
        $('#edit_IsDefault').on('change', function() {
            if ($(this).is(':checked')) {
                // Auto-check active if setting as default
                $('#edit_IsActive').prop('checked', true);
            }
        });

        // Prevent unchecking active if it's the default location
        $('#edit_IsActive').on('change', function() {
            if (!$(this).is(':checked') && $('#edit_IsDefault').is(':checked')) {
                $(this).prop('checked', true);
                alert('{{ __("messages.cannot_deactivate_default_location") }}');
            }
        });
    });
</script>
