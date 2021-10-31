@extends('layouts.app')

@push('style')
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ trans('global.add') }} {{ trans('cruds.user.title_singular') }} </h4>
                            <p class="text-muted mb-0">Required fields indicated with a <code class="highlighter-rouge">*</code> </p>
                        </div>
                        <div class="col-auto align-self-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>

                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="FullName">{{ trans('cruds.user.fields.fullname') }} <span style="color:darkred";>*</span></label>
                                    <input class="form-control {{ $errors->has('FullName') ? 'is-invalid' : '' }}" type="text" name="FullName" id="FullName" value="{{ old('FullName', '') }}" required>
                                    @if($errors->has('FullName'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('FullName') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.user.fields.fullname_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required" for="PreferredName">{{ trans('cruds.user.fields.preferredname') }} <span style="color:darkred";>*</span></label>
                                    <input class="form-control {{ $errors->has('PreferredName') ? 'is-invalid' : '' }}" type="text" name="PreferredName" id="PreferredName" value="{{ old('PreferredName', '') }}" required>
                                    @if($errors->has('PreferredName'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('PreferredName') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.user.fields.preferredname_helper') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="required" for="email">{{ trans('cruds.user.fields.email') }}</label>
                            <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email" name="email" id="email" value="{{ old('email') }}" required>
                            @if($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.email_helper') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="required" for="password">{{ trans('cruds.user.fields.password') }}</label>
                            <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
                            @if($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.user.fields.password_helper') }}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="PhoneNumber">{{ trans('cruds.user.fields.phonenumber') }} </label>
                                    <input class="form-control {{ $errors->has('PhoneNumber') ? 'is-invalid' : '' }}" type="text" name="PhoneNumber"
                                           id="PhoneNumber" value="{{ old('PhoneNumber', '') }}" >
                                    @if($errors->has('PhoneNumber'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('PhoneNumber') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.user.fields.phonenumber_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="RepCode">{{ trans('cruds.user.fields.repcode') }} </label>
                                    <input class="form-control {{ $errors->has('RepCode') ? 'is-invalid' : '' }}" type="text" name="RepCode"
                                           id="RepCode" value="{{ old('RepCode', '') }}" >
                                    @if($errors->has('RepCode'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('RepCode') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.user.fields.repcode_helper') }}</span>
                                </div>

                            </div>
                            <div class="col-md-2">
                                <div class="form-check-inline my-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="IsSalesperson[]" id="IsSalesperson" data-parsley-multiple="groups" data-parsley-mincheck="2">
                                        <label class="custom-control-label" for="IsSalesperson">{{ trans('cruds.user.fields.salesrep') }}</label>
                                    </div>
                                </div>
                                <div class="form-check-inline my-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name=IsCustomer[]" id="IsCustomer" data-parsley-multiple="groups" data-parsley-mincheck="2">
                                        <label class="custom-control-label" for="IsCustomer">{{ trans('cruds.user.fields.customer') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="required" for="roles">{{ trans('cruds.user.fields.roles') }}</label>

                                <select class="select2 mb-3 select2-multiple {{ $errors->has('roles') ? 'is-invalid' : '' }}" name="roles[]" id="roles" multiple="multiple" data-placeholder="Please Choose" required>
                                    @foreach($roles as $id => $roles)
                                        <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $roles }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('roles'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('roles') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.user.fields.roles_helper') }}</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger" type="submit">
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>

    <script src="{{ URL::asset('pages/jquery.forms-advanced.js') }}"></script>
@endpush
