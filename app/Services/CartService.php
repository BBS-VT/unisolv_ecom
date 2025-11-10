<?php

namespace App\Services;

use App\Models\Product;
class CartService
{

    /**
     * Ensure cart location lock is set based on cart contents
     */
    public static function ensureLocationLock(): ?string
    {
        $cart = session('cart', []);
        $cartLocation = session('cart_location');

        // Already locked, return current location
        if ($cartLocation) {
            return $cartLocation;
        }

        // No cart items, nothing to lock
        if (empty($cart)) {
            return null;
        }

        // Find first location-specific product and lock to it
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);

            if ($product) {
                $productLocation = $product->categories()
                    ->whereNotNull('location_id')
                    ->first()
                    ?->location_id;

                if ($productLocation) {
                    session(['cart_location' => $productLocation]);
                    return $productLocation;
                }
            }
        }

        return null;
    }

    /**
     * Get the current cart location
     */
    public static function getCartLocation(): ?string
    {
        return session('cart_location');
    }

    /**
     * Clear cart location lock
     */
    public static function clearLocationLock(): void
    {
        session()->forget('cart_location');
    }
}
