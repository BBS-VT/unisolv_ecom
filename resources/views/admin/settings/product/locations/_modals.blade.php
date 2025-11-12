<div class="modal fade" id="createLocationModal" tabindex="-1" role="dialog" aria-labelledby="createLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.locations.store') }}" method="POST" id="createLocationForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createLocationModalLabel">
                        <i class="bx bx-plus-circle align-self-center icon-sm me-2"></i>
                        {{ __('global.add') }} {{ __('global.location') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Basic Information --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="LocationCode">{{ __('global.location_code') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('LocationCode') is-invalid @enderror"
                                           id="LocationCode" name="LocationCode" value="{{ old('LocationCode', $nextCode ?? '') }}"
                                           maxlength="10" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateLocationCode()" title="{{ __('global.generate_code') }}">
                                            <i class="bx bx-refresh icon-xs"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('LocationCode')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('messages.location_code_help') }}</small>
                            </div>

                            <div class="form-group">
                                <label for="LocationName">{{ __('global.location_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('LocationName') is-invalid @enderror"
                                       id="LocationName" name="LocationName" value="{{ old('LocationName') }}"
                                       maxlength="100" required>
                                @error('LocationName')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="LocationDescription">{{ __('global.description') }}</label>
                                <textarea class="form-control @error('LocationDescription') is-invalid @enderror"
                                          id="LocationDescription" name="LocationDescription" rows="3"
                                          maxlength="500">{{ old('LocationDescription') }}</textarea>
                                @error('LocationDescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Address Information --}}
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">{{ __('global.address_information') }}</h6>

                            <div class="form-group">
                                <label for="Address1">{{ __('global.address_line_1') }}</label>
                                <input type="text" class="form-control @error('Address1') is-invalid @enderror"
                                       id="Address1" name="Address1" value="{{ old('Address1') }}" maxlength="255">
                                @error('Address1')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="Address2">{{ __('global.address_line_2') }}</label>
                                <input type="text" class="form-control @error('Address2') is-invalid @enderror"
                                       id="Address2" name="Address2" value="{{ old('Address2') }}" maxlength="255">
                                @error('Address2')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="City">{{ __('global.city') }}</label>
                                        <input type="text" class="form-control @error('City') is-invalid @enderror"
                                               id="City" name="City" value="{{ old('City') }}" maxlength="100">
                                        @error('City')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Province">{{ __('global.province') }}</label>
                                        <input type="text" class="form-control @error('Province') is-invalid @enderror"
                                               id="Province" name="Province" value="{{ old('Province') }}" maxlength="100">
                                        @error('Province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="PostalCode">{{ __('global.postal_code') }}</label>
                                        <input type="text" class="form-control @error('PostalCode') is-invalid @enderror"
                                               id="PostalCode" name="PostalCode" value="{{ old('PostalCode') }}" maxlength="20">
                                        @error('PostalCode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Country">{{ __('global.country') }}</label>
                                        <input type="text" class="form-control @error('Country') is-invalid @enderror"
                                               id="Country" name="Country" value="{{ old('Country') }}" maxlength="100">
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
                                        <label for="ContactPerson">{{ __('global.contact_person') }}</label>
                                        <input type="text" class="form-control @error('ContactPerson') is-invalid @enderror"
                                               id="ContactPerson" name="ContactPerson" value="{{ old('ContactPerson') }}" maxlength="255">
                                        @error('ContactPerson')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Phone">{{ __('global.phone') }}</label>
                                        <input type="text" class="form-control @error('Phone') is-invalid @enderror"
                                               id="Phone" name="Phone" value="{{ old('Phone') }}" maxlength="50">
                                        @error('Phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Email">{{ __('global.email') }}</label>
                                        <input type="email" class="form-control @error('Email') is-invalid @enderror"
                                               id="Email" name="Email" value="{{ old('Email') }}" maxlength="255">
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
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="IsActive" name="IsActive"
                                                {{ old('IsActive', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="IsActive">{{ __('global.active') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="IsDefault" name="IsDefault"
                                                {{ old('IsDefault', false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="IsDefault">{{ __('global.default_location') }}</label>
                                        </div>
                                        <small class="form-text text-muted">{{ __('messages.default_location_help') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="SortOrder">{{ __('global.sort_order') }}</label>
                                        <input type="number" class="form-control @error('SortOrder') is-invalid @enderror"
                                               id="SortOrder" name="SortOrder" value="{{ old('SortOrder', 0) }}"
                                               min="0" max="999">
                                        @error('SortOrder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-mb-3">
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

                                <div class="col-mb-3">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('global.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save align-self-center icon-xs me-1"></i>
                        {{ __('global.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Location Modal --}}
<div class="modal fade" id="viewLocationModal" tabindex="-1" role="dialog" aria-labelledby="viewLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div id="viewLocationModalContent">
                {{-- Content will be loaded dynamically --}}
            </div>
        </div>
    </div>
</div>

{{-- Edit Location Modal --}}
<div class="modal fade" id="editLocationModal" tabindex="-1" role="dialog" aria-labelledby="editLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div id="editLocationModalContent">
                {{-- Content will be loaded dynamically --}}
            </div>
        </div>
    </div>
</div>
