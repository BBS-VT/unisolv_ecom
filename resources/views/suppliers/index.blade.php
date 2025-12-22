@extends('layouts.master')

@section('title', 'Suppliers')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Suppliers</h1>
                <p class="text-muted mb-0">Manage your supplier database</p>
            </div>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Add Supplier
            </a>
        </div>

        <!-- Filters Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('suppliers.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Search</label>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search by name, code, or VAT number..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Suppliers</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Credit Hold</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="mdi mdi-magnify me-1"></i> Filter
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-refresh"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Suppliers Table Card -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="suppliersTable">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Supplier Name</th>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Credit Limit</th>
                            <th>Payment Terms</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($suppliers as $supplier)
                            <tr data-supplier-id="{{ $supplier->id }}">
                                <td>
                                    <span class="badge bg-secondary">{{ $supplier->acc_code }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="text-decoration-none fw-semibold">
                                        {{ $supplier->SupplierName }}
                                    </a>
                                    @if($supplier->IsOnCreditHold)
                                        <span class="badge bg-danger ms-1">
                                    <i class="mdi mdi-alert"></i> On Hold
                                </span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->primaryContact)
                                        <div class="text-truncate" style="max-width: 150px;">
                                            {{ $supplier->primaryContact->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">No contact</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $supplier->PhoneNumber ?? '-' }}
                                </td>
                                <td>
                                    @if($supplier->GeneralEmailAddress)
                                        <a href="mailto:{{ $supplier->GeneralEmailAddress }}" class="text-decoration-none">
                                            {{ $supplier->GeneralEmailAddress }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($supplier->CreditLimit > 0)
                                        R {{ number_format($supplier->CreditLimit, 2) }}
                                    @else
                                        <span class="text-muted">No limit</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $supplier->payment_terms ?? $supplier->PaymentDays . ' days' }}
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle"
                                               type="checkbox"
                                               {{ $supplier->Status ? 'checked' : '' }}
                                               data-supplier-id="{{ $supplier->id }}">
                                        <label class="form-check-label">
                                            {{ $supplier->Status ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('suppliers.show', $supplier) }}"
                                           class="btn btn-outline-primary"
                                           title="View">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}"
                                           class="btn btn-outline-secondary"
                                           title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger delete-supplier"
                                                data-supplier-id="{{ $supplier->id }}"
                                                data-supplier-name="{{ $supplier->SupplierName }}"
                                                title="Delete">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="mdi mdi-package-variant-closed mdi-48px text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No suppliers found</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($suppliers->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $suppliers->links() }}
                    </div>
                @endif
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
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Status toggle
            $(document).on('change', '.status-toggle', function() {
                const supplierId = $(this).data('supplier-id');
                const checkbox = $(this);
                const label = checkbox.next('label');

                $.ajax({
                    url: `/suppliers/${supplierId}/toggle-status`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            label.text(response.status ? 'Active' : 'Inactive');
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        toastr.error('Failed to update status');
                    }
                });
            });

            // Delete supplier
            $(document).on('click', '.delete-supplier', function() {
                const supplierId = $(this).data('supplier-id');
                const supplierName = $(this).data('supplier-name');

                Swal.fire({
                    title: 'Delete Supplier?',
                    html: `Are you sure you want to delete <strong>${supplierName}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/suppliers/${supplierId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    $(`tr[data-supplier-id="${supplierId}"]`).fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                    toastr.success(response.message);
                                }
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON?.message || 'Failed to delete supplier';
                                toastr.error(message);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
