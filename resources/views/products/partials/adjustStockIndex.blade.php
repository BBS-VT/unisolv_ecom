<div class="modal fade" id="adjustStockModalIndex" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="mdi mdi-plus-minus-variant me-2"></i>Adjust Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustStockFormIndex">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information me-2"></i>
                        Search and select a product, then adjust its stock quantity at a specific location.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="productSearchSelect" class="form-select" required style="width: 100%;">
                            <option value="">-- Search for a product --</option>
                        </select>
                        <small class="text-muted">Start typing product name or SKU to search</small>
                    </div>

                    {{-- Product info display --}}
                    <div id="productInfoDisplay" style="display: none;" class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Product:</strong> <span id="displayProductName"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>SKU:</strong> <span id="displayProductSKU"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="adjustmentFields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Location <span class="text-danger">*</span></label>
                            <select name="location_code" id="modalLocationCode" class="form-select" required>
                                <option value="">Select location...</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->LocationCode }}"
                                            data-location-name="{{ $location->display_name }}">
                                        {{ $location->display_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">
                                Current stock: <span id="currentStockDisplay" class="fw-bold">-</span>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="adjustment_type" class="form-select" required>
                                <option value="add">Add to Stock</option>
                                <option value="subtract">Subtract from Stock</option>
                                <option value="set">Set Exact Quantity</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control" min="0" step="1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Reason</label>
                            <select name="reason" class="form-select">
                                <option value="">Select reason...</option>
                                <option value="damaged">Damaged/Defective</option>
                                <option value="lost">Lost/Missing</option>
                                <option value="found">Found/Located</option>
                                <option value="correction">Inventory Correction</option>
                                <option value="sample">Sample/Demo</option>
                                <option value="theft">Theft</option>
                                <option value="return">Customer Return</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes about this adjustment..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitAdjustmentBtn" disabled>
                        <i class="mdi mdi-check me-1"></i>Adjust Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for product search
            $('#productSearchSelect').select2({
                dropdownParent: $('#adjustStockModalIndex'),
                ajax: {
                    url: '{{ route("products.search") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        console.log('Select2 sending request:', {
                            term: params.term,
                            page: params.page || 1
                        });

                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        console.log('Select2 received response:', data);

                        params.page = params.page || 1;

                        if (!data.items || data.items.length === 0) {
                            console.warn('No items returned from search');
                        }

                        return {
                            results: data.items.map(function(item) {
                                console.log('Mapping item:', item);
                                return {
                                    id: item.id,
                                    text: item.StockCode + ' - ' + item.StockItemName,
                                    stockCode: item.StockCode,
                                    name: item.StockItemName
                                };
                            }),
                            pagination: {
                                more: data.has_more
                            }
                        };
                    },
                    cache: true,
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Select2 AJAX error:', {
                            status: textStatus,
                            error: errorThrown,
                            response: jqXHR.responseText
                        });
                    }
                },
                placeholder: 'Search for a product by name or SKU',
                minimumInputLength: 2,
                templateResult: formatProduct,
                templateSelection: formatProductSelection
            });

            function formatProduct(product) {
                if (product.loading) {
                    return product.text;
                }

                var $container = $(
                    "<div class='select2-result-product clearfix'>" +
                    "<div class='select2-result-product__meta'>" +
                    "<div class='select2-result-product__title'></div>" +
                    "<div class='select2-result-product__sku'></div>" +
                    "</div>" +
                    "</div>"
                );

                $container.find(".select2-result-product__title").text(product.name || product.text);
                $container.find(".select2-result-product__sku").text('SKU: ' + (product.stockCode || ''));

                return $container;
            }

            function formatProductSelection(product) {
                return product.text;
            }

            // Handle product selection
            $('#productSearchSelect').on('select2:select', function (e) {
                var data = e.params.data;

                // Show product info
                $('#displayProductName').text(data.name);
                $('#displayProductSKU').text(data.stockCode);
                $('#productInfoDisplay').slideDown();

                // Show adjustment fields
                $('#adjustmentFields').slideDown();

                // Enable submit button
                $('#submitAdjustmentBtn').prop('disabled', false);

                // Reset location and current stock display
                $('#modalLocationCode').val('').trigger('change');
                $('#currentStockDisplay').text('-');
            });

            // Load current stock when location is selected
            $('#modalLocationCode').on('change', function() {
                const productId = $('#productSearchSelect').val();
                const locationCode = $(this).val();

                if (productId && locationCode) {
                    // Show loading
                    $('#currentStockDisplay').html('<span class="spinner-border spinner-border-sm"></span>');

                    // Fetch current stock for this product/location
                    fetch(`/api/products/${productId}/stock/${locationCode}`)
                        .then(response => response.json())
                        .then(data => {
                            $('#currentStockDisplay').text(data.quantity || 0);
                        })
                        .catch(error => {
                            console.error('Error fetching stock:', error);
                            $('#currentStockDisplay').text('Error loading');
                        });
                } else {
                    $('#currentStockDisplay').text('-');
                }
            });

            // Handle form submission
            $('#adjustStockFormIndex').on('submit', function(e) {
                e.preventDefault();

                const productId = $('#productSearchSelect').val();

                if (!productId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select a product first',
                    });
                    return;
                }

                const formData = new FormData(this);
                const submitBtn = $('#submitAdjustmentBtn');
                submitBtn.prop('disabled', true);

                fetch(`/products/${productId}/adjust-stock`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message,
                                timer: 2000,
                            }).then(() => {
                                $('#adjustStockModalIndex').modal('hide');
                                // Refresh DataTable if you have one
                                if ($.fn.DataTable.isDataTable('.dataTable')) {
                                    $('.dataTable').DataTable().ajax.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                            submitBtn.prop('disabled', false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while adjusting stock',
                        });
                        submitBtn.prop('disabled', false);
                    });
            });

            // Reset modal when closed
            $('#adjustStockModalIndex').on('hidden.bs.modal', function () {
                $('#adjustStockFormIndex')[0].reset();
                $('#productSearchSelect').val(null).trigger('change');
                $('#productInfoDisplay').hide();
                $('#adjustmentFields').hide();
                $('#submitAdjustmentBtn').prop('disabled', true);
                $('#currentStockDisplay').text('-');
            });
        });
    </script>
@endpush
