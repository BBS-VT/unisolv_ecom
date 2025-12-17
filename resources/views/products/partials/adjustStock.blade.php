<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="mdi mdi-plus-minus-variant me-2"></i>Adjust Stock - {{ $product->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustStockForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Location</label>
                        <select name="location_code" class="form-select" required>
                            <option value="">Select location...</option>
                            @foreach($locations as $location)
                                @php
                                    $currentStock = $product->stockHoldings()
                                        ->where('LocationCode', $location->LocationCode)
                                        ->first();
                                    $qty = $currentStock ? $currentStock->QuantityOnHand : 0;
                                @endphp
                                <option value="{{ $location->LocationCode }}">
                                    {{ $location->display_name }} (Current: {{ $qty }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Adjustment Type</label>
                        <select name="adjustment_type" class="form-select" required>
                            <option value="add">Add to Stock</option>
                            <option value="subtract">Subtract from Stock</option>
                            <option value="set">Set Exact Quantity</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Quantity</label>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i>Adjust Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.getElementById('adjustStockForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            fetch('{{ route("products.adjust-stock", $product) }}', {
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
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while adjusting stock',
                    });
                    submitBtn.disabled = false;
                });
        });
    </script>
@endpush
