@extends('layouts.app', ['pages' => 'settings'])

@section('title', __('global.company_settings'))

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
                <div class="row no-gutters">
                    <div class="col card-body bg-white">
                        <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                            @include('layouts._form_errors')
                            @csrf

                            <div class="form-group">
                                <label>{{ __('global.company_logo') }}</label><br>
                                <input id="avatar" name="avatar" class="d-none" type="file" onchange="changePreview(this);">
                                <label for="avatar">
                                    <div class="media align-items-center">
                                        <div class="mr-3">
                                            <div class="avatar avatar-md">
                                                <img id="file-prev" src="{{ $currentCompany->avatar ?? '' }}" class="avatar-badge rounded">
                                            </div>
                                        </div>
                                        <div class="media-body">
                                            <a class="btn btn-sm btn-light choose-button">{{ __('global.choose_logo') }}</a>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group required">
                                        <label for="name">{{ __('global.company_name') }}</label>
                                        <input name="name" type="text" class="form-control" placeholder="{{ __('global.company_name') }}"
                                               value="{{ $currentCompany->name }}" required>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="billing[phone]">{{ __('global.phone') }}</label>
                                        <input name="billing[phone]" type="text" class="form-control" value="{{ $currentCompany->billing->phone }}" placeholder="{{ __('global.phone') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
