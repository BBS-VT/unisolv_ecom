<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ $orderId ? 'Edit Order' : 'New Order' }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $orderId ? 'Edit Order #' . $orderNumber : __('global.new_order') }}</h4>
                </div>

                <div class="card-body">
                    <form wire:submit.prevent="save">
                        {{-- Order Header --}}
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="order_number">{{ __('cruds.order.fields.number') }}</label>
                                    <input
                                        type="text"
                                        wire:model="orderNumber"
                                        class="form-control"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">{{ __('cruds.order.fields.customer_name') }}</label>
                                    <div wire:ignore>
                                        <select
                                            id="customer-select"
                                            class="form-control @error('customerId') is-invalid @enderror"
                                            style="width: 100%;">
                                            <option value="">{{ __('global.pleaseSelect') }}</option>
                                            @if($customerId && $customerName)
                                                <option value="{{ $customerId }}" selected>{{ $customerName }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    @error('customerId')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="required" for="reference_number">{{ __('cruds.order.fields.ponumber') }}</label>
                                    <input
                                        type="text"
                                        wire:model="referenceNumber"
                                        class="form-control @error('referenceNumber') is-invalid @enderror">
                                    @error('referenceNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="required" for="order_date">{{ __('cruds.order.fields.order_date') }}</label>
                                    <input
                                        type="date"
                                        wire:model="orderDate"
                                        class="form-control @error('orderDate') is-invalid @enderror">
                                    @error('orderDate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Order Lines Table --}}
                        <div class="col-12 mt-5">
                            <div class="table-responsive">
                                <table class="table table-xl mb-0 thead-border-top-0 table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 30%">{{ __('global.products') }}</th>
                                        <th style="width: 8%">{{ __('global.quantity') }}</th>
                                        <th style="width: 8%">{{ __('global.stockOnhand') }}</th>
                                        <th style="width: 12%">{{ __('global.price') }}</th>
                                        @if($discountPerItem)
                                            <th style="width: 10%">{{ __('global.discount') }}</th>
                                        @endif
                                        @if($taxPerItem)
                                            <th style="width: 15%">{{ __('global.taxes') }}</th>
                                        @endif
                                        <th style="width: 12%" class="text-end">{{ __('global.total') }}</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($orderLines as $index => $line)
                                        <tr wire:key="line-{{ $line['id'] }}">
                                            {{-- Product Selection --}}
                                            <td>
                                                <div wire:ignore>
                                                    <select
                                                        id="product-select-{{ $index }}"
                                                        class="product-select form-control form-control-sm @error('orderLines.'.$index.'.product_id') is-invalid @enderror"
                                                        data-index="{{ $index }}"
                                                        style="width: 100%;"
                                                        @if(!$customerId) disabled @endif>
                                                        <option value="">{{ __('global.pleaseSelect') }}</option>
                                                        @if($line['product_id'])
                                                            <option value="{{ $line['product_id'] }}" selected>
                                                                {{ $line['product_code'] }} - {{ $line['product_name'] }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                                @error('orderLines.'.$index.'.product_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                {{-- Show additional pricing info when product is selected --}}
                                                @if($line['product_id'])
                                                    <small class="text-muted d-block mt-1">
                                                        @if($line['is_contract_price'])
                                                            <span class="badge bg-success">Contract Price</span>
                                                        @endif
                                                        P1: {{ number_format($line['price'], 2) }}
                                                        @if($line['price2'] > 0)
                                                            | P2: {{ number_format($line['price2'], 2) }}
                                                        @endif
                                                        @if($line['price3'] > 0)
                                                            | P3: {{ number_format($line['price3'], 2) }}
                                                        @endif
                                                        @if($line['price4'] > 0)
                                                            | P4: {{ number_format($line['price4'], 2) }}
                                                        @endif
                                                        @if($line['avg_cost'] > 0)
                                                            | Avg: {{ number_format($line['avg_cost'], 2) }}
                                                        @endif
                                                        @if($line['last_cost'] > 0)
                                                            | Last: {{ number_format($line['last_cost'], 2) }}
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>

                                            {{-- Quantity --}}
                                            <td>
                                                <input
                                                    type="number"
                                                    wire:model.live.debounce.500ms="orderLines.{{ $index }}.quantity"
                                                    class="form-control form-control-sm @error('orderLines.'.$index.'.quantity') is-invalid @enderror"
                                                    step="0.01"
                                                    min="0.01">
                                                @error('orderLines.'.$index.'.quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>

                                            {{-- Stock on Hand --}}
                                            <td>
                                                    <span class="badge {{ $line['stock_on_hand'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ number_format($line['stock_on_hand'], 2) }}
                                                    </span>
                                            </td>

                                            {{-- Price --}}
                                            <td>
                                                <input
                                                    type="number"
                                                    wire:model.live.debounce.500ms="orderLines.{{ $index }}.price"
                                                    class="form-control form-control-sm @error('orderLines.'.$index.'.price') is-invalid @enderror"
                                                    step="0.01"
                                                    min="0">
                                                @error('orderLines.'.$index.'.price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>

                                            {{-- Discount --}}
                                            @if($discountPerItem)
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input
                                                            type="number"
                                                            wire:model.live.debounce.500ms="orderLines.{{ $index }}.discount_percent"
                                                            class="form-control form-control-sm @error('orderLines.'.$index.'.discount_percent') is-invalid @enderror"
                                                            step="0.01"
                                                            min="0"
                                                            max="100"
                                                            @if($line['discount_locked']) readonly @endif>
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    @error('orderLines.'.$index.'.discount_percent')
                                                    <small class="text-danger d-block">{{ $message }}</small>
                                                    @enderror
                                                    @if($line['discount_locked'] && $line['is_contract_discount'])
                                                        <small class="text-success d-block">
                                                            <i class="fas fa-lock"></i> Contract Discount
                                                        </small>
                                                    @elseif($line['discount_locked'])
                                                        <small class="text-warning d-block">
                                                            <i class="fas fa-lock"></i> {{ $line['discount_reason'] }}
                                                        </small>
                                                    @elseif($line['max_discount'] < 100)
                                                        <small class="text-muted">Max: {{ $line['max_discount'] }}%</small>
                                                    @endif
                                                </td>
                                            @endif

                                            {{-- Taxes --}}
                                            @if($taxPerItem)
                                                <td>
                                                    <select
                                                        wire:model.live="orderLines.{{ $index }}.taxes"
                                                        class="form-control form-control-sm"
                                                        multiple
                                                        size="2">
                                                        @foreach($taxTypes as $tax)
                                                            <option value="{{ $tax->id }}">
                                                                {{ $tax->TaxTypeName }} ({{ $tax->TaxRate }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            @endif

                                            {{-- Line Total --}}
                                            <td class="text-end">
                                                <input
                                                    type="text"
                                                    value="{{ number_format($line['line_total'], 2) }}"
                                                    class="form-control form-control-sm text-end"
                                                    readonly>
                                            </td>

                                            {{-- Remove Button --}}
                                            <td class="text-center">
                                                <button
                                                    type="button"
                                                    wire:click="removeLine({{ $index }})"
                                                    class="btn btn-sm btn-danger"
                                                    @if(count($orderLines) <= 1) disabled @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Add Line Button --}}
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button
                                        type="button"
                                        wire:click="addLine"
                                        class="btn btn-light"
                                        @if(!$customerId) disabled @endif>
                                        <i class="fas fa-plus-circle"></i> {{ __('global.add') }} {{ __('cruds.product.title_singular') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Notes and Totals Section --}}
                        <div class="row mt-4">
                            {{-- Notes --}}
                            <div class="col-md-5 pr-4">
                                <div class="form-group">
                                    <label for="notes">{{ __('global.notes') }}</label>
                                    <textarea
                                        wire:model="notes"
                                        class="form-control"
                                        rows="2"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="private_notes">{{ __('global.private_notes') }}</label>
                                    <textarea
                                        wire:model="privateNotes"
                                        class="form-control"
                                        rows="2"></textarea>
                                </div>
                            </div>

                            {{-- Totals --}}
                            <div class="col-md-4 offset-md-3 pl-4">
                                <div class="card card-body shadow-none border">
                                    {{-- Subtotal --}}
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="h6 mb-0 w-50">
                                            <strong class="text-muted">{{ __('global.sub_total') }}</strong>
                                        </div>
                                        <div class="ms-auto h6 mb-0 text-end">
                                            {{ number_format($subTotal, 2) }}
                                        </div>
                                    </div>

                                    {{-- Tax Breakdown --}}
                                    @if(!empty($taxBreakdown))
                                        @foreach($taxBreakdown as $taxName => $taxAmount)
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="h6 mb-0 w-50">
                                                    <strong class="text-muted">{{ $taxName }}</strong>
                                                </div>
                                                <div class="ms-auto h6 mb-0 text-end">
                                                    {{ number_format($taxAmount, 2) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    {{-- Total Discount (if not per item) --}}
                                    @if(!$discountPerItem)
                                        <div class="row mb-3">
                                            <div class="col-12 h6 mb-1">
                                                <strong class="text-muted">{{ __('messages.discount') }}</strong>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        wire:model.live.debounce.500ms="totalDiscount"
                                                        type="number"
                                                        class="form-control"
                                                        step="0.01"
                                                        min="0"
                                                        max="100">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <hr>

                                    {{-- Grand Total --}}
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="h5 mb-0">
                                            <strong class="text-muted">{{ __('global.total') }}</strong>
                                        </div>
                                        <div class="ms-auto h5 mb-0">
                                            <strong>{{ number_format($grandTotal, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="text-center mt-3">
                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>{{ __('global.save') }}</span>
                                        <span wire:loading>
                                            <span class="spinner-border spinner-border-sm"></span> {{ __('global.saving') }}...
                                        </span>
                                    </button>
                                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                        {{ __('global.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

{{-- Loading Indicator Styles --}}
@push('styles')
    <style>
        [wire\:loading] {
            opacity: 0.6;
            pointer-events: none;
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Make multi-select taxes more compact */
        select[multiple] {
            height: auto !important;
            padding: 0.25rem;
        }

        select[multiple] option {
            padding: 0.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Initialize Customer Select2
            initCustomerSelect2();

            // Initialize Product Select2 for existing rows
            initAllProductSelects();

            // Re-initialize when Livewire adds new rows
            Livewire.hook('morph.added', ({ el }) => {
                initAllProductSelects();
            });

            // Re-initialize after Livewire updates
            Livewire.hook('morph.updated', ({ el, component }) => {
                initAllProductSelects();
            });
        });

        function initCustomerSelect2() {
            $('#customer-select').select2({
                ajax: {
                    url: "{{ route('ajax.customers') }}",
                    type: "get",
                    dataType: "json",
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                },
                placeholder: "{{ __('global.pleaseSelect') }}",
                allowClear: true,
                minimumInputLength: 2 // Only search after 2 characters
            }).on('change', function(e) {
                // Update Livewire property when selection changes
            @this.set('customerId', $(this).val());
            });
        }

        function initAllProductSelects() {
            $('.product-select').each(function() {
                let $select = $(this);
                let index = $select.data('index');

                // Destroy existing Select2 if it exists
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                // Initialize Select2
                $select.select2({
                    ajax: {
                        url: "{{ route('ajax.products') }}",
                        type: "get",
                        dataType: "json",
                        delay: 250,
                        data: function (params) {
                            return {
                                search: params.term,
                                customer_id: @this.customerId
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: response
                            };
                        },
                        cache: true
                    },
                    placeholder: "{{ __('global.pleaseSelect') }}",
                    allowClear: true,
                    minimumInputLength: 2, // Only search after 2 characters
                    dropdownAutoWidth: true,
                    width: '100%'
                }).on('change', function(e) {
                    let productId = $(this).val();

                    // Call Livewire method to handle product selection
                @this.call('productSelected', index, productId);
                });
            });
        }
    </script>
@endpush
