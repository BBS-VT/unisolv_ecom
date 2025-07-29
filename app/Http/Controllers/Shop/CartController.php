<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Helpers\Features;
use App\Models\Product;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Illuminate\Support\Facades\Validator;
use App\Events\CartUpdated;

class CartController extends Controller
{
    /**
     * Add product to cart via AJAX
     */
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($request->product_id);
            $quantity = $request->quantity;

            // Check stock availability unless backorders are allowed
            if (!Features::backordersEnabled() && $product->stockHolding->QuantityOnHand < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available.',
                ], 422);
            }

            if (!Session::has('cart')) {
                Session::put('cart', []);
            }

            $cart = Session::get('cart');

            // Check if the product is already in the cart
            $existingItem = false;
            foreach ($cart as $key => $item) {
                if ($item['product_id'] == $request->product_id) {
                    $cart[$key]['quantity'] += $request->quantity;
                    $existingItem = true;
                    break;
                }
            }

            // If the product is not in the cart, add it
            if (!$existingItem) {
                $price = $this->getPriceForCustomer($product);

                $cart[] = [
                    'product_id' => $product->id,
                    'name' => $product->StockItemName,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'added_at' => now()->timestamp ,
                ];
            }

            Session::put('cart', $cart);

            if (Auth::check()) {
                $this->syncCartToUser();
                event(new CartUpdated(Auth::id(), $cart, 'add'));
            }

            $cartCount = $this->getCartCount();

            return response()->json([
                'success' => true,
                'message' => 'Added to cart successfully.',
                'cart_count' => $cartCount,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->StockItemName,
                    'quantity' => $quantity,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Add to cart error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart: ' . $e->getMessage(),
            ], 500);
        }

    }

    public function showCart()
    {
        $returnUrl = url()->previous();

        if (strpos($returnUrl, '/cart') === false) {
            Session::put('shopping_return_url', $returnUrl);
        }

        $cart = Session::get('cart', []);
        $cartTotal = $this->getCartTotal();
        $returnToShoppingUrl = Session::get('shopping_return_url', route('shop.products.index'));

        return view('shop.cart.show', compact('cart', 'cartTotal', 'returnToShoppingUrl'));

    }

    public function updateCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $cart = Session::get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $request->product_id) {
                if ($request->quantity == 0) {
                    // Remove item if quantity is 0
                    unset($cart[$key]);
                } else {
                    // Check stock before updating
                    $product = Product::find($request->product_id);
                    if (!Features::backordersEnabled() && $product->stockHolding->QuantityOnHand < $request->quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => "Sorry, we only have {$product->stockHolding->QuantityOnHand} items in stock."
                        ], 422);
                    }

                    $cart[$key]['quantity'] = $request->quantity;
                }
                break;
            }
        }

        // Reindex the array
        $cart = array_values($cart);
        Session::put('cart', $cart);

        // If user is logged in, save cart to database
        if (Auth::check()) {
            $this->syncCartToUser();
            event(new CartUpdated(Auth::id(), $cart, 'update'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal(),
            'cart_html' => view('shop.cart.partials.cart-items', compact('cart'))->render()
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $cart = Session::get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $request->product_id) {
                unset($cart[$key]);
                break;
            }
        }

        // Reindex the array
        $cart = array_values($cart);
        Session::put('cart', $cart);

        // If user is logged in, save cart to database
        if (Auth::check()) {
            $this->syncCartToUser();
            event(new CartUpdated(Auth::id(), $cart, 'remove'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => $this->getCartCount(),
            'cart_total' => $this->getCartTotal(),
            'cart_html' => view('shop.cart.partials.cart-items', compact('cart'))->render()
        ]);
    }

    public function clearCart()
    {
        Session::forget('cart');

        // If user is logged in, clear cart in database
        if (Auth::check()) {
            $this->syncCartToUser();
            event(new CartUpdated(Auth::id(), [], 'clear'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0,
            'cart_total' => 0,
        ]);
    }

    /**
     * Get price for customer based on their pricing level
     */
    private function getPriceForCustomer($product)
    {
        // If user is not logged in, use default price
        if (!Auth::check()) {
            return Features::showPrices() ? $product->price : 0;
        }

        $customer = Auth::user();

        // Check if user has custom pricing
        switch ($customer->price_level) {
            case 'wholesale':
                return $product->wholesale_price ?: $product->price;
            case 'distributor':
                return $product->distributor_price ?: $product->price;
            default:
                return $product->price;
        }
    }

    private function getCartCount()
    {
        $cart = Session::get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }

    private function getCartTotal()
    {
        $total = 0;
        $cart = Session::get('cart', []);

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return $total;
    }

    private function syncCartToUser()
    {
        $user = Auth::user();
        $cart = Session::get('cart', []);

        UserCart::updateOrCreate(
            ['user_id' => $user->id],
            ['cart_data' => $cart]
        );
    }

    public function getMiniCart()
    {
        $cart = Session::get('cart', []);
        $cartTotal = $this->getCartTotal();
        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'mini_cart_html' => view('shop.partials.mini-cart', compact('cart', 'cartTotal', 'cartCount'))->render()
        ]);
    }


}
