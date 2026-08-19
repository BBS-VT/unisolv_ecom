@extends('layouts.master')

@section('title', $user->PreferredName)

@push('styles')
    <style>
        /* User Header with Gradient */
        .user-header {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(28, 117, 188, 0.2);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .user-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .user-info-section {
            position: relative;
            z-index: 1;
        }

        .user-avatar-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
        }

        .user-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            object-fit: cover;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }

        .user-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
            text-align: center;
        }

        .user-email {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .user-roles {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .user-roles .badge {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
        }

        .user-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .user-actions .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .user-actions .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        /* Info Cards */
        .info-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            height: 100%;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .info-card .card-header {
            background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 2px solid #e9ecef;
            padding: 1.25rem 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .info-card .card-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-card .card-title i {
            color: #1C75BC;
        }

        /* Detail Row Styling */
        .detail-row {
            padding: 1rem;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row:hover {
            background-color: #f8f9fa;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-weight: 500;
            color: #212529;
            margin-bottom: 0;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 1rem;
        }

        .detail-icon.primary {
            background: rgba(28, 117, 188, 0.15);
            color: #1C75BC;
        }

        .detail-icon.success {
            background: rgba(var(--bs-success-rgb), 0.15);
            color: var(--bs-success);
        }

        .detail-icon.info {
            background: rgba(var(--bs-info-rgb), 0.15);
            color: var(--bs-info);
        }

        .detail-icon.warning {
            background: rgba(var(--bs-warning-rgb), 0.15);
            color: var(--bs-warning);
        }

        /* Status Badges */
        .status-active {
            background-color: rgba(var(--bs-success-rgb), 0.15);
            color: var(--bs-success);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-inactive {
            background-color: rgba(var(--bs-danger-rgb), 0.15);
            color: var(--bs-danger);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .user-header {
                padding: 1.5rem;
            }

            .user-name {
                font-size: 1.5rem;
            }

            .user-actions {
                width: 100%;
            }

            .user-actions .btn {
                flex: 1;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        @include('flash-message')

        <!-- User Header -->
        <div class="user-header">
            <div class="user-info-section">
                <div class="user-avatar-container">
                    <div class="user-avatar">
                        <i class="mdi mdi-account"></i>
                    </div>
                </div>

                <h2 class="user-name">{{ $user->PreferredName }}</h2>

                <div class="user-email">
                    <i class="mdi mdi-email-outline"></i>
                    <span>{{ $user->email }}</span>
                </div>

                <div class="user-roles">
                    @forelse($user->roles as $role)
                        <span class="badge">{{ $role->title }}</span>
                    @empty
                        <span class="badge">{{ __('global.no_roles') }}</span>
                    @endforelse
                </div>

                <div class="user-actions">
                    @can('user_edit')
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn">
                            <i class="mdi mdi-pencil me-1"></i> {{ __('global.edit') }}
                        </a>
                    @endcan

                    <a href="{{ route('admin.users.index') }}" class="btn">
                        <i class="mdi mdi-arrow-left me-1"></i> {{ __('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contact Information -->
            <div class="col-lg-6 mb-4">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="mdi mdi-card-account-details"></i>
                            {{ __('global.contact_information') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="detail-row">
                            <div class="d-flex align-items-center">
                                <div class="detail-icon primary">
                                    <i class="mdi mdi-email"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="detail-label">{{ __('cruds.user.fields.email') }}</p>
                                    <p class="detail-value">{{ $user->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="d-flex align-items-center">
                                <div class="detail-icon success">
                                    <i class="mdi mdi-phone"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="detail-label">{{ __('cruds.user.fields.phonenumber') }}</p>
                                    <p class="detail-value">{{ $user->PhoneNumber ?? __('global.not_provided') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="d-flex align-items-center">
                                <div class="detail-icon info">
                                    <i class="mdi mdi-badge-account"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="detail-label">{{ __('cruds.user.fields.id') }}</p>
                                    <p class="detail-value">#{{ $user->id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role & Permissions -->
            <div class="col-lg-6 mb-4">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="mdi mdi-shield-account"></i>
                            {{ __('global.roles_and_permissions') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <p class="detail-label">{{ __('cruds.user.fields.roles') }}</p>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                @forelse($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->title }}</span>
                                @empty
                                    <span class="text-muted">{{ __('global.no_roles_assigned') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Information -->
            <div class="col-lg-6 mb-4">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="mdi mdi-briefcase"></i>
                            {{ __('global.sales_information') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="detail-row">
                            <div class="d-flex align-items-center">
                                <div class="detail-icon {{ $user->IsSalesperson ? 'success' : 'warning' }}">
                                    <i class="mdi mdi-account-tie"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="detail-label">{{ __('cruds.user.fields.salesrep') }}</p>
                                    <p class="detail-value">
                                        @if($user->IsSalesperson == 1)
                                            <span class="status-active">
                                                <i class="mdi mdi-check-circle"></i> {{ __('global.yes') }}
                                            </span>
                                        @else
                                            <span class="status-inactive">
                                                <i class="mdi mdi-close-circle"></i> {{ __('global.no') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($user->IsSalesperson == 1)
                            <div class="detail-row">
                                <div class="d-flex align-items-center">
                                    <div class="detail-icon primary">
                                        <i class="mdi mdi-barcode"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="detail-label">{{ __('cruds.user.fields.repcode') }}</p>
                                        <p class="detail-value">
                                            <span class="badge bg-light text-dark border">{{ $user->RepCode }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="col-lg-6 mb-4">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="mdi mdi-account-group"></i>
                            {{ __('global.customer_information') }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="detail-row">
                            <div class="d-flex align-items-center">
                                <div class="detail-icon {{ $user->IsCustomer ? 'success' : 'warning' }}">
                                    <i class="mdi mdi-account-check"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="detail-label">{{ __('cruds.user.fields.customer') }}</p>
                                    <p class="detail-value">
                                        @if($user->IsCustomer == 1)
                                            <span class="status-active">
                                                <i class="mdi mdi-check-circle"></i> {{ __('global.yes') }}
                                            </span>
                                        @else
                                            <span class="status-inactive">
                                                <i class="mdi mdi-close-circle"></i> {{ __('global.no') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($user->IsCustomer == 1 && $user->customer)
                            <div class="detail-row">
                                <div class="d-flex align-items-center">
                                    <div class="detail-icon info">
                                        <i class="mdi mdi-domain"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="detail-label">{{ __('global.linked_customer') }}</p>
                                        <p class="detail-value">
                                            <a href="{{ route('customers.show', $user->customer_id) }}" class="text-primary text-decoration-none">
                                                {{ $user->customer->CustomerName }}
                                                <i class="mdi mdi-open-in-new ms-1"></i>
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Metadata -->
            <div class="col-12">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="mdi mdi-information"></i>
                            {{ __('global.account_metadata') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <p class="detail-label">{{ __('global.created_at') }}</p>
                                <p class="detail-value">
                                    <i class="mdi mdi-calendar me-1"></i>
                                    {{ $user->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <p class="detail-label">{{ __('global.last_updated') }}</p>
                                <p class="detail-value">
                                    <i class="mdi mdi-update me-1"></i>
                                    {{ $user->updated_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p class="detail-label">{{ __('global.last_edited_by') }}</p>
                                <p class="detail-value">
                                    <i class="mdi mdi-account-edit me-1"></i>
                                    {{ $user->lastedited->PreferredName ?? __('global.system') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
