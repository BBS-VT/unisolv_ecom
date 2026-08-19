@extends('layouts.master')

@push('styles')
    <link href="{{ asset('/plugins/dropzone/basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />

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
                                <h4 class="page-title">{{ trans('cruds.productCategory.title') }}</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ trans('global.home') }}</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('product-categories.index') }}">
                                        {{ trans('cruds.productCategory.title') }}</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ trans('global.add') }} {{ trans('cruds.productCategory.title_singular') }} </li>
                                </ol>
                            </div>
                            <div class="col-auto align-self-center">
                                {{--<a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                    <span class="day-name" id="Day_Name">Today:</span>&nbsp;
                                    <span class="" id="Select_date">Jan 11</span>
                                    <i data-feather="calendar" class="align-self-center icon-xs ms-1"></i>
                                </a>--}}
                                @can('product_category_create')
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
                            <h4 class="card-title">{{ trans('global.add') }} {{ trans('cruds.productCategory.title_singular') }}</h4>
                            {{--<p class="text-muted mb-0">Here are examples of <code class="highlighter-rouge">.form-control</code> applied to each
                                textual HTML5 <code class="highlighter-rouge">&lt;input&gt;</code> <code class="highlighter-rouge">type</code>.
                            </p>--}}
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('product-categories.store') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="LastEditedBy" id="LastEditedBy" value="{{ auth()->user()->id }}">
                                <div class="form-group">
                                    <label class="required" for="StockGroupName">{{ trans('cruds.productCategory.fields.name') }}</label>
                                    <input class="form-control {{ $errors->has('StockGroupName') ? 'is-invalid' : '' }}" type="text" name="StockGroupName" id="StockGroupName" value="{{ old('StockGroupName', '') }}" required>
                                    @if($errors->has('StockGroupName'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('StockGroupName') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.productCategory.fields.name_helper') }}</span>
                                </div>
<!--                                <div class="form-group">
                                    <label for="description">{{ trans('cruds.productCategory.fields.description') }}</label>
                                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description') }}</textarea>
                                    @if($errors->has('description'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('description') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.productCategory.fields.description_helper') }}</span>
                                </div>-->
                                <div class="mb-3">
                                    <label for="location_id" class="form-label">{{ __('Sales Location') }}</label>
                                    <select class="form-select @error('location_id') is-invalid @enderror"
                                            id="location_id" name="location_id">
                                        <option value="">{{ __('All Locations (Available Everywhere)') }}</option>
                                        @foreach(\App\Models\Location::shopLocations()->get() as $location)
                                            <option value="{{ $location->id }}"
                                                {{ old('location_id', $category->location_id ?? '') == $location->id ? 'selected' : '' }}>
                                                {{ $location->LocationName }} {{ __('Only') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        {{ __('Leave as "All Locations" if this department should be available at every store/branch.') }}<br>
                                        {{ __('Select a specific location if this department is unique to one storefront.') }}
                                    </small>
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
    <script src="{{ asset('/plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('/pages/jquery.form-upload.init.js') }}"></script>
    <script src="{{ asset('/plugins/dropzone/dropzone.js') }}"></script>

<script>
    Dropzone.options.photoDropzone = {
        url: '{{ route('product-categories.storeMedia') }}',
        maxFileSize: 2, // MB
        acceptedFiles: '.jpeg,.jpg,.png,.gif',
        maxFiles: 1,
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        params: {
            size: 2,
            width: 4096,
            height: 4096
        },
        success: function (file, response) {
            $('form').find('input[name="photo"]').remove()
            $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
        },
        removedfile: function (file) {
            file.previewElement.remove()
            if (file.status !== 'error') {
                $('form').find('input[name="photo"]').remove()
                this.options.maxFiles = this.options.maxFiles + 1
            }
        },
        init: function () {
            @if(isset($productCategory) && $productCategory->photo)
                var file = {!! json_encode($productCategory->photo) !!}
                    this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, '{{ $productCategory->photo->getUrl('thumb') }}')
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
                this.options.maxFiles = this.options.maxFiles - 1
            @endif
        },
        error: function (file, response) {
            if ($.type(response) === 'string') {
                var message = response //dropzone sends it's own error messages in string
            } else {
                var message = response.errors.file
            }
            file.previewElement.classList.add('dz-error')
            _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
            _results = []
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                node = _ref[_i]
                _results.push(node.textContent = message)
            }
            return _results
        }
    }
</script>

@endpush
