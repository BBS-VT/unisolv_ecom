<span class="badge bg-{{ $order->orderstatus->colour ?? 'secondary' }}">{{ $order->orderstatus->name ?? '' }}</span>
@if($order->Authorisation == 1)
    <span class="badge bg-warning text-dark">On Hold</span>
@elseif($order->Authorisation == 2)
    <span class="badge bg-info">Released</span>
@endif
