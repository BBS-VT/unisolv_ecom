{{-- resources/views/components/stock-breakdown.blade.php --}}
@props(['product', 'showDetails' => false])

@php
    $status = $product->stock_status;
    $breakdown = $product->stock_breakdown;
    $totalBaseUnits = $product->total_base_units;
    $currentItem = collect($breakdown)->firstWhere('is_current', true);
@endphp

<div {{ $attributes->merge(['class' => 'stock-info']) }}>
    @if($showDetails)
        {{-- Detailed breakdown --}}
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i>Stock Availability
                </h6>
            </div>
            <div class="card-body">
                @if(count($breakdown) > 1)
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        This product is part of a pack size family. We can break larger packs into smaller units to fulfill your order.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>Pack Type</th>
                                <th class="text-center">Units per Pack</th>
                                <th class="text-end">Complete Packs</th>
                                <th class="text-end">Total Available</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($breakdown as $item)
                                <tr class="{{ $item['is_current'] ? 'table-primary' : '' }}">
                                    <td>
                                        {{ $item['product_name'] }}
                                        @if($item['is_current'])
                                            <span class="badge bg-primary ms-1">Viewing</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['pack_size'] == 1)
                                            <span class="badge bg-info">Single Unit</span>
                                        @else
                                            <span class="badge bg-secondary">{{ number_format($item['pack_size']) }} units</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($item['direct_stock']) }}
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($item['total_available']) }}</strong>
                                        @if($item['total_available'] > $item['direct_stock'])
                                            <small class="text-muted d-block">
                                                (+{{ number_format($item['total_available'] - $item['direct_stock']) }} from smaller units)
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Total Base Units (Singles):</td>
                                <td class="text-end">{{ number_format($totalBaseUnits) }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($currentItem)
                        <div class="alert alert-success mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Available to purchase:</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $product->stock_display }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <h4 class="mb-0">{{ number_format($currentItem['total_available']) }}</h4>
                                    <small>{{ $product->UnitOfMeasure ?? 'packs' }}</small>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Single pack size only --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Quantity Available:</span>
                        <strong class="text-{{ $status['class'] }}">
                            {{ number_format($status['quantity']) }} {{ $product->UnitOfMeasure ?? 'units' }}
                        </strong>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Compact badge --}}
        <div class="stock-badge">
            <span class="badge bg-{{ $status['class'] }}">
                <i class="bi bi-{{ $status['icon'] }} me-1"></i>
                {{ $status['text'] }}
                @if($status['quantity'] > 0)
                    <span class="ms-1">({{ number_format($status['quantity']) }})</span>
                @endif
            </span>

            @if($currentItem && $currentItem['total_available'] > $currentItem['direct_stock'])
                <small class="text-muted d-block mt-1">
                    <i class="bi bi-info-circle me-1"></i>
                    Includes {{ number_format($currentItem['total_available'] - $currentItem['direct_stock']) }} from smaller units
                </small>
            @endif
        </div>
    @endif
</div>
