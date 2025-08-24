<?php

namespace App\Helpers;

use App\Helpers\Features;
use App\Services\PromotionCalculationService;

class PricingHelper
{
    protected static PromotionCalculationService $promotionService;

    /**
     * Set the promotion service instance
     */
    public static function setPromotionService(PromotionCalculationService $service)
    {
        static::$promotionService = $service;
    }

    /**
     * Get pricing information for a product based on the customer's price level
     */
    public static function getProductPricing($product)
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
     * Get product price with promotions applied
     */
    public static function getProductPrice($product, int $quantity = 1, ?int $customerTier = null): array
    {
        $customerTier = $customerTier ?? (auth()->user() ? (auth()->user()->price_level ?? 1) : 1);

        // Get base pricing first
        $basePricing = self::getProductPricing($product);
        $originalPrice = $basePricing['price'];

        // Initialize promotion service if not set
        if (!isset(static::$promotionService)) {
            static::$promotionService = app(PromotionCalculationService::class);
        }

        // Get promotion calculation
        try {
            // Get the actual Customer model, not the User model
            $customer = auth()->check() ? auth()->user()->customer : null;

            $promotionResult = static::$promotionService->calculateBestPromotion(
                $product->StockCode,
                $quantity,
                $customer,
                $customerTier
            );

            return [
                'original_price' => $originalPrice,
                'discounted_price' => $promotionResult['discounted_price_per_item'],
                'savings' => $promotionResult['savings_per_item'],
                'has_promotion' => $promotionResult['has_promotion'],
                'promotion' => $promotionResult['promotion'],
                'total_savings' => $promotionResult['total_savings'],
                'bonus_quantity' => $promotionResult['bonus_quantity'],
                'message' => $promotionResult['message'],
                'formatted' => [
                    'original' => self::formatPrice($originalPrice / 100),
                    'discounted' => self::formatPrice($promotionResult['discounted_price_per_item'] / 100),
                    'savings' => self::formatPrice($promotionResult['savings_per_item'] / 100)
                ]
            ];
        } catch (\Exception $e) {
            // Fallback if promotion service fails
            return [
                'original_price' => $originalPrice,
                'discounted_price' => $originalPrice,
                'savings' => 0,
                'has_promotion' => false,
                'promotion' => null,
                'total_savings' => 0,
                'bonus_quantity' => 0,
                'message' => 'No promotions available',
                'formatted' => [
                    'original' => self::formatPrice($originalPrice / 100),
                    'discounted' => self::formatPrice($originalPrice / 100),
                    'savings' => self::formatPrice(0)
                ]
            ];
        }
    }

    /**
     * Format price with promotion styling
     */
    public static function formatPromotionPrice(array $priceData): string
    {
        if (!$priceData['has_promotion'] || $priceData['savings'] <= 0) {
            return '<span class="price">' . $priceData['formatted']['original'] . '</span>';
        }

        return sprintf(
            '<span class="price-original text-muted text-decoration-line-through">%s</span> ' .
            '<span class="price-discounted text-danger fw-bold">%s</span> ' .
            '<span class="badge bg-success">Save %s</span>',
            $priceData['formatted']['original'],
            $priceData['formatted']['discounted'],
            $priceData['formatted']['savings']
        );
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

    /**
     * Add pricing information with promotions to a collection of products
     */
    public static function addPromotionPricingToProducts($products, int $quantity = 1, ?int $customerTier = null)
    {
        return $products->map(function($product) use ($quantity, $customerTier) {
            $product->pricing = self::getProductPricing($product);
            $product->promotion_pricing = self::getProductPrice($product, $quantity, $customerTier);
            return $product;
        });
    }

    /**
     * Check if a product has any active promotions
     */
    public static function hasActivePromotion($product): bool
    {
        if (!isset(static::$promotionService)) {
            static::$promotionService = app(PromotionCalculationService::class);
        }

        try {
            // Get the actual Customer model, not the User model
            $customer = auth()->check() ? auth()->user()->customer : null;

            $promotionResult = static::$promotionService->calculateBestPromotion(
                $product->StockCode,
                1,
                $customer
            );

            return $promotionResult['has_promotion'];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get promotion summary for display
     */
    public static function getPromotionSummary($product): ?array
    {
        $priceData = self::getProductPrice($product, 1);

        if (!$priceData['has_promotion']) {
            return null;
        }

        $promotion = $priceData['promotion'];

        return [
            'name' => $promotion->name,
            'type' => $promotion->type,
            'savings' => $priceData['formatted']['savings'],
            'savings_percentage' => $priceData['original_price'] > 0 ? round(($priceData['savings'] / $priceData['original_price']) * 100) : 0,
            'message' => $priceData['message'],
            'ends_at' => $promotion->ends_at,
            'badge_text' => self::getPromotionBadgeText($promotion, $priceData)
        ];
    }

    /**
     * Get appropriate badge text for promotion type
     */
    private static function getPromotionBadgeText($promotion, $priceData): string
    {
        switch ($promotion->type) {
            case 'bogo':
                return 'BOGO';
            case 'quantity_break':
                $savingsPercent = $priceData['original_price'] > 0 ? round(($priceData['savings'] / $priceData['original_price']) * 100) : 0;
                return $savingsPercent . '% OFF';
            case 'bonus_quantity':
                return 'BONUS';
            case 'price_break':
                return 'VOLUME';
            default:
                $savingsPercent = $priceData['original_price'] > 0 ? round(($priceData['savings'] / $priceData['original_price']) * 100) : 0;
                return $savingsPercent > 0 ? $savingsPercent . '% OFF' : 'SPECIAL';
        }
    }

    /**
     * Get price with tier-specific formatting
     */
    public static function formatTierPrice($product, $tier = null): string
    {
        $tier = $tier ?? self::getCustomerPriceLevel();
        $priceField = 'SellingPrice' . ($tier > 1 ? $tier : '');
        $price = $tier == 1 ? $product->SellingPrice : ($product->$priceField ?? $product->SellingPrice);

        return self::formatPrice($price);
    }

    /**
     * Check if promotions are enabled globally
     */
    public static function promotionsEnabled(): bool
    {
        return config('promotions.enabled', true);
    }

    /**
     * Get featured products with promotions
     */
    public static function getFeaturedPromotedProducts(int $limit = 20)
    {
        if (!isset(static::$promotionService)) {
            static::$promotionService = app(PromotionCalculationService::class);
        }

        try {
            return static::$promotionService->getFeaturedPromotedProducts($limit);
        } catch (\Exception $e) {
            // Fallback to regular featured products if promotion service fails
            return collect();
        }
    }
}
