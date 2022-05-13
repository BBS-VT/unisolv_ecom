@extends('layouts.app', ['page' => 'settings'])

@section('title', __('global.customerSettings'))

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-2 col-sm-3">
            @include('admin.settings._aside', ['tab' => 'customers'])
        </div>
        <div class="col-xl-10 col-sm-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ __('global.customer_categories') }}</h4>
                        </div>
                        <div class="col-auto align-self-center float-right">
                            @can('settings_create')
                                <a href="{{ route('admin.customer-category.create') }}" class="btn btn-sm btn-gradient-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }}&nbsp;{{ trans('global.customer_category') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="row no-gutters">
                    <div class="col card-body bg-white">

                        @include('admin.settings.customer.category._table')

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

