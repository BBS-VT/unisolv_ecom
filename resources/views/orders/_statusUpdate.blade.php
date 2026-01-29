<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="statusUpdateForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="order_id" name="order_id">
                    <input type="hidden" id="delivery_method" name="delivery_method">

                    <div class="alert alert-info" id="orderInfo">
                        <strong>Order:</strong> <span id="orderNumber"></span><br>
                        <strong>Customer:</strong> <span id="customerName"></span><br>
                        <strong>Current Status:</strong> <span id="currentStatusBadge"></span><br>
                        <strong>Delivery Method:</strong> <span id="deliveryMethodText"></span>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">New Status <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="orderstatus" name="orderstatus" required>
                            <option value="">Select Status...</option>
                            @foreach($orderStatus as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" value="1" checked>
                            <label class="form-check-label" for="send_notification">
                                Send customer notification email
                            </label>
                        </div>
                        <small class="text-muted">Customer will be notified about the status change</small>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Any additional information for the customer..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).on('click', '.btn-update-status', function() {
            const orderId = $(this).data('order-id');
            const orderNumber = $(this).data('order-number');
            const customerName = $(this).data('customer-name');
            const deliveryMethod = $(this).data('delivery-method');
            const currentStatusId = $(this).data('current-status-id');
            const currentStatusName = $(this).data('current-status-name');
            const currentStatusColor = $(this).data('current-status-color');

            // Populate modal
            $('#order_id').val(orderId);
            $('#orderNumber').text(orderNumber);
            $('#customerName').text(customerName);
            $('#delivery_method').val(deliveryMethod);
            $('#deliveryMethodText').text(deliveryMethod === 'collection' ? 'Collection' : 'Delivery');
            $('#currentStatusBadge').html(`<span class="badge bg-${currentStatusColor}">${currentStatusName}</span>`);

            // Pre-select suggested status based on delivery method
            if (deliveryMethod === 'collection') {
                $('#orderstatus').val(6); // Ready for Collection
            } else if (deliveryMethod === 'delivery') {
                $('#orderstatus').val(7); // Ready for Delivery
            }

            // Set form action
            $('#statusUpdateForm').attr('action', `/orders/${orderId}/status`);

            // Show modal
            $('#statusModal').modal('show');
        });

        $('#statusUpdateForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Updating...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#statusModal').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reload the page or update the row
                    setTimeout(() => window.location.reload(), 2000);
                },
                error: function(xhr) {
                    let message = 'An error occurred while updating the status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="bx bx-save me-1"></i>Update Status');
                }
            });
        });
    </script>
@endpush
