@php
    use App\Helpers\Features;
    use App\Helpers\PricingHelper;
    $currentCategory = $currentCategory ?? null;
    $minPrice = request('min_price', 0);
    $maxPrice = request('max_price', 10000);
    $currency = config('app.currency', 'R');
@endphp
<style>
    /* Scrollable filter styles */
    .scrollable-filter {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 8px; /* Space for scrollbar */
    }

    /* Custom scrollbar styling for WebKit browsers */
    .scrollable-filter::-webkit-scrollbar {
        width: 6px;
    }

    .scrollable-filter::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 3px;
    }

    .scrollable-filter::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }

    .scrollable-filter::-webkit-scrollbar-thumb:hover {
        background: #ced4da;
    }

    /* For Firefox */
    .scrollable-filter {
        scrollbar-width: thin;
        scrollbar-color: #dee2e6 #f8f9fa;
    }

    /* Add a subtle fade effect at the bottom to indicate more content */
    .amazon-sidebar:has(.scrollable-filter) {
        position: relative;
    }

    .amazon-sidebar:has(.scrollable-filter)::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: linear-gradient(transparent, rgba(255,255,255,0.8));
        pointer-events: none;
        z-index: 1;
    }

    /* Enhanced filter item styling */
    .amazon-filter-item {
        padding: 6px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .amazon-filter-item:last-child {
        border-bottom: none;
    }

    .amazon-filter-item label {
        cursor: pointer;
        font-size: 0.9rem;
        line-height: 1.2;
    }

    .amazon-filter-item .text-truncate {
        min-width: 0; /* Allow text to truncate */
    }

    .amazon-filter-item .form-check-input {
        margin-top: 0;
    }

    /* Ensure consistent spacing */
    .amazon-filter-item .text-muted {
        font-size: 0.85rem;
        white-space: nowrap;
    }
</style>

<form id="filter-form" method="GET" action="{{ route('shop.products.index') }}">

    <!-- Preserve location filter if it exists -->
    @if(request('location'))
        <input type="hidden" name="location" value="{{ request('location') }}">
    @endif

    <!-- Preserve search query if it exists -->
    @if(request('search'))
        <input type="hidden" name="search" value="{{ request('search') }}">
    @endif

    <!-- Preserve sort if it exists -->
    @if(request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif

    <div class="amazon-sidebar">
        <h6>Department</h6>
        <div class="amazon-filter-section scrollable-filter">
            @forelse($categories as $category)
                <div class="amazon-filter-item d-flex align-items-center">
                    <input class="form-check-input me-2 flex-shrink-0" type="checkbox"
                           name="categories[]"
                           value="{{ $category->id }}"
                           id="category-{{ $category->id }}"
                           {{ in_array($category->id, $selectedCategories ?? []) ? 'checked' : '' }}
                           onchange="applyFilters()">
                    <label for="category-{{ $category->id }}" class="d-flex justify-content-between w-100 mb-0">
                    <span class="text-truncate me-2">
                        {{ $category->StockGroupName }}
                        @if($category->location)
                            <small class="text-muted">({{ $category->location->LocationName }})</small>
                        @endif
                    </span>
                        <span class="text-muted flex-shrink-0">({{ $category->products_count ?? 0 }})</span>
                    </label>
                </div>
            @empty
                <p class="text-muted small">No departments found for this location.</p>
            @endforelse
            {{--@foreach($categories as $category)
                <div class="amazon-filter-item d-flex align-items-center">
                    <input class="form-check-input me-2 flex-shrink-0" type="checkbox"
                           name="categories[]"
                           value="{{ $category->id }}"
                           id="category-{{ $category->id }}"
                           {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                           onchange="applyFilters()">
                    <label for="category-{{ $category->id }}" class="d-flex justify-content-between w-100 mb-0">
                        <span class="text-truncate me-2">{{ $category->StockGroupName }}</span>
                        <span class="text-muted flex-shrink-0">({{ $category->products_count ?? 0 }})</span>
                    </label>
                </div>
            @endforeach--}}
        </div>
    </div>

    @if(Features::publicPricesEnabled() || auth()->check())
        <div class="amazon-sidebar">
            <h6>Price</h6>
            <div class="amazon-filter-section">
                @php
                    // Adjust price ranges based on customer's pricing level
                    $priceLevel = PricingHelper::getCustomerPriceLevel();
                    $priceRanges = [
                        [0, 100, "Under {$currency}100"],
                        [100, 250, "{$currency}100 to {$currency}250"],
                        [250, 500, "{$currency}250 to {$currency}500"],
                        [500, 1000, "{$currency}500 to {$currency}1,000"],
                        [1000, 2500, "{$currency}1,000 to {$currency}2,500"],
                        [2500, null, "{$currency}2,500 & Above"]
                    ];
                @endphp

                @foreach($priceRanges as $index => $range)
                    @php
                        $rangeValue = $range[1] ? "{$range[0]}-{$range[1]}" : "{$range[0]}-";
                        $isSelected = request('price_range') == $rangeValue;
                    @endphp
                    <div class="amazon-filter-item">
                        <input type="radio"
                               id="price-range-{{ $index }}"
                               name="price_range"
                               value="{{ $rangeValue }}"
                               {{ $isSelected ? 'checked' : '' }}
                               onchange="applyFilters()">
                        <label for="price-range-{{ $index }}">{{ $range[2] }}</label>
                    </div>
                @endforeach
            </div>

            <!-- Custom Price Range -->
            <div class="mt-3">
                <div class="row g-2">
                    <div class="col">
                        <input type="number"
                               class="form-control form-control-sm"
                               placeholder="Min"
                               id="price_min"
                               name="price_min"
                               value="{{ request('price_min') }}">
                    </div>
                    <div class="col-auto align-self-center">to</div>
                    <div class="col">
                        <input type="number"
                               class="form-control form-control-sm"
                               placeholder="Max"
                               id="price_max"
                               name="price_max"
                               value="{{ request('price_max') }}">
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-amazon-secondary w-100" id="apply-custom-price">Go</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="amazon-sidebar">
        <h6>Availability</h6>
        <div class="amazon-filter-section">
            <div class="amazon-filter-item">
                <input type="checkbox"
                       id="in-stock"
                       name="availability[]"
                       value="in_stock"
                       {{ in_array('in_stock', request('availability', [])) ? 'checked' : '' }}
                       onchange="applyFilters()">
                <label for="in-stock">In Stock</label>
            </div>
            <div class="amazon-filter-item">
                <input type="checkbox"
                       id="low-stock"
                       name="availability[]"
                       value="low_stock"
                       {{ in_array('low_stock', request('availability', [])) ? 'checked' : '' }}
                       onchange="applyFilters()">
                <label for="low-stock">Low Stock</label>
            </div>
            @if(Features::backordersEnabled())
                <div class="amazon-filter-item">
                    <input type="checkbox"
                           id="backorder"
                           name="availability[]"
                           value="backorder"
                           {{ in_array('backorder', request('availability', [])) ? 'checked' : '' }}
                           onchange="applyFilters()">
                    <label for="backorder">Available for Backorder</label>
                </div>
            @endif
        </div>
    </div>

    <div class="amazon-sidebar">
        <button type="button" class="btn btn-outline-secondary w-100" id="clear-filters">Clear All Filters</button>
    </div>
</form>

<script>
    function applyFilters() {
        // Clear custom price inputs when using preset ranges
        if (event.target.name === 'price_range') {
            document.getElementById('price_min').value = '';
            document.getElementById('price_max').value = '';
        }

        // Submit the form
        document.getElementById('filter-form').submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle custom price range
        document.getElementById('apply-custom-price').addEventListener('click', function() {
            // Clear radio buttons when using custom range
            document.querySelectorAll('input[name="price_range"]').forEach(radio => {
                radio.checked = false;
            });

            // Submit the form
            document.getElementById('filter-form').submit();
        });

        // Handle clear filters
        document.getElementById('clear-filters').addEventListener('click', function() {
            window.location.href = '{{ route("shop.products.index") }}';
        });

        // Handle Enter key in price inputs
        document.querySelectorAll('#price_min, #price_max').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('apply-custom-price').click();
                }
            });
        });
    });
</script>
