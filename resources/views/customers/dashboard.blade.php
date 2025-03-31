@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Customer Information') }}</div>
                    <div class="card-body">
                        @if($customer)
                            <p><strong>Customer Name:</strong> {{ $customer->CustomerName }}</p>
                            <p><strong>Account Code:</strong> {{ $customer->acc_code }}</p>
                            <p><strong>Delivery Address:</strong> {{ $customer->DeliveryAddressLine1 }}, {{ $customer->DeliveryCity }}, {{ $customer->DeliveryPostalCode }}</p>
                        @else
                            <p>No customer data found.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">{{ __('Outstanding Balance') }}</div>
                    <div class="card-body">
                        @if($customerBalance)
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Aged Balance 1:</strong> <span class="{{ $customerBalance->AgedBalance1 < 0 ? 'text-danger' : '' }}">R {{ number_format($customerBalance->AgedBalance1, 2) }}</span></p>
                                    <p><strong>Aged Balance 2:</strong> <span class="{{ $customerBalance->AgedBalance2 < 0 ? 'text-danger' : '' }}">R {{ number_format($customerBalance->AgedBalance2, 2) }}</span></p>
                                    <p><strong>Aged Balance 3:</strong> <span class="{{ $customerBalance->AgedBalance3 < 0 ? 'text-danger' : '' }}">R {{ number_format($customerBalance->AgedBalance3, 2) }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Aged Balance 4:</strong> <span class="{{ $customerBalance->AgedBalance4 < 0 ? 'text-danger' : '' }}">R {{ number_format($customerBalance->AgedBalance4, 2) }}</span></p>
                                    <p><strong>Aged Balance 5:</strong> <span class="{{ $customerBalance->AgedBalance5 < 0 ? 'text-danger' : '' }}">R {{ number_format($customerBalance->AgedBalance5, 2) }}</span></p>
                                    <p><strong>Aged Balance 6:</strong> <span class="{{ $customerBalance->AgedBalance6 < 0 ? 'text-danger' : '' }}">R {{ number_format($customerBalance->AgedBalance6, 2) }}</span></p>
                                </div>
                            </div>
                        @else
                            <p>No balance information found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Recent Orders') }}</div>
                    <div class="card-body">
                        @if($orders->count() > 0)
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Order Date</th>
                                    <th>Total</th>
                                    <th>Order Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->OrderNumber }}</td>
                                        <td>{{ Carbon\Carbon::parse($order->OrderDate)->format('Y-m-d') }}</td>
                                        <td>R {{ number_format(($order->total/100), 2) }}</td>
                                        <td>{{ $order->orderStatus->name ?? 'Unknown' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No recent orders found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Order History') }}</div>
                    <div class="card-body">
                        @if($allOrders->count() > 0)
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Order Date</th>
                                    <th>Total</th>
                                    <th>Order Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($allOrders as $order)
                                    <tr>
                                        <td>{{ $order->OrderNumber }}</td>
                                        <td>{{ Carbon\Carbon::parse($order->OrderDate)->format('Y-m-d') }}</td>
                                        <td>R {{ number_format(($order->total/100), 2) }}</td>
                                        <td>{{ $order->orderStatus->name ?? 'Unknown' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $allOrders->links() }}
                        @else
                            <p>No order history found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
