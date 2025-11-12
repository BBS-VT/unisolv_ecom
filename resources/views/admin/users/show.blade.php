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
            <div class="col-lg-12">
            <div class="card">
            <!--      <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title"> {{ trans('global.user_name') }}: {{ $user->PreferredName }} </h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">-->
                    <div class="container emp-profile">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="profile-img">
                                        <img src="{{ asset('images/users/user-5.jpg') }}" />
                                        <div class="file btn btn-lg btn-primary">
                                            {{ __('global.change_photo') }}
                                            <input type="file" name="file" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="profile-head">
                                        <h5>{{ $user->PreferredName }}</h5>
                                        <h6>
                                            @foreach($user->roles as $key => $roles)
                                                <span class="label label-info">{{ $roles->title }}</span>
                                            @endforeach
                                        </h6>
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home"
                                                   aria-selected="true">{{ __('global.about') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile"
                                                   aria-selected="false">{{ __('global.timeline') }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.users.index') }}" class="profile-edit-btn">
                                        <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                        {{ __('global.back_to_list') }}
                                    </a><br /><br>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="profile-edit-btn mt-4">
                                        <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                        {{ __('global.edit') }} {{ __('global.profile') }}
                                    </a>
<!--                                    <input type="submit" class="profile-edit-btn mt-4" name="btnAddMore" value="{{ __('global.edit') }} {{ __('global.profile') }}" />-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="profile-work">

                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="tab-content profile-tab" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> {{ __('cruds.user.fields.id') }} </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p> {{ $user->id }} </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> {{ __('cruds.user.fields.email') }} </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p> {{ $user->email }} </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> {{ __('cruds.user.fields.phonenumber') }} </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p> {{ $user->PhoneNumber ?? '' }} </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> {{ __('cruds.user.fields.salesrep') }} </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>
                                                        @if ( $user->IsSalesperson == 1)
                                                            Yes
                                                        @else
                                                            No
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> {{ __('cruds.user.fields.repcode') }} </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p> {{ $user->RepCode }} </p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label> {{ __('cruds.user.fields.customer') }} </label>
                                                </div>
                                                <div class="col-md-6">
                                                    <p>
                                                        @if ( $user->IsCustomer == 1)
                                                            Yes
                                                        @else
                                                            No
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

<!--
                    <div class="form-group">

                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>

                                </th>
                                <td>

                                </td>
                            </tr>

                            <tr>
                                <th>

                                </th>
                                <td>

                                </td>
                            </tr>
                            <tr>
                                <th>

                                </th>
                                <td>

                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.customer') }}
                                </th>
                                <td>


                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.roles') }}
                                </th>
                                <td>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>


                </div>-->

            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')

@endpush

