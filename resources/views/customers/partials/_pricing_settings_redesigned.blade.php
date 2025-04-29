<!-- _pricing_settings_redesigned.blade.php -->
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">{{ __('global.pricing_and_discount_management') }}</h5>
            @can('customer_edit')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pricingSettingsModal">
                    <i data-feather="edit-2" class="icon-xs me-1"></i> {{ __('global.edit_pricing') }}
                </button>
            @endcan
        </div>

        <div class="row">
            <!-- Current Price Level -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-md rounded bg-soft-primary p-2">
                                    <i data-feather="layers" class="icon-dual-primary font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title">{{ __('cruds.customer.fields.price_level') }}</h5>
                                <p class="card-text text-muted">{{ __('global.pricing_level_description_long') }}</p>

                                <div class="bg-light p-3 rounded mt-3">
                                    <div class="d-flex align-items-center">
                                        <div class="price-level-display">
                                            <span class="display-4 fw-bold text-primary">{{ $customer->price_level ?? '1' }}</span>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-1">{{ __('global.price_level_'.$customer->price_level.'_name') }}</h6>
                                            <p class="text-muted mb-0">{{ __('global.price_level_'.$customer->price_level.'_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Settings -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-md rounded bg-soft-{{ $customer->discount_allowed ? 'success' : 'danger' }} p-2">
                                    <i data-feather="percent" class="icon-dual-{{ $customer->discount_allowed ? 'success' : 'danger' }} font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title">{{ __('cruds.customer.fields.discount_allowed') }}</h5>
                                <p class="card-text text-muted">{{ __('global.discount_allowed_description_long') }}</p>

                                <div class="bg-light p-3 rounded mt-3">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check form-switch disabled">
                                            <input class="form-check-input" type="checkbox" role="switch" id="discount-allowed-display"
                                                   {{ $customer->discount_allowed ? 'checked' : '' }} disabled>
                                            <label class="form-check-label" for="discount-allowed-display">
                                                <span class="ms-2 {{ $customer->discount_allowed ? 'text-success' : 'text-danger' }} fw-bold">
                                                    {{ $customer->discount_allowed ? __('global.discount_allowed') : __('global.discount_not_allowed') }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <p class="mt-2 mb-0 small text-muted">
                                        @if($customer->discount_allowed)
                                            {{ __('global.discount_allowed_active_info') }}
                                        @else
                                            {{ __('global.discount_allowed_inactive_info') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Information -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">{{ __('global.pricing_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col">{{ __('global.price_level') }}</th>
                                    <th scope="col">{{ __('global.description') }}</th>
                                    <th scope="col">{{ __('global.discount_rate') }}</th>
                                    <th scope="col">{{ __('global.eligibility') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="{{ $customer->price_level == 1 ? 'table-active' : '' }}">
                                    <th scope="row">1 - {{ __('global.price_level_1_name') }}</th>
                                    <td>{{ __('global.price_level_1_description') }}</td>
                                    <td>{{ __('global.price_level_1_discount') }}</td>
                                    <td>{{ __('global.price_level_1_eligibility') }}</td>
                                </tr>
                                <tr class="{{ $customer->price_level == 2 ? 'table-active' : '' }}">
                                    <th scope="row">2 - {{ __('global.price_level_2_name') }}</th>
                                    <td>{{ __('global.price_level_2_description') }}</td>
                                    <td>{{ __('global.price_level_2_discount') }}</td>
                                    <td>{{ __('global.price_level_2_eligibility') }}</td>
                                </tr>
                                <tr class="{{ $customer->price_level == 3 ? 'table-active' : '' }}">
                                    <th scope="row">3 - {{ __('global.price_level_3_name') }}</th>
                                    <td>{{ __('global.price_level_3_description') }}</td>
                                    <td>{{ __('global.price_level_3_discount') }}</td>
                                    <td>{{ __('global.price_level_3_eligibility') }}</td>
                                </tr>
                                <tr class="{{ $customer->price_level == 4 ? 'table-active' : '' }}">
                                    <th scope="row">4 - {{ __('global.price_level_4_name') }}</th>
                                    <td>{{ __('global.price_level_4_description') }}</td>
                                    <td>{{ __('global.price_level_4_discount') }}</td>
                                    <td>{{ __('global.price_level_4_eligibility') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
