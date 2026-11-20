<div class="row-actions">
    @can('customer_edit')
        <a href="{{ route('customers.edit', $customer->id) }}" class="btn" title="Edit Customer" data-bs-toggle="tooltip">
            <i class="mdi mdi-pencil"></i>
        </a>
        <button type="button" class="btn updateCustomerStatus"
                customer_id="{{ $customer->id }}"
                data-name="{{ $customer->CustomerName }}"
                data-status="{{ $customer->CustomerStatus ? 'active' : 'inactive' }}"
                title="Toggle Status" data-bs-toggle="tooltip">
            <i class="mdi mdi-toggle-switch"></i>
        </button>
    @endcan
    @can('customer_delete')
        <button type="button" class="btn btn-del delete-customer"
                data-id="{{ $customer->id }}"
                data-name="{{ $customer->CustomerName }}"
                title="Delete Customer" data-bs-toggle="tooltip">
            <i class="mdi mdi-delete"></i>
        </button>
    @endcan
</div>
