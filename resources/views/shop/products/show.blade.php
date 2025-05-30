@extends('shop.layouts.app')

@section('title', $product->StockItemName. ' - ' . config('app.name'))

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb" class="amazon-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.home') }}">{{ __('Home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}">{{ __('Products') }}</a></li>
                @if($product->category)
                    <li class="breadcrumb-item><a href="{{ route('shop.products.category', $product->category->slug) }}">{{ $product->category->CategoryName }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->StockItemName }}</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-6">
                <div class="bg-white p-4 border rounded mb-4">
                    <div class="text-center mb-3">
                        @if($product->getMedia('images')->isNotEmpty())
                            <img src="{{ $product->getFirstMediaUrl('images') }}"
                                 alt="{{ $product->StockItemName }}"
                                 class="img-fluid rounded shadow-sm" id="main-product-image">
                        @elseif($product->photo)
                            <img src="{{ $product->photo->url }}"
                                 alt="{{ $product->StockItemName }}"
                                 class="img-fluid rounded shadow-sm" id="main-product-image">
                        @else
                            <img src="https://dummyimage.com/600x600/f0f0f0/b0b0b0.png&text=No+Image"
                                 alt="{{ $product->StockItemName }}"
                                 class="img-fluid rounded shadow-sm" id="main-product-image">
                        @endif
                    </div>
                    @if($product->getMedia('images')->count() > 1)
                        <div class="d-flex justify-content-center">
                            <div class="thumbnail-container d-flex flex-wrap justify-content-center gap-2">
                                @foreach($product->getMedia('images')->take(5) as $media)
                                    <div class="thumbnail-image {{ $index === 0? 'active':"" }}"
                                         data-src="{{ $media->getUrl() }}"
                                         style="width: 60px; height: 60px; object-fit: contain;border: 2px solid {{ $index === 0 ? 'var(-amazon-orange)':'#ddd'}}; border-radius:4px; cursor:pointer;"
                                        onclick="changeMainImage('{{ asset($image) }}', this)">
                                        <img src="{{ $media->getUrl('thumb') }}"
                                             alt="{{ $product->StockItemName }}"
                                             class="img-fluid rounded">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="bg-white p-4 border rounded">
                    <h1 class="h3 mb-3 fw-normal">{{ $product->StockItemName }}</h1>
                    @if($product->brand)
                        <p class="mb-2">
                            <span class="text-muted">Brand:</span>
                            <a href="{{ route('shop.products', ['brands' => [$product->brand->id]]) }}" class="text-decoration-none text-primary">{{ $product->brand->name }}</a>
                        </p>
                    @endif

                    <hr>

                    @if(\App\Helpers\Features::publicPricesEnabled())
                        <div class="mb-4">
                        @if($product->discount_price)
                            <div class="d-flex align-items-baseline">
                                <span class="text-muted me-2">List Price:</span>
                                <span class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                            </div>
                            <div class="amazon-price fs-2 mb-2">
                                <span class="amazon-price-whole">${{ number_format($product->discount_price, 0) }}</span>
                                <span class="amazon-price-fraction">{{ sprintf('%02d', ($product->discount_price - floor($product->discount_price)) * 100) }}</span>
                            </div>
                            <div class="text-success">
                                You Save: ${{ number_format($product->price - $product->discount_price, 2) }}
                                ({{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%)
                            </div>
                        @else
                            <div class="amazon-price fs-2">
                                <span class="amazon-price-whole">${{ number_format($product->price, 0) }}</span>
                                <span class="amazon-price-fraction">{{ sprintf('%02d', ($product->price - floor($product->price)) * 100) }}</span>
                            </div>
                       @endif
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Please log in to see pricing information.
                            </div>
                        </div>
                    @endif
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-12">
                                @if($product->stockHolding->QuantityOnHand > 10)
                                    <div class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <strong>In Stock</strong>
                                    </div>
                                @elseif($product->stockHolding->QuantityOnHand > 0)
                                    <div class="text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <strong>Only {{ $product->stockHolding->QuantityOnHand }} left in stock - order soon</strong>
                                    </div>
                                @elseif(\App\Helpers\Features::backordersEnabled())
                                    <div class="text-info">
                                        <i class="bi bi-clock me-1"></i>
                                        <strong>Available for backorder</strong>
                                    </div>
                                @else
                                    <div class="text-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        <strong>Currently unavailable</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    @if($product->stockHolding->QuantityOnHand > 0 || \App\Helpers\Features::backordersEnabled())
                        <div class="mb-4">
                            <form id="add-to-cart-form">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <!-- Quantity Selector -->
                                <div class="row g-3 align-items-end mb-3">
                                    <div class="col-md-4">
                                        <label for="product-quantity" class="form-label fw-bold">Quantity:</label>
                                        <select class="form-select" id="product-quantity" name="quantity">
                                            @for($i = 1; $i <= min(10, $product->stockHolding->QuantityOnHand ?: 10); $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                            @if($product->stockHolding->QuantityOnHand > 10)
                                                <option value="10+">10+</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-amazon-secondary btn-lg">
                                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                    </button>
                                    <button type="button" class="btn btn-amazon-primary btn-lg">
                                        <i class="bi bi-lightning me-2"></i>Buy Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="border-top pt-3">
                        <div class="row g-2 small text-muted">
                            <div class="col-12">
                                <strong>SKU:</strong> {{ $product->StockCode }}
                            </div>
                            @if($product->WeightPerUnit > 0)
                                <div class="col-12">
                                    <strong>Weight:</strong> {{ $product->WeightPerUnit }} kg
                                </div>
                            @endif
                            @if($product->dimensions)
                                <div class="col-12">
                                    <strong>Dimensions:</strong> {{ $product->dimensions }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 border rounded mt-3">
                    <h6 class="mb-3">Other Options</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-heart me-2"></i>Add to Wish List
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-share me-2"></i>Share this Product
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-bell me-2"></i>Add to Price Watch
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="bg-white border rounded">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs border-bottom-0" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                                Product Description
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                                Technical Details
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content p-4" id="productTabsContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <h5 class="mb-3">About this item</h5>
                            <div class="product-description">
                                {!! nl2br(e($product->MarketingComments)) !!}
                            </div>

                            @if($product->features && count($product->features) > 0)
                                <h6 class="mt-4 mb-3">Key Features</h6>
                                <ul class="list-unstyled">
                                    @foreach($product->features as $feature)
                                        <li class="mb-2">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specifications" role="tabpanel">
                            <h5 class="mb-3">Technical Details</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                    <tr>
                                        <td class="fw-bold bg-light" style="width: 30%;">Brand</td>
                                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">Model Number</td>
                                        <td>{{ $product->model_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold bg-light">SKU</td>
                                        <td>{{ $product->StockCode }}</td>
                                    </tr>
                                    @if($product->WeightPerUnit)
                                        <tr>
                                            <td class="fw-bold bg-light">Weight</td>
                                            <td>{{ $product->WeightPerUnit }} kg</td>
                                        </tr>
                                    @endif
                                    @if($product->dimensions)
                                        <tr>
                                            <td class="fw-bold bg-light">Dimensions</td>
                                            <td>{{ $product->dimensions }}</td>
                                        </tr>
                                    @endif
                                    @if($product->warranty)
                                        <tr>
                                            <td class="fw-bold bg-light">Warranty</td>
                                            <td>{{ $product->warranty }}</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @if($relatedProducts && $relatedProducts->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="bg-white p-4 border rounded">
                        <h4 class="mb-4">Products related to this item</h4>
                        <div class="row g-3">
                            @foreach($relatedProducts->take(4) as $relatedProduct)
                                <div class="col-lg-3 col-md-6">
                                    <div class="amazon-product-card h-100" onclick="window.location.href='{{ route('shop.products.show', $relatedProduct->id) }}'">
                                        @if($product->getMedia('images')->isNotEmpty())
                                            <img src="{{ asset($relatedProduct->featured_image) }}" alt="{{ $relatedProduct->StockItemName }}" class="amazon-product-image">
                                        @else
                                            <img src="https://dummyimage.com/300x300/f0f0f0/b0b0b0.png&text=No+Image" alt="{{ $relatedProduct->StockItemName }}" class="amazon-product-image">
                                        @endif

                                        <a href="{{ route('shop.products.show', $relatedProduct->id) }}" class="amazon-product-title">
                                            {{ $relatedProduct->StockItemName }}
                                        </a>

                                        @if($relatedProduct->average_rating > 0)
                                            <div class="amazon-rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($relatedProduct->average_rating))
                                                        <i class="bi bi-star-fill"></i>
                                                    @elseif($i <= ceil($relatedProduct->average_rating))
                                                        <i class="bi bi-star-half"></i>
                                                    @else
                                                        <i class="bi bi-star"></i>
                                                    @endif
                                                @endfor
                                                <span class="text-muted ms-1">({{ $relatedProduct->reviews_count }})</span>
                                            </div>
                                        @endif

                                        @if(\App\Helpers\Features::publicPricesEnabled())
                                            <div class="amazon-price mt-2">
                                                <span class="amazon-price-whole">${{ number_format($relatedProduct->price, 0) }}</span>
                                                <span class="amazon-price-fraction">{{ sprintf('%02d', ($relatedProduct->price - floor($relatedProduct->price)) * 100) }}</span>
                                            </div>
                                        @endif

                                        <div class="mt-auto">
                                            <button class="btn btn-amazon-primary btn-sm add-to-cart-btn w-100"
                                                    data-product-id="{{ $relatedProduct->id }}"
                                                    onclick="event.stopPropagation();">
                                                <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recently Viewed -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="bg-white p-4 border rounded">
                    <h4 class="mb-4">Your browsing history</h4>
                    <div class="row g-3" id="recently-viewed">
                        <!-- Recently viewed products will be loaded here via JavaScript -->
                        <div class="col-12 text-center text-muted">
                            <p>Start browsing to see your recently viewed products here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Track product view
            trackProductView({{ $product->id }});

            $('#product-quantity').change(function() {
                if ($(this).val() === '10+') {
                    const customQty = prompt('Enter quantity (max {{ $product->stockHolding->QuantityOnHand ?: 999 }}):');
                    if (customQty && !isNaN(customQty) && customQty > 0) {
                        // Add custom option
                        $(this).append(`<option value="${customQty}" selected>${customQty}</option>`);
                        $(this).find('option[value="10+"]').remove();
                    } else {
                        $(this).val('10');
                    }
                }
            });

            $('#add-to-cart-form').click(function() {})
        })
    </script>
@endsection
