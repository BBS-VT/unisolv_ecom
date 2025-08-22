<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockItemHoldings;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class PackSizeService
{

    /**
     * Link products together using refer codes
     */
    public function linkPackSizes(array $packSizeLinks): void
    {
        foreach ($packSizeLinks as $link) {
            $this->createPackSizeLink(
                $link['StockCode'],
                $link['refer_code'],
                $link['Packsize']
            );
        }

    }

    /**
     * Create a single pack size link
     */
    public function createPackSizeLink(string $stockCode, ?string $referCode, int $packSize): void
    {
        Product::where('StockCode', $stockCode)->update([
            'refer_code' => $referCode,
            'Packsize' => $packSize
        ]);
    }

    public function getPackSizeVariations(string $stockCode): Collection
    {
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return collect();
        }

        return $product->packSizeFamily()
            ->with('stockHolding')
            ->get()
            ->map(function ($product) {
                return [
                    'StockCode' => $product->StockCode,
                    'name'      => $product->StockItemName,
                    'pack_size' => $product->packSize,
                    'available_quantity' => $product->stockHolding->QuantityOnHand ?? 0,
                    'selling_price' => $product->SellingPrice,
                    'cost_price'    => $product->stockHolding->LastCostPrice ?? 0,
                ];
            });
    }

    /**
     * Calculate stock allocation for an order
     */
    public function calculateStockAllocation(string $stockCode, int $requestedQuantity)
    {
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        $requiredBaseUnits = $requestedQuantity * $product->Packsize;
        $allocation = [];
        $remainingUnits = $requiredBaseUnits;

        // Get all pack sizes in descending order (largest first)
        $packSizes = $product->packSizeFamily()
            ->with('stockHolding')
            ->orderBy('Packsize', 'desc')
            ->get();

        foreach ($packSizes as $packSize) {
            if ($remainingUnits <= 0) break;

            $availableQuantity = $packSize->stockHolding?->quantity ?? 0;

            if ($availableQuantity > 0) {
                $unitsPerPack = $packSize->pack_size;
                $packsNeeded = min(
                    intval($remainingUnits / $unitsPerPack),
                    $availableQuantity
                );

                if ($packsNeeded > 0) {
                    $allocation[] = [
                        'StockCode' => $packSize->stock_code,
                        'Packsize' => $unitsPerPack,
                        'quantity' => $packsNeeded,
                        'units_covered' => $packsNeeded * $unitsPerPack,
                    ];

                    $remainingUnits -= ($packsNeeded * $unitsPerPack);
                }
            }
        }

        return [
            'success' => $remainingUnits <= 0,
            'requested_units' => $requiredBaseUnits,
            'allocated_units' => $requiredBaseUnits - $remainingUnits,
            'remaining_units' => $remainingUnits,
            'allocation' => $allocation,
        ];
    }

    /**
     * Check if sufficient stock is available across pack sizes
     */
    public function checkStockAvailability(string $stockCode, int $requestedQuantity): array
    {
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return [
                'available' => false,
                'message' => 'Product not found'
            ];
        }

        $requiredBaseUnits = $requestedQuantity * $product->pack_size;
        $totalAvailableUnits = $product->getTotalBaseUnitsAttribute();

        return [
            'available' => $totalAvailableUnits >= $requiredBaseUnits,
            'requested_units' => $requiredBaseUnits,
            'available_units' => $totalAvailableUnits,
            'shortage' => max(0, $requiredBaseUnits - $totalAvailableUnits),
        ];
    }

    /**
     * Get product families grouped by root product
     */
    public function getProductFamilies(): Collection
    {
        return Product::rootProducts()
            ->with(['referringProducts.stockHolding', 'stockHolding'])
            ->get()
            ->map(function ($rootProduct) {
                $family = $rootProduct->packSizeFamily()->with('stockHolding')->get();

                return [
                    'root_product' => $rootProduct->StockCode,
                    'family_name' => $rootProduct->StockItemName,
                    'pack_sizes' => $family->map(function ($product) {
                        return [
                            'stock_code' => $product->StockCode,
                            'pack_size' => $product->Packsize,
                            'quantity' => $product->stockHolding?->QuantityOnHand ?? 0,
                            'price' => $product->SellingPrice,
                        ];
                    })->sortByDesc('Packsize')->values(),
                    'total_base_units' => $family->sum(function ($product) {
                        return ($product->stockHolding?->QuantityOnHand ?? 0) * $product->Packsize;
                    }),
                ];
            });
    }

    /**
     * Update stock after an order
     */
    public function updateStockAfterOrder(array $allocation): void
    {
        foreach ($allocation as $item) {
            $stockHolding = StockHolding::where('StockCode', $item['StockCode'])->first();

            if ($stockHolding) {
                $stockHolding->QuantityOnHand -= $item['quantity'];
                $stockHolding->save();
            }
        }
    }

    /**
     * Get recommended pack sizes for a quantity
     */
    public function getRecommendedPackSizes(string $stockCode, int $requestedQuantity): array
    {
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return [];
        }

        $requiredBaseUnits = $requestedQuantity * $product->Packsize;
        $packSizes = $product->packSizeFamily()
            ->with('stockHolding')
            ->orderBy('Packsize', 'desc')
            ->get();

        $recommendations = [];
        $remainingUnits = $requiredBaseUnits;

        foreach ($packSizes as $packSize) {
            if ($remainingUnits <= 0) break;

            $packsNeeded = intval($remainingUnits / $packSize->pack_size);
            $available = $packSize->stockHolding?->quantity ?? 0;

            if ($packsNeeded > 0) {
                $recommendations[] = [
                    'stock_code' => $packSize->stock_code,
                    'pack_size' => $packSize->pack_size,
                    'needed' => $packsNeeded,
                    'available' => $available,
                    'can_supply' => min($packsNeeded, $available),
                    'price_per_pack' => $packSize->effective_price,
                    'total_price' => $packSize->effective_price * min($packsNeeded, $available),
                ];

                $remainingUnits -= (min($packsNeeded, $available) * $packSize->pack_size);
            }
        }

        return [
            'recommendations' => $recommendations,
            'fully_satisfied' => $remainingUnits <= 0,
            'remaining_units' => $remainingUnits,
        ];
    }

}
