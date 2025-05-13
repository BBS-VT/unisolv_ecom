@php
    use App\Helpers\Features;
    $currentCategory = $currentCategory ?? null;
    $minPrice = request('min_price', 0);
    $maxPrice = request('max_price', 10000);
@endphp

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Filters</h5>
        <a href="{{ url()->current() }}" class="btn btn-sm btn-link text-danger">
            <i class="fas fa-times me-1"></i> Clear
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ url()->current() }}" id="filter-form">
            {{-- Search --}}
            <div class="mb-4">
                <label class="form-label">Search</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="search"
                           value="{{ request('search') }}"
                           placeholder="Product name or SKU">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            {{-- Categories --}}
            @if(!$currentCategory)
                <div class="mb-4">
                    <h6 class="mb-3">Categories</h6>
                    <div class="category-filter-list" style="max-height: 300px; overflow-y: auto;">
                        @foreach($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="categories[]"
                                       value="{{ $category->id }}"
                                       id="category-{{ $category->id }}"
                                       {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                                       onchange="document.getElementById('filter-form').submit()">
                                <label class="form-check-label" for="category-{{ $category->id }}">
                                    {{ $category->StockGroupName }}
                                    <span class="badge bg-secondary ms-1">{{ $category->products_count }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Price Range --}}
            @if(Features::publicPricesEnabled() || auth()->check())
                <div class="mb-4">
                    <h6 class="mb-3">Price Range</h6>
                    <div class="price-input-wrapper">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">{{ config('app.currency', 'R') }}</span>
                                    <input type="number" class="form-control"
                                           name="min_price"
                                           value="{{ $minPrice }}"
                                           placeholder="Min">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">{{ config('app.currency', 'R') }}</span>
                                    <input type="number" class="form-control"
                                           name="max_price"
                                           value="{{ $maxPrice }}"
                                           placeholder="Max">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100 mt-2">
                            Update Price Range
                        </button>
                    </div>
                </div>
            @endif

            {{-- Stock Status --}}
            @if(Features::showStock())
                <div class="mb-4">
                    <h6 class="mb-3">Stock Status</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="in_stock_only"
                               id="in_stock_only"
                               value="1"
                               {{ request('in_stock_only') ? 'checked' : '' }}
                               onchange="document.getElementById('filter-form').submit()">
                        <label class="form-check-label" for="in_stock_only">
                            In Stock Only
                        </label>
                    </div>
                </div>
            @endif

            {{-- Sorting --}}
            <div class="mb-4">
                <h6 class="mb-3">Sort By</h6>
                <select class="form-select form-select-sm" name="sort" onchange="document.getElementById('filter-form').submit()">
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    @if(Features::publicPricesEnabled() || auth()->check())
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                    @endif
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                </select>
            </div>

            {{-- Hidden inputs to preserve other filters --}}
            @if($currentCategory)
                <input type="hidden" name="category" value="{{ $currentCategory->id }}">
            @endif
        </form>
    </div>
</div>

<style>
    .category-filter-list {
        border: 1px solid #e3e3e3;
        border-radius: 0.25rem;
        padding: 0.5rem;
    }

    .category-filter-list::-webkit-scrollbar {
        width: 6px;
    }

    .category-filter-list::-webkit-scrollbar-track {
        background: #f8f9fa;
    }

    .category-filter-list::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }

    .category-filter-list::-webkit-scrollbar-thumb:hover {
        background: #ced4da;
    }

    .form-check {
        padding: 0.25rem 0;
    }

    .form-check-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        cursor: pointer;
    }

    .form-check-label:hover {
        color: #0066cc;
    }
</style>
