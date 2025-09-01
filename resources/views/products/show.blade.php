@extends('layouts.master')

@section('title', __('global.product_management'))

@push('styles')
    <style>
        .column-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .product-info-item {
            margin-bottom: 8px;
        }
        .product-info-label {
            font-weight: 500;
            color: #6c757d;
        }
        .product-price {
            font-size: 1.5rem;
            font-weight: 600;
            color: #28a745;
        }
        .product-stock-label {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .in-stock {
            background-color: #d4edda;
            color: #155724;
        }
        .low-stock {
            background-color: #fff3cd;
            color: #856404;
        }
        .out-of-stock {
            background-color: #f8d7da;
            color: #721c24;
        }
        .barcode-display {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            font-family: monospace;
            letter-spacing: 2px;
        }
    </style>
@endpush

@section('content')
    @php
        // Get VAT rate - TODO: link to vat_types table
        $vatRate = 0.15; // 15% VAT

        // Function to calculate exclusive price
        function calculateExclusivePrice($inclusivePrice, $vatRate) {
            if (!$inclusivePrice) return null;
            return $inclusivePrice / (1 + $vatRate);
        }

        // Calculate exclusive prices
        $sellingPriceExcl = calculateExclusivePrice($product->SellingPrice, $vatRate);
        $sellingPrice2Excl = calculateExclusivePrice($product->SellingPrice2, $vatRate);
        $sellingPrice3Excl = calculateExclusivePrice($product->SellingPrice3, $vatRate);
        $sellingPrice4Excl = calculateExclusivePrice($product->SellingPrice4, $vatRate);

        //dd($product);
    @endphp


    <div class="mx-4">
        <div class="d-flex align-items-center mb-4">
            <div class="me-3">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-between" style="width: 40px; height: 40px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
            <div class="flex-grow-1">
                <small class="text-muted fs-6">{{ __('global.back_to_list') }}</small>
                <h2 class="mg-0">{{ $product->StockItemName }}</h2>
                <span class="badge bg-primary fs-6">{{ __('Stock Code :') }} {{ $product->StockCode }}</span>
            </div>
            <div>
                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> {{ __('global.edit') }}
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i> {{ __('global.delete') }}
                </button>
            </div>
        </div>

        <div class="row g-3">

            <div class="col-md-6">
                <div class="column-content">
                    <h4>{{ __('Product Information') }}</h4>
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-xl-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.sku') }}</div>
                                    <div>{{ $product->StockCode }}</div>
                                </div>
                                <div class="col-md-6 col-xl-8 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.name') }}</div>
                                    <div class="fw-bold">{{ $product->StockItemName }}</div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 product-info-item">
                                    <div class="product-info-label">{{ __('Catalog and eCommerce Product Description') }}</div>
                                    <div class="mt-1">
                                        {!! $product->MarketingComments ? nl2br(e($product->MarketingComments)) : '<em class="text-muted">No description available</em>' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4>{{ __('Department') }}</h4>
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.category') }}</div>
                                    <div>
                                        @php

                                        @endphp
                                        @if($allMainCategories->count())
                                            @foreach($allMainCategories as $category)
                                                <span class="badge bg-info fs-6">{{ $category->StockGroupName ?? 'N/A' }}</span>
                                            @endforeach
                                        @else
                                            <em class="text-muted">No category assigned</em>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.subCategory') }}</div>
                                    <div>
                                        @if($product->subCategories()->count() )
                                            @foreach($product->subCategories as $subCategory)
                                                @if($subCategory)
                                                    <span class="badge bg-secondary fs-6">{{ $subCategory->StockGroupName ?? 'N/A' }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <em class="text-muted">No subcategory assigned</em>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4>{{ __('Stock Details') }}</h4>
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.quantity') }}</div>
                                    <div class="d-flex align-items-center mt-1">
                                        @php
                                            $quantity = $product->stockHolding ? $product->stockHolding->QuantityOnHand : 0;
                                        @endphp
                                        <span class="fs-5 me-2">{{ $quantity }}</span>
                                        @if($quantity > 10)
                                            <span class="product-stock-label in-stock">In Stock</span>
                                        @elseif($quantity > 0)
                                            <span class="product-stock-label low-stock">Low Stock</span>
                                        @else
                                            <span class="product-stock-label out-of-stock">Out of Stock</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 product-info-item">
                                            <div class="product-info-label">{{ __('cruds.product.fields.barcode') }}</div>
                                            <div class="barcode-display mt-1">
                                                {{ $product->Barcode ?? 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="col-md-6 product-info-item">
                                            <div class="product-info-label">{{ __('cruds.product.fields.altbarcode') }}</div>
                                            <div class="barcode-display mt-1">
                                                {{ $product->AltBarcode ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4>{{ __('Selling Type') }}</h4>
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                {{--@if($product->InStore)
                                    <span class="badge bg-success">In-store selling</span>
                                @endif
                                @if($product->Online)
                                    <span class="badge bg-info">Online selling</span>
                                @endif
                                @if($product->InStore && $product->Online)
                                    <span class="badge bg-primary">Available both in-store & online</span>
                                @endif
                                @if(!$product->InStore && !$product->Online)
                                    <em class="text-muted">No selling type specified</em>
                                @endif--}}
                            </div>
                        </div>
                    </div>

                    <h4>{{ __('Activity History') }}</h4>
                    <div class="card border">
                        <div class="card-body">
                            <div class="timeline-container">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Created</span>
                                            <small class="text-muted">{{ $product->created_at->format('M d, Y H:i') }}</small>
                                        </div>
                                        <div class="text-muted">By: {{ $product->CreatedBy ? $product->createdBy->name : 'System' }}</div>
                                    </div>
                                </div>

                                @if($product->updated_at && $product->updated_at->ne($product->created_at))
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <span class="fw-bold">Last Updated</span>
                                                <small class="text-muted">{{ $product->updated_at->format('M d, Y H:i') }}</small>
                                            </div>
                                            <div class="text-muted">By: {{ $product->LastEditedBy ? $product->lastEditedBy : 'System' }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="column-content">
                    <h4>{{ __('Product Images') }}</h4>
                    <div class="card border mb-3">
                        <div class="card-body">
                            @if($product->photo)
                                <img src="{{ $product->photo->getUrl() }}" alt="{{ $product->StockItemName }}" class="product-image">
                            @else
                                <div class="text-center p-5 bg-light">
                                    <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                    <p class="mb-0 text-muted">No image available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <h4>{{ __('Pricing') }}</h4>
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.vatid') }}</div>
                                    <div>{{ $product->TaxRateID }}</div>
                                </div>
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.ave_cost') }}</div>
                                    <div>{{ $product->AverageCostPrice ? 'R ' . number_format($product->AverageCostPrice, 2) : 'N/A' }}</div>
                                </div>
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.last_cost') }}</div>
                                    <div>{{ $product->stockHolding ? 'R ' . number_format($product->stockHolding->LastCostPrice, 2) : 'N/A' }}</div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.discount') }}</div>
                                    <div>{{ $product->DiscountPercentage ? $product->DiscountPercentage . '%' : 'N/A' }}</div>
                                </div>
                                <div class="col-md-8 product-info-item">
                                    <div class="product-info-label">{{ __('Default Price (incl. VAT)') }}</div>
                                    <div class="product-price">{{ 'R '. number_format($product->SellingPrice, 2) }}</div>
                                </div>
                            </div>

                            <hr>

                            <h5 class="card-title">{{ __('Additional Price Points') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Price Level</th>
                                        <th>Excl. VAT</th>
                                        <th>Incl. VAT</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Price 1</td>
                                        <td>{{ $sellingPriceExcl ? 'R ' . number_format($sellingPriceExcl, 2) : 'N/A' }}</td>
                                        <td>{{ $product->SellingPrice ? 'R ' . number_format($product->SellingPrice, 2) : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Price 2</td>
                                        <td>{{ $sellingPrice2Excl ? 'R ' . number_format($sellingPrice2Excl, 2) : 'N/A' }}</td>
                                        <td>{{ $sellingPrice4Excl ? 'R ' . number_format($sellingPrice4Excl, 2) : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Price 3</td>
                                        <td>{{ $sellingPrice3Excl ? 'R ' . number_format($sellingPrice3Excl, 2) : 'N/A' }}</td>
                                        <td>{{ $product->SellingPrice3 ? 'R ' . number_format($product->SellingPrice3, 2) : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Price 4</td>
                                        <td>{{ $sellingPrice4Excl ? 'R ' . number_format($sellingPrice4Excl, 2) : 'N/A' }}</td>
                                        <td>{{ $product->SellingPrice4 ? 'R ' . number_format($product->SellingPrice4, 2) : 'N/A' }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h4>{{ __('Packaging') }}</h4>
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.size') }}</div>
                                    <div>{{ $product->Size ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.units') }}</div>
                                    <div>
                                        {{--@if($product->salesunits->count())
                                            {{ $product->salesunits->first()->name }}
                                        @else
                                            <em class="text-muted">Not specified</em>
                                        @endif--}}
                                    </div>
                                </div>
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.packsize') }}</div>
                                    <div>{{ $product->Packsize ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($product->refer_code || $product->referringProducts->count() > 0 || $product->Packsize)
                        <h4>{{ __('Pack Size Information') }}</h4>
                        <div class="card border mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 product-info-item">
                                        <div class="product-info-label">{{ __('Pack Size') }}</div>
                                        <div>
                                            <span class="fs-5 fw-bold">{{ $product->Packsize ?? 1 }}</span>
                                            <span class="text-muted">{{ ($product->Packsize ?? 1) == 1 ? 'unit' : 'units' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 product-info-item">
                                        <div class="product-info-label">{{ __('Pack Type') }}</div>
                                        <div>
                                            @if($product->Packsize)
                                                <span class="badge bg-success">Child Product</span>
                                            @else
                                                <span class="badge bg-primary">Root Product</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 product-info-item">
                                        <div class="product-info-label">{{ __('Refers To') }}</div>
                                        <div>
                                            @if($product->refer_code && $product->referredProduct)
                                                <a href="{{ route('products.show', $product->referredProduct->id) }}" class="text-decoration-none">
                                                    {{ $product->referredProduct->StockItemName }}
                                                    <br><small class="text-muted">{{ $product->referredProduct->StockCode }}</small>
                                                </a>
                                            @else
                                                <em class="text-muted">None</em>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($packSizeFamily->count() > 1)
                                    <hr class="my-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">{{ __('Pack Size Family') }}</h6>
                                            <div class="pack-size-family-display">
                                                @php
                                                    $sortedFamily = $packSizeFamily->sortByDesc('pack_size');
                                                    $totalBaseUnits = $sortedFamily->sum(function($member) {
                                                        return ($member->stockHolding?->QuantityOnHand ?? 0) * $member->pack_size;
                                                    });
                                                @endphp

                                                <div class="row">
                                                    <div class="col-md-8">
                                                        @foreach($sortedFamily as $index => $familyMember)
                                                            <div class="pack-size-member {{ $familyMember->StockCode === $product->StockCode ? 'current-product' : '' }} mb-2">
                                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                                    <div class="d-flex align-items-center">
                                                                        @if($familyMember->StockCode === $product->StockCode)
                                                                            <i class="fas fa-arrow-right text-primary me-3"></i>
                                                                        @else
                                                                            <div class="me-5"></div>
                                                                        @endif

                                                                        <div>
                                                                            @if($familyMember->StockCode === $product->StockCode)
                                                                                <strong class="text-primary">{{ $familyMember->StockItemName }}</strong>
                                                                            @else
                                                                                <a href="{{ route('products.show', $familyMember->id) }}" class="text-decoration-none">
                                                                                    {{ $familyMember->StockItemName }}
                                                                                </a>
                                                                            @endif

                                                                            <div class="mt-1">
                                                    <span class="badge bg-{{ $familyMember->StockCode === $product->StockCode ? 'primary' : 'secondary' }}">
                                                        {{ $familyMember->pack_size }} {{ $familyMember->pack_size == 1 ? 'unit' : 'units' }}
                                                    </span>

                                                                                @php
                                                                                    $quantity = $familyMember->stockHolding?->QuantityOnHand ?? 0;
                                                                                    $price = $familyMember->SellingPrice ?? 0;
                                                                                @endphp

                                                                                @if($quantity > 0)
                                                                                    <span class="badge bg-success ms-1">{{ $quantity }} in stock</span>
                                                                                @else
                                                                                    <span class="badge bg-danger ms-1">Out of stock</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="text-end">
                                                                        <div class="fw-bold">R {{ number_format($price, 2) }}</div>
                                                                        <small class="text-muted">{{ $familyMember->StockCode }}</small>
                                                                        @if($familyMember->pack_size > 1 && $price > 0)
                                                                            <br><small class="text-muted">R {{ number_format($price / $familyMember->pack_size, 2) }} per unit</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @if($index < $sortedFamily->count() - 1)
                                                                <div class="text-center mb-2">
                                                                    <i class="fas fa-arrow-down text-muted"></i>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="pack-size-summary p-3 bg-light rounded">
                                                            <h6 class="mb-3">{{ __('Family Summary') }}</h6>

                                                            <div class="summary-item mb-2">
                                                                <div class="d-flex justify-content-between">
                                                                    <span>{{ __('Total Products:') }}</span>
                                                                    <strong>{{ $sortedFamily->count() }}</strong>
                                                                </div>
                                                            </div>

                                                            <div class="summary-item mb-2">
                                                                <div class="d-flex justify-content-between">
                                                                    <span>{{ __('Total Base Units:') }}</span>
                                                                    <strong>{{ $totalBaseUnits }}</strong>
                                                                </div>
                                                            </div>

                                                            <div class="summary-item mb-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <span>{{ __('Pack Sizes:') }}</span>
                                                                    <strong>{{ $sortedFamily->pluck('pack_size')->sort()->values()->implode(', ') }}</strong>
                                                                </div>
                                                            </div>

                                                            <hr>

                                                            <h6 class="mb-2">{{ __('Quick Actions') }}</h6>
                                                            <div class="d-grid gap-2">
                                                                <button class="btn btn-sm btn-outline-primary" onclick="viewPackSizeReport('{{ $product->StockCode }}')">
                                                                    <i class="fas fa-chart-bar me-1"></i> {{ __('View Report') }}
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-success" onclick="checkStockAvailability('{{ $product->StockCode }}')">
                                                                    <i class="fas fa-search me-1"></i> {{ __('Check Availability') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">{{ __('global.delete_confirmation') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{ __('global.delete_confirmation_text') }} <strong>{{ $product->StockItemName }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('global.cancel') }}</button>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{ __('global.delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ URL::asset('build/libs/inputmask/jquery.inputmask.min.js')}}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush
