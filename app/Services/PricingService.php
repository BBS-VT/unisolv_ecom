<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;

class PricingService
{
    public static function getPrice(Product $product, Customer $customer)
    {
        $level = in_array($customer->price_level, [1, 2, 3, 4]) ? $customer->price_level : 1;
        return $product->{'SellingPrice' . $level} ?? $product->SellingPrice;
    }
}
