<div class="row-actions">
    <button type="button" class="btn btn-update-status" title="Update status"
            data-order-id="{{ $order->id }}"
            data-order-number="{{ $order->OrderNumber }}"
            data-customer-name="{{ $order->customer?->CustomerName }}"
            data-delivery-method="{{ $order->delivery_method }}"
            data-current-status-id="{{ $order->OrderStatusID }}"
            data-current-status-name="{{ $order->orderstatus?->name }}"
            data-current-status-color="{{ $order->orderstatus?->colour }}">
        <i class="bx bx-edit"></i>
    </button>
    @can('order_show')
        <a href="{{ route('print.order', $order->id) }}" target="_blank" class="btn"
           title="{{ __('global.view') }} {{ __('cruds.order.title_singular') }}"><i class="dripicons-preview"></i></a>
    @endcan
    @can('order_download')
        <a href="{{ route('orders.download', $order->id) }}" onClick="window.history.go(0)" class="btn"
           title="{{ __('global.downloadFile') }} {{ __('cruds.order.title_singular') }}"><i class="dripicons-download"></i></a>
    @endcan
    @can('order_delete')
        <a href="{{ route('orders.delete', $order->id) }}" class="btn btn-del delete-confirm"
           title="{{ __('global.delete') }} {{ __('cruds.order.title_singular') }}"><i class="dripicons-trash"></i></a>
    @endcan
</div>
