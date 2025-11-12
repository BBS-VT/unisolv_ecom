@extends('layouts.app')

@push('style')

@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ $customer->CustomerName }}</h4>
                            @if(isset($customer->BillToCustomerID))
                                <p class="text-muted mb-0">Main Account: {{ $customer->billingCustomer->CustomerName }}</p>
                            @endif
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                @can('order_create')
                                    <a href="{{ route('orders.create.step.one') }}" class="btn btn-sm btn-outline-dark">{{ trans('global.new_order') }}</a>
                                @endcan
                                @can('customer_edit')
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-danger">
                                        {{ trans('global.edit') }}{{ trans('cruds.customer.title_singular') }}
                                    </a>
                                @endcan
                            </div>
                                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.back_to_list') }}
                                </a>
                        </div>
                    </div>


                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab" aria-selected="true">{{ trans('global.detail') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#contacts" role="tab" aria-selected="true">{{ trans('global.contacts') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#orders" role="tab" aria-selected="true">{{ trans('global.orders') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane p3 active" id="home" role="tabpanel">
                            <br>
                            @include('customers.partials._detail')
                        </div>
                        <div class="tab-pane p3 " id="contacts" role="tabpanel">
                            <p class="mb-0 text-muted">
                                Contacts panel
                            </p>
                        </div>
                        <div class="tab-pane p3" id="orders" role="tabpanel">
                            <br>
                            @include('customers.partials._orders')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')


@endpush
