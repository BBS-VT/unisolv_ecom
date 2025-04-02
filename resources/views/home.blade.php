@extends('layouts.front')

@section('css')
    <link href="{{ URL::asset('build/libs/nouislider/nouislider.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .product-card {
            transition: all 0.3s ease;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .product-action {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .product-card:hover .product-action {
            opacity: 1;
        }
        .product-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 48px;
        }
        .category-scroll-container {
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #dee2e6 #f8f9fa; /* Firefox */
        }

        /* For Webkit browsers (Chrome, Safari) */
        .category-scroll-container::-webkit-scrollbar {
            width: 6px;
        }

        .category-scroll-container::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 4px;
        }

        .category-scroll-container::-webkit-scrollbar-thumb {
            background-color: #dee2e6;
            border-radius: 4px;
        }

        .category-scroll-container::-webkit-scrollbar-thumb:hover {
            background-color: #ced4da;
        }

        .filter-list .active {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            border-radius: 4px;
        }

        .filter-list a:hover {
            background-color: rgba(0, 0, 0, 0.03);
            border-radius: 4px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="#!" class="text-reset text-decoration-underline">{{ __('Clear All') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="search-box mb-3">
                        <input type="text" placeholder="search" class="form-control" id="searchResultList" autocomplete="off"/>
                        <i class="ri-search-line search-icon"></i>
                    </div>
                    <div class="accordion accordion-flush filter-panel" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="categoryAccordion">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategory" aria-expanded="true" aria-controls="collapseCategory">
                                    {{ __('cruds.productCategory.title') }}
                                </button>
                            </h2>
                            <div id="collapseCategory" class="accordion-collapse collapse show" aria-labelledby="categoryAccordion" data-bs-parent="#categoryFilters">
                                <div class="accordion-body p-0">
                                    <div class="category-scroll-container" style="max-height: 250px; overflow-y: auto; padding: 1rem;">
                                        <ul class="list-unstyled mb-0 filter-list">
                                            @foreach($categories as $category)
                                                <li>
                                                    <a href="{{ route('catalog', ['category' => $category->id]) }}"
                                                       class="d-flex py-1 align-items-center {{ request()->input('category') == $category->id ? ' active' : '' }}">
                                                        <div class="flex-grow-1">
                                                            <h5 class="fs-sm mb-0 listname">{{ $category->StockGroupName }}</h5>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="priceAccordion">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrice" aria-expanded="true" aria-controls="collapsePrice">
                                    Price
                                </button>
                            </h2>
                            <div id="collapsePrice" class="accordion-collapse collapse show" aria-labelledby="priceAccordion" data-bs-parent="#priceFilters">
                                <div class="accordion-body">
                                    <div id="product-price-range" data-slider-color="secondary"></div>
                                    <div class="formCost d-flex gap-2 align-items-center mt-3">
                                        <input class="form-control form-control-sm" type="text" id="minCost" value="0"> <span class="fw-semibold text-muted">to</span> <input class="form-control form-control-sm" type="text" id="maxCost" value="1000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-9">
            <div class="row align-items-center mb-4 g-3">
                <div class="col-xxl-2 col-lg-4 col-sm-6 me-auto">
                    <h5 class="mb-0">
                        {{ __('Products') }}
                    </h5>
                </div>
                <div class="col-xxl-2 col-lg-4 col-sm-6">
                    {{-- TODO: add filtering    --}}
                </div>
                <div class="col-lg-auto">

                </div>
            </div>
            <div class="row" id="product-grid">
                @foreach($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <div class="card-img-top position-relative overflow-hidden" style="height: 200px;">
                                <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/250x250/cccccc/000000.png&text=Image' }}"
                                     alt="{{ $product->StockItemName }}"
                                     class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                <div class="product-action">
                                    <a href="{{ route('product.detail', $product->id) }}" class="btn btn-primary btn-sm">
                                        <i class="ri-eye-line align-bottom"></i> View Details
                                    </a>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title mb-1">
                                    <a href="{{ route('product.detail', $product->id) }}" class="text-dark text-decoration-none product-title">
                                        {{ $product->StockItemName }}
                                    </a>
                                </h5>
                                <p class="text-muted small mb-2">{{ Str::limit($product->MarketingComments, 50) }}</p>
                                @auth
                                    <h5 class="mb-0 mt-3 text-primary">{{ $product->formatted_price }}</h5>
                                @else
                                    <a href="{{ route('login') }}">Login</a> to view price
                                @endauth
                            </div>
                            <div class="card-footer bg-transparent text-center border-top-0 pb-3">
                                <button type="button" class="btn btn-primary btn-sm add-to-cart" data-product-id="{{ $product->id }}">
                                    <i class="ri-shopping-cart-line align-bottom"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{--@foreach($products as $product)
                <div class="col-xxl-3 col-lg-4 col-md-6">
                    <div class="card ribbon-box ribbon-fill">
                        <div class="card-body p-4 m-4">
                            <img src="{{ $product->photo ? $product->photo->thumbnail : 'https://placehold.co/300x200/000/?text=Image' }}" class="img-fluid" alt="">
                        </div>
                        <div class="card-body pt-0">
                            <h5 class="fs-lg mb-3"> </h5>
                            <a href="#">
                                <h6 class="fs-md text-truncate">{{ $product->StockItemName }} </h6>
                            </a>
                            <a href="#!" class="text-decoration-underline text-muted mb-0">{{ $product->StockGroupName }}</a>
                            <div class="mt-3 hstack gap-2">
                                <a href="#!" class="btn btn-primary w-100"><i class="ph-eye me-1 align-middle"></i>{{ __('Detail') }}</a>
                                <a href="#!" class="btn btn-secondary w-100"><i class="ph-shopping-cart me-1 align-middle"></i>{{ __('Add to Cart') }}</a>

                            </div>
                        </div>
                    </div>
                </div>
                @endforeach--}}
            </div>
            <div class="row mb-4 align-items-center" id="pagination-element">
                <div class="col-sm">
                    <div class="text-muted">
                        {{ __('Showing') }} {{ $products->firstItem() }} - {{ $products->lastItem() }} {{ __('of') }} {{ $products->total() }} {{ __('results') }}
                    </div>
                </div>
                <div class="col-sm-auto mt-3 mt-sm-0">
                    <div class="pagination-block pagination pagination-separated justify-content-center justify-content-sm-end mb-sm-0">
                        <span id="page-num" class="pagination">{{ $products->appends(request()->query())->links() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row d-none" id="search-result-elem">
            <div class="col-lg-12">
                <div class="text-center py-5">
                    <div class="avatar-lg mx-auto mb-4">
                        <div class="avatar-title bg-light text-primary rounded-circle fs-4xl">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>

                    <h5>No matching records found</h5>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/wnumb/wNumb.min.js') }}"></script>

@endsection
