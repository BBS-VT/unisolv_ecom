@extends('layouts.app', ['page' => 'settings'])

@section('title', __('global.product_settings'))

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-2 col-sm-3">
            @include('admin.settings._aside', ['tab' => 'product'])
        </div>
        <div class="col-xl-10 col-sm-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title headings-color">{{ __('global.product_settings') }}</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col card-body bg-white">
                            <form action="{{ route('settings.product.update') }}" method="POST">
                                @include('layouts._form_errors')
                                @csrf

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="discount_per_item">{{ __('global.discount_per_item') }}</label><br>
                                            <div class="custom-control custom-checkbox custom-control-inline mr-1">
                                                <input type="checkbox" name="discount_per_item" id="discount_per_item" {{ $currentCompany->getSetting('discount_per_item') ? 'checked' : '' }}
                                                    class="custom-control-input">
                                                <label class="custom-control-label" for="discount_per_item">{{ __('global.yes') }}</label>
                                            </div>
                                            <label for="discount_per_item" class="mb-0">{{ __('global.yes') }}</label>
                                            <small class="form-text text-muted">
                                                {{ __('messages.discount_per_item') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
