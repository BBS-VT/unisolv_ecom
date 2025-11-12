<!-- _pricing_settings_redesigned.blade.php -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="mdi mdi-tag-multiple me-2 text-primary"></i>
        {{ __('global.pricing_and_discount_management') }}
    </h4>
    @can('customer_edit')
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pricingSettingsModal">
            <i class="mdi mdi-pencil me-1"></i> {{ __('global.edit_pricing') }}
        </button>
    @endcan
</div>

<div class="row">
    <!-- Current Price Level -->
    <div class="col-lg-6 mb-4">
        <div class="card info-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="balance-icon primary me-3">
                        <i class="mdi mdi-layers"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1">{{ __('cruds.customer.fields.price_level') }}</h5>
                        <p class="text-muted small mb-0">{{ __('global.pricing_level_description_long') }}</p>
                    </div>
                </div>

                <div class="bg-light p-3 rounded">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="level-indicator">
                                {{ $customer->price_level ?? '1' }}
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ __('global.price_level_'.$customer->price_level.'_name') }}</h6>
                            <p class="text-muted small mb-0">{{ __('global.price_level_'.$customer->price_level.'_description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Discount Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card info-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="balance-icon {{ $customer->discount_allowed ? 'success' : 'danger' }} me-3">
                        <i class="mdi mdi-percent"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-1">{{ __('cruds.customer.fields.discount_allowed') }}</h5>
                        <p class="text-muted small mb-0">{{ __('global.discount_allowed_description_long') }}</p>
                    </div>
                </div>

                <div class="bg-light p-3 rounded">
                    <div class="d-flex align-items-center mb-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   id="discount-allowed-display"
                                   {{ $customer->discount_allowed ? 'checked' : '' }}
                                   disabled>
                            <label class="form-check-label ms-2" for="discount-allowed-display">
                                <span class="{{ $customer->discount_allowed ? 'text-success' : 'text-danger' }} fw-semibold">
                                    {{ $customer->discount_allowed ? __('global.discount_allowed') : __('global.discount_not_allowed') }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <p class="small text-muted mb-0">
                        @if($customer->discount_allowed)
                            <i class="mdi mdi-check-circle text-success me-1"></i>
                            {{ __('global.discount_allowed_active_info') }}
                        @else
                            <i class="mdi mdi-close-circle text-danger me-1"></i>
                            {{ __('global.discount_allowed_inactive_info') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Information Table -->
<div class="card info-card">
    <div class="card-header card-header-custom">
        <h5 class="card-title-custom mb-0">
            <i class="mdi mdi-information-outline"></i>
            {{ __('global.pricing_information') }}
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col" class="ps-4">{{ __('global.price_level') }}</th>
                    <th scope="col">{{ __('global.description') }}</th>
                    <th scope="col">{{ __('global.discount_rate') }}</th>
                    <th scope="col" class="pe-4">{{ __('global.eligibility') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr class="{{ $customer->price_level == 1 ? 'table-active' : '' }}">
                    <th scope="row" class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($customer->price_level == 1)
                                <i class="mdi mdi-check-circle text-success me-2"></i>
                            @endif
                            <span class="fw-semibold">1 - {{ __('global.price_level_1_name') }}</span>
                        </div>
                    </th>
                    <td>{{ __('global.price_level_1_description') }}</td>
                    <td>
                            <span class="badge bg-light text-dark border">
                                {{ __('global.price_level_1_discount') }}
                            </span>
                    </td>
                    <td class="pe-4">
                        <small class="text-muted">{{ __('global.price_level_1_eligibility') }}</small>
                    </td>
                </tr>
                <tr class="{{ $customer->price_level == 2 ? 'table-active' : '' }}">
                    <th scope="row" class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($customer->price_level == 2)
                                <i class="mdi mdi-check-circle text-success me-2"></i>
                            @endif
                            <span class="fw-semibold">2 - {{ __('global.price_level_2_name') }}</span>
                        </div>
                    </th>
                    <td>{{ __('global.price_level_2_description') }}</td>
                    <td>
                            <span class="badge bg-light text-dark border">
                                {{ __('global.price_level_2_discount') }}
                            </span>
                    </td>
                    <td class="pe-4">
                        <small class="text-muted">{{ __('global.price_level_2_eligibility') }}</small>
                    </td>
                </tr>
                <tr class="{{ $customer->price_level == 3 ? 'table-active' : '' }}">
                    <th scope="row" class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($customer->price_level == 3)
                                <i class="mdi mdi-check-circle text-success me-2"></i>
                            @endif
                            <span class="fw-semibold">3 - {{ __('global.price_level_3_name') }}</span>
                        </div>
                    </th>
                    <td>{{ __('global.price_level_3_description') }}</td>
                    <td>
                            <span class="badge bg-light text-dark border">
                                {{ __('global.price_level_3_discount') }}
                            </span>
                    </td>
                    <td class="pe-4">
                        <small class="text-muted">{{ __('global.price_level_3_eligibility') }}</small>
                    </td>
                </tr>
                <tr class="{{ $customer->price_level == 4 ? 'table-active' : '' }}">
                    <th scope="row" class="ps-4">
                        <div class="d-flex align-items-center">
                            @if($customer->price_level == 4)
                                <i class="mdi mdi-check-circle text-success me-2"></i>
                            @endif
                            <span class="fw-semibold">4 - {{ __('global.price_level_4_name') }}</span>
                        </div>
                    </th>
                    <td>{{ __('global.price_level_4_description') }}</td>
                    <td>
                            <span class="badge bg-light text-dark border">
                                {{ __('global.price_level_4_discount') }}
                            </span>
                    </td>
                    <td class="pe-4">
                        <small class="text-muted">{{ __('global.price_level_4_eligibility') }}</small>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
