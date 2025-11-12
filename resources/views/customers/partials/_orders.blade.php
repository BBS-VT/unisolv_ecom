<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="mdi mdi-cart-outline me-2 text-primary"></i>
        {{ trans('cruds.order.title') }}
    </h4>
    @can('order_create')
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus me-1"></i> {{ trans('global.new_order') }}
        </a>
    @endcan
</div>

<div class="card info-card">
    <div class="card-body">
        @if($customerOrders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="ordersTable">
                    <thead>
                    <tr>
                        <th style="width: 10px;"></th>
                        <th>{{ trans('cruds.order.fields.number') }}</th>
                        <th>{{ trans('cruds.order.fields.customer_name') }}</th>
                        <th>{{ trans('cruds.order.fields.salesrep') }}</th>
                        <th>{{ trans('cruds.order.fields.ponumber') }}</th>
                        <th>{{ trans('cruds.order.fields.created_at') }}</th>
                        <th>{{ trans('cruds.order.fields.status') }}</th>
                        <th class="text-end">{{ trans('global.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customerOrders as $key => $order)
                        <tr data-entry-id="{{ $order->id }}">
                            <td></td>
                            <td>
                                <strong class="text-dark">{{ $order->OrderNumber ?? '' }}</strong>
                            </td>
                            <td>{{ $order->customer->CustomerName ?? '' }}</td>
                            <td>{{ $order->salesperson->PreferredName ?? '' }}</td>
                            <td>
                                @if($order->CustomerPurchaseOrderNumber)
                                    <span class="badge bg-light text-dark border">{{ $order->CustomerPurchaseOrderNumber }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="mdi mdi-calendar-outline me-1"></i>
                                    {{ $order->created_at ? $order->created_at->format('d M Y') : '' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = 'secondary';
                                    switch($order->OrderStatusID) {
                                        case 1:
                                            $statusClass = 'danger';
                                            break;
                                        case 2:
                                            $statusClass = 'warning';
                                            break;
                                        case 3:
                                            $statusClass = 'info';
                                            break;
                                        case 4:
                                            $statusClass = 'success';
                                            break;
                                    }
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ $order->orderstatus->name ?? '' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    @can('order_show')
                                        <a href="{{ route('orders.show', $order->id) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-light"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{ trans('global.view') }} {{ trans('cruds.order.title_singular') }}">
                                            <i class="mdi mdi-eye text-info"></i>
                                        </a>
                                    @endcan

                                    @can('order_edit')
                                        <a href="{{ route('orders.download', $order->id) }}"
                                           class="btn btn-sm btn-light"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{ trans('global.downloadFile') }} {{ trans('cruds.order.title_singular') }}">
                                            <i class="mdi mdi-download text-primary"></i>
                                        </a>
                                    @endcan

                                    @can('order_delete')
                                        <form action="{{ route('orders.delete', $order->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                              class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-light"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="{{ trans('global.delete') }} {{ trans('cruds.order.title_singular') }}">
                                                <i class="mdi mdi-delete text-danger"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="mdi mdi-cart-outline"></i>
                </div>
                <p class="empty-state-text">{{ trans('global.no_orders_yet') }}</p>
                @can('order_create')
                    <a href="{{ route('orders.create') }}" class="btn btn-primary mt-3">
                        <i class="mdi mdi-plus me-1"></i> {{ trans('global.create_first_order') }}
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable if there are orders
            @if($customerOrders->count() > 0)
            $('#ordersTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[5, 'desc']], // Sort by created_at column descending
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "{{ trans('global.search') }}...",
                    lengthMenu: "{{ trans('global.show') }} _MENU_ {{ trans('global.entries') }}",
                    info: "{{ trans('global.showing') }} _START_ {{ trans('global.to') }} _END_ {{ trans('global.of') }} _TOTAL_ {{ trans('global.entries') }}",
                    infoEmpty: "{{ trans('global.showing') }} 0 {{ trans('global.to') }} 0 {{ trans('global.of') }} 0 {{ trans('global.entries') }}",
                    infoFiltered: "({{ trans('global.filtered_from') }} _MAX_ {{ trans('global.total_entries') }})",
                    paginate: {
                        first: "{{ trans('global.first') }}",
                        last: "{{ trans('global.last') }}",
                        next: "{{ trans('global.next') }}",
                        previous: "{{ trans('global.previous') }}"
                    }
                },
                columnDefs: [
                    { orderable: false, targets: [0, 7] } // Disable sorting on first and last columns
                ]
            });
            @endif

            // Re-initialize tooltips after DataTable draws
            $('#ordersTable').on('draw.dt', function() {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            });
        });
    </script>
@endpush
