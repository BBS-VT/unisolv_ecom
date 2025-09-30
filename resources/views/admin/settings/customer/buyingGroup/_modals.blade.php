{{-- Create Buying Group Modal --}}
<div class="modal fade" id="createBuyingGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.buying-group.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="bx bx-plus-circle me-2"></i>
                        {{ __('global.add') }} {{ __('global.buying_group') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="LastEditedBy" id="LastEditedBy" value="{{ auth()->user()->id }}" hidden>
                    <div class="form-group mb-3">
                        <label for="BuyingGroupName" class="form-label">
                            {{ __('cruds.buyingGroup.fields.group_name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('BuyingGroupName') is-invalid @enderror"
                               id="BuyingGroupName" name="BuyingGroupName"
                               value="{{ old('BuyingGroupName') }}"
                               required>
                        @error('BuyingGroupName')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="ValidFrom" class="form-label">
                                    {{ __('cruds.buyingGroup.fields.valid_from') }}
                                </label>
                                <input type="date" class="form-control @error('ValidFrom') is-invalid @enderror"
                                       id="ValidFrom" name="ValidFrom"
                                       value="{{ old('ValidFrom') }}">
                                @error('ValidFrom')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="ValidTo" class="form-label">
                                    {{ __('cruds.buyingGroup.fields.valid_to') }}
                                </label>
                                <input type="date" class="form-control @error('ValidTo') is-invalid @enderror"
                                       id="ValidTo" name="ValidTo"
                                       value="{{ old('ValidTo') }}">
                                @error('ValidTo')
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

{{-- Edit Buying Group Modal --}}
<div class="modal fade" id="editBuyingGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="editBuyingGroupModalContent">
                {{-- Content will be loaded dynamically --}}
            </div>
        </div>
    </div>
</div>
