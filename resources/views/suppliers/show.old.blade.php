@extends('layouts.master')

@section('title', $supplier->SupplierName)

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                {{--<nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                        <li class="breadcrumb-item active">{{ $supplier->SupplierName }}</li>
                    </ol>
                </nav>--}}
                <h1 class="h3 mb-0">{{ $supplier->SupplierName }}</h1>
                <p class="text-muted mb-0">Supplier Code: <strong>{{ $supplier->acc_code }}</strong></p>
            </div>
            <div>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary">
                    <i class="mdi mdi-pencil me-1"></i> Edit Supplier
                </a>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="mdi mdi-information me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Supplier Name</label>
                                <p class="mb-0 fw-semibold">{{ $supplier->SupplierName }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted mb-1">Status</label>
                                <div>
                                    @if($supplier->Status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif

                                    @if($supplier->IsOnCreditHold)
                                        <span class="badge bg-danger ms-1">On Hold</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted mb-1">Account Code</label>
                                <p class="mb-0"><code>{{ $supplier->acc_code }}</code></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">VAT Number</label>
                                <p class="mb-0">{{ $supplier->VatNr ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Tax Reference</label>
                                <p class="mb-0">{{ $supplier->tax_reference ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Phone Number</label>
                                <p class="mb-0">
                                    @if($supplier->PhoneNumber)
                                        <a href="tel:{{ $supplier->PhoneNumber }}">{{ $supplier->PhoneNumber }}</a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Fax Number</label>
                                <p class="mb-0">{{ $supplier->FaxNumber ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Email</label>
                                <p class="mb-0">
                                    @if($supplier->GeneralEmailAddress)
                                        <a href="mailto:{{ $supplier->GeneralEmailAddress }}">{{ $supplier->GeneralEmailAddress }}</a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted mb-1">Website</label>
                                <p class="mb-0">
                                    @if($supplier->WebsiteURL)
                                        <a href="{{ $supplier->WebsiteURL }}" target="_blank">{{ $supplier->WebsiteURL }}</a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            @if($supplier->notes)
                                <div class="col-12">
                                    <label class="form-label text-muted mb-1">Notes</label>
                                    <p class="mb-0">{{ $supplier->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Financial Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="mdi mdi-currency-usd me-2"></i>Financial Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Credit Limit</label>
                                <p class="mb-0 fw-semibold">
                                    @if($supplier->CreditLimit > 0)
                                        R {{ number_format($supplier->CreditLimit, 2) }}
                                    @else
                                        <span class="text-muted">No limit</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Payment Days</label>
                                <p class="mb-0">{{ $supplier->PaymentDays }} days</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Payment Terms</label>
                                <p class="mb-0">{{ $supplier->payment_terms ?? 'Net ' . $supplier->PaymentDays }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Standard Discount</label>
                                <p class="mb-0">{{ $supplier->StandardDiscountPercentage }}%</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Currency</label>
                                <p class="mb-0">{{ $supplier->currency?->code ?? 'ZAR' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted mb-1">Account Opened</label>
                                <p class="mb-0">{{ $supplier->AccountOpenedDate?->format('d M Y') ?? '-' }}</p>
                            </div>
                        </div>

                        @if($supplier->bank_name)
                            <hr>
                            <h6 class="mb-3">Banking Details</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-muted mb-1">Bank Name</label>
                                    <p class="mb-0">{{ $supplier->bank_name }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted mb-1">Account Number</label>
                                    <p class="mb-0"><code>{{ $supplier->bank_account }}</code></p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted mb-1">Branch Code</label>
                                    <p class="mb-0">{{ $supplier->bank_branch }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Addresses Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="mdi mdi-map-marker me-2"></i>Addresses</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="mb-2">Delivery Address</h6>
                                @if($supplier->hasAddress('delivery'))
                                    @php $address = $supplier->address('delivery', $supplier->SupplierName); @endphp
                                    <p class="mb-0">
                                        {{ $address->address_1 }}<br>
                                        @if($address->address_2){{ $address->address_2 }}<br>@endif
                                        {{ $address->city }}@if($address->province), {{ $address->province }}@endif<br>
                                        {{ $address->zip }}
                                    </p>
                                @else
                                    <p class="text-muted mb-0">No delivery address on file</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-2">Postal Address</h6>
                                @if($supplier->hasAddress('postal'))
                                    @php $address = $supplier->address('postal', $supplier->SupplierName); @endphp
                                    <p class="mb-0">
                                        {{ $address->address_line1 }}<br>
                                        @if($address->address_line2){{ $address->address_line2 }}<br>@endif
                                        {{ $address->city }}@if($address->province), {{ $address->province }}@endif<br>
                                        {{ $address->postal_code }}
                                    </p>
                                @else
                                    <p class="text-muted mb-0">No postal address on file</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contacts Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="mdi mdi-account-multiple me-2"></i>Contacts</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                            <i class="mdi mdi-plus me-1"></i> Add Contact
                        </button>
                    </div>
                    <div class="card-body">
                        @if($supplier->contacts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody id="contactsTableBody">
                                    @foreach($supplier->contacts as $contact)
                                        <tr data-contact-id="{{ $contact->id }}">
                                            <td>
                                                {{ $contact->name }}
                                                @if($contact->is_primary)
                                                    <span class="badge bg-primary ms-1">Primary</span>
                                                @endif
                                            </td>
                                            <td>{{ $contact->position ?? '-' }}</td>
                                            <td>
                                                @if($contact->email)
                                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td type="tel">{{ $contact->bestPhone ?? '-' }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input contact-status-toggle"
                                                           type="checkbox"
                                                           {{ $contact->is_active ? 'checked' : '' }}
                                                           data-contact-id="{{ $contact->id }}">
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    @if(!$contact->is_primary)
                                                        <button type="button"
                                                                class="btn btn-outline-primary make-primary-btn"
                                                                data-contact-id="{{ $contact->id }}"
                                                                title="Make Primary">
                                                            <i class="mdi mdi-star"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button"
                                                            class="btn btn-outline-secondary edit-contact-btn"
                                                            data-contact-id="{{ $contact->id }}"
                                                            data-contact='@json($contact)'
                                                            title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-outline-danger delete-contact-btn"
                                                            data-contact-id="{{ $contact->id }}"
                                                            data-contact-name="{{ $contact->name }}"
                                                            title="Delete">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="mdi mdi-account-off mdi-48px text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No contacts added yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Quick Actions Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="mdi mdi-flash me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button"
                                    class="btn btn-outline-primary"
                                    onclick="toggleCreditHold()">
                                <i class="mdi mdi-lock-{{ $supplier->IsOnCreditHold ? 'open' : 'outline' }} me-2"></i>
                                {{ $supplier->IsOnCreditHold ? 'Remove Credit Hold' : 'Place on Credit Hold' }}
                            </button>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="mdi mdi-file-document-plus me-2"></i>Create Purchase Order
                            </a>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="mdi mdi-history me-2"></i>View Purchase History
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Operational Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="mdi mdi-cog me-2"></i>Operational Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-muted mb-1">Lead Time</label>
                            <p class="mb-0">{{ $supplier->lead_time_days }} days</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted mb-1">Minimum Order Value</label>
                            <p class="mb-0">
                                @if($supplier->minimum_order_value)
                                    R {{ number_format($supplier->minimum_order_value, 2) }}
                                @else
                                    <span class="text-muted">No minimum</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted mb-1">Country</label>
                            <p class="mb-0">{{ $supplier->country?->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Purchase Orders -->
                {{--@if($supplier->purchaseOrders->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="mdi mdi-file-document me-2"></i>Recent Purchase Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                @foreach($supplier->purchaseOrders as $po)
                                    <a href="#" class="list-group-item list-group-item-action px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">PO-{{ $po->id }}</h6>
                                                <small class="text-muted">{{ $po->created_at->format('d M Y') }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-{{ $po->status_color }}">{{ $po->status }}</span>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif--}}
            </div>
        </div>
    </div>

    <!-- Add Contact Modal -->
    <div class="modal fade" id="addContactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addContactForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="mobile" class="form-control">
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_primary" class="form-check-input" id="isPrimary">
                            <label class="form-check-label" for="isPrimary">Set as primary contact</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Contact Modal -->
    <div class="modal fade" id="editContactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editContactForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editContactId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" id="editPosition" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" id="editDepartment" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" id="editPhone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="mobile" id="editMobile" class="form-control">
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_primary" class="form-check-input" id="editIsPrimary">
                            <label class="form-check-label" for="editIsPrimary">Set as primary contact</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="editIsActive">
                            <label class="form-check-label" for="editIsActive">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Contact</button>
                    </div>
                </form>
            </div>
        </div>
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

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
    <script src="{{ URL::asset('build/libs/toastr/toastr.js') }}"></script>

    <script>
        const supplierId = {{ $supplier->id }};

        // Add contact
        $('#addContactForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('is_primary', $('#isPrimary').is(':checked') ? 1 : 0);
            formData.append('is_active', $('#isActive').is(':checked') ? 1 : 0);

            $.ajax({
                url: `/suppliers/${supplierId}/contacts`,
                method: 'POST',
                data: Object.fromEntries(formData),
                success: function(response) {
                    if (response.success) {
                        $('#addContactModal').modal('hide');
                        $('#addContactForm')[0].reset();
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.values(errors).forEach(error => {
                            toastr.error(error[0]);
                        });
                    } else {
                        toastr.error('Failed to add contact');
                    }
                }
            });
        });

        // Edit contact
        $(document).on('click', '.edit-contact-btn', function() {
            const contact = $(this).data('contact');

            $('#editContactId').val(contact.id);
            $('#editName').val(contact.name);
            $('#editPosition').val(contact.position);
            $('#editDepartment').val(contact.department);
            $('#editEmail').val(contact.email);
            $('#editPhone').val(contact.phone);
            $('#editMobile').val(contact.mobile);
            $('#editIsPrimary').prop('checked', contact.is_primary);
            $('#editIsActive').prop('checked', contact.is_active);

            $('#editContactModal').modal('show');
        });

        $('#editContactForm').on('submit', function(e) {
            e.preventDefault();

            const contactId = $('#editContactId').val();
            const formData = new FormData(this);
            formData.append('is_primary', $('#editIsPrimary').is(':checked') ? 1 : 0);
            formData.append('is_active', $('#editIsActive').is(':checked') ? 1 : 0);

            $.ajax({
                url: `/suppliers/${supplierId}/contacts/${contactId}`,
                method: 'PUT',
                data: Object.fromEntries(formData),
                success: function(response) {
                    if (response.success) {
                        $('#editContactModal').modal('hide');
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    toastr.error('Failed to update contact');
                }
            });
        });

        // Delete contact
        $(document).on('click', '.delete-contact-btn', function() {
            const contactId = $(this).data('contact-id');
            const contactName = $(this).data('contact-name');

            Swal.fire({
                title: 'Delete Contact?',
                text: `Remove ${contactName} from this supplier?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/suppliers/${supplierId}/contacts/${contactId}`,
                        method: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                $(`tr[data-contact-id="${contactId}"]`).fadeOut(300, function() {
                                    $(this).remove();
                                });
                                toastr.success(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete contact');
                        }
                    });
                }
            });
        });

        // Make primary
        $(document).on('click', '.make-primary-btn', function() {
            const contactId = $(this).data('contact-id');

            $.ajax({
                url: `/suppliers/${supplierId}/contacts/${contactId}/make-primary`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    toastr.error('Failed to set primary contact');
                }
            });
        });

        // Toggle contact status
        $(document).on('change', '.contact-status-toggle', function() {
            const contactId = $(this).data('contact-id');

            $.ajax({
                url: `/suppliers/${supplierId}/contacts/${contactId}/toggle-active`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    }
                },
                error: function() {
                    toastr.error('Failed to update status');
                }
            });
        });

        // Toggle credit hold
        function toggleCreditHold() {
            $.ajax({
                url: `/suppliers/${supplierId}/toggle-credit-hold`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function() {
                    toastr.error('Failed to update credit hold status');
                }
            });
        }
    </script>
@endpush
