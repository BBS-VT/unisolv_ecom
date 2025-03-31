@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            Import Status
        </div>

        <div class="card-body">
            @if(Session::has('import_job_id'))
                <div class="alert alert-info" id="current-import-alert">
                    <h5>Current Import Progress</h5>
                    <div class="progress" style="height: 25px;">
                        <div id="current-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar" style="width: 0%;"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                    <p class="mt-2" id="current-status-text">Starting import...</p>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const importJobId = {{ Session::get('import_job_id') }};
                        const checkProgress = () => {
                            fetch(`/admin/imports/check-progress/${importJobId}`)
                                .then(response => response.json())
                                .then(data => {
                                    const progressBar = document.getElementById('current-progress-bar');
                                    const statusText = document.getElementById('current-status-text');

                                    // Update progress bar
                                    progressBar.style.width = `${data.progress}%`;
                                    progressBar.setAttribute('aria-valuenow', data.progress);
                                    progressBar.textContent = `${data.progress}%`;

                                    // Update status text
                                    if (data.status === 'completed') {
                                        statusText.textContent = `Import completed successfully! Processed ${data.processed_rows} records.`;
                                        progressBar.classList.remove('progress-bar-animated');
                                        progressBar.classList.remove('bg-info');
                                        progressBar.classList.add('bg-success');
                                        clearInterval(progressInterval);
                                    } else if (data.status === 'failed') {
                                        statusText.textContent = `Import failed: ${data.error_message}`;
                                        progressBar.classList.remove('progress-bar-animated');
                                        progressBar.classList.remove('bg-info');
                                        progressBar.classList.add('bg-danger');
                                        clearInterval(progressInterval);
                                    } else {
                                        statusText.textContent = `Processing: ${data.processed_rows} of ${data.total_rows} records (${data.progress}%)`;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error checking import progress:', error);
                                });
                        };

                        // Check progress every 3 seconds
                        const progressInterval = setInterval(checkProgress, 3000);

                        // Initial check
                        checkProgress();
                    });
                </script>
            @endif

            <h5 class="mt-4">Recent Imports</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($recentImports as $import)
                        <tr>
                            <td>{{ $import->filename }}</td>
                            <td>{{ $import->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($import->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($import->status == 'processing')
                                    <span class="badge badge-info">Processing</span>
                                @elseif($import->status == 'failed')
                                    <span class="badge badge-danger">Failed</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar @if($import->status == 'completed') bg-success
                                                            @elseif($import->status == 'failed') bg-danger @endif"
                                         role="progressbar" style="width: {{ $import->progress_percentage }}%;"
                                         aria-valuenow="{{ $import->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $import->progress_percentage }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $import->processed_rows }} / {{ $import->total_rows }} rows
                                @if($import->status == 'failed')
                                    <br>
                                    <small class="text-danger">{{ $import->error_message }}</small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
