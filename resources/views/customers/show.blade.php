@extends('layouts.app')

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        /* Header Styles */
        .customer-header {
            position: relative;
            border-radius: 0.5rem;
            background: linear-gradient(120deg, #f8f9fa 0%, #eef1f5 100%);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        .customer-info-section {
            display: flex;
            align-items: center;
        }

        .customer-name-section {
            margin-right: 1.5rem;
        }

        .customer-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #212529;
        }

        .customer-account {
            display: flex;
            align-items: center;
            margin-bottom: 0;
            color: #495057;
        }

        .customer-account-label {
            font-size: 0.9rem;
            color: #6c757d;
            margin-right: 0.5rem;
        }

        .customer-account-number {
            font-weight: 500;
        }

        .customer-opened-date {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .customer-actions {
            margin-left: auto;
            display: flex;
            align-content: end;
            gap: 0.5rem;
        }

        .customer-status-badges {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
        }

        /* Cards Styles */
        .customer-info-card {
            height: 100%;
            transition: all 0.2s;
        }

        .customer-info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.07);
        }

        .info-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-weight: 600;
            margin-bottom: 0;
        }

        /* Balance Cards */
        .balance-card {
            border-left: 4px solid;
            transition: all 0.2s;
            height: 100%;
        }

        .balance-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.07);
        }

        .balance-card.current {
            border-left-color: #1C75BC;
        }

        .balance-card.overdue {
            border-left-color: #dc3545;
        }

        .balance-card.bf {
            border-left-color: #ffc107;
        }

        .balance-card.paid {
            border-left-color: #28a745;
        }

        /* Address Card */
        .address-card {
            height: 100%;
        }

        /* Contact Styles */
        .customer-contact-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(52, 116, 235, 0.1);
            color: #3474eb;
            margin-right: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .contact-details {
            flex-grow: 1;
        }

        .contact-label {
            margin-bottom: 0;
            font-size: 0.75rem;
            color: #6c757d;
        }

        .contact-value {
            margin-bottom: 0;
            font-weight: 500;
        }

        /* Badge Styles */
        .badge-soft-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            font-weight: 500;
        }

        .badge-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            font-weight: 500;
        }

        .badge-soft-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            font-weight: 500;
        }

        .badge-soft-primary {
            background-color: rgba(52, 116, 235, 0.1);
            color: #3474eb;
            font-weight: 500;
        }

        /* Tab Styles */
        .tab-content {
            padding: 1.5rem;
            background-color: #fff;
            border: 1px solid rgba(0,0,0,.125);
            border-top: none;
            border-bottom-left-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        .nav-pills .nav-link {
            padding: 0.75rem 1.25rem;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            background-color: #fff;
            color: #3474eb;
            border: 1px solid rgba(0,0,0,.125);
            border-bottom: none;
            margin-bottom: -1px;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Price Level Styles */
        .price-level-selector {
            gap: 10px;
            display: flex;
        }

        .custom-price-level {
            flex: 1;
            position: relative;
            padding: 0;
            margin: 0;
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
            border: 1px solid #e1e4e8;
            border-radius: 6px;
            padding: 15px 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .custom-price-level.active .price-level-label,
        .custom-price-level input[type="radio"]:checked + .price-level-label {
            border-color: #3474eb;
            background-color: rgba(52, 116, 235, 0.08);
            box-shadow: 0 0 0 1px #3474eb;
        }

        .level-indicator {
            font-size: 20px;
            font-weight: bold;
            color: #3474eb;
            background-color: rgba(52, 116, 235, 0.1);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }

        .level-text {
            font-size: 13px;
            font-weight: 500;
            text-align: center;
        }

        .form-check-input:checked {
            background-color: #3474eb;
            border-color: #3474eb;
        }

        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            margin-top: 0.2em;
        }

        .last-edit-info {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 20px;
            text-align: right;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .customer-actions {
                margin-top: 1rem;
                margin-left: 0;
                flex-wrap: wrap;
            }

            .customer-actions .btn {
                flex: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .customer-status-badges {
                position: static;
                margin-top: 1rem;
                display: flex;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @include('flash-message')

        <!-- Customer Header -->
        <div class="customer-header">

                <div class="customer-info-section">
                    <div class="customer-name-section">
                        <h3 class="customer-name">{{ $customer->CustomerName }}</h3>
                        <p class="customer-account">
                            <span class="customer-account-label">{{ __('global.account') }}:</span>
                            <span
                                class="customer-account-number">{{ $customer->acc_main }} {{ ($customer->acc_sub == 0) ? '' : $customer->acc_sub }}</span>
                        </p>
                        <p class="customer-opened-date pt-2">
                            <i data-feather="calendar" class="icon-xxs me-1"></i>
                            {{ __('cruds.customer.fields.opened_date') }}: {{ $customer->AccountOpenedDate }}
                        </p>
                    </div>
                </div>

                <div class="customer-actions">
                    @can('order_create')
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                            <i data-feather="plus-circle" class="icon-xs me-1"></i> {{ __('global.new_order') }}
                        </a>
                    @endcan

                    @can('customer_edit')
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-outline-danger">
                            <i data-feather="edit" class="icon-xs me-1"></i> {{ __('global.edit') }}
                        </a>
                    @endcan

                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="icon-xs me-1"></i> {{ __('global.back_to_list') }}
                    </a>
                </div>

                <div class="customer-status-badges">
                    @if($customer->CustomerStatus==1)
                        <span class="badge badge-soft-success px-3 py-2 me-2">{{ __('global.active') }}</span>
                    @else
                        <span class="badge badge-soft-danger px-3 py-2 me-2">{{ __('global.closed') }}</span>
                    @endif

                    @if($customer->IsOnCreditHold==1)
                        <span class="badge badge-soft-warning px-3 py-2">{{ __('global.credit_hold') }}</span>
                    @endif
                </div>

        </div>

        <!-- Balance Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card balance-card bf">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-soft-warning">
                                    <i data-feather="alert-triangle" class="icon-dual-warning font-size-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0">{{ __('global.balance_bf') }}</p>
                                <h4 class="mb-0">{{ number_format($balance_bf, 2, ".", " ") }}</h4>
                            </div>
                            <div class="dropdown">
                                <a class="text-muted dropdown-toggle font-size-16" role="button" data-bs-toggle="dropdown" aria-haspopup="true">
                                    <i data-feather="info" class="icon-xs"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="javascript:void(0);">{{ __('global.total_outstanding_balance_bf_info') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card balance-card current">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-soft-primary">
                                    <i data-feather="calendar" class="icon-dual-primary font-size-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0">{{ __('global.current') }}</p>
                                <h4 class="mb-0">
                                    @if($displaySubAccount == "1")
                                        {{ !empty($customer->customerSubBalance->AgedBalance1) ? number_format($customer->customerSubBalance->AgedBalance1, 2, ".", " ") : '0.00' }}
                                    @else
                                        {{ !empty($customer->customerBalance->AgedBalance1) ? number_format($customer->customerBalance->AgedBalance1, 2, ".", " ") : '0.00' }}
                                    @endif
                                </h4>
                            </div>
                            <div class="dropdown">
                                <a class="text-muted dropdown-toggle font-size-16" role="button" data-bs-toggle="dropdown" aria-haspopup="true">
                                    <i data-feather="info" class="icon-xs"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="javascript:void(0);">{{ __('global.current_balance_info') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card balance-card overdue">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-soft-danger">
                                    <i data-feather="alert-circle" class="icon-dual-danger font-size-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0">{{ __('global.overdue') }}</p>
                                <h4 class="mb-0">{{ number_format($overdue_balance, 2, ".", " ") }}</h4>
                            </div>
                            <div class="dropdown">
                                <a class="text-muted dropdown-toggle font-size-16" role="button" data-bs-toggle="dropdown" aria-haspopup="true">
                                    <i data-feather="info" class="icon-xs"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="javascript:void(0);">{{ __('global.overdue_balance_info') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card balance-card paid">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded bg-soft-success">
                                    <i data-feather="check-circle" class="icon-dual-success font-size-20"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-muted mb-0">{{ __('global.last_paid') }}</p>
                                <h4 class="mb-0">0.00</h4>
                            </div>
                            <div class="dropdown">
                                <a class="text-muted dropdown-toggle font-size-16" role="button" data-bs-toggle="dropdown" aria-haspopup="true">
                                    <i data-feather="info" class="icon-xs"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="javascript:void(0);">{{ __('global.last_paid_info') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-2">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#displayBalance" class="text-primary">
                                <small>{{ __('global.view') }} {{ __('global.detail_balance') }} <i data-feather="chevron-right" class="icon-xxs"></i></small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs -->
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-pills nav-border" id="customerTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="details-tab" data-bs-toggle="tab" href="#details-tab-pane" role="tab" aria-controls="details-tab-pane" aria-selected="true">
                            <i data-feather="info" class="icon-xs me-1"></i> {{ __('global.detail') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="contacts-tab" data-bs-toggle="tab" href="#contacts-tab-pane" role="tab" aria-controls="contacts-tab-pane" aria-selected="false">
                            <i data-feather="users" class="icon-xs me-1"></i> {{ __('global.contacts') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders-tab-pane" role="tab" aria-controls="orders-tab-pane" aria-selected="false">
                            <i data-feather="shopping-cart" class="icon-xs me-1"></i> {{ __('global.orders') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pricing-tab" data-bs-toggle="tab" href="#pricing-tab-pane" role="tab" aria-controls="pricing-tab-pane" aria-selected="false">
                            <i data-feather="tag" class="icon-xs me-1"></i> {{ __('global.pricing') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="notes-tab" data-bs-toggle="tab" href="#notes-tab-pane" role="tab" aria-controls="notes-tab-pane" aria-selected="false">
                            <i data-feather="file-text" class="icon-xs me-1"></i> {{ __('global.notes') }}
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="customerTabContent">
                    <!-- Details Tab -->
                    <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                        @include('customers.partials._detail_redesigned')
                    </div>

                    <!-- Contacts Tab -->
                    <div class="tab-pane fade" id="contacts-tab-pane" role="tabpanel" aria-labelledby="contacts-tab" tabindex="0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">{{ __('global.contacts') }}</h5>
                                    @can('contact_create')
                                        <button type="button" class="btn btn-sm btn-primary">
                                            <i data-feather="plus" class="icon-xs me-1"></i> {{ __('global.add_contact') }}
                                        </button>
                                    @endcan
                                </div>

                                <!-- Contacts will be listed here -->
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-muted">{{ __('global.contacts_will_appear_here') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders-tab-pane" role="tabpanel" aria-labelledby="orders-tab" tabindex="0">
                        @include('customers.partials._orders')
                    </div>

                    <!-- Pricing Tab -->
                    <div class="tab-pane fade" id="pricing-tab-pane" role="tabpanel" aria-labelledby="pricing-tab" tabindex="0">
                        @include('customers.partials._pricing_settings_redesigned')
                    </div>

                    <!-- Notes Tab -->
                    <div class="tab-pane fade" id="notes-tab-pane" role="tabpanel" aria-labelledby="notes-tab" tabindex="0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">{{ __('global.notes') }}</h5>
                                    @can('note_create')
                                        <button type="button" class="btn btn-sm btn-primary">
                                            <i data-feather="plus" class="icon-xs me-1"></i> {{ __('global.add_note') }}
                                        </button>
                                    @endcan
                                </div>

                                <!-- Notes will be listed here -->
                                <div class="card">
                                    <div class="card-body">
                                        <p class="text-muted">{{ __('global.notes_will_appear_here') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="last-edit-info">
            {{ __('global.last_edit') }} {{ $customer->lastedited->PreferredName }} on {{ $customer->updated_at }}
        </div>
    </div>

    <!-- Balance Details Modal -->
    <div class="modal fade" id="displayBalance" tabindex="-1" aria-labelledby="displayBalanceLabel" aria-hidden="true">
        @include('customers.partials._balance_redesigned')
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

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

            // Initialize Feather icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Bootstrap 5 tooltip initialization
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Info dropdowns in balance cards
            $('.dropdown-toggle').dropdown();

            // Modal trigger adjustment for Bootstrap 5
            $('[data-bs-toggle="modal"]').on('click', function() {
                var targetModal = $(this).data('bs-target');
                $(targetModal).modal('show');
            });
        });
    </script>
@endsection
