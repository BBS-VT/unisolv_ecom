<?php

namespace App\Helpers;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Cache;

class Features
{
    /**
     * Get the current company ID
     */
    private static function getCompanyId(): ?int
    {
        return auth()->user()?->currentCompany()?->id;
    }

    /**
     * Get a setting value with caching
     */
    private static function getSetting(string $key, $default = null)
    {
        $companyId = self::getCompanyId();

        if (!$companyId) {
            return $default ?? CompanySetting::getDefaultSetting($key);
        }

        // Cache company settings for performance
        $cacheKey = "company_settings_{$companyId}_{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $companyId) {
            return CompanySetting::getSetting($key, $companyId);
        });
    }

    /**
     * Clear cached settings for a company
     */
    public static function clearCache(?int $companyId = null): void
    {
        $companyId = $companyId ?? self::getCompanyId();

        if ($companyId) {
            Cache::flush(); // Or use Cache::tags() for more selective clearing
        }
    }

    /**
     * Check if B2B e-commerce is enabled
     */
    public static function ecommerceEnabled(): bool
    {
        return (bool) self::getSetting('b2b_ecommerce_enabled', false);

    }

    /**
     * Check if guest checkout is allowed
     */
    public static function guestCheckoutEnabled(): bool
    {
        return self::ecommerceEnabled() &&
            (bool) self::getSetting('ecommerce_guest_checkout', false);
    }

    /**
     * Check if prices should be shown without login
     */
    public static function publicPricesEnabled(): bool
    {
        return self::ecommerceEnabled() &&
            (bool) self::getSetting('ecommerce_public_prices', false);
    }

    /**
     * Check if backorders are allowed
     */
    public static function backordersEnabled(): bool
    {
        return self::ecommerceEnabled() &&
            (bool) self::getSetting('ecommerce_backorders', false);
    }

    /**
     * Check if order approval is required
     */
    public static function orderApprovalRequired(): bool
    {
        return self::ecommerceEnabled() &&
            (bool) self::getSetting('ecommerce_require_approval', true);
    }

    /**
     * Check if Bootstrap 5 is enabled
     */
    public static function bootstrap5Enabled(): bool
    {
        return (bool) self::getSetting('bootstrap5_enabled', false);
    }

    /**
     * Get minimum order amount
     */
    public static function minimumOrderAmount(): float
    {
        return (float) self::getSetting('ecommerce_min_order_amount', 0);
    }

    /**
     * Get products per page for e-commerce
     */
    public static function productsPerPage(): int
    {
        return (int) self::getSetting('ecommerce_products_per_page', 24);
    }

    /**
     * Check if new customers require approval
     */
    public static function newCustomerRequiresApproval(): bool
    {
        return (bool) self::getSetting('ecommerce_new_customer_requires_approval', true);
    }
}
