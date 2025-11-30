{{-- resources/views/livewire/sales-order/order-form.blade.php --}}

<div class="container-fluid">
    {{-- Page Title --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">{{ $orderId ? 'Edit Order #' . $orderNumber : 'New Order' }}</h4>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $orderId ? 'Edit Order #' . $orderNumber : __('global.new_order') }}</h4>
                </div>

                <div class="card-body">
                    {{-- Debug Info --}}
                    <div class="alert alert-info">
                        <strong>DEBUG:</strong>
                        Customer: {{ $customerId ?? 'Not selected' }} |
                        Lines: {{ count($orderLines) }} |
                        Total: {{ $grandTotal }}
                    </div>

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
                                    <label class="required">Customer</label>
                                    <select
                                        wire:model.live="customerId"
                                        class="form-control @error('customerId') is-invalid @enderror">
                                        <option value="">Please Select</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->acc_main }}">{{ $customer->CustomerName }}</option>
                                        @endforeach
                                    </select>
                                    @error('customerId')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="required">Reference Number</label>
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
                                    <label class="required">Order Date</label>
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

                        {{-- Simple test of order lines --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Order Lines ({{ count($orderLines) }})</h5>

                                <button
                                    type="button"
                                    wire:click="addLine"
                                    class="btn btn-primary mb-3">
                                    Add Line
                                </button>

                                @foreach($orderLines as $index => $line)
                                    <div class="card mb-2" wire:key="line-{{ $line['id'] }}">
                                        <div class="card-body">
                                            <p>Line {{ $index + 1 }}: Product ID = {{ $line['product_id'] ?? 'none' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <button
                                    type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Save Order</span>
                                    <span wire:loading>Saving...</span>
                                </button>
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif
</div>
