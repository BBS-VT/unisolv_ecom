@extends('layouts.front')

@section('css')
    <link href="{{ URL::asset('build/libs/nouislider/nouislider.min.css') }}" rel="stylesheet" type="text/css" />

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
                                <div class="accordion-body">
                                    <ul class="list-unstyled mb-0 filter-list">
                                        <li>
                                            @foreach($categories as $category)
                                                <a href="{{ route('catalog', ['category' => $category->id]) }}" class="d-flex py-1 align-items-center {{ request()->input('category') == $category->id ? ' active' : '' }}">
                                                    <div class="flex-grow-1">
                                                        <h5 class="fs-sm mb-0 listname">{{ $category->StockGroupName }}</h5>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </li>
                                    </ul>
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
                    <div class="col-md-4">
                        <section class="panel">
                            <div class="pro-img-box">
                                <img class="card-img-top" src="{{ $product->photo ? $product->photo->thumbnail : 'https://via.placeholder.com/500x280&text=Image' }}" alt="">
                                <a href="#"></a>
                            </div>

                            <div class="panel-body text-center">
                                <h4>
                                    <a href="#" class="pro-title">{{ $product->StockItemName }}</a>
                                </h4>
                                <p class="card-text">{{ Str::limit($product->MarketingComments, 50) }}</p>
                            </div>
                        </section>
                    </div>
                @endforeach
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
    <script src="{{ URL::asset('build/js/pages/ecommerce-product-grid-list.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
