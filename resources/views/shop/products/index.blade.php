
@extends('shop.layouts.app')

@section('title', 'All Products')

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">All Products</li>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3 mb-4">
                @include('shop.products.partials.filters')
            </div>

            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h2">All Products</h1>
                        <p class="text-muted mb-0">
                            Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                        </p>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-md-6 col-lg-4 mb-4">
                            @include('shop.products.partials.product-card', ['product' => $product])
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i> No products found matching your criteria.
                                <a href="{{ route('shop.products.index') }}" class="alert-link">Clear filters</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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
    </script>
@endsection
