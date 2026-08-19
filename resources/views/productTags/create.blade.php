@extends('layouts.master')

@push('styles')

@endpush

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="row">
                            <div class="col">
                                <h4 class="page-title">{{ trans('cruds.productTag.title') }}</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ trans('global.home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('product-tags.index') }}">
                                            {{ trans('cruds.productTag.title') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ trans('global.add') }} {{ trans('cruds.productTag.title_singular') }} </li>
                                </ol>
                            </div>
                            <div class="col-auto align-self-center">
                                {{--<a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                    <span class="day-name" id="Day_Name">Today:</span>&nbsp;
                                    <span class="" id="Select_date">Jan 11</span>
                                    <i data-feather="calendar" class="align-self-center icon-xs ms-1"></i>
                                </a>--}}
                                @can('product_tag_create')
                                    <a href="{{ URL::previous() }}" class="btn btn-sm btn-outline-primary">
                                        <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                                        {{ trans('global.back_to_list') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ trans('global.add') }} {{ trans('cruds.productTag.title_singular') }}</h4>
                            {{--<p class="text-muted mb-0">Here are examples of <code class="highlighter-rouge">.form-control</code> applied to each
                                textual HTML5 <code class="highlighter-rouge">&lt;input&gt;</code> <code class="highlighter-rouge">type</code>.
                            </p>--}}
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route("product-tags.store") }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label class="required" for="name">{{ trans('cruds.productTag.fields.name') }}</label>
                                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                                    @if($errors->has('name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.productTag.fields.name_helper') }}</span>
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

        </div>
    </div>


@endsection

@push('scripts')


@endpush
