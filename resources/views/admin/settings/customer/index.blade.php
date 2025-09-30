@extends('layouts.master', ['page' => 'settings'])

@section('title', __('global.customer_settings'))

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
                                @include('admin.settings._aside', ['tab' => 'customers'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">{{ __('global.customer_settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" id="customerSettingsTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab">
                                        <i class="bx bx-cog me-1"></i>
                                        {{ __('global.general') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="category-tab" data-bs-toggle="tab" href="#category" role="tab">
                                        <i class="bx bx-category me-1"></i>
                                        {{ __('global.customer_category') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="buyingGroup-tab" data-bs-toggle="tab" href="#buyingGroup" role="tab">
                                        <i class="bx bx-group me-1"></i>
                                        {{ __('global.buying_group') }}
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <!-- General Settings Tab -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <form action="{{ route('settings.customer.update') }}" method="POST">
                                        @include('layouts._form_errors')
                                        @csrf

                                        <div class="card border">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0">
                                                    <i class="bx bx-cog me-1"></i>
                                                    {{ __('global.general_settings') }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group mb-0">
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" class="form-check-input"
                                                               id="display_subaccount" name="display_subaccount"
                                                            {{ $currentCompany->getSetting('display_subaccount') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="display_subaccount">
                                                            <strong>{{ __('global.display_subaccount') }}</strong>
                                                        </label>
                                                    </div>
                                                    <small class="form-text text-muted">
                                                        {{ __('messages.display_subaccount') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bx bx-save me-1"></i>
                                                {{ __('global.update') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Customer Categories Tab -->
                                <div class="tab-pane fade" id="category" role="tabpanel">
                                    @include('admin.settings.customer.category._table')
                                </div>

                                <!-- Buying Groups Tab -->
                                <div class="tab-pane fade" id="buyingGroup" role="tabpanel">
                                    @include('admin.settings.customer.buyingGroup._table')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('admin.settings.customer.category._modals')
    @include('admin.settings.customer.buyingGroup._modals')

@endsection
