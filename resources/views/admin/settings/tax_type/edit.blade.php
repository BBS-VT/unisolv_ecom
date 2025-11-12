@extends('layouts.app', ['page' => 'settings'])

@section('title', __('global.edit'))

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-2 col-sm-3">
            @include('admin.settings._aside', ['tab' => 'tax_types'])
        </div>
        <div class="col-xl-10 col-sm-9">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ __('global.edit') }} {{ __('cruds.taxType.title') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <form action="{{ route('settings.tax_types.update', $tax_type->id) }}" method="POST">
                        @include('layouts._form_errors')
                        @csrf

                        @include('admin.settings.tax_type._form')

                        <div class="form-group text-end mt-4">
                            <button type="submit" class="btn btn-danger">{{ __('global.save') }}</button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ trans('global.cancel') }}</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
