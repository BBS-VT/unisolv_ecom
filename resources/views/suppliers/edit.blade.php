@extends('layouts.master')

@section('title', 'Edit Supplier')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                    <li class="breadcrumb-item active">Edit Supplier</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ __('Update Supplier') }}</h1>
        </div>

        <form action="{{ route('suppliers.store') }}" method="POST">
            @method('PATCH')
            @csrf

            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="mdi mdi-information me-2"></i>Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="SupplierName"
                                           class="form-control @error('SupplierName') is-invalid @enderror"
                                           value="{{ old('SupplierName') }}"
                                           required>
                                    @error('SupplierName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="checkbox"
                                               name="Status"
                                               class="form-check-input"
                                               id="statusSwitch"
                                               value="1"
                                            {{ old('Status', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statusSwitch">Active</label>
                                    </div>
                                </div>

                                <!-- Backward Compatibility Fields -->
                                <div class="col-md-6">
                                    <label class="form-label">Account Main (Legacy)</label>
                                    <input type="text"
                                           name="acc_main"
                                           class="form-control @error('acc_main') is-invalid @enderror"
                                           value="{{ old('acc_main') }}"
                                           maxlength="11"
                                           placeholder="For ERP compatibility">
                                    @error('acc_main')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Leave blank to auto-generate</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Account Sub (Legacy)</label>
                                    <input type="text"
                                           name="acc_sub"
                                           class="form-control @error('acc_sub') is-invalid @enderror"
                                           value="{{ old('acc_sub', '000') }}"
                                           maxlength="3">
                                    @error('acc_sub')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">VAT Number</label>
                                    <input type="text"
                                           name="VatNr"
                                           class="form-control @error('VatNr') is-invalid @enderror"
                                           value="{{ old('VatNr') }}">
                                    @error('VatNr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tax Reference</label>
                                    <input type="text"
                                           name="tax_reference"
                                           class="form-control @error('tax_reference') is-invalid @enderror"
                                           value="{{ old('tax_reference') }}">
                                    @error('tax_reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="mdi mdi-phone me-2"></i>Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text"
                                           name="PhoneNumber"
                                           class="form-control @error('PhoneNumber') is-invalid @enderror"
                                           value="{{ old('PhoneNumber') }}">
                                    @error('PhoneNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Fax Number</label>
                                    <input type="text"
                                           name="FaxNumber"
                                           class="form-control @error('FaxNumber') is-invalid @enderror"
                                           value="{{ old('FaxNumber') }}">
                                    @error('FaxNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email"
                                           name="GeneralEmailAddress"
                                           class="form-control @error('GeneralEmailAddress') is-invalid @enderror"
                                           value="{{ old('GeneralEmailAddress') }}">
                                    @error('GeneralEmailAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Website URL</label>
                                    <input type="url"
                                           name="WebsiteURL"
                                           class="form-control @error('WebsiteURL') is-invalid @enderror"
                                           value="{{ old('WebsiteURL') }}"
                                           placeholder="https://example.com">
                                    @error('WebsiteURL')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="mdi mdi-map-marker me-2"></i>Addresses</h5>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#deliveryAddress">
                                        Delivery Address
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#postalAddress">
                                        Postal Address
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!-- Delivery Address -->
                                <div class="tab-pane fade show active" id="deliveryAddress">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Address Line 1</label>
                                            <input type="text" name="delivery_address_line1" class="form-control" value="{{ old('delivery_address_line1') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Address Line 2</label>
                                            <input type="text" name="delivery_address_line2" class="form-control" value="{{ old('delivery_address_line2') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" name="delivery_city" class="form-control" value="{{ old('delivery_city') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Province</label>
                                            <input type="text" name="delivery_province" class="form-control" value="{{ old('delivery_province') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="delivery_postal_code" class="form-control" value="{{ old('delivery_postal_code') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Postal Address -->
                                <div class="tab-pane fade" id="postalAddress">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyDeliveryAddress()">
                                            <i class="mdi mdi-content-copy me-1"></i> Copy from Delivery Address
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Address Line 1</label>
                                            <input type="text" name="postal_address_line1" class="form-control" value="{{ old('postal_address_line1') }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Address Line 2</label>
                                            <input type="text" name="postal_address_line2" class="form-control" value="{{ old('postal_address_line2') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">City</label>
                                            <input type="text" name="postal_city" class="form-control" value="{{ old('postal_city') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Province</label>
                                            <input type="text" name="postal_province" class="form-control" value="{{ old('postal_province') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="postal_postal_code" class="form-control" value="{{ old('postal_postal_code') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Financial Terms -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="mdi mdi-currency-usd me-2"></i>Financial Terms</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Credit Limit (R)</label>
                                <input type="number"
                                       name="CreditLimit"
                                       class="form-control @error('CreditLimit') is-invalid @enderror"
                                       value="{{ old('CreditLimit', 0) }}"
                                       step="0.01"
                                       min="0">
                                @error('CreditLimit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">0 = No limit</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Days</label>
                                <input type="number"
                                       name="PaymentDays"
                                       class="form-control @error('PaymentDays') is-invalid @enderror"
                                       value="{{ old('PaymentDays', 30) }}"
                                       min="0">
                                @error('PaymentDays')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Payment Terms</label>
                                <input type="text"
                                       name="payment_terms"
                                       class="form-control @error('payment_terms') is-invalid @enderror"
                                       value="{{ old('payment_terms') }}"
                                       placeholder="e.g., Net 30, 2/10 Net 30">
                                @error('payment_terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Standard Discount (%)</label>
                                <input type="number"
                                       name="StandardDiscountPercentage"
                                       class="form-control @error('StandardDiscountPercentage') is-invalid @enderror"
                                       value="{{ old('StandardDiscountPercentage', 0) }}"
                                       step="0.01"
                                       min="0"
                                       max="100">
                                @error('StandardDiscountPercentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <select name="currency_id" class="form-select @error('currency_id') is-invalid @enderror">
                                    <option value="">Select Currency</option>
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->code }} - {{ $currency->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Account Opened Date</label>
                                <input type="date"
                                       name="AccountOpenedDate"
                                       class="form-control @error('AccountOpenedDate') is-invalid @enderror"
                                       value="{{ old('AccountOpenedDate', date('Y-m-d')) }}">
                                @error('AccountOpenedDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check">
                                <input type="checkbox"
                                       name="IsOnCreditHold"
                                       class="form-check-input"
                                       id="creditHoldSwitch"
                                       value="1"
                                    {{ old('IsOnCreditHold') ? 'checked' : '' }}>
                                <label class="form-check-label" for="creditHoldSwitch">
                                    Place on Credit Hold
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Banking Details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="mdi mdi-bank me-2"></i>Banking Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text"
                                       name="bank_name"
                                       class="form-control @error('bank_name') is-invalid @enderror"
                                       value="{{ old('bank_name') }}">
                                @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Account Number</label>
                                <input type="text"
                                       name="bank_account"
                                       class="form-control @error('bank_account') is-invalid @enderror"
                                       value="{{ old('bank_account') }}">
                                @error('bank_account')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Branch Code</label>
                                <input type="text"
                                       name="bank_branch"
                                       class="form-control @error('bank_branch') is-invalid @enderror"
                                       value="{{ old('bank_branch') }}">
                                @error('bank_branch')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Operational Details -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="mdi mdi-cog me-2"></i>Operational Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Lead Time (Days)</label>
                                <input type="number"
                                       name="lead_time_days"
                                       class="form-control @error('lead_time_days') is-invalid @enderror"
                                       value="{{ old('lead_time_days', 7) }}"
                                       min="0">
                                @error('lead_time_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Minimum Order Value (R)</label>
                                <input type="number"
                                       name="minimum_order_value"
                                       class="form-control @error('minimum_order_value') is-invalid @enderror"
                                       value="{{ old('minimum_order_value') }}"
                                       step="0.01"
                                       min="0">
                                @error('minimum_order_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select name="CountryID" class="form-select @error('CountryID') is-invalid @enderror">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('CountryID') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('CountryID')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label">Internal Notes</label>
                                <textarea name="notes"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Create Supplier
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        :root {
            --primary-color: #005F84;
            --primary-hover: #004a68;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
    </style>
@endsection

@push('scripts')
    <script>
        /*function copyDeliveryAddress() {
            $('input[name="postal_address_line1"]').val($('input[name="delivery_address_line1"]').val());
            $('input[name="postal_address_line2"]').val($('input[name="delivery_address_line2"]').val());
            $('input[name="postal_city"]').val($('input[name="delivery_city"]').val());
            $('input[name="postal_province"]').val($('input[name="delivery_province"]').val());
            $('input[name="postal_postal_code"]').val($('input[name="delivery_postal_code"]').val());

            toastr.success('Address copied from delivery address');
        }*/
    </script>
@endpush
