@auth
    <li class="amazon-nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-1"></i>
            {{ Auth::user()->customer->CustomerName ?? 'My Account' }}
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
            <li>
                <a class="dropdown-item" href="{{ route('shop.account.index') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('shop.account.orders.index') }}">
                    <i class="fas fa-shopping-bag me-2"></i>
                    My Orders
                    @php
                        $newOrdersCount = \App\Models\Order::where('CustomerID', Auth::user()->customer->id ?? 0)
                            ->where('OrderStatusID', 1)
                            ->count();
                    @endphp
                    @if($newOrdersCount > 0)
                        <span class="badge bg-primary ms-1">{{ $newOrdersCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('shop.account.profile') }}">
                    <i class="fas fa-user-edit me-2"></i>
                    Profile Settings
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('shop.cart.show') }}">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Shopping Cart
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="badge bg-success ms-1">{{ count(session('cart')) }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('shop.products.index') }}">
                    <i class="fas fa-store me-2"></i>
                    Continue Shopping
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>

            {{-- Account Status Indicator --}}
            @if(Auth::user()->customer && Auth::user()->customer->IsOnCreditHold)
                <li>
                    <span class="dropdown-item-text text-danger small">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Account on Credit Hold
                    </span>
                </li>
                <li><hr class="dropdown-divider"></li>
            @endif

            <li>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </li>
@else
    <li class="amazon-nav-item">

        <a href="#" class="amazon-nav-item me-3" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="fas fa-sign-in-alt me-1"></i>
            Login
        </a>
    </li>
@endauth

{{-- Account Quick Stats Notification --}}
@auth
    @if(Auth::user()->customer)
        @php
            $customer = Auth::user()->customer;
            $creditUtilization = $customer->CreditLimit > 0 ? 0 : 0; // Calculate actual utilization
            $showCreditWarning = $creditUtilization > 80;
        @endphp

        @if($showCreditWarning || $customer->IsOnCreditHold)
            <li class="nav-item">
                <span class="navbar-text">
                    @if($customer->IsOnCreditHold)
                        <span class="badge bg-danger">
                            <i class="fas fa-exclamation-triangle"></i> Credit Hold
                        </span>
                    @elseif($showCreditWarning)
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-exclamation-triangle"></i> {{ number_format($creditUtilization, 0) }}% Credit Used
                        </span>
                    @endif
                </span>
            </li>
        @endif
    @endif
@endauth
