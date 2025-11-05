{{-- Updated Stock Quantity Import Modal --}}
<div class="modal fade" id="importQuantities" tabindex="-1" aria-labelledby="importQuantitiesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="importQuantitiesLabel">
                    <i class="bx bx-package me-2"></i>
                    {{ __('global.import') }} {{ __('cruds.product.fields.quantity') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.stock-holdings.import') }}" method="POST" enctype="multipart/form-data" id="importQuantitiesForm">
                @csrf

                <div class="modal-body">
                    {{-- Instructions Card --}}
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0">
                                <i class="bx bx-info-circle me-1"></i>
                                {{ __('global.csv_format_requirements') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">{{ __('messages.stock_import_instructions') }}</p>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th style="width: 30%;">{{ __('global.column') }}</th>
                                        <th style="width: 20%;">{{ __('global.position') }}</th>
                                        <th style="width: 15%;">{{ __('global.required') }}</th>
                                        <th>{{ __('global.description') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><strong>Stock Code</strong></td>
                                        <td><code>Column A</code></td>
                                        <td><span class="badge bg-danger">Required</span></td>
                                        <td>{{ __('messages.stock_code_description') }}</td>
                                    </tr>
                                    @if($currentCompany->getSetting('sales_locations'))
                                        <tr class="table-info">
                                            <td><strong>Stock Location</strong></td>
                                            <td><code>Column DF</code></td>
                                            <td><span class="badge bg-warning">Optional</span></td>
                                            <td>{{ __('messages.location_code_description') }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Quantity on Hand</strong></td>
                                        <td><code>Column K</code></td>
                                        <td><span class="badge bg-danger">Required</span></td>
                                        <td>{{ __('messages.quantity_description') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bin Location</strong></td>
                                        <td><code>Column G</code></td>
                                        <td><span class="badge bg-warning">Optional</span></td>
                                        <td>{{ __('messages.bin_location_description') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last Cost Price</strong></td>
                                        <td><code>Column Z</code></td>
                                        <td><span class="badge bg-warning">Optional</span></td>
                                        <td>{{ __('messages.cost_price_description') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Reorder Level</strong></td>
                                        <td><code>Column Q</code></td>
                                        <td><span class="badge bg-warning">Optional</span></td>
                                        <td>{{ __('messages.reorder_level_description') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Target Stock Level</strong></td>
                                        <td><code>Column S</code></td>
                                        <td><span class="badge bg-warning">Optional</span></td>
                                        <td>{{ __('messages.target_stock_description') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-warning d-flex align-items-start mt-3 mb-0">
                                <i class="bx bx-time fs-4 me-2"></i>
                                <div>
                                    <strong>{{ __('global.background_processing') }}</strong>
                                    <p class="mb-0 small">{{ __('messages.background_import_help') }}</p>
                                </div>
                            </div>
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
                                <label for="quantities_import_file" class="form-label">
                                    {{ __('global.csv_file') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="file"
                                       class="form-control @error('import_file') is-invalid @enderror"
                                       id="quantities_import_file"
                                       name="import_file"
                                       accept=".csv,.txt"
                                       required
                                       onchange="handleQuantitiesFileSelect(this)">
                                @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="bx bx-info-circle me-1"></i>
                                    {{ __('messages.csv_format_help') }}
                                </small>
                            </div>

                            {{-- File Preview --}}
                            <div id="quantitiesFilePreview" class="d-none">
                                <div class="alert alert-success d-flex align-items-center mb-0">
                                    <i class="bx bx-check-circle fs-4 me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong id="quantitiesFileName"></strong>
                                        <div class="small text-muted" id="quantitiesFileSize"></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearQuantitiesFile()">
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
                            @if($currentCompany->getSetting('sales_locations'))
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="create_missing_locations" name="create_missing_locations">
                                    <label class="form-check-label" for="create_missing_locations">
                                        <strong>{{ __('global.create_missing_locations') }}</strong>
                                        <br><small class="text-muted">{{ __('messages.create_missing_locations_help') }}</small>
                                    </label>
                                </div>
                            @endif

                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="update_prices" name="update_prices" checked>
                                <label class="form-check-label" for="update_prices">
                                    <strong>{{ __('global.update_cost_prices') }}</strong>
                                    <br><small class="text-muted">{{ __('messages.update_prices_help') }}</small>
                                </label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="skip_quantity_errors" name="skip_errors" checked>
                                <label class="form-check-label" for="skip_quantity_errors">
                                    <strong>{{ __('global.skip_errors') }}</strong>
                                    <br><small class="text-muted">{{ __('messages.skip_quantity_errors_help') }}</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Download Template Link --}}
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.stock-holdings.download-template') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-download me-1"></i>
                            {{ __('global.download_stock_template') }}
                        </a>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>
                        {{ __('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-success" id="importQuantitiesBtn">
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
        function handleQuantitiesFileSelect(input) {
            const filePreview = document.getElementById('quantitiesFilePreview');
            const fileName = document.getElementById('quantitiesFileName');
            const fileSize = document.getElementById('quantitiesFileSize');

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

        function clearQuantitiesFile() {
            const fileInput = document.getElementById('quantities_import_file');
            const filePreview = document.getElementById('quantitiesFilePreview');

            fileInput.value = '';
            filePreview.classList.add('d-none');
        }

        // Handle form submission with loading state
        document.getElementById('importQuantitiesForm').addEventListener('submit', function(e) {
            const importBtn = document.getElementById('importQuantitiesBtn');
            const originalText = importBtn.innerHTML;

            // Disable button and show loading
            importBtn.disabled = true;
            importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __("global.importing") }}...';

            // Note: Form will submit normally, button will re-enable on page reload
        });

        // Reset form when modal closes
        document.getElementById('importQuantities').addEventListener('hidden.bs.modal', function () {
            document.getElementById('importQuantitiesForm').reset();
            clearQuantitiesFile();
        });
    </script>
@endpush
