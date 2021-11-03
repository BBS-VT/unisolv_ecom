@extends('layouts.app', ['page' => 'orders'])

@section('title', __('global.orders'))

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ __('cruds.order.title_singular') }} {{ __('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center float-right">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <a href="{{ route('orders') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'new' ? 'active' : '' }}">
                                    {{ __('global.new_orders') }}
                                </a>
                                <a href="{{ route('orders', 'due') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'processed' ? 'active' : '' }}">
                                    {{ __('global.processed_orders') }}
                                </a>
                                <a href="{{ route('orders', 'all') }}" class="btn btn-sm btn-outline-dark {{ $tab == 'all' ? 'active' : '' }}">
                                    {{ __('global.all_orders') }}
                                </a>
                            </div>
                            @can('order_create')
                                <a href="{{ route("orders.create.step.one") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.new_order') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                @include('orders._filters')
                <div class="card">
<!--                    <div class="card-header bg-white p-0">
                        <div class="row no-gutters flex nav">
                            <a href="{{ route('orders') }}" class="col-2 border-right dashboard-area-tabs__tab card-body text-center {{ $tab == 'new' ? 'active' : '' }}">
                                <span class="card-header__title m-0">
                                    {{ __('global.new_orders') }}
                                </span>
                            </a>
                            <a href="{{ route('orders', 'due') }}" class="col-2 border-right dashboard-area-tabs__tab card-body text-center {{ $tab == 'processed' ? 'active' : '' }}">
                                <span class="card-header__title m-0">
                                    {{ __('global.processed_orders') }}
                                </span>
                            </a>
                            <a href="{{ route('orders', 'all') }}" class="col-2 border-right dashboard-area-tabs__tab card-body text-center {{ $tab == 'all' ? 'active' : '' }}">
                                <span class="card-header__title m-0">
                                    {{ __('global.all_orders') }}
                                </span>
                            </a>
                        </div>
                    </div>-->

                    @include('orders._table')
                </div>
            </div>
        </div>
    </div>
@endsection
