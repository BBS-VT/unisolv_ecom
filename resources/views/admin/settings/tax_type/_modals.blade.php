{{-- Create Tax Type Modal --}}
<div class="modal fade" id="createTaxTypeModal" tabindex="-1" aria-labelledby="createTaxTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('settings.tax_types.store') }}" method="POST" id="createTaxTypeForm">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="createTaxTypeModalLabel">
                        <i class="bx bx-plus-circle me-2"></i>
                        {{ __('global.add') }} {{ __('cruds.taxType.title_singular') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">
                                    {{ __('cruds.taxType.fields.name') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name"
                                       value="{{ old('name') }}"
                                       placeholder="{{ __('cruds.taxType.fields.name') }}"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="percent" class="form-label">
                                    {{ __('cruds.taxType.fields.percent') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('percent') is-invalid @enderror"
                                           id="percent" name="percent"
                                           value="{{ old('percent') }}"
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
                                <label for="description" class="form-label">
                                    {{ __('cruds.taxType.fields.description') }}
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description"
                                          rows="4"
                                          placeholder="{{ __('cruds.taxType.fields.description') }}">{{ old('description') }}</textarea>
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
                        {{ __('global.create') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Tax Type Modal --}}
<div class="modal fade" id="editTaxTypeModal" tabindex="-1" aria-labelledby="editTaxTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="editTaxTypeModalContent">
                {{-- Content will be loaded dynamically --}}
            </div>
        </div>
    </div>
</div>
