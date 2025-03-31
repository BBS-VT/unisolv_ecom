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
                            <h4 class="card-title">{{ __('global.customer_settings') }}</h4>
                        </div>
                        <div class="col-auto align-self-center float-right">

                        </div>
                    </div>
                </div>

                <div class="row no-gutters">
                    <div class="col card-body bg-white">
                        <ul class="nav nav-tabs" id="nav-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" role="tab" href="#general" aria-selected="true">{{ __('global.general') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" role="tab" href="#category" aria-selected="true">{{ __('global.customer_category') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" role="tab" href="#buyingGroup" aria-selected="true">{{ __('global.buying_group') }}</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane p3 active" id="general" role="tabpanel">
                                <div class="col card-body bg-white">
                                    <form action="{{ route('settings.customer.update') }}" method="POST">
                                        @include('layouts._form_errors')
                                        @csrf
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="display_subaccount">{{ __('global.display_subaccount') }}</label><br>
                                                    <div class="custom-control custom-checkbox custom-control-inline mr-1">
                                                        <input type="checkbox" name="display_subaccount" id="display_subaccount" {{ $currentCompany->getSetting('display_subaccount') ? 'checked' : '' }}
                                                        class="custom-control-input">
                                                        <label class="custom-control-label" for="display_subaccount">{{ __('global.yes') }}</label>
                                                    </div>
                                                    <label for="display_subaccount" class="mb-0">{{ __('global.yes') }}</label>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.display_subaccount') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group text-right">
                                            <button type="submit" class="btn btn-danger">{{ __('global.update') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane p3" id="category" role="tabpanel">
                                <div class="card">
                                    @include('admin.settings.customer.category._table')
                                </div>
                            </div>
                            <div class="tab-pane p3" id="buyingGroup" role="tabpanel">
                                <div class="card">
                                    @include('admin.settings.customer.buyingGroup._table')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('#nav-tab a[href="#{{ old('tab') }}"]').tab('show')
        });
    </script>
@endsection

