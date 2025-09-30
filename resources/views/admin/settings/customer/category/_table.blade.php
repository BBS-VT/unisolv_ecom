<div class="row mb-3">
    <div class="col">
        <h6 class="mb-0"></h6>
    </div>
    <div class="col-auto">
        @can('settings_create')
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createCustomerCategoryModal">
                <i class="bx bx-plus-circle me-1"></i>
                {{ __('global.add') }} {{ __('global.customer_category') }}
            </button>
        @endcan
    </div>
</div>

@if($customerCategories->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>{{ __('cruds.customerCategory.fields.account_type') }}</th>
                <th>{{ __('cruds.customerCategory.fields.category_name') }}</th>
                <th class="text-center" style="width: 120px;">{{ __('global.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($customerCategories as $customerCategory)
                <tr>
                    <td>
                        <span class="badge bg-info-subtle text-info">{{ $customerCategory->AccountType ?? '' }}</span>
                    </td>
                    <td>
                        <strong>{{ $customerCategory->CustomerCategoryName ?? '' }}</strong>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            @can('customer_category_edit')
                                <button type="button" class="btn btn-outline-success"
                                        title="{{ __('global.edit') }}"
                                        onclick="editCustomerCategory({{ $customerCategory->id }})">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                            @endcan

                            @can('customer_category_delete')
                                <button type="button" class="btn btn-outline-danger"
                                        title="{{ __('global.delete') }}"
                                        onclick="deleteCustomerCategory({{ $customerCategory->id }}, '{{ $customerCategory->CustomerCategoryName }}')">
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

    @if($customerCategories->hasPages())
        <div class="row mt-3">
            <div class="col">
                {{ $customerCategories->links() }}
            </div>
        </div>
    @endif
@else
    <div class="text-center py-5">
        <i class="bx bx-category display-1 text-muted opacity-25"></i>
        <h5 class="mt-3 text-muted">{{ __('global.no_customer_categories_yet') }}</h5>
        <p class="text-muted">{{ __('messages.add_first_customer_category') }}</p>
        @can('settings_create')
            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createCustomerCategoryModal">
                <i class="bx bx-plus-circle me-1"></i>
                {{ __('global.add') }} {{ __('global.customer_category') }}
            </button>
        @endcan
    </div>
@endif

@push('scripts')
    <script>
        function editCustomerCategory(categoryId) {
            fetch(`{{ route('admin.customer-category.index') }}/${categoryId}/edit`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editCustomerCategoryModalContent').innerHTML = html;
                    const modal = new bootstrap.Modal(document.getElementById('editCustomerCategoryModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __("global.error_loading_data") }}');
                });
        }

        function deleteCustomerCategory(categoryId, categoryName) {
            if (confirm(`{{ __('global.are_you_sure_delete') }} "${categoryName}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('admin.customer-category.index') }}/${categoryId}`;

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
