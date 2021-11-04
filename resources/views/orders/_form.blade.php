<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            @if (!auth()->user()->repcode)
                <input type="hidden" name="salesperson_id" value="{{ auth()->user()->id }}" />
            @else
                <input type="hidden" name="salesperson_id" value="{{ auth()->user()->RepCode }}" />
            @endif
                <label for="order_number">{{ __('cruds.order.fields.number') }}</label>
            <input type="text" name="order_number" class="form-control" value="{{ $order->order_number }}" readonly>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="customer">{{ __('cruds.order.fields.customer_name') }}</label>
<!--            <select id="customer" name="customer_id" data-toggle="select" class="form-control select2-hidden-accessible" data-select2-id="customer">
                <option disabled selected>{{ __('global.pleaseSelect') }}</option>
                @if($order->CustomerID)
                    <option value="{{ $order->customer_id }}"
                        selected="">
                        {{ $order->customer->CustomerName }}
                    </option>
                @endif
            </select>-->
            <select class="form-control mb-3 select2-canal {{ $errors->has('CustomerID') ? 'is-invalid' : '' }}"  name="customer_id"  required>
                @foreach($customers as $id => $customer)
                    <option value="{{ $id }}" >{{ $customer }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
