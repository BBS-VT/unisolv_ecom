<?php

namespace App\Services;

use App\Models\StockItemHoldings;
use App\Models\Location;

class LocationAssignmentService
{

    /**
     * Determine the best location to fulfill an order item
     *
     * @param string $stockCode
     * @param float $quantity
     * @param string|null $preferredLocation
     * @return string
     */
    public static function assignLocation($stockCode, $quantity, $preferredLocation = null)
    {
        // If multi-location is disabled, always use default
        if (!app('currentCompany')->getSetting('sales_locations')) {
            return '0000';
        }

        // If a preferred location is specified and has stock, use it
        if ($preferredLocation) {
            $stock = StockItemHoldings::where('StockCode', $stockCode)
                ->where('LocationCode', $preferredLocation)
                ->first();

            if ($stock && $stock->QuantityOnHand >= $quantity) {
                return $preferredLocation;
            }
        }

        // Find the first active location with sufficient stock
        $stockHoldings = StockItemHoldings::where('StockCode', $stockCode)
            ->where('QuantityOnHand', '>=', $quantity)
            ->whereHas('location', function($q) {
                $q->where('IsActive', true);
            })
            ->orderByDesc('QuantityOnHand') // Prioritize locations with more stock
            ->first();

        if ($stockHoldings) {
            return $stockHoldings->LocationCode;
        }

        // If no location has enough stock, try the default location
        $defaultLocation = Location::where('IsDefault', true)->first();
        if ($defaultLocation) {
            return $defaultLocation->LocationCode;
        }

        // Final fallback
        return '0000';
    }

    /**
     * Check if an order can be fulfilled from a single location
     *
     * @param array $items Array of ['stock_code' => quantity]
     * @return string|false Returns LocationCode or false if split required
     */
    public static function canFulfillFromSingleLocation($items)
    {
        $activeLocations = Location::where('IsActive', true)->pluck('LocationCode');

        foreach ($activeLocations as $locationCode) {
            $canFulfillAll = true;

            foreach ($items as $stockCode => $quantity) {
                $available = StockItemHoldings::where('StockCode', $stockCode)
                    ->where('LocationCode', $locationCode)
                    ->value('QuantityOnHand') ?? 0;

                if ($available < $quantity) {
                    $canFulfillAll = false;
                    break;
                }
            }

            if ($canFulfillAll) {
                return $locationCode;
            }
        }

        return false;
    }

    /**
     * Split order items across multiple locations if needed
     *
     * @param array $items Array of ['stock_code' => quantity]
     * @return array Array of ['stock_code' => ['LocationCode' => quantity]]
     */
    public static function splitAcrossLocations($items)
    {
        $assignments = [];

        foreach ($items as $stockCode => $requestedQty) {
            $remaining = $requestedQty;
            $assignments[$stockCode] = [];

            // Get all locations with stock for this item
            $stockHoldings = StockItemHoldings::where('StockCode', $stockCode)
                ->where('QuantityOnHand', '>', 0)
                ->whereHas('location', function($q) {
                    $q->where('IsActive', true);
                })
                ->orderByDesc('QuantityOnHand')
                ->get();

            foreach ($stockHoldings as $holding) {
                if ($remaining <= 0) break;

                $allocate = min($remaining, $holding->QuantityOnHand);
                $assignments[$stockCode][$holding->LocationCode] = $allocate;
                $remaining -= $allocate;
            }

            // If still can't fulfill, assign remaining to default location (for backorders)
            if ($remaining > 0) {
                $defaultLocation = Location::where('IsDefault', true)->value('LocationCode') ?? '0000';

                if (isset($assignments[$stockCode][$defaultLocation])) {
                    $assignments[$stockCode][$defaultLocation] += $remaining;
                } else {
                    $assignments[$stockCode][$defaultLocation] = $remaining;
                }
            }
        }

        return $assignments;
    }
}
