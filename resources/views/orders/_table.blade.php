@if($orders->count() > 0)
    <div class="table-responsive">
        <table id="datatable-orders" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
                <tr>
                    <td></td>
                    <th>{{ __('cruds.order.fields.number') }}</th>
                    <th>{{ __('cruds.order.fields.customer_name') }}</th>
                    <th>{{ __('cruds.order.fields.salesrep') }}</th>
                    <th>{{ __('cruds.order.fields.ponumber') }}</th>
                    <th>{{ __('cruds.order.fields.created_at') }}</th>
                    <th>{{ __('cruds.order.fields.status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody class="list" id="orders">
                @foreach ($orders as $order)
                    <tr>
                        <td class="align-bottom"></td>
                        <td class="h6">
                            <a href="{{ route('orders.show', $order->id) }}">
                                {{ $order->OrderNumber }}
                            </a>
                        </td>
                        <td class="h6">
                            {{ $order->customer->CustomerName }}
                        </td>
                        <td class="h6">
                            {{ $order->salesperson->PreferredName }}
                        </td>
                        <td class="h6">
                            {{ $order->CustomerPurchaseOrderNumber }}
                        </td>
                        <td class="h6">
                            {{ $order->OrderDate->format('Y-m-d') }}
                        </td>
                        <td>
                            <span class=" badge <?php if ( $order->OrderStatusID == 1 ) { echo "badge-danger"; }
                            elseif ( $order->OrderStatusID == 2) { echo "badge-warning"; }
                            elseif ( $order->OrderStatusID == 3) { echo "badge-info"; }
                            elseif ( $order->OrderStatusID == 4) { echo "badge-success"; }
                            elseif ( $order->OrderStatusID == 5) { echo "badge-secondary"; }
                            ?>"> {{ $order->orderstatus->name ?? '' }} </span>

                            {{--@if($order->Authorisation == 0 )
                                <span class="badge badge-success">Authorised </span>
                            @elseif($order->Authorisation == 1)
                                <span class="badge badge-warning">On Hold</span>
                            @elseif($order->Authorisation == 2)
                                <span class="badge badge-info">Released</span>
                            @endif--}}
                        </td>
                        <td>
                            @can('order_show')
                                <a href="{{ route('orders.show', $order->id) }}" target="_blank" data-toggle="tooltip"
                                   title="{{ __('global.view') }} {{ __('cruds.order.title_singular') }}"
                                   data-placement="top">
                                    <i class="las dripicons-preview text-info font-18"></i>
                                </a> &nbsp;
                            @endcan
                            @can('order_download')
                                <a href="{{ route('orders.download', $order->id) }}"  onClick="window.history.go(0)"
                                   data-toggle="tooltip" title="{{ __('global.downloadFile') }} {{ __('cruds.order.title_singular') }}"
                                   data-placement="top"> <i class="las dripicons-download text-info font-18"></i>
                                </a> &nbsp;
                            @endcan
                            @can('order_delete')
                                <a href="{{ route('orders.delete', $order->id) }}" class="delete-confirm"
                                   data-toggle="tooltip" data-placement="top" title="{{ __('global.delete') }} {{ __('cruds.order.title_singular') }}">
                                    <i class="dripicons-trash text-danger font-18"></i>
                                </a> &nbsp;
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- <div class="row card-body pagination justify-content-center text-center">
        {{ $orders->links() }}
    </div>-->
@else
    <div class="row justify-content-center card-body pb-0 pt-5">
        <p class="h4">{{ __('global.no_orders_yet') }}</p>
    </div>
@endif
