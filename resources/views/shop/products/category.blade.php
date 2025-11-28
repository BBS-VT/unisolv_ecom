@extends('shop.layouts.app')

@section('title', $category->StockGroupName)

@section('content')
    <nav aria-label="breadcrumb" class="amazon-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.products.index') }}">{{ __('Products') }}</a></li>
            <li class="breadcrumb-item active">{{ $category->StockGroupName }}</li>
        </ol>
    </nav>
    <div class="row mx-1">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 col-md-4">
            @include('shop.products.partials.filters', ['currentCategory' => $category])
        </div>

        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 border rounded">
                <div>
                    <h5 class="mb-1">
                        @if(request('search'))
                            Results for "{{ request('search') }}"
                        @else
                            {{ $category->StockGroupName }}
                        @endif
                    </h5>
                    <p class="text-muted mb-0">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
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

                        @foreach($selectedCategories ?? [] as $categoryId)
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

            @if($products->hasPages())
                <div class="d-flex justify-content-center mt-5">
                    {{ $products->links('shop.components.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
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

            // Handle product card clicks (but not buttons)
            $(document).on('click', '.amazon-product-card', function(e) {
                if ($(e.target).closest('button, a').length > 0) {
                    return;
                }

                const productUrl = $(this).data('product-url');
                if (productUrl) {
                    window.location.href = productUrl;
                }
            });

            // Add to cart functionality
            $(document).on('click', '.add-to-cart-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Add to cart button clicked');

                const productId = $(this).data('product-id');
                const button = $(this);
                const originalText = button.html();

                console.log('Product ID:', productId);

                if (!productId) {
                    console.error('No product ID found');
                    alert('Error: No product ID found');
                    return;
                }

                // Show loading state
                button.html('<i class="bi bi-hourglass-split me-1"></i>Adding...');
                button.prop('disabled', true);

                console.log('Making AJAX request to:', '{{ route("shop.cart.add") }}');

                $.ajax({
                    url: '{{ route("shop.cart.add") }}',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        quantity: 1,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        console.log('AJAX request starting....')
                    },
                    success: function(response) {
                        console.log('AJAX success:', response);

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
                        } else {
                            console.error('Server returned success=false:', response);
                            alert('Error: ' + (response.message || 'Unknown error'));
                            button.html(originalText);
                            button.prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error
                        });

                        let errorMessage = 'Error adding product to cart';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);

                        // Reset button
                        button.html(originalText);
                        button.prop('disabled', false);
                    },
                    complete: function() {
                        console.log('AJAX request completed');
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

            // Password toggle functionality
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function (e) {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }

// Enhanced login form handling
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');

            if (loginForm && loginBtn) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Clear previous errors
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                    // Show loading state
                    loginBtn.disabled = true;
                    loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';

                    // Submit form via fetch for better error handling
                    fetch(loginForm.action, {
                        method: 'POST',
                        body: new FormData(loginForm),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (response.ok) {
                                // Success - reload the page to show logged in state
                                window.location.reload();
                            } else {
                                return response.json();
                            }
                        })
                        .then(data => {
                            if (data && data.errors) {
                                // Show validation errors
                                Object.keys(data.errors).forEach(field => {
                                    const input = document.getElementById(field);
                                    const errorDiv = document.getElementById(field + '-error');
                                    if (input && errorDiv) {
                                        input.classList.add('is-invalid');
                                        errorDiv.textContent = data.errors[field][0];
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Login error:', error);
                            alert('An error occurred during login. Please try again.');
                        })
                        .finally(() => {
                            // Reset button state
                            loginBtn.disabled = false;
                            loginBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Login';
                        });
                });
            }
        });
    </script>
    <script>
        window.cartRoutes = {
            add: '{{ route("shop.cart.add") }}',
            remove: '{{ route("shop.cart.remove") }}',
            update: '{{ route("shop.cart.update") }}',
            mini: '{{ route("shop.cart.mini") }}',
            clear: '{{ route("shop.cart.clear") }}'
        };
    </script>

@endpush
