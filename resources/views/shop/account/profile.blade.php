@extends('shop.layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.account.index') }}">Account</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">My Profile</h1>
                        <p class="text-muted mb-0">Manage your account information and delivery preferences</p>
                    </div>
                    <div>
                        <a href="{{ route('shop.account.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Form -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('shop.account.profile.update') }}">
                            @csrf
                            @method('PUT')

                            <!-- Account Details (Read-only) -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted text-uppercase small mb-3">Account Details</h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" value="{{ $customer->CustomerName }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Account Code</label>
                                    <input type="text" class="form-control" value="{{ $customer->acc_code }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Credit Limit</label>
                                    <input type="text" class="form-control" value="{{ \App\Helpers\PricingHelper::formatPrice($customer->CreditLimit) }}" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price Level</label>
                                    <input type="text" class="form-control" value="Level {{ $customer->price_level }}" readonly>
                                </div>
                            </div>

                            <hr>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted text-uppercase small mb-3">Contact Information</h6>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="GeneralEmailAddress" class="form-label">
                                        Email Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control @error('GeneralEmailAddress') is-invalid @enderror"
                                           id="GeneralEmailAddress"
                                           name="GeneralEmailAddress"
                                           value="{{ old('GeneralEmailAddress', $customer->GeneralEmailAddress) }}"
                                           required>
                                    @error('GeneralEmailAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This email will be used for order confirmations and account notifications.</div>
                                </div>
                            </div>

                            <hr>

                            <!-- Delivery Address -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted text-uppercase small mb-3">Delivery Address</h6>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="DeliveryAddressLine1" class="form-label">
                                        Address Line 1 <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('DeliveryAddressLine1') is-invalid @enderror"
                                           id="DeliveryAddressLine1"
                                           name="DeliveryAddressLine1"
                                           value="{{ old('DeliveryAddressLine1', $customer->DeliveryAddressLine1) }}"
                                           required>
                                    @error('DeliveryAddressLine1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="DeliveryAddressLine2" class="form-label">Address Line 2</label>
                                    <input type="text"
                                           class="form-control @error('DeliveryAddressLine2') is-invalid @enderror"
                                           id="DeliveryAddressLine2"
                                           name="DeliveryAddressLine2"
                                           value="{{ old('DeliveryAddressLine2', $customer->DeliveryAddressLine2) }}">
                                    @error('DeliveryAddressLine2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="DeliveryCity" class="form-label">
                                        City <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('DeliveryCity') is-invalid @enderror"
                                           id="DeliveryCity"
                                           name="DeliveryCity"
                                           value="{{ old('DeliveryCity', $customer->DeliveryCity) }}"
                                           required>
                                    @error('DeliveryCity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="DeliveryState" class="form-label">
                                        State <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('DeliveryState') is-invalid @enderror"
                                           id="DeliveryState"
                                           name="DeliveryState"
                                           value="{{ old('DeliveryState', $customer->DeliveryState) }}"
                                           required>
                                    @error('DeliveryState')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="DeliveryPostCode" class="form-label">
                                        Post Code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control @error('DeliveryPostCode') is-invalid @enderror"
                                           id="DeliveryPostCode"
                                           name="DeliveryPostCode"
                                           value="{{ old('DeliveryPostCode', $customer->DeliveryPostCode) }}"
                                           required>
                                    @error('DeliveryPostCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            <!-- Password Change -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted text-uppercase small mb-3">Change Password</h6>
                                    <p class="text-muted small">Leave password fields blank if you don't want to change your password.</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           id="current_password"
                                           name="current_password">
                                    @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           id="password"
                                           name="password">
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password_confirmation"
                                           name="password_confirmation">
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Profile
                                    </button>
                                    <a href="{{ route('shop.account.index') }}" class="btn btn-outline-secondary ms-2">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Status Sidebar -->
            <div class="col-lg-4">
                <!-- Account Status -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Account Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Account Status:</span>
                                    @if($customer->IsOnCreditHold)
                                        <span class="badge bg-danger">Credit Hold</span>
                                    @else
                                        <span class="badge bg-success">Active</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Credit Limit:</span>
                                    <span class="text-success fw-bold">{{ \App\Helpers\PricingHelper::formatPrice($customer->CreditLimit / 100) }}</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Price Level:</span>
                                    <span class="badge bg-info">Level {{ $customer->price_level }}</span>
                                </div>
                            </div>
                        </div>

                        @if($customer->IsOnCreditHold)
                            <hr>
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Credit Hold Notice</strong><br>
                                Your account is currently on credit hold. Please contact customer service for assistance.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('shop.account.orders.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> View Orders
                            </a>
                            <a href="{{ route('shop.products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-shopping-cart"></i> Start Shopping
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Profile Tips -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0">Profile Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small text-muted mb-0">
                            <li class="mb-2">Keep your delivery address up to date to ensure accurate shipping.</li>
                            <li class="mb-2">Your email address is used for order confirmations and important account notifications.</li>
                            <li class="mb-2">Use a strong password and change it regularly for security.</li>
                            <li class="mb-0">Contact customer service if you need to update your credit limit or account details.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-label {
            font-weight: 600;
        }

        .form-control[readonly] {
            background-color: #f8f9fa;
            border-color: #e9ecef;
        }

        .text-uppercase.small {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card-header h6 {
            color: #495057;
        }

        .alert-warning {
            border-left: 4px solid #ffc107;
        }

        .badge {
            font-size: 0.75rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Password strength indicator (optional)
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = document.getElementById('password-strength');

            if (!strengthIndicator) return;

            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const strengthColor = ['danger', 'warning', 'warning', 'info', 'success'];

            if (password.length > 0) {
                strengthIndicator.innerHTML = `<small class="text-${strengthColor[strength-1] || 'danger'}">Password strength: ${strengthText[strength-1] || 'Very Weak'}</small>`;
            } else {
                strengthIndicator.innerHTML = '';
            }
        });

        // Form validation feedback
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');

            function validatePasswordMatch() {
                if (passwordField.value && confirmPasswordField.value) {
                    if (passwordField.value !== confirmPasswordField.value) {
                        confirmPasswordField.setCustomValidity('Passwords do not match');
                    } else {
                        confirmPasswordField.setCustomValidity('');
                    }
                }
            }

            passwordField.addEventListener('input', validatePasswordMatch);
            confirmPasswordField.addEventListener('input', validatePasswordMatch);
        });
    </script>
@endpush
