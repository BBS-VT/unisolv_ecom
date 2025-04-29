<div class="card">
    <div class="card-header bg-light">
        <div class="d-flex align-items-center">
            <i data-feather="tag" class="icon-dual-primary me-2"></i>
            <h5 class="card-title mb-0">{{ __('global.pricing_settings') }}</h5>
            @can('customer_edit')
                <button type="button" data-toggle="modal" data-target="#pricingSettingsModal" class="btn btn-sm btn-outline-primary ms-auto float-end">
                    <i data-feather="edit-2" class="icon-xs"></i> {{ __('global.edit') }}
                </button>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary text-primary rounded">
                                <i data-feather="layers" class="icon-dual-primary font-size-18"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="font-size-15 mb-0">{{ __('cruds.customer.fields.price_level') }}</h5>
                        <p class="text-muted mb-0">{{ __('global.pricing_level_description') }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="badge badge-soft-dark font-size-16 px-3 py-2">{{ $customer->price_level ?? '1' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-{{ $customer->discount_allowed ? 'success' : 'danger' }} text-{{ $customer->discount_allowed ? 'success' : 'danger' }} rounded">
                                <i data-feather="percent" class="icon-dual-{{ $customer->discount_allowed ? 'success' : 'danger' }} font-size-18"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="font-size-15 mb-0">{{ __('cruds.customer.fields.discount_allowed') }}</h5>
                        <p class="text-muted mb-0">{{ __('global.discount_allowed_description') }}</p>
                    </div>
                    <div class="flex-shrink-0">
                        @if($customer->discount_allowed == 1)
                            <span class="badge badge-soft-success px-3 py-2">{{ __('global.yes') }}</span>
                        @else
                            <span class="badge badge-soft-danger px-3 py-2">{{ __('global.no') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for editing pricing settings -->
@can('customer_edit')
    <div class="modal fade" id="pricingSettingsModal" tabindex="-1" role="dialog" aria-labelledby="pricingSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="pricingSettingsModalLabel">
                        <i data-feather="sliders" class="icon-dual-primary me-2"></i>
                        {{ __('global.edit') }} {{ __('global.pricing_settings') }}
                    </h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="la la-times"></i></span>
                    </button>
                </div>
                <form action="{{ route('customers.update_pricing', $customer->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label text-muted">{{ __('cruds.customer.fields.price_level') }}</label>
                            <div class="d-flex justify-content-between price-level-selector">
                                @for($i = 1; $i <= 4; $i++)
                                    <div class="form-check custom-price-level {{ $customer->price_level == $i ? 'active' : '' }}">
                                        <input class="form-check-input" type="radio" name="price_level" id="price_level_{{ $i }}"
                                               value="{{ $i }}" {{ old('price_level', $customer->price_level) == $i ? 'checked' : '' }}>
                                        <label class="form-check-label price-level-label" for="price_level_{{ $i }}">
                                            <div class="level-indicator">{{ $i }}</div>
                                            <div class="level-text">{{ __('global.price_level_'.$i.'_name') }}</div>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                            <small class="form-text text-muted mt-2">{{ __('global.price_level_help') }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">{{ __('cruds.customer.fields.discount_allowed') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="discount_allowed" name="discount_allowed" value="1"
                                    {{ old('discount_allowed', $customer->discount_allowed) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="discount_allowed">
                                    {{ __('global.allow_additional_discounts') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">{{ __('global.discount_allowed_help') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                            {{ __('global.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i data-feather="save" class="icon-xs me-1"></i>
                            {{ __('global.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
