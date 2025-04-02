@extends('layouts.app', ['page' => 'profile'])

@section('title', __('My Profile'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col">
                            <h4 class="page-title">{{ __('My Profile') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="dripicons-user mr-1 text-primary"></i> {{ __('Account Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar-box">
                                <div class="avatar-lg bg-primary rounded-circle">
                                    <span class="avatar-title text-white font-24">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                        </div>

                        <div class="account-info-item">
                            <small class="text-muted d-block">{{ __('Account Code') }}</small>
                            <strong>{{ $customer->acc_code ?? 'N/A' }}</strong>
                        </div>
                        <div class="account-info-item">
                            <small class="text-muted d-block">{{ __('Customer Name') }}</small>
                            <strong>{{ $customer->CustomerName ?? 'N/A' }}</strong>
                        </div>
                        <div class="account-info-item">
                            <small class="text-muted d-block">{{ __('Phone Number') }}</small>
                            <strong>{{ $customer->PhoneNumber ?? 'N/A' }}</strong>
                        </div>
                        <div class="account-info-item">
                            <small class="text-muted d-block">{{ __('Joined') }}</small>
                            <strong>{{ $user->created_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="{{ route('customer.profile.edit') }}" class="btn btn-sm btn-outline-primary btn-block">
                            <i class="dripicons-pencil mr-1"></i> {{ __('Edit Profile') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="dripicons-location mr-1 text-primary"></i> {{ __('Delivery Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($customer)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="account-info-item">
                                        <small class="text-muted d-block">{{ __('Address Line 1') }}</small>
                                        <strong>{{ $customer->DeliveryAddressLine1 ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="account-info-item">
                                        <small class="text-muted d-block">{{ __('Address Line 2') }}</small>
                                        <strong>{{ $customer->DeliveryAddressLine2 ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="account-info-item">
                                        <small class="text-muted d-block">{{ __('City') }}</small>
                                        <strong>{{ $customer->DeliveryCity ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="account-info-item">
                                        <small class="text-muted d-block">{{ __('Postal Code') }}</small>
                                        <strong>{{ $customer->DeliveryPostalCode ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">{{ __('No delivery information available.') }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="dripicons-lock mr-1 text-primary"></i> {{ __('Password & Security') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="current_password">{{ __('Current Password') }}</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('New Password') }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">{{ __('Confirm New Password') }}</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Update Password') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
