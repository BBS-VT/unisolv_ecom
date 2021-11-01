@extends('layouts.app', ['page' => 'settings'])

@section('title', __('cruds.taxType.title'))

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
                        <div class="form-row align-items-center mb-4">
                            <div class="col">
                                <p class="h4 mb-0">
                                    <strong class="headings-color">{{ __('cruds.taxType.title') }}</strong>
                                </p>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('settings.tax_types.create') }}" class="btn btn-xs btn-primary text-white">
                                    {{ __('global.add') }} {{ __('cruds.taxType.title') }}
                                </a>
                            </div>
                        </div>

                        @if($tax_types->count() > 0)
                            <div class="table-responsive" data-toggle="lists">
                                <table class="table table-xl mb-0 table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('cruds.taxType.fields.name') }}</th>
                                            <th>{{ __('cruds.taxType.fields.percent') }}</th>
                                            <th class="w-30">{{ __('global.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list" id="tax_types">
                                        @foreach($tax_types as $tax_type)
                                            <tr>
                                                <td class="h6">
                                                    <a href="{{ route('settings.tax_types.edit', $tax_type->id) }}">
                                                        <string class="h6">
                                                            {{ $tax_type->name }}
                                                        </string>
                                                    </a>
                                                </td>
                                                <td class="h6">
                                                    {{ $tax_type->percent }}
                                                </td>
                                                <td class="h6">
                                                    <a href="{{ route('settings.tax_types.edit', $tax_type->id) }}" class="btn text-primary">
                                                        <i class="feather-edit"></i>
                                                        {{ __('global.edit') }}
                                                    </a>
                                                    <a href="{{ route('settings.tax_types.delete', $tax_type->id) }}" class="btn text-danger delete-confirm">
                                                        <i class="feather-delete"></i>
                                                        {{ __('global.delete') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row card-body pagination justify-content-center text-center">
                                {{ $tax_types->links() }}
                            </div>
                        @else
                            <div class="row justify-content-center card-body pb-0 pt-5">
                                <i class="typcn typcn-clipboard fs-64px"></i>
                            </div>
                            <div class="row justify-content-center card-body pb-5">
                                <p class="h4">{{ __('global.no_tax_types_yet') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
