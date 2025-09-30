{{-- resources/views/admin/settings/customer/buyingGroup/_table.blade.php --}}

<div class="row mb-3">
    <div class="col">
        <h6 class="mb-0"></h6>
    </div>
    <div class="col-auto">
        @can('buying_group_create')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createBuyingGroupModal">
                <i class="bx bx-plus-circle me-1"></i>
                {{ __('global.add') }} {{ __('global.buying_group') }}
            </button>
        @endcan
    </div>
</div>

@if($buyingGroups->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>{{ __('cruds.buyingGroup.fields.group_name') }}</th>
                <th>{{ __('cruds.buyingGroup.fields.valid_from') }}</th>
                <th>{{ __('cruds.buyingGroup.fields.valid_to') }}</th>
                <th class="text-center" style="width: 120px;">{{ __('global.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($buyingGroups as $buyingGroup)
                <tr>
                    <td>
                        <strong>{{ $buyingGroup->BuyingGroupName ?? '' }}</strong>
                    </td>
                    <td>
                        @if($buyingGroup->ValidFrom)
                            <small class="text-muted">
                                <i class="bx bx-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($buyingGroup->ValidFrom)->format('d/m/Y') }}
                            </small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($buyingGroup->ValidTo)
                            <small class="text-muted">
                                <i class="bx bx-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($buyingGroup->ValidTo)->format('d/m/Y') }}
                            </small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            @can('buying_group_edit')
                                <button type="button" class="btn btn-outline-success"
                                        title="{{ __('global.edit') }}"
                                        onclick="editBuyingGroup({{ $buyingGroup->id }})">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                            @endcan

                            @can('buying_group_delete')
                                <button type="button" class="btn btn-outline-danger"
                                        title="{{ __('global.delete') }}"
                                        onclick="deleteBuyingGroup({{ $buyingGroup->id }}, '{{ $buyingGroup->BuyingGroupName }}')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if($buyingGroups->hasPages())
        <div class="row mt-3">
            <div class="col">
                {{ $buyingGroups->links() }}
            </div>
        </div>
    @endif
@else
    <div class="text-center py-5">
        <i class="bx bx-group display-1 text-muted opacity-25"></i>
        <h5 class="mt-3 text-muted">{{ __('global.no_buying_groups_yet') }}</h5>
        <p class="text-muted">{{ __('messages.add_first_buying_group') }}</p>
        @can('buying_group_create')
            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createBuyingGroupModal">
                <i class="bx bx-plus-circle me-1"></i>
                {{ __('global.add') }} {{ __('global.buying_group') }}
            </button>
        @endcan
    </div>
@endif

@push('scripts')
    <script>
        function editBuyingGroup(groupId) {
            fetch(`/admin/buying-group/${groupId}/edit`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editBuyingGroupModalContent').innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('editBuyingGroupModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("global.error_loading_data") }}');
                });
        }

        function deleteBuyingGroup(groupId, groupName) {
            if (confirm(`{{ __('global.are_you_sure_delete') }} "${groupName}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('admin.buying-group.index') }}/${groupId}`;

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
    </script>
@endpush
