
@extends('shop.layouts.app')

@section('title', 'All Products')

@section('content')

    <nav aria-label="breadcrumb" class="amazon-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.home') }}">Home</a></li>
            <li class="breadcrumb-item active">All Products</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 col-md-4">
            @include('shop.products.partials.filters')
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 border rounded">
                <div>
                    <h5 class="mb-1">
                        @if(request('search'))
                            Results for "{{ request('search') }}"
                        @else
                            {{ __('All Products') }}
                        @endif
                    </h5>
                    <p class="text-muted mb-0">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-2">Sort by:</span>
                    <select class="form-select form-select-sm" id="sort-select" style="width:auto;">
                        <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Featured</option>
                        <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                    </select>
                </div>
            </div>

            @if(request()->hasAny(['categories', 'price_range', 'availability', 'search']))
                <div class="bg-white p-3 border rounded mb-3">
                    <div class="d-flex flex-wrap align-items-center">
                        <span class="me-2"><strong>{{ __('Applied filters:') }}</strong></span>
                        @if(request('search'))
                            <span class="badge bg-primary me-2 mb-1">
                                Search: "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="text-white ms-1">x</a>
                            </span>
                        @endif

                        @foreach(request('categories', []) as $categoryId)
                            @php $category = $categories->find($categoryId) @endphp
                            @if($category)
                                <span class="badge bg-secondary me-2 mb-1">
                                    {{ $category->StockGroupName }}
                                     <a href="{{ request()->fullUrlWithoutQuery(['categories' => $categoryId]) }}" class="text-white ms-1">×</a>
                                </span>
                            @endif
                        @endforeach

                        @if(request('price_range'))
                            <span class="badge bg-secondary me-2 mb-1">
                                Price: ${{ str_replace('-', ' - ', request('price_range')) }}
                                <a href="{{ request()->fullUrlWithoutQuery('price_range') }}" class="text-white ms-1">×</a>
                            </span>
                        @endif

                        <a href="{{ route('shop.products.index') }}" class="btn btn-sm btn-outline-secondary ms-2">Clear All</a>
                    </div>
                </div>
            @endif

            <!-- Products Grid -->
            <div class="row g-3" id="products-container">
                @forelse($products as $product)
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-3">
                        @include('shop.products.partials.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="bi bi-search" style="font-size: 4rem; color: #ccc;"></i>
                            </div>
                            <h3>{{ __('No products found matching your criteria.') }}</h3>
                            <p class="text-muted">{{ __('Try adjusting your search or filter criteria') }}</p>
                            <a href="{{ route('shop.products.index') }}" class="btn btn-amazon-primary">{{ __('Browse All
                                Products') }}</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->links('shop.components.pagination') }}
                </div>
            @endif
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Filter change handlers
            $('input[type="checkbox"], input[type="radio"]').change(function() {
                applyFilters();
            });

            // Sort change handler
            $('#sort-select').change(function() {
                applyFilters();
            });

            // Custom price range
            $('#apply-price-range').click(function() {
                const min = $('#price_min').val();
                const max = $('#price_max').val();

                if (min || max) {
                    // Clear radio buttons
                    $('input[name="price_range"]').prop('checked', false);

                    // Apply custom range
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('price_min', min || '0');
                    currentUrl.searchParams.set('price_max', max || '999999');
                    window.location.href = currentUrl.toString();
                }
            });

            // Clear filters
            $('#clear-filters').click(function() {
                window.location.href = '{{ route("shop.products.index") }}';
            });

            // Add to cart functionality
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const productId = $(this).data('product-id');
                const button = $(this);
                const originalText = button.html();

                // Show loading state
                button.html('<i class="bi bi-hourglass-split me-1"></i>Adding...');
                button.prop('disabled', true);

                $.ajax({
                    url: '{{ route("shop.cart.add") }}',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: 1,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update cart badge
                            $('.cart-badge').text(response.cart_count);

                            // Show success state
                            button.html('<i class="bi bi-check-circle me-1"></i>Added!');
                            button.removeClass('btn-amazon-primary').addClass('btn-success');

                            // Show toast notification
                            showCartToast('Product added to cart successfully', 'success');

                            // Reset button after 2 seconds
                            setTimeout(function() {
                                button.html(originalText);
                                button.removeClass('btn-success').addClass('btn-amazon-primary');
                                button.prop('disabled', false);
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        showCartToast(response.message || 'Error adding product to cart', 'danger');

                        // Reset button
                        button.html(originalText);
                        button.prop('disabled', false);
                    }
                });
            });

            function applyFilters() {
                const currentUrl = new URL(window.location);

                // Clear existing filter params
                currentUrl.searchParams.delete('categories');
                currentUrl.searchParams.delete('price_range');
                currentUrl.searchParams.delete('availability');
                currentUrl.searchParams.delete('sort');

                // Add selected filters
                const categories = [];
                $('input[name="categories[]"]:checked').each(function() {
                    categories.push($(this).val());
                });
                if (categories.length > 0) {
                    currentUrl.searchParams.set('categories', categories.join(','));
                }

                const priceRange = $('input[name="price_range"]:checked').val();
                if (priceRange) {
                    currentUrl.searchParams.set('price_range', priceRange);
                }

                const availability = [];
                $('input[name="availability[]"]:checked').each(function() {
                    availability.push($(this).val());
                });
                if (availability.length > 0) {
                    currentUrl.searchParams.set('availability', availability.join(','));
                }

                const sort = $('#sort-select').val();
                if (sort && sort !== 'relevance') {
                    currentUrl.searchParams.set('sort', sort);
                }

                // Navigate to new URL
                window.location.href = currentUrl.toString();
            }

            function showCartToast(message, type = 'success') {
                const toast = $('#cartToast');
                const toastBody = toast.find('.toast-body');
                const toastHeader = toast.find('.toast-header');

                toastBody.text(message);

                // Reset classes
                toastHeader.removeClass('bg-success bg-danger text-white');

                if (type === 'success') {
                    toastHeader.addClass('bg-success text-white');
                } else if (type === 'danger') {
                    toastHeader.addClass('bg-danger text-white');
                }

                const bsToast = new bootstrap.Toast(toast[0]);
                bsToast.show();
            }
        });
    </script>
    <script>
        window.cartRoutes = {
            add: '{{ route("cart.add") }}',
            remove: '{{ route("cart.remove") }}',
            update: '{{ route("cart.update") }}',
            mini: '{{ route("cart.mini") }}',
            clear: '{{ route("cart.clear") }}'
        };
    </script>
    {{--<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add to cart functionality
            document.querySelectorAll('.add-to-cart-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const productId = this.dataset.productId;
                    const quantity = this.querySelector('input[name="quantity"]').value;
                    const button = this.querySelector('button[type="submit"]');

                    // Disable button during submission
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Adding...';

                    fetch('{{ route("shop.cart.add") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: quantity
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update cart count in navbar
                                document.querySelector('#cart-count').textContent = data.cartCount;

                                // Reset button
                                button.innerHTML = '<i class="fas fa-check me-1"></i> Added!';
                                button.classList.remove('btn-primary');
                                button.classList.add('btn-success');

                                setTimeout(() => {
                                    button.disabled = false;
                                    button.innerHTML = '<i class="fas fa-cart-plus me-1"></i> Add to Cart';
                                    button.classList.remove('btn-success');
                                    button.classList.add('btn-primary');
                                }, 2000);
                            } else {
                                // Show error
                                alert(data.message || 'Error adding to cart');
                                button.disabled = false;
                                button.innerHTML = '<i class="fas fa-cart-plus me-1"></i> Add to Cart';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error adding to cart');
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-cart-plus me-1"></i> Add to Cart';
                        });
                });
            });
        });
    </script>--}}
@endsection
