@extends('layouts.app', ['page' => 'dashboard'])

@section('title', __('Customer Dashboard'))

@section('style')

@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col">
                            <h4 class="page-title">{{ __('My Account') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        {{ __('Customer Information') }}
                    </div>
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
            <div class="col-lg-9">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-3">
                        <div class="card report-card">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <p class="text-dark mb-0 fw-semibold">{{ __('Current') }}</p>
                                        @if($customerBalance)
                                            <h3 class="m-0">R {{ number_format($customerBalance->AgedBalance1, 2) }}</h3>
                                        @else
                                            <p>No balance information found.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card report-card">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <p class="text-dark mb-0 fw-semibold">{{ __('30 Days') }}</p>
                                        @if($customerBalance)
                                            <h3 class="m-0">R {{ number_format($customerBalance->AgedBalance2, 2) }}</h3>
                                        @else
                                            <p>No balance information found.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card report-card">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <p class="text-dark mb-0 fw-semibold">{{ __('60 Days') }}</p>
                                        @if($customerBalance)
                                            <h3 class="m-0">R {{ number_format($customerBalance->AgedBalance3, 2) }}</h3>
                                        @else
                                            <p>No balance information found.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card report-card">
                            <div class="card-body">
                                <div class="row d-flex justify-content-center">
                                    <div class="col">
                                        <p class="text-dark mb-0 fw-semibold">{{ __('90 Days') }}</p>
                                        @if($customerBalance)
                                            <h3 class="m-0">R {{ number_format($customerBalance->AgedBalance4, 2) }}</h3>
                                        @else
                                            <p>No balance information found.</p>
                                        @endif
                                    </div>
                                </div>
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
