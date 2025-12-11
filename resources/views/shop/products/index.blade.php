
@extends('shop.layouts.app')

@section('title', 'All Products')

@section('content')

    <nav aria-label="breadcrumb" class="amazon-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.home') }}">Home</a></li>
            <li class="breadcrumb-item active">All Products</li>
        </ol>
    </nav>

    <div class="row mx-1">
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
                <div class="d-flex justify-content-center mt-3 mb-2">
                    {{ $products->links('shop.components.pagination') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="fas fa-user-circle me-2 text-primary"></i>Customer Login
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="text-center mb-4">
                        <p class="text-muted">Login to access your wholesale pricing and place orders</p>
                    </div>

                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="redirect_to_shop" value="1">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email') }}" required autofocus>
                            </div>
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="password-error"></div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Need an account? <a href="#" class="text-primary">Contact us for business registration</a>
                        </small>
                    </div>
                </div>
            </div>
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
