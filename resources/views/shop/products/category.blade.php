@extends('shop.layouts.app')

@section('title', $category->StockGroupName)

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}">{{ __('Products') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $category->StockGroupName }}</a></li>
@endsection

@section('content')
    <div class="container mt-4">

        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2">{{ $category->StockGroupName }}</h1>
                <p class="text-muted">
                    <i class="fas fa-box me-1"></i> {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 mb-4">
                @include('shop.products.partials.filters', ['currentCategory' => $category])
            </div>

            <div class="col-lg-9">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="text-muted mb-0">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                        </p>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('shop.categories.show', $category->slug ?? $category->id) }}" class="d-flex justify-content-end">
                            <select name="sort" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="row">
                    @forelse($products as $product)
                        <div class="col-md-6 col-lg-4 mb-4">
                            @include('shop.products.partials.product-card', ['product' => $product])
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i> {{ __('No products found in this category.') }}
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($products->hasPages())
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-center">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
