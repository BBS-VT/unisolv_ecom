<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Customer;
use App\Models\SpecialDeals;
use Carbon\Carbon;

class DiscountService
{
    /**
     * Get applicable discount rules for a product and customer
     *
     * @param Product $product
     * @param string $customerId
     * @param bool $isContractPrice
     * @return array
     */
    public function getApplicableDiscount(Product $product, $customerId, $isContractPrice = false)
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return $this->getProductDiscountRules($product);
        }

        // Check if customer has discounts blocked
        if ($customer->discount_allowed === false || $customer->discount_allowed === 0) {
            return [
                'discount' => 0,
                'max_discount' => 0,
                'is_locked' => true,
                'reason' => 'Customer discounts blocked',
                'is_contract' => false,
            ];
        }

        // Check for contract discount
        $contractDiscount = $this->getContractDiscount($product, $customer);

        if ($contractDiscount !== null) {
            // Contract discount found
            return [
                'discount' => $contractDiscount['discount'],
                'max_discount' => $contractDiscount['discount'],
                'is_locked' => true, // Contract discounts are locked
                'reason' => 'Contract discount',
                'is_contract' => true,
            ];
        }

        // Check for customer standard discount
        if ($customer->StandardDiscountPercentage > 0) {
            // Standard discount overrides product discount rules
            // But we still respect product's "no discount" rule (-1)
            if ($product->DiscountPercentage === -1) {
                return [
                    'discount' => 0,
                    'max_discount' => 0,
                    'is_locked' => true,
                    'reason' => 'Product does not allow discounts',
                    'is_contract' => false,
                ];
            }

            return [
                'discount' => $customer->StandardDiscountPercentage,
                'max_discount' => $customer->StandardDiscountPercentage,
                'is_locked' => true, // Standard customer discount is locked
                'reason' => 'Customer standard discount',
                'is_contract' => false,
            ];
        }

        // No contract or standard discount, use product discount rules
        return $this->getProductDiscountRules($product);
    }

    /**
     * Get contract discount from special deals
     */
    private function getContractDiscount(Product $product, Customer $customer)
    {
        $today = Carbon::today();

        // Check in priority order: Customer > Buying Group > Customer Category > Stock Group

        // 1. Customer-specific
        $deal = SpecialDeals::where('StockItemID', $product->StockCode)
            ->where('CustomerID', $customer->acc_main)
            ->whereDate('StartDate', '<=', $today)
            ->whereDate('EndDate', '>=', $today)
            ->whereNotNull('DiscountPercentage')
            ->first();

        if ($deal && $deal->DiscountPercentage > 0) {
            return ['discount' => $deal->DiscountPercentage];
        }

        // 2. Buying Group
        if ($customer->BuyingGroupID) {
            $deal = SpecialDeals::where('StockItemID', $product->StockCode)
                ->where('BuyingGroupID', $customer->BuyingGroupID)
                ->whereDate('StartDate', '<=', $today)
                ->whereDate('EndDate', '>=', $today)
                ->whereNotNull('DiscountPercentage')
                ->first();

            if ($deal && $deal->DiscountPercentage > 0) {
                return ['discount' => $deal->DiscountPercentage];
            }
        }

        // 3. Customer Category
        if ($customer->CustomerCategoryID) {
            $deal = SpecialDeals::where('StockItemID', $product->StockCode)
                ->where('CustomerCategoryID', $customer->CustomerCategoryID)
                ->whereDate('StartDate', '<=', $today)
                ->whereDate('EndDate', '>=', $today)
                ->whereNotNull('DiscountPercentage')
                ->first();

            if ($deal && $deal->DiscountPercentage > 0) {
                return ['discount' => $deal->DiscountPercentage];
            }
        }

        // 4. Stock Group
        if ($product->StockGroupID) {
            $deal = SpecialDeals::where('StockGroupID', $product->StockGroupID)
                ->whereNull('StockItemID')
                ->whereDate('StartDate', '<=', $today)
                ->whereDate('EndDate', '>=', $today)
                ->whereNotNull('DiscountPercentage')
                ->first();

            if ($deal && $deal->DiscountPercentage > 0) {
                return ['discount' => $deal->DiscountPercentage];
            }
        }

        return null;
    }

    /**
     * Get product-level discount rules
     */
    private function getProductDiscountRules(Product $product)
    {
        $discountPercentage = $product->DiscountPercentage;

        // -1 means no discount allowed
        if ($discountPercentage === -1 || $discountPercentage === '-1') {
            return [
                'discount' => 0,
                'max_discount' => 0,
                'is_locked' => true,
                'reason' => 'Product does not allow discounts',
                'is_contract' => false,
            ];
        }

        // null or empty means unlimited discount
        if ($discountPercentage === null || $discountPercentage === '') {
            return [
                'discount' => 0,
                'max_discount' => 100, // Unlimited
                'is_locked' => false,
                'reason' => 'Unlimited discount allowed',
                'is_contract' => false,
            ];
        }

        // Specific percentage means max discount
        return [
            'discount' => 0, // Don't pre-fill
            'max_discount' => (float) $discountPercentage,
            'is_locked' => false,
            'reason' => "Max discount: {$discountPercentage}%",
            'is_contract' => false,
        ];
    }

    /**
     * Validate if a discount is allowed
     *
     * @param float $requestedDiscount
     * @param array $discountRules
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateDiscount($requestedDiscount, $discountRules)
    {
        // If discount is locked, can't change it
        if ($discountRules['is_locked']) {
            if ($requestedDiscount != $discountRules['discount']) {
                return [
                    'valid' => false,
                    'message' => $discountRules['reason'],
                ];
            }
            return ['valid' => true, 'message' => ''];
        }

        // Check against max discount
        if ($requestedDiscount > $discountRules['max_discount']) {
            return [
                'valid' => false,
                'message' => "Discount cannot exceed {$discountRules['max_discount']}%",
            ];
        }

        return ['valid' => true, 'message' => ''];
    }
}
