@extends('layouts.app')

@section('style')

@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                @include('flash-message')
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                @if($customer->CustomerStatus==1)
                                    <span class="badge badge-soft-success bold menu-arrow">{{ __('global.active') }}</span>
                                @else
                                    <span class="badge badge-soft-danger bold menu-arrow">{{ __('global.closed') }}</span>
                                @endif
                                &nbsp;&nbsp;
                                @if($customer->IsOnCreditHold==1)
                                    <span class="badge badge-warning">{{ __('global.credit_hold') }}</span>
                                @endif
                                &nbsp;&nbsp;
                                <h4 class="card-title">{{ __('global.account') }} &nbsp; &#x3a; &nbsp;
                                    {{ $customer->acc_main }} {{ ($customer->acc_sub == 0) ? '' : $customer->acc_sub }}</h4>
                                {{--@if(isset($customer->BillToCustomerID))
                                    <p class="text-muted mb-0">{{ __('global.accountMain') }} &nbsp; &#x3a; &nbsp;{{ $customer->billingCustomer->CustomerName }}</p>
                                @endif--}}
                            </div>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                @can('order_create')
                                    {{-- <a href="{{ route('orders.create.step.one') }}" class="btn btn-sm btn-outline-dark">{{ __('global.new_order') }}</a>--}}
                                    <a href="{{ route('orders.create') }}" class="btn btn-sm btn-outline-dark">{{ __('global.new_order') }}</a>
                                @endcan
                                @can('customer_edit')
                                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-danger">
                                        {{ __('global.edit') }}{{ __('cruds.customer.title_singular') }}
                                    </a>
                                @endcan
                            </div>
                                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                    {{ __('global.back_to_list') }}
                                </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav-border nav nav-pills mb-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#home" role="tab" aria-selected="true">{{ __('global.detail') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#contacts" role="tab" aria-selected="true">{{ __('global.contacts') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#orders" role="tab" aria-selected="true">{{ __('global.orders') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#notes" role="tab" aria-selected="true">{{ __('global.notes') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane p3 active" id="home" role="tabpanel">
                            <br>
                              @include('customers.partials._detail')
                        </div>
                        <div class="tab-pane p3 " id="contacts" role="tabpanel">
                            <p class="mb-0 text-muted">
                                <br>
                                Contacts panel
                            </p>
                        </div>
                        <div class="tab-pane p3" id="orders" role="tabpanel">
                            <br>
                            @include('customers.partials._orders')
                        </div>
                        <div class="tab-pane p3" id="notes" role="tabpanel">
                            <p class="mb-0 text-muted">
                                <br>
                                Future Notes panel
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>

    <script>
        $(document).ready(function(){
           $('.phone').inputmask('999 999 9999');
        });
    </script>

@endsection
