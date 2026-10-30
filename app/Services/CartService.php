<?php

namespace App\Services;

use App\Models\Location;
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
     * The location whose stock a cart is checked against and fulfilled from:
     * the locked location, else the product's own department location,
     * else the default location.
     */
    public static function stockLocation(?Product $product = null): string
    {
        return session('cart_location')
            ?? $product?->categories()->whereNotNull('location_id')->first()?->location_id
            ?? Location::where('IsDefault', true)->value('LocationCode')
            ?? '0000';
    }

    /**
     * Clear cart location lock
     */
    public static function clearLocationLock(): void
    {
        session()->forget('cart_location');
    }
}
