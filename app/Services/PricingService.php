<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SpecialDeals;
use Carbon\Carbon;

class PricingService
{
    public static function getPrice(Product $product, Customer $customer)
    {
        $level = in_array($customer->price_level, [1, 2, 3, 4]) ? $customer->price_level : 1;
        return $product->{'SellingPrice' . $level} ?? $product->SellingPrice;
    }

    /**
     * Get the applicable price for a product based on customer and contract pricing
     *
     * @param Product $product
     * @param string $customerId
     * @return array
     */
    public function getProductPricing(Product $product, $customerId)
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return $this->getDefaultPricing($product);
        }

        // Check for active special deals (contract pricing)
        $specialDeal = $this->getActiveSpecialDeal($product, $customer);

        if ($specialDeal && $specialDeal->UnitPrice > 0) {
            // Contract pricing takes precedence

            $defaultTax = $product->getDefaultTaxes();

            return [
                'price' => $specialDeal->UnitPrice,
                'price2' => $product->SellingPrice2 ?? 0,
                'price3' => $product->SellingPrice3 ?? 0,
                'price4' => $product->SellingPrice4 ?? 0,
                'avg_cost' => $product->stockHolding->LastCostPrice ?? 0,
                'last_cost' => $product->stockHolding->LastCostPrice ?? 0,
                'is_contract' => true,
                'contract_deal' => $specialDeal,
                'default_tax_id' => $defaultTax ? $defaultTax->id : null,
                'tax_rate' => $defaultTax ? $defaultTax->TaxRate : 0,
            ];
        }

        // No contract pricing, use customer's price level
        $priceLevel = $customer->price_level ?? 1;
        $price = $this->getPriceByLevel($product, $priceLevel);

        $defaultTax = $product->getDefaultTaxes();

        return [
            'price' => $price,
            'price2' => $product->SellingPrice2 ?? 0,
            'price3' => $product->SellingPrice3 ?? 0,
            'price4' => $product->SellingPrice4 ?? 0,
            'avg_cost' => $product->stockHolding->LastCostPrice ?? 0,
            'last_cost' => $product->stockHolding->LastCostPrice ?? 0,
            'is_contract' => false,
            'contract_deal' => null,
            'default_tax_id' => $defaultTax ? $defaultTax->id : null,
            'tax_rate' => $defaultTax ? $defaultTax->percent : 0,
        ];
    }

    /**
     * Get active special deal for product and customer
     * Priority: Customer specific > Buying Group > Customer Category > Stock Group
     */
    private function getActiveSpecialDeal(Product $product, Customer $customer)
    {
        $today = Carbon::today();

        // 1. Customer-specific deal (highest priority)
        $deal = SpecialDeals::where('StockItemID', $product->StockCode)
            ->where('CustomerID', $customer->acc_main)
            ->whereDate('StartDate', '<=', $today)
            ->whereDate('EndDate', '>=', $today)
            ->first();

        if ($deal) {
            return $deal;
        }

        // 2. Buying Group deal
        if ($customer->BuyingGroupID) {
            $deal = SpecialDeals::where('StockItemID', $product->StockCode)
                ->where('BuyingGroupID', $customer->BuyingGroupID)
                ->whereDate('StartDate', '<=', $today)
                ->whereDate('EndDate', '>=', $today)
                ->first();

            if ($deal) {
                return $deal;
            }
        }

        // 3. Customer Category deal
        if ($customer->CustomerCategoryID) {
            $deal = SpecialDeals::where('StockItemID', $product->StockCode)
                ->where('CustomerCategoryID', $customer->CustomerCategoryID)
                ->whereDate('StartDate', '<=', $today)
                ->whereDate('EndDate', '>=', $today)
                ->first();

            if ($deal) {
                return $deal;
            }
        }

        // 4. Stock Group deal (lowest priority)
        if ($product->StockGroupID) {
            $deal = SpecialDeals::where('StockGroupID', $product->StockGroupID)
                ->whereNull('StockItemID') // Stock group deals don't have specific item
                ->whereDate('StartDate', '<=', $today)
                ->whereDate('EndDate', '>=', $today)
                ->first();

            if ($deal) {
                return $deal;
            }
        }

        return null;
    }

    /**
     * Get price based on customer's price level
     */
    private function getPriceByLevel(Product $product, $priceLevel)
    {
        switch ($priceLevel) {
            case 2:
                return $product->SellingPrice2 ?? $product->SellingPrice;
            case 3:
                return $product->SellingPrice3 ?? $product->SellingPrice;
            case 4:
                return $product->SellingPrice4 ?? $product->SellingPrice;
            default:
                return $product->SellingPrice;
        }
    }

    /**
     * Get default pricing when no customer is selected
     */
    private function getDefaultPricing(Product $product)
    {
        $defaultTax = $product->getDefaultTaxes();

        return [
            'price' => $product->SellingPrice,
            'price2' => $product->SellingPrice2 ?? 0,
            'price3' => $product->SellingPrice3 ?? 0,
            'price4' => $product->SellingPrice4 ?? 0,
            'avg_cost' => $product->stockHolding->LastCostPrice ?? 0,
            'last_cost' => $product->stockHolding->LastCostPrice ?? 0,
            'is_contract' => false,
            'contract_deal' => null,
            'default_tax_id' => $defaultTax ? $defaultTax->id : null,
            'tax_rate' => $defaultTax ? $defaultTax->TaxRate : 0,
        ];
    }
}
