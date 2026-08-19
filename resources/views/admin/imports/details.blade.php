@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1C75BC 0%, #2A3042 100%);">
                    <div class="card-body py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="text-white mb-0">
                                    <i class="mdi mdi-database-import me-2"></i>Import Details
                                </h1>
                                <p class="text-white-50 mb-0 mt-2">{{ $importJob->filename }}</p>
                            </div>
                            <a href="{{ route('admin.imports.status') }}" class="btn btn-light">
                                <i class="mdi mdi-arrow-left me-1"></i>Back to Imports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted">Total Rows</h6>
                                <h3 class="mb-0">{{ number_format($importJob->total_rows) }}</h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle rounded-circle">
                                <i class="mdi mdi-file-document text-primary font-size-24"></i>
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
                                <h6 class="text-muted">Successful</h6>
                                <h3 class="mb-0 text-success">{{ number_format($importJob->successful_rows) }}</h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle rounded-circle">
                                <i class="mdi mdi-check-circle text-success font-size-24"></i>
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
                                <h6 class="text-muted">Failed</h6>
                                <h3 class="mb-0 text-danger">{{ number_format($importJob->failed_rows) }}</h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle rounded-circle">
                                <i class="mdi mdi-alert-circle text-danger font-size-24"></i>
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
                                <h6 class="text-muted">Items Updated</h6>
                                <h3 class="mb-0 text-info">{{ number_format($importJob->items_updated) }}</h3>
                            </div>
                            <div class="avatar-sm">
                            <span class="avatar-title bg-info-subtle rounded-circle">
                                <i class="mdi mdi-update text-info font-size-24"></i>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Import Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="150">Status:</th>
                                        <td>{!! $importJob->status_badge !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Started:</th>
                                        <td>{{ $importJob->started_at ? $importJob->started_at->format('Y-m-d H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Completed:</th>
                                        <td>{{ $importJob->completed_at ? $importJob->completed_at->format('Y-m-d H:i:s') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Duration:</th>
                                        <td>{{ $importJob->duration ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <th width="150">Imported By:</th>
                                        <td>{{ $importJob->user ? $importJob->user->name : 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Job ID:</th>
                                        <td><code>{{ $importJob->job_id }}</code></td>
                                    </tr>
                                    @if($importJob->error_message)
                                        <tr>
                                            <th>Error:</th>
                                            <td class="text-danger">{{ $importJob->error_message }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Changes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Stock Changes ({{ number_format($importJob->stockTransactions()->count()) }} total - page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Location</th>
                                    <th class="text-end">Before</th>
                                    <th class="text-end">Change</th>
                                    <th class="text-end">After</th>
                                    <th>Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>
                                            {{ $transaction->StockCode }}
                                            @if($transaction->product)
                                                <br><small class="text-muted">{{ Str::limit($transaction->product->StockItemName, 40) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->location)
                                                {{ $transaction->location->LocationName }}
                                            @else
                                                {{ $transaction->LocationCode }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($transaction->quantity_before, 2) }}</td>
                                        <td class="text-end">
                                            <span class="{{ $transaction->quantity_change < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $transaction->quantity_change > 0 ? '+' : '' }}{{ number_format($transaction->quantity_change, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end font-weight-bold">{{ number_format($transaction->quantity_after, 2) }}</td>
                                        <td>
                                            <small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="mdi mdi-information-outline me-2"></i>
                                            No stock changes recorded
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
