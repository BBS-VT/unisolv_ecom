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
                                        @if($product->mainCategories()->count())
                                            @foreach($product->categories as $category)
                                                @if($category)
                                                    <span class="badge bg-info fs-6">{{ $category->StockGroupName ?? 'N/A' }}</span>
                                                @endif
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
                                        <span class="fs-5 me-2">{{ $product->Quantity }}</span>
                                        @if($product->Quantity > 10)
                                            <span class="product-stock-label in-stock">In Stock</span>
                                        @elseif($product->Quantity > 0)
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
                                    <div>{{ $product->AverageCostPrice }}</div>
                                </div>
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.last_cost') }}</div>
                                    <div>{{ $product->LastCostPrice }}</div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-4 product-info-item">
                                    <div class="product-info-label">{{ __('cruds.product.fields.discount') }}</div>
                                    <div>{{ $product->DiscountPercentage ? $product->DiscountPercentage . '%' : 'N/A' }}</div>
                                </div>
                                <div class="col-md-8 product-info-item">
                                    <div class="product-info-label">{{ __('Default Price (incl. VAT)') }}</div>
                                    <div class="product-price">{{ number_format($product->SellingPrice, 2) }}</div>
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
                                        <td></td>
                                        <td>{{ number_format($product->SellingPrice, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Price 2</td>
                                        <td></td>
                                        <td>{{ number_format($product->SellingPrice2, 2) ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Price 3</td>
                                        <td></td>
                                        <td>{{ number_format($product->SellingPrice3, 2) ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Price 4</td>
                                        <td></td>
                                        <td>{{ number_format($product->SellingPrice4, 2) ?? 'N/A' }}</td>
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
