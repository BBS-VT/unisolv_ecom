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
            @include('admin.settings._aside', ['tab' => 'company'])
        </div>
        <div class="col-xl-10 col-sm-9">
            <div class="card">
                <div class="row">
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
                                        <label for="name" required>{{ __('global.company_name') }}</label>
                                        <input name="name" type="text" class="form-control" placeholder="{{ __('global.company_name') }}"
                                               value="{{ $currentCompany->name }}" >
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="billing[phone]">{{ __('global.phone') }}</label>
                                        <input name="billing[phone]" type="text" class="form-control" value="{{ $currentCompany->billing->phone }}" placeholder="{{ __('global.phone') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group required">
                                        <label for="billing[country_id]">{{ __('global.country') }}</label>
                                        <select id="billing[country_id]" name="billing[country_id]" data-toggle="select" class="form-control select2-hidden-accessible"
                                                data-select2-id="billing[country_id]" required>
                                            <option disabled selected>{{ __('global.select_country') }}</option>
                                            @foreach(get_countries_select2_array() as $option)
                                                <option value="{{ $option['id'] }}" {{ $currentCompany->billing->country_id == $option['id'] ? 'selected=""' : '' }}>
                                                    {{ $option['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="billing[state]">{{ __('global.state') }}</label>
                                        <input for="billing[state]" type="text" class="form-control" value="{{ $currentCompany->billing->state }}" placeholder="{{ __('global.state') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="billing[city]">{{ __('global.city') }}</label>
                                        <input name="billing[city]" type="text" class="form-control" value="{{ $currentCompany->billing->city }}" placeholder="{{ __('global.city') }}">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="billing[zip]">{{ __('global.postal_code') }}</label>
                                        <input name="billing[zip]" type="text" class="form-control" value="{{ $currentCompany->billing->zip }}" placeholder="{{ __('global.postal_code') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group required">
                                <label for="billing[address_1]">{{ __('global.address') }}</label>
                                <textarea name="billing[address_1]" class="form-control" rows="2" placeholder="{{ __('global.address') }}" required>{{ $currentCompany->billing->address_1 }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="vat_number">{{ __('global.vat_number') }}</label>
                                        <input name="vat_number" type="text" class="form-control" placeholder="{{ __('global.vat_number') }}" value="{{ $currentCompany->vat_number }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-right mt-4">
                                <button type="submit" class="btn btn-danger">{{ __('global.update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
