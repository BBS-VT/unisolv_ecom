<div class="customer-info">
    <span class="customer-name">{{ $customer->CustomerName }}</span>
    @if($customer->DeliveryAddressLine1)
        <span class="customer-address">
            {{ $customer->DeliveryAddressLine1 }}
            @if($customer->DeliveryAddressLine2), {{ $customer->DeliveryAddressLine2 }}@endif
        </span>
    @endif
</div>
