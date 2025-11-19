@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body py-4">
                        <h1 class="text-white mb-0">
                            <i class="mdi mdi-database-import me-2"></i>Import Status
                        </h1>
                        <p class="text-white-50 mb-0 mt-2">Monitor stock import progress and history</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress Imports -->
        @if($inProgressImports->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="mdi mdi-sync mdi-spin me-2"></i>In Progress
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($inProgressImports as $import)
                                <div class="mb-3" data-import-id="{{ $import->id }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>{{ $import->filename }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Started {{ $import->started_at->diffForHumans() }}
                                                @if($import->user)
                                                    by {{ $import->user->name }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="progress-percentage">{{ $import->progress_percentage }}%</div>
                                            <small class="text-muted">
                                                <span class="processed-rows">{{ number_format($import->processed_rows) }}</span> /
                                                {{ number_format($import->total_rows) }} rows
                                            </small>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                             role="progressbar"
                                             style="width: {{ $import->progress_percentage }}%"
                                             aria-valuenow="{{ $import->progress_percentage }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                            {{ $import->progress_percentage }}%
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-success me-3">
                                            <i class="mdi mdi-check-circle me-1"></i>
                                            <span class="successful-rows">{{ number_format($import->successful_rows) }}</span> successful
                                        </small>
                                        <small class="text-danger me-3">
                                            <i class="mdi mdi-alert-circle me-1"></i>
                                            <span class="failed-rows">{{ number_format($import->failed_rows) }}</span> failed
                                        </small>
                                        <small class="text-info">
                                            <i class="mdi mdi-update me-1"></i>
                                            <span class="items-updated">{{ number_format($import->items_updated) }}</span> items updated
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Imports -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Imports</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Filename</th>
                                    <th>Status</th>
                                    <th>Started</th>
                                    <th>Duration</th>
                                    <th class="text-end">Total Rows</th>
                                    <th class="text-end">Successful</th>
                                    <th class="text-end">Failed</th>
                                    <th class="text-end">Items Updated</th>
                                    <th>User</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentImports as $import)
                                    <tr>
                                        <td>
                                            <strong>{{ $import->filename }}</strong>
                                            @if($import->error_message)
                                                <br><small class="text-danger">{{ Str::limit($import->error_message, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{!! $import->status_badge !!}</td>
                                        <td>
                                            <small>{{ $import->started_at ? $import->started_at->format('Y-m-d H:i:s') : '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $import->duration ?? '-' }}</small>
                                        </td>
                                        <td class="text-end">{{ number_format($import->total_rows) }}</td>
                                        <td class="text-end text-success">{{ number_format($import->successful_rows) }}</td>
                                        <td class="text-end text-danger">{{ number_format($import->failed_rows) }}</td>
                                        <td class="text-end text-info">{{ number_format($import->items_updated) }}</td>
                                        <td>
                                            @if($import->user)
                                                {{ $import->user->name }}
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.imports.details', $import->id) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-eye me-1"></i>Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <i class="mdi mdi-information-outline me-2"></i>
                                            No imports found
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-refresh progress for in-progress imports
            @if($inProgressImports->count() > 0)
            setInterval(function() {
                @foreach($inProgressImports as $import)
                updateImportProgress({{ $import->id }});
                @endforeach
            }, 3000); // Update every 3 seconds

            function updateImportProgress(importId) {
                fetch(`/admin/imports/${importId}/progress`)
                    .then(response => response.json())
                    .then(data => {
                        const container = document.querySelector(`[data-import-id="${importId}"]`);
                        if (!container) return;

                        // Update progress bar
                        const progressBar = container.querySelector('.progress-bar');
                        progressBar.style.width = data.progress_percentage + '%';
                        progressBar.textContent = data.progress_percentage + '%';
                        progressBar.setAttribute('aria-valuenow', data.progress_percentage);

                        // Update text values
                        container.querySelector('.progress-percentage').textContent = data.progress_percentage + '%';
                        container.querySelector('.processed-rows').textContent = data.processed_rows.toLocaleString();
                        container.querySelector('.successful-rows').textContent = data.successful_rows.toLocaleString();
                        container.querySelector('.failed-rows').textContent = data.failed_rows.toLocaleString();
                        container.querySelector('.items-updated').textContent = data.items_updated.toLocaleString();

                        // Reload page if completed
                        if (data.status === 'completed' || data.status === 'failed') {
                            setTimeout(() => location.reload(), 2000);
                        }
                    })
                    .catch(error => console.error('Error updating progress:', error));
            }
            @endif
        </script>
    @endpush
@endsection
