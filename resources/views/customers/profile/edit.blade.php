@extends('layouts.app', ['page' => 'profile'])

@section('title', __('Edit Profile'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <div class="row">
                        <div class="col">
                            <h4 class="page-title">{{ __('Edit Profile') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="dripicons-user me-1 text-primary"></i> {{ __('Profile Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('customer.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">{{ __('Email Address') }}</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone Number') }}</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $customer->PhoneNumber ?? '') }}">
                                        @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3">{{ __('Delivery Address') }}</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_address_line1">{{ __('Address Line 1') }}</label>
                                        <input type="text" class="form-control @error('delivery_address_line1') is-invalid @enderror" id="delivery_address_line1" name="delivery_address_line1" value="{{ old('delivery_address_line1', $customer->DeliveryAddressLine1 ?? '') }}">
                                        @error('delivery_address_line1')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_address_line2">{{ __('Address Line 2') }}</label>
                                        <input type="text" class="form-control @error('delivery_address_line2') is-invalid @enderror" id="delivery_address_line2" name="delivery_address_line2" value="{{ old('delivery_address_line2', $customer->DeliveryAddressLine2 ?? '') }}">
                                        @error('delivery_address_line2')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_city">{{ __('City') }}</label>
                                        <input type="text" class="form-control @error('delivery_city') is-invalid @enderror" id="delivery_city" name="delivery_city" value="{{ old('delivery_city', $customer->DeliveryCity ?? '') }}">
                                        @error('delivery_city')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_postal_code">{{ __('Postal Code') }}</label>
                                        <input type="text" class="form-control @error('delivery_postal_code') is-invalid @enderror" id="delivery_postal_code" name="delivery_postal_code" value="{{ old('delivery_postal_code', $customer->DeliveryPostalCode ?? '') }}">
                                        @error('delivery_postal_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
                                <a href="{{ route('customer.profile') }}" class="btn btn-light ms-2">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
