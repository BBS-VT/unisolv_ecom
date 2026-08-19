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
                                    <li class="breadcrumb-item active">{{ trans('global.edit') }} {{ trans('cruds.productCategory.title_singular') }} </li>
                                </ol>
                            </div>
                            <div class="col-auto align-self-center">

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
                            <h4 class="card-title">{{ trans('global.edit') }} {{ trans('cruds.productCategory.title_singular') }}</h4>
                            {{--<p class="text-muted mb-0">Here are examples of <code class="highlighter-rouge">.form-control</code> applied to each
                                textual HTML5 <code class="highlighter-rouge">&lt;input&gt;</code> <code class="highlighter-rouge">type</code>.
                            </p>--}}
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route("product-categories.update", [$productCategory->id]) }}" enctype="multipart/form-data">
                                @method('PUT')
                                @csrf
                                <div class="form-group">
                                    <label class="required" for="name">{{ trans('cruds.productCategory.fields.name') }}</label>
                                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $productCategory->name) }}" required>
                                    @if($errors->has('name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('name') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.productCategory.fields.name_helper') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="description">{{ trans('cruds.productCategory.fields.description') }}</label>
                                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', $productCategory->description) }}</textarea>
                                    @if($errors->has('description'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('description') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.productCategory.fields.description_helper') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="photo">{{ trans('cruds.productCategory.fields.photo') }}</label>
                                    <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}" id="photo-dropzone">
                                    </div>
                                    @if($errors->has('photo'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('photo') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.productCategory.fields.photo_helper') }}</span>
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
            maxFilesize: 2, // MB
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
