<div class="row">
    <!-- Customer Information Column -->
    <div class="col-lg-4">
        <div class="card customer-info-card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i data-feather="user" class="icon-xs me-1"></i> {{ __('global.customer_information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="contact-item">
                    <div class="customer-contact-icon">
                        <i data-feather="phone" class="icon-xs"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.phone') }}</p>
                        <p class="contact-value phone">{{ $customer->PhoneNumber ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="customer-contact-icon">
                        <i data-feather="printer" class="icon-xs"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.fax') }}</p>
                        <p class="contact-value phone">{{ $customer->FaxNumber ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="customer-contact-icon">
                        <i data-feather="mail" class="icon-xs"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.email') }}</p>
                        <p class="contact-value">{{ $customer->GeneralEmailAddress ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="contact-item">
                    <div class="customer-contact-icon">
                        <i data-feather="globe" class="icon-xs"></i>
                    </div>
                    <div class="contact-details">
                        <p class="contact-label">{{ __('global.website') }}</p>
                        <p class="contact-value">
                            @if($customer->WebsiteURL)
                                <a href="{{ $customer->WebsiteURL }}" target="_blank" class="text-primary">{{ $customer->WebsiteURL }}</a>
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-6">
                        <p class="info-label">{{ __('cruds.customer.fields.vat_nr') }}</p>
                        <p class="info-value">{{ $customer->VatNr ?? 'No VAT Nr' }}</p>
                    </div>
                    <div class="col-6">
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
            <div class="col-md-6">
                <div class="card address-card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i data-feather="map-pin" class="icon-xs me-1"></i> {{ __('global.postal_address') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.address_1') }}</td>
                                <td class="py-1">{{ $customer->PostalAddressLine1 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.address_2') }}</td>
                                <td class="py-1">{{ $customer->PostalAddressLine2 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.city') }}</td>
                                <td class="py-1">{{ $customer->PostalCity ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.postal_code') }}</td>
                                <td class="py-1">{{ $customer->PostalPostalCode ?? 'N/A' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card address-card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i data-feather="truck" class="icon-xs me-1"></i> {{ __('global.delivery_address') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.address_1') }}</td>
                                <td class="py-1">{{ $customer->DeliveryAddressLine1 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.address_2') }}</td>
                                <td class="py-1">{{ $customer->DeliveryAddressLine2 ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.city') }}</td>
                                <td class="py-1">{{ $customer->DeliveryCity ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted py-1">{{ __('cruds.customer.fields.postal_code') }}</td>
                                <td class="py-1">{{ $customer->DeliveryPostalCode ?? 'N/A' }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Business Details Card -->
            <div class="col-md-12 mt-2">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i data-feather="briefcase" class="icon-xs me-1"></i> {{ __('global.business_details') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                    <tr>
                                        <td class="text-muted py-1">{{ __('global.trade_discount') }}</td>
                                        <td class="py-1">{{ $customer->StandardDiscountPercentage ?? '0%' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-1">{{ __('global.credit_limit') }}</td>
                                        <td class="py-1">{{ $customer->CreditLimit ?? '0.00' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-1">{{ __('cruds.customer.fields.delivery') }}</td>
                                        <td class="py-1">{{ $customer->DeliveryRoute ?? 'N/A' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tbody>
                                    <tr>
                                        <td class="text-muted py-1">{{ __('cruds.customer.fields.salerep') }}</td>
                                        <td class="py-1">{{ $customer->salesrep->PreferredName ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-1">{{ __('cruds.customer.fields.contract') }}</td>
                                        <td class="py-1">{{ $customer->BuyingGroupID ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted py-1">{{ __('cruds.customer.fields.type') }}</td>
                                        <td class="py-1">{{ $customer->CustomerCategoryID ?? 'N/A' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pricing Settings Card -->
                        <div class="mt-3">
                            <h6 class="mb-3">{{ __('global.pricing_settings') }}</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded bg-soft-primary">
                                                <i data-feather="layers" class="icon-dual-primary font-size-18"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ __('cruds.customer.fields.price_level') }}</h6>
                                            <p class="text-muted mb-0 small">{{ __('global.pricing_level_description') }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge badge-soft-dark px-3 py-2">{{ $customer->price_level ?? '1' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded bg-soft-{{ $customer->discount_allowed ? 'success' : 'danger' }}">
                                                <i data-feather="percent" class="icon-dual-{{ $customer->discount_allowed ? 'success' : 'danger' }} font-size-18"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">{{ __('cruds.customer.fields.discount_allowed') }}</h6>
                                            <p class="text-muted mb-0 small">{{ __('global.discount_allowed_description') }}</p>
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

                            @can('customer_edit')
                                <div class="text-end mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pricingSettingsModal">
                                        <i data-feather="edit-2" class="icon-xs me-1"></i> {{ __('global.edit_pricing') }}
                                    </button>
                                </div>
                            @endcan
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pricingSettingsModalLabel">
                        <i data-feather="sliders" class="icon-xs me-1"></i> {{ __('global.edit') }} {{ __('global.pricing_settings') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('customers.update_pricing', $customer->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label text-muted">{{ __('cruds.customer.fields.price_level') }}</label>
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

                        <div class="alert alert-warning">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i data-feather="alert-triangle" class="text-warning me-2"></i>
                                </div>
                                <div>
                                    <p class="mb-0">{{ __('global.pricing_change_warning') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('global.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save" class="icon-xs me-1"></i>
                            {{ __('global.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan
