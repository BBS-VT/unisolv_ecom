@extends('layouts.master', ['page' => 'settings'])

@section('title', __('global.company_settings'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('Settings') }}</h4>
                <div class="page-title-right"></div>
            </div>
        </div>
    </div>

    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="card filemanager-sidebar me-md-2">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-4">
                                @include('admin.settings._aside', ['tab' => 'company'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
                                @include('layouts._form_errors')
                                @csrf

                                {{-- Company Logo --}}
                                <div class="card border mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0">
                                            <i class="bx bx-image me-1"></i>
                                            {{ __('global.company_logo') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <input id="avatar" name="avatar" class="d-none" type="file" onchange="changePreview(this);" accept="image/*">
                                        <label for="avatar" class="cursor-pointer">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="avatar-lg">
                                                        <img id="file-prev"
                                                             src="{{ $currentCompany->avatar ?? asset('images/default-company-logo.png') }}"
                                                             class="rounded img-thumbnail"
                                                             style="width: 100px; height: 100px; object-fit: cover;">
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-outline-primary btn-sm">
                                                        <i class="bx bx-upload me-1"></i>
                                                        {{ __('global.choose_logo') }}
                                                    </button>
                                                    <p class="text-muted small mb-0 mt-2">
                                                        {{ __('messages.logo_requirements') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Company Information --}}
                                <div class="card border mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0">
                                            <i class="bx bx-building me-1"></i>
                                            {{ __('global.company_information') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="name" class="form-label">
                                                        {{ __('global.company_name') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input name="name" type="text"
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           placeholder="{{ __('global.company_name') }}"
                                                           value="{{ old('name', $currentCompany->name) }}"
                                                           required>
                                                    @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="billing_phone" class="form-label">
                                                        {{ __('global.phone') }}
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-phone"></i>
                                                        </span>
                                                        <input name="billing[phone]" type="text"
                                                               class="form-control @error('billing.phone') is-invalid @enderror"
                                                               value="{{ old('billing.phone', $currentCompany->billing->phone ?? '') }}"
                                                               placeholder="{{ __('global.phone') }}">
                                                        @error('billing.phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="vat_number" class="form-label">
                                                        {{ __('global.vat_number') }}
                                                    </label>
                                                    <input name="vat_number" type="text"
                                                           class="form-control @error('vat_number') is-invalid @enderror"
                                                           placeholder="{{ __('global.vat_number') }}"
                                                           value="{{ old('vat_number', $currentCompany->vat_number) }}">
                                                    @error('vat_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="billing_email" class="form-label">
                                                        {{ __('global.email') }}
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="bx bx-envelope"></i>
                                                        </span>
                                                        <input name="billing[email]" type="text"
                                                               class="form-control @error('billing.email') is-invalid @enderror"
                                                               value="{{ old('billing.email', $currentCompany->billing->email ?? '') }}"
                                                               placeholder="{{ __('global.email') }}">
                                                        @error('billing.email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Company Address --}}
                                <div class="card border mb-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0">
                                            <i class="bx bx-map me-1"></i>
                                            {{ __('global.company_address') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-3">
                                            <label for="billing_address_1" class="form-label">
                                                {{ __('global.address') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="billing[address_1]"
                                                      class="form-control @error('billing.address_1') is-invalid @enderror"
                                                      rows="2"
                                                      placeholder="{{ __('global.address') }}"
                                                      required>{{ old('billing.address_1', $currentCompany->billing->address_1 ?? '') }}</textarea>
                                            @error('billing.address_1')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="billing_city" class="form-label">
                                                        {{ __('global.city') }}
                                                    </label>
                                                    <input name="billing[city]" type="text"
                                                           class="form-control @error('billing.city') is-invalid @enderror"
                                                           value="{{ old('billing.city', $currentCompany->billing->city ?? '') }}"
                                                           placeholder="{{ __('global.city') }}">
                                                    @error('billing.city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="billing_state" class="form-label">
                                                        {{ __('global.state') }}
                                                    </label>
                                                    <input name="billing[state]" type="text"
                                                           class="form-control @error('billing.state') is-invalid @enderror"
                                                           value="{{ old('billing.state', $currentCompany->billing->state ?? '') }}"
                                                           placeholder="{{ __('global.state') }}">
                                                    @error('billing.state')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="billing_zip" class="form-label">
                                                        {{ __('global.postal_code') }}
                                                    </label>
                                                    <input name="billing[zip]" type="text"
                                                           class="form-control @error('billing.zip') is-invalid @enderror"
                                                           value="{{ old('billing.zip', $currentCompany->billing->zip ?? '') }}"
                                                           placeholder="{{ __('global.postal_code') }}">
                                                    @error('billing.zip')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="billing_country_id" class="form-label">
                                                        {{ __('global.country') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="billing[country_id]"
                                                            class="form-select @error('billing.country_id') is-invalid @enderror"
                                                            required>
                                                        <option value="" disabled {{ !old('billing.country_id', $currentCompany->billing->country_id ?? '') ? 'selected' : '' }}>
                                                            {{ __('global.select_country') }}
                                                        </option>
                                                        @foreach(get_countries_select2_array() as $option)
                                                            <option value="{{ $option['id'] }}"
                                                                {{ old('billing.country_id', $currentCompany->billing->country_id ?? '') == $option['id'] ? 'selected' : '' }}>
                                                                {{ $option['text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('billing.country_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save me-1"></i>
                                                {{ __('global.update') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function changePreview(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        document.getElementById('file-prev').src = e.target.result;
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection
