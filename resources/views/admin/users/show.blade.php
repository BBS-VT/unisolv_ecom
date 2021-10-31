@extends('layouts.app')

@push('style')

@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="row">
                    <div class="col">
                        <h4 class="page-title">{{ trans('cruds.user.title') }}</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ trans('global.home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">
                                    {{ trans('cruds.user.title') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }} </li>
                        </ol>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                            <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                            {{ trans('global.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> {{ trans('global.user_name') }}: {{ $user->FullName }} </h4>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-default" href="{{ route('admin.users.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.id') }}
                                </th>
                                <td>
                                    {{ $user->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.fullname') }}
                                </th>
                                <td>
                                    {{ $user->FullName }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.preferredname') }}
                                </th>
                                <td>
                                    {{ $user->PreferredName }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.email') }}
                                </th>
                                <td>
                                    {{ $user->email }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.phonenumber') }}
                                </th>
                                <td>
                                    {{ $user->PhoneNumber }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.salesrep') }}
                                </th>
                                <td>
                                    @if ( $user->IsSalesperson == 1)
                                        Yes
                                    @else
                                        No
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.customer') }}
                                </th>
                                <td>
                                    @if ( $user->IsCustomer == 1)
                                        Yes
                                    @else
                                        No
                                    @endif

                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.roles') }}
                                </th>
                                <td>
                                    @foreach($user->roles as $key => $roles)
                                        <span class="label label-info">{{ $roles->title }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection

@push('custom-scripts')

@endpush

