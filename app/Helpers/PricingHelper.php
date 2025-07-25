<?php

namespace App\Helpers;

use App\Helpers\Features;


class PricingHelper
{
    /**
     * Get pricing information for a product based on the customer's price level
     */
    public static function getProductPricing ($product)
    {
        $customer = auth()->user()?->customer;
        $priceLevel = $customer->price_level ?? 1;

        $priceField = 'SellingPrice' . ($priceLevel > 1 ? $priceLevel : '');
        $basePrice = $product->SellingPrice;
        $customerPrice = $priceLevel == 1 ? $basePrice : ($product->$priceField ?? $basePrice);

        $discountPercentage = 0;
        if ($priceLevel > 1 && $basePrice > 0 && $customerPrice < $basePrice) {
            $discountPercentage = round((($basePrice - $customerPrice) / $basePrice) * 100);
        }

        $taxRate = $product->taxType ? $product->taxType->percent : 0;

        return [
            'price' => $customerPrice,
            'base_price' => $basePrice,
            'price_level' => $priceLevel,
            'discount_percentage' => $discountPercentage,
            'tax_rate' => $taxRate,
            'price_ex_tax' => $customerPrice / (1 + ($taxRate / 100)),
            'show_prices' => Features::publicPricesEnabled() || auth()->check(),
        ];
    }

    /**
     * Get the name of the price tier
     */
    public static function getPriceTierName($priceLevel = null)
    {
        if ($priceLevel === null) {
            $customer = auth()->user()?->customer;
            $priceLevel = $customer->price_level ?? 1;
        }

        $tiers = [
            1 => 'Retail',
            2 => 'Wholesale',
            3 => 'Special',
            4 => 'Premium'
        ];

        return $tiers[$priceLevel] ?? 'Retail';
    }

    /**
     * Get the CSS class for the price tier badge
     */
    public static function getPriceTierClass($priceLevel = null)
    {
        if ($priceLevel === null) {
            $customer = auth()->user()?->customer;
            $priceLevel = $customer->price_level ?? 1;
        }

        $classes = [
            1 => 'tier-retail',
            2 => 'tier-wholesale',
            3 => 'tier-special',
            4 => 'tier-premium'
        ];

        return $classes[$priceLevel] ?? 'tier-retail';
    }

    /**
     * Get customer's current price level
     */
    public static function getCustomerPriceLevel()
    {
        $customer = auth()->user()?->customer;
        return $customer->price_level ?? 1;
    }

    /**
     * Format price with currency symbol
     */
    public static function formatPrice($amount, $showCurrency = true)
    {
        $formatted = number_format($amount, 2);

        if ($showCurrency) {
            $currency = config('app.currency', 'R');
            return $currency . ' ' . $formatted;
        }

        return $formatted;
    }

    /**
     * Calculate savings amount between two prices
     */
    public static function calculateSavings($originalPrice, $customerPrice)
    {
        if ($originalPrice <= 0 || $customerPrice >= $originalPrice) {
            return [
                'amount' => 0,
                'percentage' => 0
            ];
        }

        $savingsAmount = $originalPrice - $customerPrice;
        $savingsPercentage = round(($savingsAmount / $originalPrice) * 100);

        return [
            'amount' => $savingsAmount,
            'percentage' => $savingsPercentage
        ];
    }

    /**
     * Check if customer has wholesale pricing
     */
    public static function hasWholesalePricing()
    {
        return self::getCustomerPriceLevel() > 1;
    }

    /**
     * Add pricing information to a collection of products
     */
    public static function addPricingToProducts($products)
    {
        return $products->map(function($product) {
            $product->pricing = self::getProductPricing($product);
            return $product;
        });
    }
}
