<div class="modal-header bg-light">
    <h5 class="modal-title">
        <i class="bx bx-map-pin align-self-center icon-sm me-2"></i>
        {{ $location->LocationName }}
        @if($location->IsDefault)
            <span class="badge bg-primary ms-2">{{ __('global.default') }}</span>
        @endif
        @if(!$location->IsActive)
            <span class="badge bg-secondary ms-1">{{ __('global.inactive') }}</span>
        @endif
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3">
        {{-- Basic Information Card --}}
        <div class="col-md-6">
            <div class="card border h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ __('global.basic_information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">{{ __('global.location_code') }}</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-primary-subtle text-primary fs-6">{{ $location->LocationCode }}</span>
                        </dd>

                        <dt class="col-sm-5 text-muted">{{ __('global.location_name') }}</dt>
                        <dd class="col-sm-7">{{ $location->LocationName }}</dd>

                        @if($location->LocationDescription)
                            <dt class="col-sm-5 text-muted">{{ __('global.description') }}</dt>
                            <dd class="col-sm-7">{{ $location->LocationDescription }}</dd>
                        @endif

                        <dt class="col-sm-5 text-muted">{{ __('global.status') }}</dt>
                        <dd class="col-sm-7">
                            @if($location->IsActive)
                                <span class="badge bg-success">
                                    <i class="bx bx-check-circle me-1"></i>
                                    {{ __('global.active') }}
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bx bx-x-circle me-1"></i>
                                    {{ __('global.inactive') }}
                                </span>
                            @endif
                        </dd>

                        <dt class="col-sm-5 text-muted">{{ __('global.sort_order') }}</dt>
                        <dd class="col-sm-7 mb-2">{{ $location->SortOrder }}</dd>

                        <dt class="col-sm-5 text-muted">{{ __('global.store_display') }}</dt>
                        <dd class="col-sm-7 mb-2">{{ $location->show_in_shop }}</dd>

                        <dt class="col-sm-5 text-muted">{{ __('global.store_sort_order') }}</dt>
                        <dd class="col-sm-7 mb-0">{{ $location->shop_sort_order }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Stock Information Card --}}
        <div class="col-md-6">
            <div class="card border h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">
                        <i class="bx bx-package me-1"></i>
                        {{ __('global.stock_information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-primary mb-1">{{ $stockCount ?? 0 }}</h3>
                                <small class="text-muted">{{ __('global.products') }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <h3 class="text-success mb-1">{{ number_format($totalQuantity ?? 0, 2) }}</h3>
                                <small class="text-muted">{{ __('global.total_quantity') }}</small>
                            </div>
                        </div>
                    </div>

                    @if($stockCount > 0)
                        <a href="{{ route('products.index', ['location' => $location->LocationCode]) }}"
                           class="btn btn-outline-primary btn-sm w-100" target="_blank">
                            <i class="bx bx-list-ul me-1"></i>
                            {{ __('global.view_stock_holdings') }}
                        </a>
                    @else
                        <div class="alert alert-info mb-0 py-2">
                            <i class="bx bx-info-circle me-1"></i>
                            <small>{{ __('global.no_stock_at_location') }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Address Information Card --}}
        <div class="col-md-6">
            <div class="card border h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">
                        <i class="bx bx-map me-1"></i>
                        {{ __('global.address_information') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($location->formatted_address)
                        <address class="mb-0">
                            @if($location->Address1)
                                <i class="bx bx-building-house text-muted me-1"></i>
                                {{ $location->Address1 }}<br>
                            @endif
                            @if($location->Address2)
                                <span class="ms-4">{{ $location->Address2 }}</span><br>
                            @endif
                            @if($location->City || $location->Province)
                                <i class="bx bx-map-alt text-muted me-1"></i>
                                {{ $location->City }}@if($location->City && $location->Province), @endif{{ $location->Province }}<br>
                            @endif
                            @if($location->PostalCode)
                                <i class="bx bx-envelope text-muted me-1"></i>
                                {{ $location->PostalCode }}<br>
                            @endif
                            @if($location->Country)
                                <i class="bx bx-globe text-muted me-1"></i>
                                {{ $location->Country }}
                            @endif
                        </address>

                        @if($location->Address1)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($location->formatted_address) }}"
                               target="_blank" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                                <i class="bx bx-map me-1"></i>
                                {{ __('global.view_on_map') }}
                            </a>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bx bx-map-pin display-4 opacity-25"></i>
                            <p class="mb-0 small">{{ __('global.no_address_provided') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Contact Information Card --}}
        <div class="col-md-6">
            <div class="card border h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">
                        <i class="bx bx-user me-1"></i>
                        {{ __('global.contact_information') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($location->ContactPerson || $location->Phone || $location->Email || $location->fulfillment_email)
                        <dl class="row mb-0">
                            @if($location->ContactPerson)
                                <dt class="col-sm-4 text-muted">
                                    <i class="bx bx-user-circle me-1"></i>
                                    {{ __('global.contact_person') }}
                                </dt>
                                <dd class="col-sm-8">{{ $location->ContactPerson }}</dd>
                            @endif

                            @if($location->Phone)
                                <dt class="col-sm-4 text-muted">
                                    <i class="bx bx-phone me-1"></i>
                                    {{ __('global.phone') }}
                                </dt>
                                <dd class="col-sm-8">
                                    <a href="tel:{{ $location->Phone }}" class="text-decoration-none">
                                        {{ $location->Phone }}
                                    </a>
                                </dd>
                            @endif

                            @if($location->Email)
                                <dt class="col-sm-4 text-muted">
                                    <i class="bx bx-envelope me-1"></i>
                                    {{ __('global.email') }}
                                </dt>
                                <dd class="col-sm-8">
                                    <a href="mailto:{{ $location->Email }}" class="text-decoration-none">
                                        {{ $location->Email }}
                                    </a>
                                </dd>
                            @endif

                            @if($location->fulfillment_email)
                                <dt class="col-sm-4 text-muted">
                                    <i class="bx bx-package me-1"></i>
                                    {{ __('global.fulfillment_email') }}
                                </dt>
                                <dd class="col-sm-8 mb-0">
                                    <a href="mailto:{{ $location->fulfillment_email }}" class="text-decoration-none">
                                        {{ $location->fulfillment_email }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ __('messages.fulfillment_email_for_location') }}</small>
                                </dd>
                            @endif
                        </dl>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bx bx-user-x display-4 opacity-25"></i>
                            <p class="mb-0 small">{{ __('global.no_contact_provided') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Audit Information --}}
        <div class="col-12">
            <div class="card border">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">
                        <i class="bx bx-time me-1"></i>
                        {{ __('global.audit_information') }}
                    </h6>
                </div>
                <div class="card-body py-2">
                    <div class="row small text-muted">
                        <div class="col-md-4">
                            <i class="bx bx-calendar-plus me-1"></i>
                            <strong>{{ __('global.created_at') }}:</strong>
                            {{ $location->created_at->format('Y-m-d H:i') }}
                        </div>
                        <div class="col-md-4">
                            <i class="bx bx-calendar-edit me-1"></i>
                            <strong>{{ __('global.updated_at') }}:</strong>
                            {{ $location->updated_at->format('Y-m-d H:i') }}
                        </div>
                        @if($location->LastEditedBy)
                            <div class="col-md-4">
                                <i class="bx bx-user-check me-1"></i>
                                <strong>{{ __('global.last_edit') }}:</strong>
                                {{ $location->LastEditedBy }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        <i class="bx bx-x me-1"></i>
        {{ __('global.close') }}
    </button>

    @can('settings_edit')
        <button type="button" class="btn btn-success" onclick="editLocation('{{ $location->LocationCode }}')">
            <i class="bx bx-edit me-1"></i>
            {{ __('global.edit') }}
        </button>
    @endcan

    @can('settings_edit')
        @if($location->IsActive)
            <button type="button" class="btn btn-warning text-white" onclick="toggleLocationStatus('{{ $location->LocationCode }}')">
                <i class="bx bx-pause-circle me-1"></i>
                {{ __('global.deactivate') }}
            </button>
        @else
            <button type="button" class="btn btn-success" onclick="toggleLocationStatus('{{ $location->LocationCode }}')">
                <i class="bx bx-play-circle me-1"></i>
                {{ __('global.activate') }}
            </button>
        @endif
    @endcan

    @if(!$location->IsDefault)
        @can('settings_edit')
            <button type="button" class="btn btn-primary" onclick="setDefaultLocation('{{ $location->LocationCode }}')">
                <i class="bx bx-star me-1"></i>
                {{ __('global.set_as_default') }}
            </button>
        @endcan
    @endif
</div>
<script>
    function toggleLocationStatus(locationCode) {
        if (confirm('{{ __("global.confirm_toggle_status") }}')) {
            fetch(`/admin/locations/${locationCode}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('viewLocationModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.message || '{{ __("global.error_occurred") }}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("global.error_occurred") }}');
                });
        }
    }

    function setDefaultLocation(locationCode) {
        if (confirm('{{ __("global.set_as_default_location_confirm") }}')) {
            fetch(`/admin/locations/${locationCode}/set-default`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('viewLocationModal'));
                        modal.hide();
                        location.reload();
                    } else {
                        alert(data.message || '{{ __("global.error_occurred") }}');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("global.error_occurred") }}');
                });
        }
    }
</script>
