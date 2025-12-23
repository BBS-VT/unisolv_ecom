
@extends('layouts.master')

@section('title', $supplier->SupplierName)

@section('content')
    <div class="container-fluid">
        <div class="row align-items-center mb-4">
            <div class="col-md-7">
                <h1 class="h3 mb-1 text-primary-emphasis fw-bold">{{ $supplier->SupplierName }}</h1>
                <p class="text-muted mb-0">
                    <span class="badge bg-light text-dark border me-2">{{ $supplier->acc_code }}</span>
                    <i class="mdi mdi-map-marker-outline"></i> {{ $delivery->city ?? 'No City Set' }}
                </p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <div class="btn-group shadow-sm">
                    <a href="#" class="btn btn-white border"><i class="mdi mdi-printer me-1"></i> {{ __('Statement') }}</a>
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-white border text-primary"><i class="mdi mdi-pencil"></i></a>
                </div>
                <a href="#" class="btn btn-primary ms-2 shadow-sm">
                    <i class="mdi mdi-plus me-1"></i> {{ __('New Purchase Order') }}
                </a>
            </div>
        </div>

        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-danger border-4">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fs-11 fw-bold">{{ __('Outstanding Balance') }}</h6>
                        <h3 class="mb-0 fw-bold text-danger">R {{ number_format($supplier->total_balance ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fs-11 fw-bold">{{ __('Credit Limit') }}</h6>
                        <h3 class="mb-0 fw-bold">R {{ number_format($supplier->CreditLimit, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fs-11 fw-bold">{{ __('Open Orders') }}</h6>
                        <h3 class="mb-0 fw-bold">{{ $supplier->purchase_orders_count ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="text-muted text-uppercase fs-11 fw-bold">{{ __('Account Status') }}</h6>
                        <div class="mt-1">
                            @if($supplier->IsOnCreditHold)
                                <span class="badge bg-danger">{{ __('CREDIT HOLD') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('ACTIVE') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom">
                        <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="supplierTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#overview">
                                    {{ __('Overview') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#ledger">
                                    {{ __('Transactions') }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#contacts">
                                    {{ __('Contacts') }}</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="overview">
                                <div class="row g-4">
                                    <div class="col-md-4 border-end">
                                        <h6 class="text-uppercase text-muted fs-11 fw-bold mb-3">{{ __('Company Profile') }}</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <small class="text-muted d-block">{{ __('VAT Number')}}</small>
                                                <span class="fw-semibold">{{ $supplier->VatNr ?? 'Not Registered' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <small class="text-muted d-block">{{ __('Tax Reference') }}</small>
                                                <span>{{ $supplier->tax_reference ?? '-' }}</span>
                                            </li>
                                            <li class="mb-2">
                                                <small class="text-muted d-block">{{ __('Website') }}</small>
                                                @if($supplier->WebsiteURL)
                                                    <a href="{{ $supplier->WebsiteURL }}" target="_blank" class="text-decoration-none">
                                                        {{ Str::limit($supplier->WebsiteURL, 30) }} <i class="mdi mdi-open-in-new small"></i>
                                                    </a>
                                                @else - @endif
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-4 border-end">
                                        <h6 class="text-uppercase text-muted fs-11 fw-bold mb-3">
                                            <i class="mdi mdi-truck-delivery-outline me-1"></i> {{ __('Delivery Address') }}
                                        </h6>
                                        @if($supplier->hasAddress('delivery'))
                                            @php $delivery = $supplier->address('delivery', $supplier->SupplierName); @endphp
                                            <address class="mb-0 text-dark">
                                                <strong>{{ $delivery->address_1 }}</strong><br>
                                                @if($delivery->address_2){{ $delivery->address_2 }}<br>@endif
                                                {{ $delivery->city }}, {{ $delivery->province }}<br>
                                                <span class="fw-bold">{{ $delivery->zip }}</span>
                                            </address>
                                            <a href="https://www.google.com/maps/search/{{ urlencode($delivery->address_1 . ' ' . $delivery->city) }}"
                                               target="_blank" class="btn btn-link btn-sm p-0 mt-2 text-primary text-decoration-none">
                                                <i class="mdi mdi-map-marker"></i> View on Maps
                                            </a>
                                        @else
                                            <p class="text-muted small italic">No delivery address set.</p>
                                        @endif
                                    </div>

                                    <div class="col-md-4">
                                        <h6 class="text-uppercase text-muted fs-11 fw-bold mb-3">
                                            <i class="mdi mdi-mailbox-outline me-1"></i> Postal Address
                                        </h6>
                                        @if($supplier->hasAddress('postal'))
                                            @php $postal = $supplier->address('postal', $supplier->SupplierName); @endphp
                                            <address class="mb-0 text-muted">
                                                {{ $postal->address_1 }}<br>
                                                @if($postal->address_2){{ $postal->address_2 }}<br>@endif
                                                {{ $postal->city }}<br>
                                                {{ $postal->zip }}
                                            </address>
                                        @else
                                            <p class="text-muted small italic">No postal address set.</p>
                                        @endif
                                    </div>
                                </div>

                                @if($supplier->notes)
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="bg-light p-3 rounded border-start border-3 border-info">
                                                <h6 class="fs-11 fw-bold text-uppercase text-info">Internal Notes</h6>
                                                <p class="mb-0 small text-dark">{{ $supplier->notes }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="ledger">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light text-muted small text-uppercase">
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">Balance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>2023-10-24</td>
                                            <td><a href="#" class="fw-bold">INV-9902</a></td>
                                            <td><span class="badge bg-light text-dark border">Invoice</span></td>
                                            <td class="text-end text-danger">- R 4,500.00</td>
                                            <td class="text-end fw-bold">R 4,500.00</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="contacts">
                                <livewire:supplier.contact-manager :supplier="$supplier" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card border-0 shadow-sm mb-4 bg-primary text-white">
                    <div class="card-body">
                        <h6>Quick Support</h6>
                        @if($primaryContact = $supplier->contacts->where('is_primary', true)->first())
                            <p class="small mb-1">{{ $primaryContact->name }}</p>
                            <a href="mailto:{{ $primaryContact->email }}" class="text-white d-block mb-1"><i class="mdi mdi-email-outline"></i> {{ $primaryContact->email }}</a>
                            <a href="tel:{{ $primaryContact->bestPhone }}" class="text-white d-block"><i class="mdi mdi-phone-outline"></i> {{ $primaryContact->bestPhone }}</a>
                        @else
                            <p class="small opacity-75">No primary contact assigned.</p>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">Recent Documents</div>
                    <div class="list-group list-group-flush small">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                            <span>PO-2023-001</span>
                            <span class="text-muted">12 Oct</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fs-11 { font-size: 0.85rem; }
        .btn-white { background: #fff; border-color: #dee2e6; color: #333; }
        .nav-tabs .nav-link { border: none; color: #6c757d; padding: 1rem 1.25rem; }
        .nav-tabs .nav-link.active { border-bottom: 2px solid var(--primary-color); color: var(--primary-color); }
    </style>
@endsection
