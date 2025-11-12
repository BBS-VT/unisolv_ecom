@extends('layouts.app', ['page' => 'orders'])

@section('title', __('global.create_order'))

@section('style')
    <link href="{{ URL::asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <style>
        #orderDetailsTable .form-control {
            padding: 0.3rem 0.5rem;
            font-size: 0.875rem;
        }

        .product-select {
            width: 100%;
        }

        .stock-input, .price-select, .quantity-input, .discount-input, .total-input {
            max-width: 80%;
            text-align: center;
        }

        .remove-row-btn {
            padding: 0.2rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('content')

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Create Sales Order</h1>
        </div>
    </div>

    <!-- Customer and Order Details -->
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="order_number">Order Number</label>
                <input type="text" id="order_number" class="form-control" value="{{ $order->order_number ?? 'Auto-Generated' }}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="customer">Customer</label>
                <select id="customer" name="customer_id" data-bs-toggle="select" class="form-control select2">
                    <option disabled selected>Select Customer</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="reference_number">Reference Number</label>
                <input type="text" id="reference_number" class="form-control" placeholder="Enter PO Number">
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="table-responsive">
        <table class="table table-bordered" id="orderDetailsTable">
            <thead class="thead-light">
            <tr>
                <th style="width: 40%;">Product</th>
                <th class="d-none d-sm-table-cell" style="width: 10%;">Stock On Hand</th>
                <th style="width: 15%;">Price</th>
                <th style="width: 10%;">Quantity</th>
                <th class="d-none d-sm-table-cell" style="width: 10%;">Discount</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 5%;">Action</th>
            </tr>
            </thead>
            <tbody>
            <!-- Product Row Template -->
            <tr id="productRowTemplate" class="d-none">
                <td>
                    <select class="form-control select2 product-select">
                        <option disabled selected>Select Product</option>
                    </select>
                    <div class="product-prices mt-3" style="display: none;">
                        <p><strong>Selling Price 1:</strong> <span id="price"></span></p>
                        <p><strong>Selling Price 2:</strong> <span id="price2"></span></p>
                        <p><strong>Selling Price 3:</strong> <span id="price3"></span></p>
                        <p><strong>Average Cost:</strong> <span id="average-cost"></span></p>
                        <p><strong>Last Cost:</strong> <span id="last-cost"></span></p>
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control stock-input" readonly>
                </td>
                <td>
                    <select class="form-control price-select">
                        <option disabled selected>Select Price</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control quantity-input" value="1" min="1">
                </td>
                <td>
                    <input type="number" class="form-control discount-input" value="0" min="0">
                </td>
                <td>
                    <input type="text" class="form-control total-input" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-row-btn">
                        <i class="material-icons icon-16pt">clear</i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
        <button type="button" id="addProductRow" class="btn btn-primary mt-3">Add Product</button>
    </div>

    <!-- Order Summary -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" class="form-control" rows="4" placeholder="Enter any additional notes..."></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <label>Subtotal</label>
                        <input type="text" id="subtotal" class="form-control text-end" value="0.00" readonly>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <label>Discount</label>
                        <input type="text" id="totalDiscount" class="form-control text-end" value="0.00" readonly>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <label>Tax</label>
                        <input type="text" id="totalTax" class="form-control text-end" value="0.00" readonly>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mt-2">
                        <label><strong>Total</strong></label>
                        <input type="text" id="grandTotal" class="form-control text-end fw-bold" value="0.00" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="row mt-4">
        <div class="col text-end">
            <button type="button" id="saveOrder" class="btn btn-success save_order_button pull-right">Save Order</button>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ URL::asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ URL::asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>

<script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $(document).ready(function () {
        // Initialize Select2 for customer and product selection
        $('#customer').select2({
            ajax: {
                url: "{{ route('ajax.customers') }}",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term
                    };
                },
                processResults: function (response) {
                    return { results: response };
                },
                cache: true
            },

        });

        // Setup Customer options
        $("#customer").change(function() {
            setupCustomer();
        });

        function setupCustomer() {
            var customer_id = $("#customer").val();

            // Get and set customer's default price
            var defaultPrice = $('#customer').find(':selected').data('default_price');
            window.sharedData.default_price = defaultPrice || 'price';
        }

        $('#addProductRow').on('click', function () {
            const template = $('#productRowTemplate').clone().removeClass('d-none');
            $('#orderDetailsTable tbody').append(template);

            var customer_id = $("#customer").val();

            // Initialize Select2 for the product dropdown
            template.find('.product-select').select2({
                ajax: {
                    url: "{{ route('ajax.products') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            _token: CSRF_TOKEN,
                            search: params.term,
                            customer_id: customer_id
                        };
                    },
                    processResults: function (data) {
                        return { results: data };
                    },
                    cache: true,
                },
                width: 'resolve',
                dropdownAutoWidth: true
            });

            // Handle product selection to populate prices
            template.find('.product-select').on('change', function () {
                const selectedProductId = $(this).val();
                const row = $(this).closest('tr');
                const priceSelect = row.find('.price-select');
                const stockInput = row.find('.stock-input');
                const productPricesSection = row.find('.product-prices');

                $aja
            })
            /*template.find('.product-select').on('change', function () {
                const selectedOption = $(this).find(':selected');
                const priceSelect = template.find('.price-select');
                const stockInput = template.find('.stock-input');

                // Populate prices and stock
                priceSelect.empty().append(`
                    <option value="${selectedOption.data('price')}">Price 1 - ${selectedOption.data('price')}</option>
                    <option value="${selectedOption.data('price2')}">Price 2 - ${selectedOption.data('price2')}</option>
                    <option value="${selectedOption.data('price3')}">Price 3 - ${selectedOption.data('price3')}</option>
                `);

                stockInput.val(selectedOption.data('stock'));
            });*/

            // Handle price and quantity updates
            template.find('.quantity-input, .price-select, .discount-input').on('input change', function () {
                const row = $(this).closest('tr');
                const quantity = parseFloat(row.find('.quantity-input').val());
                const price = parseFloat(row.find('.price-select').val());
                const discount = parseFloat(row.find('.discount-input').val()) || 0;

                const discountAmount = (quantity * price * discount) / 100;
                const total = (quantity * price) - discountAmount;

                row.find('.total-input').val(total.toFixed(2));
            });
        });

        // Remove product row
        $(document).on('click', '.remove-row-btn', function () {
            $(this).closest('tr').remove();
        });
    });
</script>
@endsection
