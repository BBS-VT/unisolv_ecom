{{-- Updated Product Import Modal --}}
<div class="modal fade" id="importStockmaster" tabindex="-1" aria-labelledby="importStockmasterLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="importStockmasterLabel">
                    <i class="bx bx-upload me-2"></i>
                    {{ __('global.import') }} {{ __('cruds.product.title') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.imports.process') }}" method="POST" enctype="multipart/form-data" id="importProductForm">
                @csrf

                <div class="modal-body">
                    {{-- Instructions Card --}}
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i class="bx bx-info-circle fs-4 me-2"></i>
                        <div>
                            <h6 class="alert-heading mb-1">{{ __('global.import_instructions') }}</h6>
                            <small>
                                {{ __('messages.product_import_instructions') }}
                            </small>
                        </div>
                    </div>

                    {{-- File Upload Section --}}
                    <div class="card border mb-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0">
                                <i class="bx bx-file me-1"></i>
                                {{ __('global.select_file') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="import_file" class="form-label">
                                    {{ __('global.csv_file') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="file"
                                       class="form-control @error('import_file') is-invalid @enderror"
                                       id="import_file"
                                       name="import_file"
                                       accept=".csv,.txt"
                                       required
                                       onchange="handleFileSelect(this)">
                                @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="bx bx-info-circle me-1"></i>
                                    {{ __('messages.csv_format_help') }}
                                </small>
                            </div>

                            {{-- File Preview --}}
                            <div id="filePreview" class="d-none">
                                <div class="alert alert-success d-flex align-items-center mb-0">
                                    <i class="bx bx-check-circle fs-4 me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong id="fileName"></strong>
                                        <div class="small text-muted" id="fileSize"></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Import Options --}}
                    <div class="card border">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0">
                                <i class="bx bx-cog me-1"></i>
                                {{ __('global.import_options') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing" checked>
                                <label class="form-check-label" for="update_existing">
                                    <strong>{{ __('global.update_existing_products') }}</strong>
                                    <br><small class="text-muted">{{ __('messages.update_existing_help') }}</small>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="create_new" name="create_new" checked>
                                <label class="form-check-label" for="create_new">
                                    <strong>{{ __('global.create_new_products') }}</strong>
                                    <br><small class="text-muted">{{ __('messages.create_new_help') }}</small>
                                </label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="skip_errors" name="skip_errors" checked>
                                <label class="form-check-label" for="skip_errors">
                                    <strong>{{ __('global.skip_errors') }}</strong>
                                    <br><small class="text-muted">{{ __('messages.skip_errors_help') }}</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Download Template Link --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.imports.download-template') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-download me-1"></i>
                            {{ __('global.download_template') }}
                        </a>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>
                        {{ __('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="importBtn">
                        <i class="bx bx-upload me-1"></i>
                        {{ __('global.upload_and_import') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function handleFileSelect(input) {
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Show preview
                filePreview.classList.remove('d-none');
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
            } else {
                filePreview.classList.add('d-none');
            }
        }

        function clearFile() {
            const fileInput = document.getElementById('import_file');
            const filePreview = document.getElementById('filePreview');

            fileInput.value = '';
            filePreview.classList.add('d-none');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Handle form submission with loading state
        document.getElementById('importProductForm').addEventListener('submit', function(e) {
            const importBtn = document.getElementById('importBtn');
            const originalText = importBtn.innerHTML;

            // Disable button and show loading
            importBtn.disabled = true;
            importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __("global.importing") }}...';

            // Note: Form will submit normally, button will re-enable on page reload
        });

        // Reset form when modal closes
        document.getElementById('importStockmaster').addEventListener('hidden.bs.modal', function () {
            document.getElementById('importProductForm').reset();
            clearFile();
        });
    </script>
@endpush
