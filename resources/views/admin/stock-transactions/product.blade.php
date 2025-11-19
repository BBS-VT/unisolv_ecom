@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="text-white mb-0">
                                    <i class="mdi mdi-package-variant me-2"></i>Product Transaction History
                                </h1>
                                <p class="text-white-50 mb-0 mt-2">{{ $product->StockItemName }}</p>
                            </div>
                            <a href="{{ route('admin.stock-transactions.index') }}" class="btn btn-light">
                                <i class="mdi mdi-arrow-left me-1"></i>Back to All Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Product Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                @if($product->photo)
                                    <img src="{{ $product->photo->url }}" alt="{{ $product->StockItemName }}" class="img-fluid rounded">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="mdi mdi-image-off text-muted" style="font-size: 48px;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="150">Stock Code:</th>
                                                <td><strong>{{ $product->StockCode }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Product Name:</th>
                                                <td>{{ $product->StockItemName }}</td>
                                            </tr>
                                            <tr>
                                                <th>Brand:</th>
                                                <td>{{ $product->Brand ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Pack Size:</th>
                                                <td>{{ $product->Packsize }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="150">Current Stock:</th>
                                                <td>
                                                <span class="badge bg-primary fs-6">
                                                    {{ number_format($product->quantity_on_hand, 2) }} units
                                                </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Selling Price:</th>
                                                <td>R {{ number_format($product->SellingPrice, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status:</th>
                                                <td>
                                                    @if($product->status)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Selling Type:</th>
                                                <td>{!! $product->selling_type_badge !!}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Stock by Location -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Current Stock by Location</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th>Location</th>
                                    <th class="text-end">Quantity on Hand</th>
                                    <th class="text-end">Reorder Level</th>
                                    <th class="text-end">Target Stock Level</th>
                                    <th>Bin Location</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($product->stockHoldings as $holding)
                                    <tr>
                                        <td>
                                            @if($holding->location)
                                                <strong>{{ $holding->location->LocationName }}</strong>
                                            @else
                                                {{ $holding->LocationCode }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ number_format($holding->QuantityOnHand, 2) }}</strong>
                                        </td>
                                        <td class="text-end">{{ number_format($holding->ReorderLevel, 2) }}</td>
                                        <td class="text-end">{{ number_format($holding->TargetStockLevel, 2) }}</td>
                                        <td>{{ $holding->BinLocation ?? '-' }}</td>
                                        <td>
                                            @if($holding->isBelowReorderLevel())
                                                <span class="badge bg-danger">
                                                    <i class="mdi mdi-alert me-1"></i>Below Reorder Level
                                                </span>
                                            @elseif($holding->QuantityOnHand >= $holding->TargetStockLevel)
                                                <span class="badge bg-success">
                                                    <i class="mdi mdi-check me-1"></i>Adequate Stock
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="mdi mdi-minus me-1"></i>Low Stock
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <i class="mdi mdi-information-outline me-2"></i>
                                            No stock holdings found
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                                @if($product->stockHoldings->count() > 0)
                                    <tfoot class="table-light">
                                    <tr>
                                        <th>Total Across All Locations:</th>
                                        <th class="text-end">
                                            <span class="badge bg-primary fs-6">
                                                {{ number_format($product->stockHoldings->sum('QuantityOnHand'), 2) }}
                                            </span>
                                        </th>
                                        <th colspan="4"></th>
                                    </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Total Transactions</h6>
                                <h3 class="mb-0">{{ number_format($transactions->total()) }}</h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle rounded-circle">
                                <i class="mdi mdi-swap-horizontal text-primary font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Orders (Sold)</h6>
                                <h3 class="mb-0 text-danger">
                                    {{ number_format(abs($transactions->where('transaction_type', 'order')->sum('quantity_change')), 2) }}
                                </h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle rounded-circle">
                                <i class="mdi mdi-cart-arrow-down text-danger font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Imports (Added)</h6>
                                <h3 class="mb-0 text-success">
                                    +{{ number_format($transactions->where('transaction_type', 'import')->where('quantity_change', '>', 0)->sum('quantity_change'), 2) }}
                                </h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle rounded-circle">
                                <i class="mdi mdi-database-import text-success font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Adjustments</h6>
                                <h3 class="mb-0 text-info">
                                    {{ $transactions->whereIn('transaction_type', ['adjustment', 'transfer', 'return'])->count() }}
                                </h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-info-subtle rounded-circle">
                                <i class="mdi mdi-tune text-info font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Transaction History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th class="text-end">Before</th>
                                    <th class="text-end">Change</th>
                                    <th class="text-end">After</th>
                                    <th>Reference</th>
                                    <th>User</th>
                                    <th>Notes</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <small>{{ $transaction->created_at->format('Y-m-d') }}</small><br>
                                            <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            @if($transaction->location)
                                                {{ $transaction->location->LocationName }}
                                            @else
                                                {{ $transaction->LocationCode }}
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'order' => 'danger',
                                                    'return' => 'success',
                                                    'adjustment' => 'warning',
                                                    'transfer' => 'info',
                                                    'import' => 'primary',
                                                    'initial' => 'secondary'
                                                ];
                                                $typeIcons = [
                                                    'order' => 'cart-arrow-down',
                                                    'return' => 'keyboard-return',
                                                    'adjustment' => 'tune',
                                                    'transfer' => 'transfer',
                                                    'import' => 'database-import',
                                                    'initial' => 'database-plus'
                                                ];
                                                $color = $typeColors[$transaction->transaction_type] ?? 'secondary';
                                                $icon = $typeIcons[$transaction->transaction_type] ?? 'help-circle';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                <i class="mdi mdi-{{ $icon }} me-1"></i>
                                                {{ ucfirst($transaction->transaction_type) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($transaction->quantity_before, 2) }}</td>
                                        <td class="text-end">
                                            <span class="fw-bold {{ $transaction->quantity_change < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $transaction->quantity_change > 0 ? '+' : '' }}{{ number_format($transaction->quantity_change, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($transaction->quantity_after, 2) }}</td>
                                        <td>
                                            @if($transaction->reference_type == 'Order' && $transaction->reference_id)
                                                <a href="{{ route('orders.show', $transaction->reference_id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-eye me-1"></i>Order #{{ $transaction->reference_id }}
                                                </a>
                                            @elseif($transaction->reference_type == 'ImportJob' && $transaction->reference_id)
                                                <a href="{{ route('admin.imports.details', $transaction->reference_id) }}"
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="mdi mdi-eye me-1"></i>Import #{{ $transaction->reference_id }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->user)
                                                <small>{{ $transaction->user->name }}</small>
                                            @else
                                                <small class="text-muted">System</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->notes)
                                                <small class="text-muted">{{ Str::limit($transaction->notes, 30) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="mdi mdi-information-outline me-2"></i>
                                            No transactions found for this product
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
