<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h4 class="card-title">{{ trans('cruds.order.title_singular') }} {{ trans('global.list') }}</h4>
                    </div>
                    <div class="col-auto align-self-center">

                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.order.fields.number') }}</th>
                        <th>{{ trans('cruds.order.fields.customer_name') }}</th>
                        <th>{{ trans('cruds.order.fields.salesrep') }}</th>
                        <th>{{ trans('cruds.order.fields.ponumber') }}</th>
                        <th>{{ trans('cruds.order.fields.created_at') }}</th>
                        <th>{{ trans('cruds.order.fields.status') }}</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customerOrders as $key => $order)
                        <tr data-entry-id="{{ $order->id }}">
                            <td> </td>
                            <td> {{ $order->OrderNumber ?? '' }} </td>
                            <td> {{ $order->customer->CustomerName ?? '' }} </td>
                            <td> {{ $order->salesperson->PreferredName ?? '' }} </td>
                            <td> {{ $order->CustomerPurchaseOrderNumber ?? '' }}</td>
                            <td> {{ $order->created_at ?? '' }} </td>
                            <td>
                                <span class=" badge <?php if ( $order->OrderStatusID == 1 ) { echo "badge-danger"; }
                                elseif ( $order->OrderStatusID == 2) { echo "badge-warning"; }
                                elseif ( $order->OrderStatusID == 3) { echo "badge-info"; }
                                elseif ( $order->OrderStatusID == 4) { echo "badge-success"; }
                                ?>"> {{ $order->orderstatus->name ?? '' }} </span>
                            <td>
                                @can('order_show')
                                    <a href="{{ route('orders.show', $order->id) }}" target="_blank" data-toggle="tooltip"
                                       title="{{ trans('global.view') }} {{ trans('cruds.order.title_singular') }}"
                                       data-placement="top">
                                        <i class="las dripicons-preview text-info font-18"></i>
                                    </a>
                                @endcan
                                &nbsp;

                                @can('order_delete')
                                    <form action="{{ route('orders.delete', $order->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                data-toggle="tooltip" data-placement="top"
                                                title="{{ trans('global.delete') }} {{ trans('cruds.order.title_singular') }}">
                                            <i class="dripicons-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                                &nbsp;
                                @can('order_edit')
                                    <a href="{{ route('orders.download', $order->id) }}"  onClick="history.go(0)"
                                       data-toggle="tooltip" title="{{ trans('global.downloadFile') }} {{ trans('cruds.order.title_singular') }}"
                                       data-placement="top"> <i class="las dripicons-download text-info font-18"></i>
                                    </a>
                                @endcan

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
