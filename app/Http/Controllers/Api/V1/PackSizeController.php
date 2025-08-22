<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\PackSizeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackSizeController extends Controller
{
    private PackSizeService $packSizeService;

    public function __construct(PackSizeService $packSizeService)
    {
        $this->packSizeService = $packSizeService;
    }

    /**
     * Get all pack size variations for a product
     *
     */
    public function getPackSizeVariations(string $stockCode): JsonResponse
    {
        $variations = $this->packSizeService->getPackSizeVariations($stockCode);

        return response()->json([
            'stock_code' => $stockCode,
            'variations' => $variations,
        ]);
    }

    /**
     * Check stock availability for a specific quantity
     */
    public function checkStockAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'stock_code' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $availability = $this->packSizeService->checkStockAvailability(
            $request->stock_code,
            $request->quantity,
        );

        return response()->json($availability);
    }

    /**
     * Get recommended pack sizes for a quantity
     */
    public function getRecommendedPackSizes(Request $request): JsonResponse
    {
        $request->validate([
            'stock_code' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $recommendations = $this->packSizeService->getRecommendedPackSizes(
            $request->stock_code,
            $request->quantity
        );

        return response()->json($recommendations);
    }

    /**
     * Calculate stock allocation for an order
     */
    public function calculateStockAllocation(Request $request): JsonResponse
    {
        $request->validate([
            'stock_code' => 'required|string',
            'quantity' => 'required|integer|min:1'
        ]);

        $allocation = $this->packSizeService->calculateStockAllocation(
            $request->stock_code,
            $request->quantity
        );

        return response()->json($allocation);
    }

    /**
     * Get all product families
     */
    public function getProductFamilies(): JsonResponse
    {
        $families = $this->packSizeService->getProductFamilies();

        return response()->json([
            'families' => $families,
            'total_families' => $families->count()
        ]);
    }

    /**
     * Link pack sizes (admin function)
     */
    public function linkPackSizes(Request $request): JsonResponse
    {
        $request->validate([
            'links' => 'required|array',
            'links.*.stock_code' => 'required|string|exists:products,stock_code',
            'links.*.refer_code' => 'nullable|string|exists:products,stock_code',
            'links.*.pack_size' => 'required|integer|min:1'
        ]);

        try {
            $this->packSizeService->linkPackSizes($request->links);

            return response()->json([
                'success' => true,
                'message' => 'Pack sizes linked successfully',
                'linked_count' => count($request->links)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to link pack sizes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pack size hierarchy for a product
     */
    public function getPackSizeHierarchy(string $stockCode): JsonResponse
    {
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        $root = $product->getRootProduct();
        $family = $product->packSizeFamily()->with('stockHolding')->get();

        $hierarchy = [];
        $current = $root;

        while ($current) {
            $hierarchy[] = [
                'stock_code' => $current->StockCode,
                'name' => $current->StockItemName,
                'pack_size' => $current->Packsize,
                'refer_code' => $current->refer_code,
                'quantity' => $current->stockHolding?->QuantityOnHand ?? 0,
                'price' => $current->effective_price,
                'is_root' => !$current->refer_code,
            ];

            // Find the next smaller pack size that refers to current
            $current = $family->where('refer_code', $current->StockCode)->first();
        }

        return response()->json([
            'stock_code' => $stockCode,
            'root_product' => $root->StockCode,
            'hierarchy' => $hierarchy,
            'total_base_units' => $product->getTotalBaseUnitsAttribute()
        ]);
    }



}
