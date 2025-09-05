<div class="table-responsive">
    <table class="table table-striped table-hover" id="locationsTable">
        <thead>
        <tr>
            <th>{{ __('global.location_code') }}</th>
            <th>{{ __('global.location_name') }}</th>
            <th>{{ __('global.description') }}</th>
            <th>{{ __('global.address') }}</th>
            <th>{{ __('global.contact') }}</th>
            <th>{{ __('global.status') }}</th>
            <th class="text-center">{{ __('global.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($locations ?? [] as $location)
            <tr>
                <td>
                    <strong>{{ $location->LocationCode }}</strong>
                    @if($location->IsDefault)
                        <span class="badge badge-primary badge-sm ml-1">{{ __('global.default') }}</span>
                    @endif
                </td>
                <td>{{ $location->LocationName }}</td>
                <td>
                    @if($location->LocationDescription)
                        {{ Str::limit($location->LocationDescription, 50) }}
                    @else
                        <span class="text-muted">{{ __('global.no_description') }}</span>
                    @endif
                </td>
                <td>
                    @if($location->formatted_address)
                        <small>{{ Str::limit($location->formatted_address, 60) }}</small>
                    @else
                        <span class="text-muted">{{ __('global.no_address') }}</span>
                    @endif
                </td>
                <td>
                    @if($location->ContactPerson || $location->Phone || $location->Email)
                        <small>
                            @if($location->ContactPerson)
                                <strong>{{ $location->ContactPerson }}</strong><br>
                            @endif
                            @if($location->Phone)
                                <i data-feather="phone" class="icon-xs"></i> {{ $location->Phone }}<br>
                            @endif
                            @if($location->Email)
                                <i data-feather="mail" class="icon-xs"></i> {{ $location->Email }}
                            @endif
                        </small>
                    @else
                        <span class="text-muted">{{ __('global.no_contact') }}</span>
                    @endif
                </td>
                <td>
                    @if($location->IsActive)
                        <span class="badge badge-success">{{ __('global.active') }}</span>
                    @else
                        <span class="badge badge-secondary">{{ __('global.inactive') }}</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm" role="group">
                        @can('settings_show')
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewLocationModal"
                                    data-location-code="{{ $location->LocationCode }}" title="{{ __('global.show') }}">
                                <i class="fa fa-eye icon-xs me-1"></i>
                            </button>
                        @endcan

                        @can('settings_edit')
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editLocationModal"
                                    data-location-code="{{ $location->LocationCode }}" title="{{ __('global.edit') }}">
                                <i class="fa fa-edit icon-xs me-1"></i>
                            </button>
                        @endcan

                        @can('settings_delete')
                            @if(!$location->IsDefault)
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        title="{{ __('global.delete') }}"
                                        onclick="deleteLocation('{{ $location->LocationCode }}', '{{ $location->LocationName }}')">
                                    <i data-feather="trash-2" class="icon-xs"></i>
                                </button>
                            @endif
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i data-feather="map-pin" class="icon-lg mb-2"></i>
                        <p class="mb-0">{{ __('global.no_locations_found') }}</p>
                        <small>{{ __('messages.add_first_location') }}</small>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
    <script>
        function deleteLocation(locationCode, locationName) {
            if (confirm(`{{ __('global.are_you_sure_delete') }} "${locationName}"?`)) {
                // Create and submit delete form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/locations/${locationCode}`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function generateLocationCode() {
            fetch('/admin/locations/generate-code')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('LocationCode').value = data.code;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Initialize DataTable if available
        $(document).ready(function() {
            if (typeof $.fn.DataTable !== 'undefined' && $('#locationsTable tbody tr').length > 0) {
                $('#locationsTable').DataTable({
                    responsive: true,
                    order: [[5, 'desc'], [0, 'asc']], // Sort by status (active first), then by location code
                    pageLength: 25,
                    language: {
                        search: "{{ __('global.search') }}:",
                        lengthMenu: "{{ __('global.show') }} _MENU_ {{ __('global.entries') }}",
                        info: "{{ __('global.showing') }} _START_ {{ __('global.to') }} _END_ {{ __('global.of') }} _TOTAL_ {{ __('global.entries') }}",
                        paginate: {
                            first: "{{ __('global.first') }}",
                            last: "{{ __('global.last') }}",
                            next: "{{ __('global.next') }}",
                            previous: "{{ __('global.previous') }}"
                        }
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // View Modal: Load on show
            const viewModalEl = document.getElementById('viewLocationModal');
            viewModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const code = button?.getAttribute('data-location-code');
                const container = document.getElementById('viewLocationModalContent');
                container.innerHTML = '<div class="p-4 text-center"><div class="spinner-border" role="status"></div></div>';

                fetch(`/admin/locations/${encudeURIComponent(code)}`)
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        if (windows.feather) feather.replace();
                    })
                    .catch(error => {
                        container.innerHTML = `<div class="alert alert-danger">{{ __('global.error_loading_data') }}</div>`;
                        console.error('Error:', error);
                    });
            });

            viewModalEl.addEventListener('hidden.bs.modal', function () {
                document.getElementById('viewLocationModalContent').innerHTML = '';
            });

            // Edit modal: load on show
            const editModalEl = document.getElementById('editLocationModal');
            editModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const code = button?.getAttribute('data-location-code');
                const container = document.getElementById('editLocationModalContent');
                container.innerHTML = '<div class="p-4 text-center"><div class="spinner-border" role="status"></div></div>';

                fetch(`/admin/locations/${encodeURIComponent(code)}/edit`)
                    .then(r => r.text())
                    .then(html => {
                        container.innerHTML = html;
                        if (window.feather) feather.replace();
                    })
                    .catch(() => {
                        container.innerHTML = `<div class="alert alert-danger m-3">{{ __('global.error_loading_data') }}</div>`;
                    });
            });

            editModalEl.addEventListener('hidden.bs.modal', function () {
                document.getElementById('editLocationModalContent').innerHTML = '';
            });
        });
    </script>
@endpush
