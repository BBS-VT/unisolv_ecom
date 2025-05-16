@php
    $company = auth()->user()?->currentCompany();
@endphp

<footer class="bg-dark text-white mt-5">
    <div class="container py-5">
        <div class="row">
            {{-- Company Information --}}
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">{{ $company?->name ?? config('app.name') }}</h5>
                <p class="text-muted">
                    Your trusted B2B partner for quality products and exceptional service.
                </p>
                @if($company)
                    <address class="text-muted">
                        @if($company->address1)
                            {{ $company->address1 }}<br>
                        @endif
                        @if($company->address2)
                            {{ $company->address2 }}<br>
                        @endif
                        @if($company->city || $company->postal_code)
                            {{ $company->city }} {{ $company->postal_code }}<br>
                        @endif
                        @if($company->phone)
                            <i class="fas fa-phone me-1"></i> {{ $company->phone }}<br>
                        @endif
                        @if($company->email)
                            <i class="fas fa-envelope me-1"></i> {{ $company->email }}
                        @endif
                    </address>
                @endif
            </div>

            {{-- Quick Links --}}
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('shop.products.index') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1 small"></i> All Products
                        </a>
                    </li>
                    @auth
                        <li class="mb-2">
                            <a href="{{ route('shop.account.orders.index') }}" class="text-muted text-decoration-none">
                                <i class="fas fa-chevron-right me-1 small"></i> Order History
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('shop.account.index') }}" class="text-muted text-decoration-none">
                                <i class="fas fa-chevron-right me-1 small"></i> My Account
                            </a>
                        </li>
                    @endauth
                    <li class="mb-2">
                        <a href="{{ route('shop.contact') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1 small"></i> Contact Us
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('shop.terms') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1 small"></i> Terms & Conditions
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Customer Service --}}
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Customer Service</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <span class="text-muted">Mon-Fri: 8AM - 5PM</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-truck me-2 text-primary"></i>

                    </li>
                    <li class="mb-2">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>
                        <span class="text-muted">Secure checkout</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-undo me-2 text-primary"></i>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-secondary my-4">

        {{-- Bottom Bar --}}
        <div class="row">
            <div class="col-md-6">
                <p class="text-muted mb-0">
                    &copy; {{ date('Y') }} {{ $company?->name ?? config('app.name') }}. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-muted mb-0">
                    Powered by <a href="#" class="text-decoration-none text-primary">{{ config('app.name') }}</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    footer {
        margin-top: auto;
    }

    footer a:hover {
        color: #fff !important;
    }

    footer .text-primary {
        color: #66b3ff !important;
    }
</style>
