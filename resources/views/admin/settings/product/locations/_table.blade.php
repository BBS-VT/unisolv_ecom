{{-- resources/views/admin/settings/product/locations/_table.blade.php --}}

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
                            <a href="{{ route('admin.locations.show', $location->LocationCode) }}"
                               class="btn btn-outline-primary btn-sm" title="{{ __('global.show') }}">
                                <i data-feather="eye" class="icon-xs"></i>
                            </a>
                        @endcan

                        @can('settings_edit')
                            <a href="{{ route('admin.locations.edit', $location->LocationCode) }}"
                               class="btn btn-outline-success btn-sm" title="{{ __('global.edit') }}">
                                <i data-feather="edit-2" class="icon-xs"></i>
                            </a>
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

        function viewLocation(locationCode) {
            fetch(`/admin/locations/${locationCode}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('viewLocationModalContent').innerHTML = html;
                    $('#viewLocationModal').modal('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("global.error_loading_data") }}');
                });
        }

        function editLocation(locationCode) {
            fetch(`/admin/locations/${locationCode}/edit`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editLocationModalContent').innerHTML = html;
                    $('#editLocationModal').modal('show');
                    // Reinitialize feather icons
                    feather.replace();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("global.error_loading_data") }}');
                });
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
@endpush
