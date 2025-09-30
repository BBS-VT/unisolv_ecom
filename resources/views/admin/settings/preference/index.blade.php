@extends('layouts.master', ['page' => 'settings'])

@section('title', __('global.preferences'))

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
                                @include('admin.settings._aside', ['tab' => 'preferences'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('settings.preferences.update') }}" method="POST">
                                @include('layouts._form_errors')
                                @csrf

                                {{-- Regional Settings --}}
                                <div class="card border">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0">
                                            <i class="bx bx-globe me-1"></i>
                                            {{ __('global.regional_settings') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="currency_id" class="form-label">
                                                        {{ __('global.main_currency') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="currency_id" id="currency_id"
                                                            class="form-select @error('currency_id') is-invalid @enderror"
                                                            required>
                                                        <option value="" disabled {{ !$currentCompany->getSetting('currency_id') ? 'selected' : '' }}>
                                                            {{ __('global.select_currency') }}
                                                        </option>
                                                        @foreach(get_currencies_select2_array() as $option)
                                                            <option value="{{ $option['id'] }}"
                                                                {{ $currentCompany->getSetting('currency_id') == $option['id'] ? 'selected' : '' }}>
                                                                {{ $option['text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('currency_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">{{ __('messages.main_currency_help') }}</small>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="language" class="form-label">
                                                        {{ __('global.language') }}
                                                    </label>
                                                    <select id="language" name="language"
                                                            class="form-select @error('language') is-invalid @enderror">
                                                        <option value="" disabled {{ !$currentCompany->getSetting('language') ? 'selected' : '' }}>
                                                            {{ __('global.pleaseSelect') }}
                                                        </option>
                                                        @foreach(get_languages_select2_array() as $language)
                                                            <option value="{{ $language['id'] }}"
                                                                {{ $currentCompany->getSetting('language') == $language['id'] ? 'selected' : '' }}>
                                                                {{ $language['text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('language')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">{{ __('messages.language_help') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Date & Time Settings --}}
                                <div class="card border mt-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0">
                                            <i class="bx bx-calendar me-1"></i>
                                            {{ __('global.date_time_settings') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="timezone" class="form-label">
                                                        {{ __('global.timezone') }}
                                                    </label>
                                                    <select id="timezone" name="timezone"
                                                            class="form-select @error('timezone') is-invalid @enderror">
                                                        <option value="" disabled {{ !$currentCompany->getSetting('timezone') ? 'selected' : '' }}>
                                                            {{ __('global.pleaseSelect') }}
                                                        </option>
                                                        @foreach(get_timezones_select2_array() as $timezone)
                                                            <option value="{{ $timezone['id'] }}"
                                                                {{ $currentCompany->getSetting('timezone') == $timezone['id'] ? 'selected' : '' }}>
                                                                {{ $timezone['text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('timezone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">{{ __('messages.timezone_help') }}</small>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="date_format" class="form-label">
                                                        {{ __('global.date_format') }}
                                                    </label>
                                                    <select id="date_format" name="date_format"
                                                            class="form-select @error('date_format') is-invalid @enderror">
                                                        <option value="" disabled {{ !$currentCompany->getSetting('date_format') ? 'selected' : '' }}>
                                                            {{ __('global.pleaseSelect') }}
                                                        </option>
                                                        @foreach(get_date_formats_select2_array() as $date_format)
                                                            <option value="{{ $date_format['id'] }}"
                                                                {{ $currentCompany->getSetting('date_format') == $date_format['id'] ? 'selected' : '' }}>
                                                                {{ $date_format['text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('date_format')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">{{ __('messages.date_format_help') }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Financial Year Settings --}}
                                <div class="card border mt-3">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0">
                                            <i class="bx bx-chart me-1"></i>
                                            {{ __('global.financial_year') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="financial_month_starts" class="form-label">
                                                        {{ __('global.year_starts') }}
                                                        <i class="bx bx-info-circle text-muted"
                                                           data-bs-toggle="tooltip"
                                                           title="{{ __('messages.financial_year_starts') }}"></i>
                                                    </label>
                                                    <select id="financial_month_starts" name="financial_month_starts"
                                                            class="form-select @error('financial_month_starts') is-invalid @enderror">
                                                        <option value="" disabled {{ !$currentCompany->getSetting('financial_month_starts') ? 'selected' : '' }}>
                                                            {{ __('global.pleaseSelect') }}
                                                        </option>
                                                        @foreach(get_months_select2_array() as $month)
                                                            <option value="{{ $month['id'] }}"
                                                                {{ $currentCompany->getSetting('financial_month_starts') == $month['id'] ? 'selected' : '' }}>
                                                                {{ $month['text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('financial_month_starts')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="financial_month_ends" class="form-label">
                                                        {{ __('global.year_ends') }}
                                                        <i class="bx bx-info-circle text-muted"
                                                           data-bs-toggle="tooltip"
                                                           title="{{ __('messages.financial_year_ends') }}"></i>
                                                    </label>
                                                    <select id="financial_month_ends" name="financial_month_ends"
                                                            class="form-select @error('financial_month_ends') is-invalid @enderror">
                                                        <option value="" disabled {{ !$currentCompany->getSetting('financial_month_ends') ? 'selected' : '' }}>
                                                            {{ __('global.pleaseSelect') }}
                                                        </option>
                                                        @foreach(get_months_select2_array() as $month)
                                                            <option value="{{ $month['id'] }}"
                                                                {{ $currentCompany->getSetting('financial_month_ends') == $month['id'] ? 'selected' : '' }}>
                                                                {{ $month['text'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('financial_month_ends')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save me-1"></i>
                                                {{ __('global.save') }}
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
            // Initialize Bootstrap tooltips
            document.addEventListener('DOMContentLoaded', function() {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        </script>
    @endpush
@endsection
