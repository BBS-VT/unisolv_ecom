<div class="row">
    <!-- Customer Information Column -->
    <div class="col-lg-4 mb-4">
        <div class="card info-card h-100">
            <div class="card-header card-header-custom">
                <h5 class="card-title-custom">
                    <i class="mdi mdi-account"></i>
                    {{ __('global.customer_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="mdi mdi-phone"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.phone') }}</p>
                        <p class="contact-value phone">{{ $customer->PhoneNumber ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="mdi mdi-printer"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.fax') }}</p>
                        <p class="contact-value phone">{{ $customer->FaxNumber ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="mdi mdi-email"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.email') }}</p>
                        <p class="contact-value">{{ $customer->GeneralEmailAddress ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="mdi mdi-web"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.website') }}</p>
                        <p class="contact-value">
                            @if($customer->WebsiteURL)
                                <a href="{{ $customer->WebsiteURL }}" target="_blank" class="text-primary text-decoration-none">
                                    {{ $customer->WebsiteURL }}
                                    <i class="mdi mdi-open-in-new ms-1" style="font-size: 0.875rem;"></i>
                                </a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row">
                    <div class="col-6 mb-3">
                        <p class="info-label">{{ __('cruds.customer.fields.vat_nr') }}</p>
                        <p class="info-value">{{ $customer->VatNr ?? 'No VAT Nr' }}</p>
                    </div>
                    <div class="col-6 mb-3">
                        <p class="info-label">{{ __('cruds.customer.fields.store_ean') }}</p>
                        <p class="info-value">{{ $customer->StoreEAN ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address and Details Column -->
    <div class="col-lg-8">
        <div class="row">
            <!-- Address Cards -->
            <div class="col-md-6 mb-4">
                <div class="card info-card h-100">
                    <div class="card-header card-header-custom">
                        <h5 class="card-title-custom">
                            <i class="mdi mdi-map-marker"></i>
                            {{ __('global.postal_address') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                            <tr>
                                <td class="text-muted py-2" style="width: 40%;">{{ __('cruds.customer.fields.address_1') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->PostalAddressLine1 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">{{ __('cruds.customer.fields.address_2') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->PostalAddressLine2 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">{{ __('cruds.customer.fields.city') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->PostalCity ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">{{ __('cruds.customer.fields.postal_code') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->PostalPostalCode ?? 'N/A' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card info-card h-100">
                    <div class="card-header card-header-custom">
                        <h5 class="card-title-custom">
                            <i class="mdi mdi-truck-delivery"></i>
                            {{ __('global.delivery_address') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tbody>
                            <tr>
                                <td class="text-muted py-2" style="width: 40%;">{{ __('cruds.customer.fields.address_1') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->DeliveryAddressLine1 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">{{ __('cruds.customer.fields.address_2') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->DeliveryAddressLine2 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">{{ __('cruds.customer.fields.city') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->DeliveryCity ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-2">{{ __('cruds.customer.fields.postal_code') }}</td>
                                <td class="py-2 fw-medium">{{ $customer->DeliveryPostalCode ?? 'N/A' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Business Details Card -->
            <div class="col-md-12">
                <div class="card info-card">
                    <div class="card-header card-header-custom">
                        <h5 class="card-title-custom">
                            <i class="mdi mdi-briefcase"></i>
                            {{ __('global.business_details') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm mb-0">
                                    <tbody>
                                    <tr>
                                        <td class="text-muted py-2" style="width: 45%;">{{ __('global.trade_discount') }}</td>
                                        <td class="py-2 fw-medium">{{ $customer->StandardDiscountPercentage ?? '0%' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-2">{{ __('global.credit_limit') }}</td>
                                        <td class="py-2 fw-medium">R {{ number_format($customer->CreditLimit ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-2">{{ __('cruds.customer.fields.delivery') }}</td>
                                        <td class="py-2 fw-medium">{{ $customer->DeliveryRoute ?? 'N/A' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm mb-0">
                                    <tbody>
                                    <tr>
                                        <td class="text-muted py-2" style="width: 45%;">{{ __('cruds.customer.fields.salerep') }}</td>
                                        <td class="py-2 fw-medium">{{ $customer->salesrep->PreferredName ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-2">{{ __('cruds.customer.fields.contract') }}</td>
                                        <td class="py-2 fw-medium">{{ $customer->BuyingGroupID ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-2">{{ __('cruds.customer.fields.type') }}</td>
                                        <td class="py-2 fw-medium">{{ $customer->CustomerCategoryID ?? 'N/A' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pricing Settings Section -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="mdi mdi-tag-multiple text-primary me-2"></i>
                                    {{ __('global.pricing_settings') }}
                                </h6>
                                @can('customer_edit')
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pricingSettingsModal">
                                        <i class="mdi mdi-pencil me-1"></i> {{ __('global.edit_pricing') }}
                                    </button>
                                @endcan
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="balance-icon primary me-3" style="width: 40px; height: 40px; font-size: 1.25rem;">
                                            <i class="mdi mdi-layers"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="text-muted small mb-1">{{ __('cruds.customer.fields.price_level') }}</p>
                                            <h6 class="mb-0">{{ __('global.pricing_level_description') }}</h6>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-light text-dark border fs-6 px-3 py-2">{{ $customer->price_level ?? '1' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="balance-icon {{ $customer->discount_allowed ? 'success' : 'danger' }} me-3" style="width: 40px; height: 40px; font-size: 1.25rem;">
                                            <i class="mdi mdi-percent"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="text-muted small mb-1">{{ __('cruds.customer.fields.discount_allowed') }}</p>
                                            <h6 class="mb-0">{{ __('global.discount_allowed_description') }}</h6>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if($customer->discount_allowed == 1)
                                                <span class="badge badge-soft-success fs-6 px-3 py-2">{{ __('global.yes') }}</span>
                                            @else
                                                <span class="badge badge-soft-danger fs-6 px-3 py-2">{{ __('global.no') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Settings Modal -->
@can('customer_edit')
    <div class="modal fade" id="pricingSettingsModal" tabindex="-1" aria-labelledby="pricingSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pricingSettingsModalLabel">
                        <i class="mdi mdi-tune me-2"></i> {{ __('global.edit') }} {{ __('global.pricing_settings') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customers.update_pricing', $customer->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">{{ __('cruds.customer.fields.price_level') }}</label>
                            <div class="price-level-selector">
                                @for($i = 1; $i <= 4; $i++)
                                    <div class="custom-price-level {{ $customer->price_level == $i ? 'active' : '' }}">
                                        <input class="form-check-input" type="radio" name="price_level" id="price_level_{{ $i }}"
                                               value="{{ $i }}" {{ old('price_level', $customer->price_level) == $i ? 'checked' : '' }}>
                                        <label class="form-check-label price-level-label" for="price_level_{{ $i }}">
                                            <div class="level-indicator">{{ $i }}</div>
                                            <div class="level-text">{{ __('global.price_level_'.$i.'_name') }}</div>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                            <small class="form-text text-muted mt-2 d-block">{{ __('global.price_level_help') }}</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">{{ __('cruds.customer.fields.discount_allowed') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="discount_allowed" name="discount_allowed" value="1"
                                    {{ old('discount_allowed', $customer->discount_allowed) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label" for="discount_allowed">
                                    {{ __('global.allow_additional_discounts') }}
                                </label>
                            </div>
                            <small class="form-text text-muted mt-2 d-block">{{ __('global.discount_allowed_help') }}</small>
                        </div>

                        <div class="alert alert-warning d-flex align-items-start" role="alert">
                            <div class="flex-shrink-0">
                                <i class="mdi mdi-alert-outline fs-4 me-2"></i>
                            </div>
                            <div>
                                {{ __('global.pricing_change_warning') }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('global.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i>
                            {{ __('global.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
