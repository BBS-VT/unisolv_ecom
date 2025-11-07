<form action="{{ route('admin.locations.update', $location->LocationCode) }}" method="POST" id="editLocationForm">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <div>
            <h5 class="modal-title d-flex align-items-center gap-2">
                <i data-feather="edit-2" class="icon-sm"></i>
                <span>{{ __('global.edit') }} {{ __('global.location') }}</span>
            </h5>
            <div class="small text-muted mt-1">
                {{ __('global.location_name') }}:
                <strong>{{ $location->LocationName }}</strong>
                &nbsp;•&nbsp; {{ __('global.location_code') }}:
                <code>{{ $location->LocationCode }}</code>
                @if($location->IsDefault)
                    <span class="badge bg-primary ms-2">{{ __('global.default') }}</span>
                @endif
                @unless($location->IsActive)
                    <span class="badge bg-secondary ms-1">{{ __('global.inactive') }}</span>
                @endunless
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        {{-- Stock warning (if any) --}}
        @if($location->stockHoldings && $location->stockHoldings->count() > 0)
            <div class="alert alert-warning d-flex align-items-start">
                <i data-feather="alert-triangle" class="icon-sm me-2 mt-1"></i>
                <div>
                    <strong class="d-block">{{ __('global.warning') }}</strong>
                    {{ __('messages.location_has_stock_holdings', ['count' => $location->stockHoldings->count()]) }}
                    {{ __('messages.location_code_change_warning') }}
                </div>
            </div>
        @endif

        <div class="row gx-3 gy-3">
            {{-- Left: Basic info --}}
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted d-flex align-items-center mb-3">
                        <i data-feather="info" class="icon-xs me-2"></i>
                        <span class="fw-semibold">{{ __('global.basic_information') }}</span>
                    </div>

                    <div class="mb-3">
                        <label for="edit_LocationCode" class="form-label">
                            {{ __('global.location_code') }} <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control @error('LocationCode') is-invalid @enderror"
                            id="edit_LocationCode"
                            name="LocationCode"
                            value="{{ old('LocationCode', $location->LocationCode) }}"
                            maxlength="10" required
                            aria-describedby="locationCodeHelp"
                        >
                        @error('LocationCode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="locationCodeHelp" class="form-text">{{ __('messages.location_code_help') }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_LocationName" class="form-label">
                            {{ __('global.location_name') }} <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control @error('LocationName') is-invalid @enderror"
                            id="edit_LocationName"
                            name="LocationName"
                            value="{{ old('LocationName', $location->LocationName) }}"
                            maxlength="100" required
                        >
                        @error('LocationName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-0">
                        <label for="edit_LocationDescription" class="form-label">{{ __('global.description') }}</label>
                        <textarea
                            class="form-control @error('LocationDescription') is-invalid @enderror"
                            id="edit_LocationDescription"
                            name="LocationDescription"
                            rows="3"
                            maxlength="500"
                        >{{ old('LocationDescription', $location->LocationDescription) }}</textarea>
                        @error('LocationDescription')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Right: Address --}}
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <div class="text-muted d-flex align-items-center mb-3">
                        <i data-feather="map" class="icon-xs me-2"></i>
                        <span class="fw-semibold">{{ __('global.address_information') }}</span>
                    </div>

                    <div class="mb-3">
                        <label for="edit_Address1" class="form-label">{{ __('global.address_line_1') }}</label>
                        <input type="text" class="form-control @error('Address1') is-invalid @enderror"
                               id="edit_Address1" name="Address1"
                               value="{{ old('Address1', $location->Address1) }}" maxlength="255">
                        @error('Address1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_Address2" class="form-label">{{ __('global.address_line_2') }}</label>
                        <input type="text" class="form-control @error('Address2') is-invalid @enderror"
                               id="edit_Address2" name="Address2"
                               value="{{ old('Address2', $location->Address2) }}" maxlength="255">
                        @error('Address2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_City" class="form-label">{{ __('global.city') }}</label>
                            <input type="text" class="form-control @error('City') is-invalid @enderror"
                                   id="edit_City" name="City"
                                   value="{{ old('City', $location->City) }}" maxlength="100">
                            @error('City') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_Province" class="form-label">{{ __('global.province') }}</label>
                            <input type="text" class="form-control @error('Province') is-invalid @enderror"
                                   id="edit_Province" name="Province"
                                   value="{{ old('Province', $location->Province) }}" maxlength="100">
                            @error('Province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-0">
                            <label for="edit_PostalCode" class="form-label">{{ __('global.postal_code') }}</label>
                            <input type="text" class="form-control @error('PostalCode') is-invalid @enderror"
                                   id="edit_PostalCode" name="PostalCode"
                                   value="{{ old('PostalCode', $location->PostalCode) }}" maxlength="20">
                            @error('PostalCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-0">
                            <label for="edit_Country" class="form-label">{{ __('global.country') }}</label>
                            <input type="text" class="form-control @error('Country') is-invalid @enderror"
                                   id="edit_Country" name="Country"
                                   value="{{ old('Country', $location->Country) }}" maxlength="100">
                            @error('Country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="col-12">
                <div class="border rounded p-3">
                    <div class="text-muted d-flex align-items-center mb-3">
                        <i data-feather="user" class="icon-xs me-2"></i>
                        <span class="fw-semibold">{{ __('global.contact_information') }}</span>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_ContactPerson" class="form-label">{{ __('global.contact_person') }}</label>
                            <input type="text" class="form-control @error('ContactPerson') is-invalid @enderror"
                                   id="edit_ContactPerson" name="ContactPerson"
                                   value="{{ old('ContactPerson', $location->ContactPerson) }}" maxlength="255">
                            @error('ContactPerson') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_Phone" class="form-label">{{ __('global.phone') }}</label>
                            <input type="text" class="form-control @error('Phone') is-invalid @enderror"
                                   id="edit_Phone" name="Phone"
                                   value="{{ old('Phone', $location->Phone) }}" maxlength="50">
                            @error('Phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-0">
                            <label for="edit_Email" class="form-label">{{ __('global.email') }}</label>
                            <input type="email" class="form-control @error('Email') is-invalid @enderror"
                                   id="edit_Email" name="Email"
                                   value="{{ old('Email', $location->Email) }}" maxlength="255">
                            @error('Email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <div class="col-12">
                <div class="border rounded p-3">
                    <div class="text-muted d-flex align-items-center mb-3">
                        <i data-feather="settings" class="icon-xs me-2"></i>
                        <span class="fw-semibold">{{ __('global.settings') }}</span>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_IsActive" name="IsActive"
                                    {{ old('IsActive', $location->IsActive) ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_IsActive">{{ __('global.active') }}</label>
                            </div>
                            @if($location->IsDefault)
                                <div class="form-text text-warning">{{ __('messages.cannot_deactivate_default_location') }}</div>
                            @endif
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_IsDefault" name="IsDefault"
                                    {{ old('IsDefault', $location->IsDefault) ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_IsDefault">{{ __('global.default_location') }}</label>
                            </div>
                            <div class="form-text">{{ __('messages.default_location_help') }}</div>
                        </div>

                        <div class="col-md-4 mb-0">
                            <label for="edit_SortOrder" class="form-label">{{ __('global.sort_order') }}</label>
                            <input type="number" class="form-control @error('SortOrder') is-invalid @enderror"
                                   id="edit_SortOrder" name="SortOrder"
                                   value="{{ old('SortOrder', $location->SortOrder) }}" min="0" max="999">
                            @error('SortOrder') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="show_in_shop"
                                       id="show_in_shop"
                                    {{ old('show_in_shop', $location->show_in_shop ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_in_shop">
                                    {{ __('Display in Shop Navigation') }}
                                </label>
                            </div>
                            <small class="text-muted">{{ __('Show this location as a filter option in the online shop') }}</small>
                        </div>

                        <div class="col-md-6">
                            <label for="shop_sort_order" class="form-label">{{ __('Shop Display Order') }}</label>
                            <input type="number" class="form-control"
                                   id="shop_sort_order" name="shop_sort_order"
                                   value="{{ old('shop_sort_order', $location->shop_sort_order ?? 0) }}" min="0">
                            <small class="text-muted">{{ __('Lower numbers appear first in the navigation') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- (Optional) audit strip could go here  --}}
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('global.cancel') }}</button>
        <button type="submit" class="btn btn-primary">
            <i data-feather="save" class="icon-xs me-1"></i>{{ __('global.update') }}
        </button>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('#editLocationForm').on('submit', function (e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const original = submitBtn.html();

            submitBtn.prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>{{ __("global.updating") }}…');

            $.ajax({
                url: form.attr('action'),
                method: 'POST', // _method=PUT is present
                data: form.serialize(),
                success: function () {
                    const el = document.getElementById('editLocationModal');
                    const inst = bootstrap.Modal.getOrCreateInstance(el);
                    inst.hide();
                    location.reload();
                },
                error: function (xhr) {
                    submitBtn.prop('disabled', false).html(original);

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        form.find('.is-invalid').removeClass('is-invalid');
                        form.find('.invalid-feedback').remove();
                        $.each(errors, function (field, messages) {
                            const input = form.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                        });
                    } else {
                        alert('{{ __("global.error_occurred") }}');
                    }
                }
            });
        });

        // Keep active if default is checked
        $('#edit_IsDefault').on('change', function () {
            if ($(this).is(':checked')) $('#edit_IsActive').prop('checked', true);
        });

        // Prevent deactivating a default
        $('#edit_IsActive').on('change', function () {
            if (!$(this).is(':checked') && $('#edit_IsDefault').is(':checked')) {
                $(this).prop('checked', true);
                alert('{{ __("messages.cannot_deactivate_default_location") }}');
            }
        });
    });
</script>
