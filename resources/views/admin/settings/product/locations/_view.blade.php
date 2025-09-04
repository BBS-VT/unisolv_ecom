<div class="modal-header">
    <h5 class="modal-title">
        <i data-feather="map-pin" class="align-self-center icon-sm me-2"></i>
        {{ $location->LocationName }}
        @if($location->IsDefault)
            <span class="badge badge-primary badge-sm ml-2">{{ __('global.default') }}</span>
        @endif
        @if(!$location->IsActive)
            <span class="badge badge-secondary badge-sm ml-1">{{ __('global.inactive') }}</span>
        @endif
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row">
        {{-- Basic Information --}}
        <div class="col-md-6">
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-3">
                        <i data-feather="info" class="icon-xs me-1"></i>
                        {{ __('global.basic_information') }}
                    </h6>

                    <div class="row">
                        <div class="col-sm-6">
                            <strong>{{ __('global.location_code') }}:</strong><br>
                            <span class="badge badge-outline-primary">{{ $location->LocationCode }}</span>
                        </div>

                        {{-- Audit Information --}}
                        <div class="col-12 mt-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted mb-3">
                                        <i data-feather="clock" class="icon-xs me-1"></i>
                                        {{ __('global.audit_information') }}
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>{{ __('global.created_at') }}:</strong><br>
                                            <small class="text-muted">{{ $location->created_at->format('Y-m-d H:i:s') }}</small>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>{{ __('global.updated_at') }}:</strong><br>
                                            <small class="text-muted">{{ $location->updated_at->format('Y-m-d H:i:s') }}</small>
                                        </div>
                                        @if($location->LastEditedBy)
                                            <div class="col-md-3">
                                                <strong>{{ __('global.last_edited_by') }}:</strong><br>
                                                <small class="text-muted">{{ $location->LastEditedBy }}</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('global.close') }}</button>

                        @can('settings_edit')
                            <button type="button" class="btn btn-success" onclick="editLocation('{{ $location->LocationCode }}')">
                                <i data-feather="edit-2" class="align-self-center icon-xs me-1"></i>
                                {{ __('global.edit') }}
                            </button>
                        @endcan

                        @if($location->IsActive)
                            @can('settings_edit')
                                <button type="button" class="btn btn-warning" onclick="toggleLocationStatus('{{ $location->LocationCode }}', false)">
                                    <i data-feather="pause" class="align-self-center icon-xs me-1"></i>
                                    {{ __('global.deactivate') }}
                                </button>
                            @endcan
                        @else
                            @can('settings_edit')
                                <button type="button" class="btn btn-success" onclick="toggleLocationStatus('{{ $location->LocationCode }}', true)">
                                    <i data-feather="play" class="align-self-center icon-xs me-1"></i>
                                    {{ __('global.activate') }}
                                </button>
                            @endcan
                        @endif

                        @if(!$location->IsDefault)
                            @can('settings_edit')
                                <button type="button" class="btn btn-primary" onclick="setDefaultLocation('{{ $location->LocationCode }}')">
                                    <i data-feather="star" class="align-self-center icon-xs me-1"></i>
                                    {{ __('global.set_as_default') }}
                                </button>
                            @endcan
                        @endif
                    </div>

                    <script>
                        function toggleLocationStatus(locationCode, activate) {
                            const action = activate ? 'activate' : 'deactivate';
                            const actionText = activate ? '{{ __("global.activate") }}' : '{{ __("global.deactivate") }}';

                            if (confirm(`{{ __('global.are_you_sure') }} ${actionText.toLowerCase()} {{ __('global.this_location') }}?`)) {
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
                                            $('#viewLocationModal').modal('hide');
                                            location.reload(); // Reload page to show updated status
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
                                            $('#viewLocationModal').modal('hide');
                                            location.reload(); // Reload page to show updated default status
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
                    </script>>
                    <div class="col-sm-6">
                        <strong>{{ __('global.location_name') }}:</strong><br>
                        {{ $location->LocationName }}
                    </div>
                </div>

                @if($location->LocationDescription)
                    <div class="mt-3">
                        <strong>{{ __('global.description') }}:</strong><br>
                        <p class="text-muted mb-0">{{ $location->LocationDescription }}</p>
                    </div>
                @endif

                <div class="mt-3 row">
                    <div class="col-sm-6">
                        <strong>{{ __('global.status') }}:</strong><br>
                        @if($location->IsActive)
                            <span class="badge badge-success">{{ __('global.active') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ __('global.inactive') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <strong>{{ __('global.sort_order') }}:</strong><br>
                        {{ $location->SortOrder }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Address Information --}}
    <div class="col-md-6">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="card-title text-muted mb-3">
                    <i data-feather="map" class="icon-xs me-1"></i>
                    {{ __('global.address_information') }}
                </h6>

                @if($location->formatted_address)
                    <address class="mb-0">
                        @if($location->Address1)
                            {{ $location->Address1 }}<br>
                        @endif
                        @if($location->Address2)
                            {{ $location->Address2 }}<br>
                        @endif
                        @if($location->City || $location->Province)
                            {{ $location->City }}@if($location->City && $location->Province), @endif{{ $location->Province }}<br>
                        @endif
                        @if($location->PostalCode)
                            {{ $location->PostalCode }}<br>
                        @endif
                        @if($location->Country)
                            {{ $location->Country }}
                        @endif
                    </address>
                @else
                    <p class="text-muted mb-0">{{ __('global.no_address_provided') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Contact Information --}}
    <div class="col-md-6 mt-3">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="card-title text-muted mb-3">
                    <i data-feather="user" class="icon-xs me-1"></i>
                    {{ __('global.contact_information') }}
                </h6>

                @if($location->ContactPerson || $location->Phone || $location->Email)
                    @if($location->ContactPerson)
                        <div class="mb-2">
                            <strong>{{ __('global.contact_person') }}:</strong><br>
                            {{ $location->ContactPerson }}
                        </div>
                    @endif

                    @if($location->Phone)
                        <div class="mb-2">
                            <strong>{{ __('global.phone') }}:</strong><br>
                            <a href="tel:{{ $location->Phone }}" class="text-decoration-none">
                                <i data-feather="phone" class="icon-xs me-1"></i>
                                {{ $location->Phone }}
                            </a>
                        </div>
                    @endif

                    @if($location->Email)
                        <div class="mb-2">
                            <strong>{{ __('global.email') }}:</strong><br>
                            <a href="mailto:{{ $location->Email }}" class="text-decoration-none">
                                <i data-feather="mail" class="icon-xs me-1"></i>
                                {{ $location->Email }}
                            </a>
                        </div>
                    @endif
                @else
                    <p class="text-muted mb-0">{{ __('global.no_contact_provided') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Stock Information --}}
    <div class="col-md-6 mt-3">
        <div class="card border-0 bg-light">
            <div class="card-body">
                <h6 class="card-title text-muted mb-3">
                    <i data-feather="package" class="icon-xs me-1"></i>
                    {{ __('global.stock_information') }}
                </h6>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="mb-2">
                            <h4 class="text-primary mb-0">{{ $stockCount ?? 0 }}</h4>
                            <small class="text-muted">{{ __('global.products') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-2">
                            <h4 class="text-success mb-0">{{ number_format($totalQuantity ?? 0, 2) }}</h4>
                            <small class="text-muted">{{ __('global.total_quantity') }}</small>
                        </div>
                    </div>
                </div>

                @if($stockCount > 0)
                    <div class="mt-3">
                        <a href="{{ route('admin.stock.index', ['location' => $location->LocationCode]) }}"
                           class="btn btn-sm btn-outline-primary btn-block" target="_blank">
                            <i data-feather="eye" class="icon-xs me-1"></i>
                            {{ __('global.view_stock_holdings') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
