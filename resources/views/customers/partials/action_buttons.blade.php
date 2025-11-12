<div class="btn-group btn-group-sm" role="group">
    @can('customer_show')
        <a href="{{ route('customers.show', $customer->id) }}"
           class="btn btn-outline-primary"
           title="View Customer"
           data-bs-toggle="tooltip">
            <i class="mdi mdi-eye"></i>
        </a>
    @endcan

    @can('customer_edit')
        <a href="{{ route('customers.edit', $customer->id) }}"
           class="btn btn-outline-secondary"
           title="Edit Customer"
           data-bs-toggle="tooltip">
            <i class="mdi mdi-pencil"></i>
        </a>
    @endcan

    @can('customer_edit')
        <button type="button"
                class="btn btn-outline-info updateCustomerStatus"
                customer_id="{{ $customer->id }}"
                data-name="{{ $customer->CustomerName }}"
                data-status="{{ $customer->status }}"
                title="Toggle Status"
                data-bs-toggle="tooltip">
            <i class="mdi mdi-toggle-switch"></i>
        </button>
    @endcan

    @can('customer_delete')
        <button type="button"
                class="btn btn-outline-danger delete-customer"
                data-id="{{ $customer->id }}"
                data-name="{{ $customer->CustomerName }}"
                title="Delete Customer"
                data-bs-toggle="tooltip">
            <i class="mdi mdi-delete"></i>
        </button>
    @endcan
</div>
