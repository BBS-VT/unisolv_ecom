@extends('layouts.master')

@section('title', $customer->CustomerName)

@push('styles')
    <style>
        /* Modern Header with Gradient */
        .customer-header {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(28, 117, 188, 0.2);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .customer-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .customer-info-section {
            position: relative;
            z-index: 1;
        }

        .customer-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }

        .customer-account {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            gap: 0.5rem;
        }

        .customer-account-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .customer-account-number {
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-family: 'Courier New', monospace;
        }

        .customer-opened-date {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .customer-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .customer-actions .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .customer-actions .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
        }

        .customer-actions .btn i {
            opacity: 0.9;
        }

        .customer-status-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .customer-status-badges .badge {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 20px;
        }

        /* Balance Summary Cards */
        .balance-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }

        .balance-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .balance-card.bf {
            border-left: 4px solid var(--bs-warning);
        }

        .balance-card.current {
            border-left: 4px solid #1C75BC;
        }

        .balance-card.overdue {
            border-left: 4px solid var(--bs-danger);
        }

        .balance-card.paid {
            border-left: 4px solid var(--bs-success);
        }

        .balance-card .card-body {
            padding: 1.5rem;
        }

        .balance-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .balance-icon.warning {
            background: rgba(var(--bs-warning-rgb), 0.15);
            color: var(--bs-warning);
        }

        .balance-icon.primary {
            background: rgba(28, 117, 188, 0.15);
            color: #1C75BC;
        }

        .balance-icon.danger {
            background: rgba(var(--bs-danger-rgb), 0.15);
            color: var(--bs-danger);
        }

        .balance-icon.success {
            background: rgba(var(--bs-success-rgb), 0.15);
            color: var(--bs-success);
        }

        .balance-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .balance-amount {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
            color: #212529;
        }

        .balance-info-btn {
            background: none;
            border: none;
            color: #6c757d;
            padding: 0.25rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .balance-info-btn:hover {
            color: #1C75BC;
        }

        .balance-detail-link {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1C75BC;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: gap 0.3s ease;
        }

        .balance-detail-link:hover {
            gap: 0.5rem;
            color: #2A3042;
        }

        /* Tabs Enhancement */
        .nav-pills {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 8px;
            gap: 0.5rem;
        }

        .nav-pills .nav-link {
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            color: #6c757d;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .nav-pills .nav-link:hover {
            background: rgba(28, 117, 188, 0.1);
            color: #1C75BC;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(28, 117, 188, 0.3);
        }

        .nav-pills .nav-link.active i {
            color: white;
        }

        .tab-content {
            padding: 2rem;
            background-color: #fff;
            border-radius: 0 0 8px 8px;
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

        .info-card .card-body {
            padding: 1.5rem;
        }

        .info-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-weight: 600;
            margin-bottom: 0;
            color: #212529;
            font-size: 1rem;
        }

        /* Contact Styles */
        .contact-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            border-radius: 8px;
            transition: background-color 0.2s ease;
            margin-bottom: 0.75rem;
        }

        .contact-item:hover {
            background-color: #f8f9fa;
        }

        .contact-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            color: white;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .contact-details {
            flex-grow: 1;
        }

        .contact-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .contact-value {
            margin-bottom: 0;
            font-weight: 500;
            color: #212529;
        }

        /* Badge Enhancements */
        .badge-soft-success {
            background-color: rgba(var(--bs-success-rgb), 0.15);
            color: var(--bs-success);
            font-weight: 600;
        }

        .badge-soft-danger {
            background-color: rgba(var(--bs-danger-rgb), 0.15);
            color: var(--bs-danger);
            font-weight: 600;
        }

        .badge-soft-warning {
            background-color: rgba(var(--bs-warning-rgb), 0.15);
            color: var(--bs-warning);
            font-weight: 600;
        }

        /* Price Level Selector */
        .price-level-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
        }

        .custom-price-level {
            position: relative;
        }

        .custom-price-level input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .price-level-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        .price-level-label:hover {
            border-color: #1C75BC;
            background-color: rgba(28, 117, 188, 0.05);
        }

        .custom-price-level.active .price-level-label,
        .custom-price-level input[type="radio"]:checked + .price-level-label {
            border-color: #1C75BC;
            background: linear-gradient(135deg, rgba(28, 117, 188, 0.1) 0%, rgba(42, 48, 66, 0.1) 100%);
            box-shadow: 0 4px 12px rgba(28, 117, 188, 0.2);
        }

        .level-indicator {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }

        .level-text {
            font-size: 0.875rem;
            font-weight: 600;
            text-align: center;
            color: #495057;
        }

        /* Form Controls */
        .form-switch .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #1C75BC;
            border-color: #1C75BC;
        }

        /* Last Edit Info */
        .last-edit-info {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 2rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .last-edit-info i {
            color: #1C75BC;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .empty-state-text {
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .customer-header {
                padding: 1.5rem;
            }

            .customer-name {
                font-size: 1.5rem;
            }

            .customer-actions {
                width: 100%;
                margin-top: 1rem;
            }

            .customer-actions .btn {
                flex: 1;
            }

            .customer-status-badges {
                width: 100%;
                margin-top: 1rem;
            }

            .balance-amount {
                font-size: 1.25rem;
            }

            .tab-content {
                padding: 1rem;
            }

            .price-level-selector {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            }
        }

        /* Card Header Enhancement */
        .card-header-custom {
            background: linear-gradient(to right, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 2px solid #e9ecef;
            padding: 1.25rem 1.5rem;
        }

        .card-title-custom {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-title-custom i {
            color: #1C75BC;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        @include('flash-message')

        <!-- Enhanced Customer Header -->
        <div class="customer-header">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="customer-info-section">
                        <h2 class="customer-name">
                            <i class="mdi mdi-account-circle me-2"></i>{{ $customer->CustomerName }}
                        </h2>
                        <div class="customer-account">
                            <span class="customer-account-label">Account:</span>
                            <span class="customer-account-number">
                                {{ $customer->acc_main }}{{ ($customer->acc_sub == 0) ? '' : '-' . $customer->acc_sub }}
                            </span>
                        </div>
                        <div class="customer-opened-date">
                            <i class="mdi mdi-calendar-outline"></i>
                            <span>{{ __('cruds.customer.fields.opened_date') }}: {{ $customer->AccountOpenedDate }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex flex-column align-items-lg-end gap-3">
                        <!-- Status Badges -->
                        <div class="customer-status-badges">
                            @if($customer->CustomerStatus==1)
                                <span class="badge badge-soft-success">
                                    <i class="mdi mdi-check-circle me-1"></i>{{ __('global.active') }}
                                </span>
                            @else
                                <span class="badge badge-soft-danger">
                                    <i class="mdi mdi-close-circle me-1"></i>{{ __('global.closed') }}
                                </span>
                            @endif

                            @if($customer->IsOnCreditHold==1)
                                <span class="badge badge-soft-warning">
                                    <i class="mdi mdi-alert me-1"></i>{{ __('global.credit_hold') }}
                                </span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="customer-actions">
                            @can('order_create')
                                <a href="{{ route('orders.create') }}" class="btn">
                                    <i class="mdi mdi-plus-circle me-1"></i> {{ __('global.new_order') }}
                                </a>
                            @endcan

                            @can('customer_edit')
                                <a href="{{ route('customers.edit', $customer->id) }}" class="btn">
                                    <i class="mdi mdi-pencil me-1"></i> {{ __('global.edit') }}
                                </a>
                            @endcan

                            <a href="{{ route('customers.index') }}" class="btn">
                                <i class="mdi mdi-arrow-left me-1"></i> {{ __('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Summary Cards -->
        <div class="row mb-4">
            <!-- Balance B/F -->
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card balance-card bf">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="balance-icon warning">
                                <i class="mdi mdi-alert-outline"></i>
                            </div>
                            <button type="button" class="balance-info-btn" data-bs-toggle="tooltip"
                                    title="{{ __('global.total_outstanding_balance_bf_info') }}">
                                <i class="mdi mdi-information-outline"></i>
                            </button>
                        </div>
                        <p class="balance-label mb-2">{{ __('global.balance_bf') }}</p>
                        <h3 class="balance-amount">R {{ number_format($balance_bf, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Current Balance -->
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card balance-card current">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="balance-icon primary">
                                <i class="mdi mdi-calendar-month"></i>
                            </div>
                            <button type="button" class="balance-info-btn" data-bs-toggle="tooltip"
                                    title="{{ __('global.current_balance_info') }}">
                                <i class="mdi mdi-information-outline"></i>
                            </button>
                        </div>
                        <p class="balance-label mb-2">{{ __('global.current') }}</p>
                        <h3 class="balance-amount">
                            R @if($displaySubAccount == "1")
                                {{ !empty($customer->customerSubBalance->AgedBalance1) ? number_format($customer->customerSubBalance->AgedBalance1, 2) : '0.00' }}
                            @else
                                {{ !empty($customer->customerBalance->AgedBalance1) ? number_format($customer->customerBalance->AgedBalance1, 2) : '0.00' }}
                            @endif
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Overdue Balance -->
            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                <div class="card balance-card overdue">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="balance-icon danger">
                                <i class="mdi mdi-alert-circle-outline"></i>
                            </div>
                            <button type="button" class="balance-info-btn" data-bs-toggle="tooltip"
                                    title="{{ __('global.overdue_balance_info') }}">
                                <i class="mdi mdi-information-outline"></i>
                            </button>
                        </div>
                        <p class="balance-label mb-2">{{ __('global.overdue') }}</p>
                        <h3 class="balance-amount text-danger">R {{ number_format($overdue_balance, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Last Paid -->
            <div class="col-lg-3 col-md-6">
                <div class="card balance-card paid">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="balance-icon success">
                                <i class="mdi mdi-check-circle-outline"></i>
                            </div>
                            <button type="button" class="balance-info-btn" data-bs-toggle="tooltip"
                                    title="{{ __('global.last_paid_info') }}">
                                <i class="mdi mdi-information-outline"></i>
                            </button>
                        </div>
                        <p class="balance-label mb-2">{{ __('global.last_paid') }}</p>
                        <h3 class="balance-amount text-success">R 0.00</h3>
                        <div class="mt-3">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#displayBalance"
                               class="balance-detail-link">
                                {{ __('global.view') }} {{ __('global.detail_balance') }}
                                <i class="mdi mdi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-pills" id="customerTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab"
                                data-bs-target="#details-tab-pane" type="button" role="tab">
                            <i class="mdi mdi-information-outline"></i>
                            <span>{{ __('global.detail') }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="contacts-tab" data-bs-toggle="tab"
                                data-bs-target="#contacts-tab-pane" type="button" role="tab">
                            <i class="mdi mdi-account-group"></i>
                            <span>{{ __('global.contacts') }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="orders-tab" data-bs-toggle="tab"
                                data-bs-target="#orders-tab-pane" type="button" role="tab">
                            <i class="mdi mdi-cart-outline"></i>
                            <span>{{ __('global.orders') }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pricing-tab" data-bs-toggle="tab"
                                data-bs-target="#pricing-tab-pane" type="button" role="tab">
                            <i class="mdi mdi-tag-multiple"></i>
                            <span>{{ __('global.pricing') }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notes-tab" data-bs-toggle="tab"
                                data-bs-target="#notes-tab-pane" type="button" role="tab">
                            <i class="mdi mdi-note-text-outline"></i>
                            <span>{{ __('global.notes') }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="customerTabContent">
                    <!-- Details Tab -->
                    <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel"
                         aria-labelledby="details-tab" tabindex="0">
                        @include('customers.partials._detail_redesigned')
                    </div>

                    <!-- Contacts Tab -->
                    <div class="tab-pane fade" id="contacts-tab-pane" role="tabpanel"
                         aria-labelledby="contacts-tab" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">
                                <i class="mdi mdi-account-group me-2 text-primary"></i>
                                {{ __('global.contacts') }}
                            </h4>
                            @can('contact_create')
                                <button type="button" class="btn btn-primary">
                                    <i class="mdi mdi-plus me-1"></i> {{ __('global.add_contact') }}
                                </button>
                            @endcan
                        </div>

                        <div class="card info-card">
                            <div class="card-body">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="mdi mdi-account-multiple-outline"></i>
                                    </div>
                                    <p class="empty-state-text">{{ __('global.no_contacts_yet') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders-tab-pane" role="tabpanel"
                         aria-labelledby="orders-tab" tabindex="0">
                        @include('customers.partials._orders')
                    </div>

                    <!-- Pricing Tab -->
                    <div class="tab-pane fade" id="pricing-tab-pane" role="tabpanel"
                         aria-labelledby="pricing-tab" tabindex="0">
                        @include('customers.partials._pricing_settings_redesigned')
                    </div>

                    <!-- Notes Tab -->
                    <div class="tab-pane fade" id="notes-tab-pane" role="tabpanel"
                         aria-labelledby="notes-tab" tabindex="0">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">
                                <i class="mdi mdi-note-text-outline me-2 text-primary"></i>
                                {{ __('global.notes') }}
                            </h4>
                            @can('note_create')
                                <button type="button" class="btn btn-primary">
                                    <i class="mdi mdi-plus me-1"></i> {{ __('Add Note') }}
                                </button>
                            @endcan
                        </div>

                        <div class="card info-card">
                            <div class="card-body">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="mdi mdi-note-outline"></i>
                                    </div>
                                    <p class="empty-state-text">{{ __('global.notes_will_appear_here') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last Edit Info -->
        <div class="last-edit-info">
            <i class="mdi mdi-clock-outline"></i>
            <span>{{ __('global.last_edit') }}: <strong>{{ $customer->lastedited->PreferredName }}</strong> on {{ $customer->updated_at->format('d M Y, H:i') }}</span>
        </div>
    </div>

    <!-- Balance Details Modal -->
    <div class="modal fade" id="displayBalance" tabindex="-1" aria-labelledby="displayBalanceLabel" aria-hidden="true">
        @include('customers.partials._balance_redesigned')
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>

    <script>
        $(document).ready(function(){
            // Initialize phone input mask
            $('.phone').inputmask('999 999 9999');

            // Price level selector
            $('.custom-price-level').click(function() {
                $('.custom-price-level').removeClass('active');
                $(this).addClass('active');
                $(this).find('input[type="radio"]').prop('checked', true);
            });

            // Bootstrap 5 tooltip initialization
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Initialize popovers
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

            // Tab activation tracking
            const tabTriggerList = document.querySelectorAll('#customerTab button');
            tabTriggerList.forEach(function(tabTrigger) {
                tabTrigger.addEventListener('shown.bs.tab', function(event) {
                    console.log('Tab activated:', event.target.id);
                    // You can add analytics tracking here
                });
            });

            // Smooth scroll for in-page links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && href !== 'javascript:void(0);') {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });

            // Add loading state to action buttons
            $('.customer-actions .btn').on('click', function() {
                const $btn = $(this);
                if (!$btn.attr('href') || $btn.attr('href') === 'javascript:void(0);') return;

                $btn.prop('disabled', true);
                const originalHtml = $btn.html();
                $btn.html('<span class="spinner-border spinner-border-sm me-1"></span>Loading...');

                // Re-enable after 3 seconds (in case of slow navigation)
                setTimeout(function() {
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                }, 3000);
            });
        });
    </script>
@endpush
