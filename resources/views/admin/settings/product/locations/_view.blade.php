<div class="modal-header">
    <div>
        <h5 class="modal-title d-flex align-items-center gap-2">
            <i data-feather="map-pin" class="align-self-center icon-sm"></i>
            <span>{{ $location->LocationName }}</span>
            @if($location->IsDefault)
                <span class="badge bg-primary">{{ __('global.default') }}</span>
            @endif
            @unless($location->IsActive)
                <span class="badge bg-secondary">{{ __('global.inactive') }}</span>
            @endunless
        </h5>
        <div class="small text-muted mt-1">
            {{ __('global.location_code') }}: <code>{{ $location->LocationCode }}</code>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    {{-- Quick stats --}}
    <div class="row gx-3 gy-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="border rounded p-3 text-center h-100">
                <div class="fw-semibold mb-1">{{ __('global.products') }}</div>
                <div class="display-6 mb-0">{{ $stockCount ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="border rounded p-3 text-center h-100">
                <div class="fw-semibold mb-1">{{ __('global.total_quantity') }}</div>
                <div class="display-6 mb-0">{{ number_format($totalQuantity ?? 0, 2) }}</div>
            </div>
        </div>
        @if(($stockCount ?? 0) > 0)
            <div class="col-12 col-md-6 d-flex align-items-stretch">
                <a href="{{ route('admin.stock.index', ['location' => $location->LocationCode]) }}"
                   target="_blank"
                   class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                    <i data-feather="eye" class="icon-xs me-2"></i> {{ __('global.view_stock_holdings') }}
                </a>
            </div>
        @endif
    </div>

    <div class="row gx-3 gy-3">
        {{-- Left: Details --}}
        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <div class="text-muted d-flex align-items-center mb-3">
                    <i data-feather="info" class="icon-xs me-2"></i>
                    <span class="fw-semibold">{{ __('global.basic_information') }}</span>
                </div>

                <dl class="row mb-2">
                    <dt class="col-5 small text-muted">{{ __('global.status') }}</dt>
                    <dd class="col-7">
                        @if($location->IsActive)
                            <span class="badge bg-success">{{ __('global.active') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('global.inactive') }}</span>
                        @endif
                    </dd>

                    <dt class="col-5 small text-muted">{{ __('global.sort_order') }}</dt>
                    <dd class="col-7">{{ $location->SortOrder }}</dd>
                </dl>

                @if($location->LocationDescription)
                    <div class="mt-3">
                        <div class="small text-muted mb-1">{{ __('global.description') }}</div>
                        <p class="mb-0">{{ $location->LocationDescription }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Address + Contact --}}
        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <div class="text-muted d-flex align-items-center mb-3">
                    <i data-feather="map" class="icon-xs me-2"></i>
                    <span class="fw-semibold">{{ __('global.address_information') }}</span>
                </div>

                @if($location->Address1 || $location->Address2 || $location->City || $location->Province || $location->PostalCode || $location->Country)
                    <address class="mb-2">
                        @if($location->Address1) {{ $location->Address1 }}<br>@endif
                        @if($location->Address2) {{ $location->Address2 }}<br>@endif
                        @if($location->City || $location->Province)
                            {{ $location->City }}@if($location->City && $location->Province), @endif{{ $location->Province }}<br>
                        @endif
                        @if($location->PostalCode) {{ $location->PostalCode }}<br>@endif
                        @if($location->Country) {{ $location->Country }}@endif
                    </address>
                    @if($location->formatted_address)
                        <a class="small text-decoration-none"
                           target="_blank" rel="noopener"
                           href="https://www.google.com/maps/search/?api=1&query={{ urlencode($location->formatted_address) }}">
                            {{ __('global.open_in_maps') }}
                        </a>
                    @endif
                @else
                    <p class="text-muted mb-0">{{ __('global.no_address_provided') }}</p>
                @endif

                <hr class="my-3">

                <div class="text-muted d-flex align-items-center mb-3">
                    <i data-feather="user" class="icon-xs me-2"></i>
                    <span class="fw-semibold">{{ __('global.contact_information') }}</span>
                </div>

                @if($location->ContactPerson || $location->Phone || $location->Email)
                    <dl class="row mb-0">
                        @if($location->ContactPerson)
                            <dt class="col-5 small text-muted">{{ __('global.contact_person') }}</dt>
                            <dd class="col-7">{{ $location->ContactPerson }}</dd>
                        @endif
                        @if($location->Phone)
                            <dt class="col-5 small text-muted">{{ __('global.phone') }}</dt>
                            <dd class="col-7">
                                <a href="tel:{{ $location->Phone }}" class="text-decoration-none">
                                    <i data-feather="phone" class="icon-xs me-1"></i>{{ $location->Phone }}
                                </a>
                            </dd>
                        @endif
                        @if($location->Email)
                            <dt class="col-5 small text-muted">{{ __('global.email') }}</dt>
                            <dd class="col-7">
                                <a href="mailto:{{ $location->Email }}" class="text-decoration-none">
                                    <i data-feather="mail" class="icon-xs me-1"></i>{{ $location->Email }}
                                </a>
                            </dd>
                        @endif
                    </dl>
                @else
                    <p class="text-muted mb-0">{{ __('global.no_contact_provided') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Audit strip --}}
    <div class="border-top pt-3 mt-3 small text-muted">
        <div class="row">
            <div class="col-md-4">
                <strong>{{ __('global.created_at') }}:</strong>
                <span class="ms-1">{{ $location->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            <div class="col-md-4 mt-2 mt-md-0">
                <strong>{{ __('global.updated_at') }}:</strong>
                <span class="ms-1">{{ $location->updated_at->format('Y-m-d H:i:s') }}</span>
            </div>
            @if($location->LastEditedBy)
                <div class="col-md-4 mt-2 mt-md-0">
                    <strong>{{ __('global.last_edit') }}</strong>
                    <span class="ms-1">{{ $location->LastEditedBy }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('global.close') }}</button>

    @can('settings_edit')
        <button type="button"
                class="btn btn-success"
                data-bs-target="#editLocationModal"
                data-bs-toggle="modal"
                data-location-code="{{ $location->LocationCode }}">
            <i data-feather="edit-2" class="icon-xs me-1"></i>{{ __('global.edit') }}
        </button>
    @endcan

    @can('settings_edit')
        @if($location->IsActive)
            <button type="button" class="btn btn-warning"
                    onclick="toggleLocationStatus('{{ $location->LocationCode }}', false)">
                <i data-feather="pause" class="icon-xs me-1"></i>{{ __('global.deactivate') }}
            </button>
        @else
            <button type="button" class="btn btn-success"
                    onclick="toggleLocationStatus('{{ $location->LocationCode }}', true)">
                <i data-feather="play" class="icon-xs me-1"></i>{{ __('global.activate') }}
            </button>
        @endif
    @endcan

    @can('settings_edit')
        @if(!$location->IsDefault)
            <button type="button" class="btn btn-primary"
                    onclick="setDefaultLocation('{{ $location->LocationCode }}')">
                <i data-feather="star" class="icon-xs me-1"></i>{{ __('global.set_as_default') }}
            </button>
        @endif
    @endcan
</div>

<script>
    function hideViewModal() {
        const el = document.getElementById('viewLocationModal');
        const inst = bootstrap.Modal.getOrCreateInstance(el);
        inst.hide();
    }

    function toggleLocationStatus(locationCode, activate) {
        const actionText = activate ? '{{ __("global.activate") }}' : '{{ __("global.deactivate") }}';
        if (!confirm(`{{ __('global.are_you_sure') }} ${actionText.toLowerCase()} {{ __('global.this_location') }}?`)) return;

        fetch(`/admin/locations/${locationCode}/toggle-status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(r => r.json())
            .then(data => { if (data.success) { hideViewModal(); location.reload(); } else { alert(data.message || '{{ __("global.error_occurred") }}'); } })
            .catch(() => alert('{{ __("global.error_occurred") }}'));
    }

    function setDefaultLocation(locationCode) {
        if (!confirm('{{ __("global.set_as_default_location_confirm") }}')) return;

        fetch(`/admin/locations/${locationCode}/set-default`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(r => r.json())
            .then(data => { if (data.success) { hideViewModal(); location.reload(); } else { alert(data.message || '{{ __("global.error_occurred") }}'); } })
            .catch(() => alert('{{ __("global.error_occurred") }}'));
    }
</script>
