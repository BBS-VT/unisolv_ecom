@extends('layouts.master')

@section('title', 'Promotions Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">{{ __('Promotions Management') }}</h1>
                <p class="mb-0 text-muted">{{ __('Manage sales promotions and discounts') }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('promotions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('Create Promotion') }}
                </a>
                <a href="{{ route('promotions.import') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-file-import me-1"></i> {{ __('Import Promotions') }}
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Scheduled') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['scheduled']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('Expired') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['expired']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Online Only') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['online_only']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-globe fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">{{ __('Imported') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['imported']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-download fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['total']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tags fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statistics['active']) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Filters & Search') }}</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('promotions.index') }}" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Name, Stock Code, Product...">
                    </div>

                    <div class="col-md-2">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" id="type" name="type">
                            <option value="">All Types</option>
                            <option value="date_range" {{ request('type') === 'date_range' ? 'selected' : '' }}>Date Range</option>
                            <option value="bogo" {{ request('type') === 'bogo' ? 'selected' : '' }}>BOGO</option>
                            <option value="quantity_break" {{ request('type') === 'quantity_break' ? 'selected' : '' }}>Quantity Break</option>
                            <option value="bonus_quantity" {{ request('type') === 'bonus_quantity' ? 'selected' : '' }}>Bonus Quantity</option>
                            <option value="price_break" {{ request('type') === 'price_break' ? 'selected' : '' }}>Price Break</option>
                            <option value="online_only" {{ request('type') === 'online_only' ? 'selected' : '' }}>Online Only</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location"
                               value="{{ request('location') }}" placeholder="Location name...">
                    </div>

                    <div class="col-md-2">
                        <label for="is_online_only" class="form-label">Online Only</label>
                        <select class="form-control" id="is_online_only" name="is_online_only">
                            <option value="">All</option>
                            <option value="1" {{ request('is_online_only') === '1' ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('is_online_only') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('import_result'))
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Import Results</h6>
                <p class="mb-0">{{ session('success') }}</p>

                @if(session('import_details'))
                    <hr>
                    @if(!empty(session('import_details')['errors']))
                        <h6 class="text-danger">Errors:</h6>
                        <ul class="mb-2">
                            @foreach(session('import_details')['errors'] as $error)
                                <li class="small text-danger">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty(session('import_details')['warnings']))
                        <h6 class="text-warning">Warnings:</h6>
                        <ul class="mb-0">
                            @foreach(session('import_details')['warnings'] as $warning)
                                <li class="small text-warning">{{ $warning }}</li>
                            @endforeach
                        </ul>
                    @endif
                @endif

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    Promotions ({{ $promotions->total() }} total)
                </h6>

                @if($promotions->count() > 0)
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">
                            Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                            Clear
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                                    data-bs-toggle="dropdown" id="bulkActions">
                                Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('active')">Mark Active</a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('inactive')">Mark Inactive</a></li>
                                <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus('expired')">Mark Expired</a></li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-body p-0">
                @if($promotions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                                </th>
                                <th>
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-decoration-none text-dark">
                                        Promotion Name
                                        @if(request('sort') === 'name')
                                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Location</th>
                                <th>Usage</th>
                                <th width="120">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($promotions as $promotion)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="promotion-checkbox" value="{{ $promotion->id }}">
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong>{{ $promotion->name }}</strong>
                                                @if($promotion->is_featured)
                                                    <span class="badge bg-warning text-dark ms-1">Featured</span>
                                                @endif
                                                @if($promotion->is_online_only)
                                                    <span class="badge bg-info ms-1">Online Only</span>
                                                @endif
                                                @if($promotion->is_imported)
                                                    <span class="badge bg-secondary ms-1">Imported</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $promotion->stock_code }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($promotion->product)
                                            <div>
                                                <strong>{{ Str::limit($promotion->product->ProductName, 30) }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $promotion->stock_code }}</small>
                                            </div>
                                        @else
                                            <span class="text-danger">Product not found</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $promotion->type === 'date_range' ? 'primary' : ($promotion->type === 'bogo' ? 'success' : 'info') }}">
                                            {{ ucwords(str_replace('_', ' ', $promotion->type)) }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'inactive' => 'secondary',
                                                'scheduled' => 'warning',
                                                'expired' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$promotion->status] ?? 'secondary' }}">
                                            {{ ucfirst($promotion->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="small">
                                            <strong>Start:</strong> {{ $promotion->starts_at->format('M j, Y') }}<br>
                                            <strong>End:</strong> {{ $promotion->ends_at->format('M j, Y') }}
                                        </div>
                                    </td>

                                    <td>
                                        @if($promotion->location_name)
                                            {{ $promotion->location_name }}
                                            <br><small class="text-muted">{{ $promotion->location_code }}</small>
                                        @else
                                            <span class="text-muted">All locations</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="small">
                                            <strong>{{ number_format($promotion->usage_count) }}</strong>
                                            @if($promotion->usage_limit_total)
                                                / {{ number_format($promotion->usage_limit_total) }}
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('promotions.show', $promotion) }}"
                                               class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('promotions.edit', $promotion) }}"
                                               class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                    onclick="deletePromotion({{ $promotion->id }})" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $promotions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">{{ __('No promotions found') }}</h5>
                        <p class="text-muted">Create your first promotion or import from CSV to get started.</p>
                        <a href="{{ route('promotions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> {{ __('Create Promotion') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this promotion? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form id="bulkActionForm" method="POST" action="{{ route('promotions.bulk-status') }}" style="display: none;">
        @csrf
        <input type="hidden" name="status" id="bulkStatus">
        <div id="bulkPromotionIds"></div>
    </form>
@endsection

@push('scripts')
    <script>
        function deletePromotion(id) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/promotions/${id}`;

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        function selectAll() {
            document.querySelectorAll('.promotion-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
            document.getElementById('selectAllCheckbox').checked = true;
        }

        function clearSelection() {
            document.querySelectorAll('.promotion-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
        }

        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            document.querySelectorAll('.promotion-checkbox').forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }

        function bulkUpdateStatus(status) {
            const checkedBoxes = document.querySelectorAll('.promotion-checkbox:checked');

            if (checkedBoxes.length === 0) {
                alert('Please select at least one promotion.');
                return;
            }

            if (confirm(`Are you sure you want to mark ${checkedBoxes.length} promotion(s) as ${status}?`)) {
                const form = document.getElementById('bulkActionForm');
                const bulkPromotionIds = document.getElementById('bulkPromotionIds');
                const bulkStatus = document.getElementById('bulkStatus');

                // Clear existing inputs
                bulkPromotionIds.innerHTML = '';

                // Add selected promotion IDs
                checkedBoxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'promotion_ids[]';
                    input.value = checkbox.value;
                    bulkPromotionIds.appendChild(input);
                });

                bulkStatus.value = status;
                form.submit();
            }
        }

        // Auto-submit form on filter change
        document.querySelectorAll('select[name="type"], select[name="status"], select[name="is_online_only"]').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
@endpush
