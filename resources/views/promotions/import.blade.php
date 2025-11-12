@extends('layouts.master')

@section('title', 'Import Promotions')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">{{ __('Import Promotions') }}</h1>
                <p class="mb-0 text-muted">{{ __('messages.import_promotions') }}</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Promotions') }}
                </a>
                <a href="{{ route('promotions.download-template') }}" class="btn btn-outline-primary">
                    <i class="fas fa-download me-1"></i> {{ __('Download Template') }}
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Import Form -->
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-upload me-2"></i>
                            {{ __('Upload File') }}
                        </h6>
                    </div>

                    <div class="card-body">
                        <form id="importForm" action="{{ route('promotions.import.process') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label for="import_file" class="form-label">
                                    <strong>Select Excel/CSV File</strong>
                                </label>
                                <input type="file" class="form-control @error('import_file') is-invalid @enderror"
                                       id="import_file" name="import_file"
                                       accept=".csv,.xlsx,.xls"
                                       onchange="previewFile()">

                                @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <div class="form-text">
                                    Supported formats: CSV, Excel (.xlsx, .xls). Maximum file size: 10MB
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="update_existing"
                                           name="update_existing" value="1" checked>
                                    <label class="form-check-label" for="update_existing">
                                        <strong>Update Existing Promotions</strong>
                                    </label>
                                </div>
                                <div class="form-text">
                                    If checked, existing promotions for the same product and location will be updated.
                                    Otherwise, duplicates will be skipped.
                                </div>
                            </div>

                            <!-- File Preview Section -->
                            <div id="file-preview" class="mb-4" style="display: none;">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-eye me-1"></i>
                                            File Preview
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="preview-content">
                                            <!-- Preview content will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="importBtn" disabled>
                                    <i class="fas fa-upload me-2"></i>
                                    <span class="btn-text">Import Promotions</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Instructions -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 fw-bold text-success">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('Import Instructions') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="small">
                            <h6 class="text-primary">Expected File Format:</h6>
                            <ul class="mb-3">
                                <li><strong>Column 0:</strong> Location Code</li>
                                <li><strong>Column 1:</strong> Location Name</li>
                                <li><strong>Column 6:</strong> Stock Code</li>
                                <li><strong>Column 9:</strong> Date From</li>
                                <li><strong>Column 10:</strong> Date To</li>
                                <li><strong>Column 11-14:</strong> Selling Prices 1-4</li>
                                <li><strong>Column 32:</strong> Quantity Type (F/B)</li>
                                <li><strong>Column 33-35:</strong> Price Breaks</li>
                                <li><strong>Column 36-44:</strong> Bonus Quantities</li>
                            </ul>

                            <h6 class="text-primary">Import Process:</h6>
                            <ol class="mb-3">
                                <li>Upload your POS export file</li>
                                <li>Preview file structure and data</li>
                                <li>Validate column mapping</li>
                                <li>Import with automatic validation</li>
                                <li>Review import results</li>
                            </ol>

                            <h6 class="text-primary">Automatic Actions:</h6>
                            <ul class="mb-0">
                                <li>Products marked as featured</li>
                                <li>Promotion types auto-detected</li>
                                <li>Prices converted to cents</li>
                                <li>Date formats normalized</li>
                                <li>Validation and error reporting</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Recent Imports -->
                @if(session('import_result'))
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 fw-bold text-info">
                                <i class="fas fa-history me-2"></i>
                                Last Import Results
                            </h6>
                        </div>
                        <div class="card-body">
                            @php $result = session('import_result') @endphp

                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <div class="border-end">
                                        <div class="h4 text-success mb-0">{{ $result['successful_rows'] ?? 0 }}</div>
                                        <small class="text-muted">Imported</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="h4 text-danger mb-0">{{ $result['error_count'] ?? 0 }}</div>
                                    <small class="text-muted">Errors</small>
                                </div>
                            </div>

                            @if($result['warning_count'] > 0)
                                <div class="alert alert-warning py-2 small">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $result['warning_count'] }} warnings generated
                                </div>
                            @endif

                            <div class="small">
                                <strong>Batch ID:</strong>
                                <code>{{ Str::limit($result['batch_id'] ?? '', 8) }}</code>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Processing Import...</h5>
                    <p class="text-muted mb-0">Please wait while we process your file. This may take a few minutes for large files.</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let previewData = null;

        function previewFile() {
            const fileInput = document.getElementById('import_file');
            const file = fileInput.files[0];
            const importBtn = document.getElementById('importBtn');
            const previewSection = document.getElementById('file-preview');
            const previewContent = document.getElementById('preview-content');

            if (!file) {
                previewSection.style.display = 'none';
                importBtn.disabled = true;
                return;
            }

            // Show loading state
            previewContent.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-primary me-2"></div>
            Analyzing file structure...
        </div>
    `;
            previewSection.style.display = 'block';

            // Create FormData for AJAX upload
            const formData = new FormData();
            formData.append('import_file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            fetch('{{ route("promotions.import.preview") }}', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    previewData = data;
                    displayPreview(data);
                    importBtn.disabled = !data.valid;
                })
                .catch(error => {
                    console.error('Preview error:', error);
                    previewContent.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Failed to preview file: ${error.message || 'Unknown error'}
            </div>
        `;
                    importBtn.disabled = true;
                });
        }

        function displayPreview(data) {
            const previewContent = document.getElementById('preview-content');

            let html = '';

            // Validation status
            if (data.valid) {
                html += `
            <div class="alert alert-success py-2">
                <i class="fas fa-check-circle me-1"></i>
                File structure is valid. Found ${data.row_count} data rows.
            </div>
        `;
            } else {
                html += `
            <div class="alert alert-danger py-2">
                <i class="fas fa-times-circle me-1"></i>
                ${data.message || 'File structure validation failed'}
            </div>
        `;

                if (data.missing_columns && data.missing_columns.length > 0) {
                    html += `
                <div class="alert alert-warning py-2">
                    <strong>Missing columns:</strong> ${data.missing_columns.join(', ')}
                </div>
            `;
                }
            }

            // File info
            html += `
        <div class="row mb-3">
            <div class="col-md-6">
                <small class="text-muted">File Size:</small><br>
                <strong>${formatFileSize(document.getElementById('import_file').files[0].size)}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted">Data Rows:</small><br>
                <strong>${data.row_count || 0}</strong>
            </div>
        </div>
    `;

            // Column mapping preview
            if (data.header) {
                html += `
            <h6 class="text-primary mb-2">Column Mapping:</h6>
            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Index</th>
                            <th>Found Header</th>
                            <th>Expected</th>
                            <th width="60">Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

                const expectedColumns = {
                    0: 'Location Code',
                    1: 'Location Name',
                    6: 'Stock Code',
                    9: 'Date From',
                    10: 'Date To',
                    11: 'Selling Price 1',
                    12: 'Selling Price 2',
                    13: 'Selling Price 3',
                    14: 'Selling Price 4'
                };

                Object.entries(expectedColumns).forEach(([index, expected]) => {
                    const actual = data.header[index] || '(missing)';
                    const isValid = data.header[index] && data.header[index].trim() !== '';
                    const statusIcon = isValid ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';

                    html += `
                <tr class="${isValid ? '' : 'table-warning'}">
                    <td>${index}</td>
                    <td>${actual}</td>
                    <td>${expected}</td>
                    <td class="text-center">${statusIcon}</td>
                </tr>
            `;
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;
            }

            // Sample data preview
            if (data.preview_rows && data.preview_rows.length > 0) {
                html += `
            <h6 class="text-primary mb-2">Sample Data:</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
        `;

                // Show first 10 columns for preview
                const maxCols = Math.min(10, data.header ? data.header.length : 10);
                for (let i = 0; i < maxCols; i++) {
                    const headerName = data.header && data.header[i] ? data.header[i] : `Col ${i}`;
                    html += `<th class="small">${headerName}</th>`;
                }

                html += `
                        </tr>
                    </thead>
                    <tbody>
        `;

                // Show first 3 sample rows
                data.preview_rows.slice(0, 3).forEach((row, rowIndex) => {
                    html += '<tr>';
                    for (let i = 0; i < maxCols; i++) {
                        const cellValue = row[i] || '';
                        const displayValue = cellValue.toString().length > 20 ?
                            cellValue.toString().substring(0, 20) + '...' : cellValue;
                        html += `<td class="small">${displayValue}</td>`;
                    }
                    html += '</tr>';
                });

                html += `
                    </tbody>
                </table>
            </div>
        `;

                if (data.row_count > 3) {
                    html += `<small class="text-muted">... and ${data.row_count - 3} more rows</small>`;
                }
            }

            previewContent.innerHTML = html;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Handle form submission
        document.getElementById('importForm').addEventListener('submit', function(e) {
            const importBtn = document.getElementById('importBtn');
            const btnText = importBtn.querySelector('.btn-text');

            // Disable button and show loading state
            importBtn.disabled = true;
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            // Show loading modal
            const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
            loadingModal.show();

            // Note: Form will submit normally, loading modal will be hidden on page reload
        });

        // Auto-hide alerts after 10 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                if (!alert.classList.contains('alert-permanent')) {
                    setTimeout(() => {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }, 10000);
                }
            });
        });

        // Drag and drop functionality
        const fileInput = document.getElementById('import_file');
        const importForm = document.getElementById('importForm');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            importForm.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            importForm.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            importForm.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            importForm.classList.add('border-primary', 'bg-light');
        }

        function unhighlight(e) {
            importForm.classList.remove('border-primary', 'bg-light');
        }

        importForm.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                fileInput.files = files;
                previewFile();
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .drag-over {
            border: 2px dashed #007bff !important;
            background-color: #f8f9ff !important;
        }

        .table th {
            border-top: none;
        }

        .table-sm td, .table-sm th {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .card-header h6 {
            line-height: 1.5;
        }

        #file-preview {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .alert-permanent {
            /* For alerts that shouldn't auto-hide */
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .btn-lg {
            padding: 0.75rem 1.5rem;
            font-size: 1.125rem;
        }

        .border-end {
            border-end: 1px solid #dee2e6 !important;
        }
    </style>
@endpush
