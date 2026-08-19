@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);">
                    <div class="card-body py-4">
                        <h1 class="text-white mb-0">
                            <i class="mdi mdi-history me-2"></i>Stock Transaction History
                        </h1>
                        <p class="text-white-50 mb-0 mt-2">View all stock movements and changes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.stock-transactions.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Product Code</label>
                                <input type="text" name="stock_code" class="form-control"
                                       value="{{ request('stock_code') }}" placeholder="e.g., 000000024685">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Location</label>
                                <select name="location_code" class="form-select">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->LocationCode }}"
                                            {{ request('location_code') == $location->LocationCode ? 'selected' : '' }}>
                                            {{ $location->LocationName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    @foreach($transactionTypes as $key => $label)
                                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="mdi mdi-filter me-1"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>Product</th>
                                    <th>Location</th>
                                    <th>Type</th>
                                    <th class="text-end">Before</th>
                                    <th class="text-end">Change</th>
                                    <th class="text-end">After</th>
                                    <th>Reference</th>
                                    <th>User</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ $transaction->created_at->format('Y-m-d H:i:s') }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.stock-transactions.product', $transaction->StockCode) }}">
                                                {{ $transaction->StockCode }}
                                            </a>
                                            @if($transaction->product)
                                                <br><small class="text-muted">{{ Str::limit($transaction->product->StockItemName, 30) }}</small>
                                            @endif
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
                                                $color = $typeColors[$transaction->transaction_type] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst($transaction->transaction_type) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($transaction->quantity_before, 2) }}</td>
                                        <td class="text-end">
                                            <span class="{{ $transaction->quantity_change < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $transaction->quantity_change > 0 ? '+' : '' }}{{ number_format($transaction->quantity_change, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end font-weight-bold">{{ number_format($transaction->quantity_after, 2) }}</td>
                                        <td>
                                            @if($transaction->reference_type == 'Order' && $transaction->reference_id)
                                                <a href="{{ route('orders.show', $transaction->reference_id) }}">
                                                    Order #{{ $transaction->reference_id }}
                                                </a>
                                            @elseif($transaction->reference_type == 'ImportJob' && $transaction->reference_id)
                                                <a href="{{ route('admin.imports.details', $transaction->reference_id) }}">
                                                    Import #{{ $transaction->reference_id }}
                                                </a>
                                            @else
                                                <small class="text-muted">{{ $transaction->notes }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->user)
                                                {{ $transaction->user->name }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="mdi mdi-information-outline me-2"></i>
                                            No transactions found
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
