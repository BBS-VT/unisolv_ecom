@php
    use App\Helpers\Features;
    $currentCategory = $currentCategory ?? null;
    $minPrice = request('min_price', 0);
    $maxPrice = request('max_price', 10000);
@endphp

<div class="amazon-sidebar">
    <h6>Department</h6>
    <div class="amazon-filter-section">
        @foreach($categories as $category)
            <div class="amazon-filter-item">
                <input class="form-check-input" type="checkbox"
                       name="categories[]"
                       value="{{ $category->id }}"
                       id="category-{{ $category->id }}"
                       {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                       onchange="document.getElementById('filter-form').submit()">
                <label for="category-{{ $category->id }}">
                    {{ $category->StockGroupName }}
                    <span class="text-muted ms-auto">{{ $category->products_count }}</span>
                </label>
            </div>
        @endforeach
    </div>

</div>

@if(\App\Helpers\Features::publicPricesEnabled() || auth()->check())
    <div class="amazon-sidebar">
        <h6>Price</h6>
        <div class="amazon-filter-section">
            <div class="amazon-filter-item">
                <input type="radio" id="price-under-25" name="price_range" value="0-25">
                <label for="price-under-25">Under $25</label>
            </div>
            <div class="amazon-filter-item">
                <input type="radio" id="price-25-50" name="price_range" value="25-50">
                <label for="price-25-50">$25 to $50</label>
            </div>
            <div class="amazon-filter-item">
                <input type="radio" id="price-50-100" name="price_range" value="50-100">
                <label for="price-50-100">$50 to $100</label>
            </div>
            <div class="amazon-filter-item">
                <input type="radio" id="price-100-200" name="price_range" value="100-200">
                <label for="price-100-200">$100 to $200</label>
            </div>
            <div class="amazon-filter-item">
                <input type="radio" id="price-over-200" name="price_range" value="200-">
                <label for="price-over-200">$200 & Above</label>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <div class="row g-2">
            <div class="col">
                <input type="number" class="form-control form-control-sm" placeholder="Min" id="price_min">
            </div>
            <div class="col-auto align-self-center">to</div>
            <div class="col">
                <input type="number" class="form-control form-control-sm" placeholder="Max" id="price_max">
            </div>
            <div class="col-12">
                <button class="btn btn-sm btn-amazon-secondary w-100" id="apply-price-range">Go</button>
            </div>
        </div>
    </div>
@endif
<div class="amazon-sidebar">
    <h6>Availability</h6>
    <div class="amazon-filter-section">
        <div class="amazon-filter-item">
            <input type="checkbox" id="in-stock" name="availability[]" value="in_stock"
                {{ in_array('in_stock', request('availability', [])) ? 'checked' : '' }}>
            <label for="in-stock">In Stock</label>
        </div>
        <div class="amazon-filter-item">
            <input type="checkbox" id="low-stock" name="availability[]" value="low_stock"
                {{ in_array('low_stock', request('availability', [])) ? 'checked' : '' }}>
            <label for="low-stock">Low Stock</label>
        </div>
        @if(\App\Helpers\Features::backordersEnabled())
            <div class="amazon-filter-item">
                <input type="checkbox" id="backorder" name="availability[]" value="backorder"
                    {{ in_array('backorder', request('availability', [])) ? 'checked' : '' }}>
                <label for="backorder">Available for Backorder</label>
            </div>
        @endif
    </div>
</div>
<div class="amazon-sidebar">
    <button class="btn btn-outline-secondary w-100" id="clear-filters">Clear All Filters</button>
</div>



