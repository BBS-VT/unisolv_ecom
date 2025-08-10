<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Services\PromotionCalculationService;
use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PromotionController extends Controller
{
    protected PromotionCalculationService $promotionService;

    public function __construct(PromotionCalculationService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Display featured promoted products
     */
    public function featured(): View
    {
        $featuredProducts = $this->promotionService->getFeaturedPromotedProducts(24);

        return view('shop.promotions.featured', compact('featuredProducts'));
    }

    /**
     * Get promotion details for a specific product
     */
    public function getProductPromotions(Request $request, string $stockCode): JsonResponse
    {
        $customerTier = auth()->user() ? (auth()->user()->price_level ?? 1) : 1;
        $quantity = $request->get('quantity', 1);

        $promotionResult = $this->promotionService->calculateBestPromotion(
            $stockCode,
            $quantity,
            auth()->user(),
            $customerTier
        );

        return response()->json($promotionResult);
    }

    /**
     * Calculate promotions for cart items
     */
    public function calculateCartPromotions(Request $request): JsonResponse
    {
        $cartItems = $request->validate([
            'items' => 'required|array',
            'items.*.stock_code' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1'
        ])['items'];

        $result = $this->promotionService->calculateCartPromotions(
            $cartItems,
            auth()->user()
        );

        return response()->json($result);
    }

    /**
     * Apply promotion to cart item (AJAX endpoint)
     */
    public function applyToCartItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'stock_code' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'promotion_id' => 'nullable|exists:promotions,id'
        ]);

        $customer = auth()->user();
        $customerTier = $customer ? ($customer->price_level ?? 1) : 1;

        if ($validated['promotion_id']) {
            // Specific promotion requested
            $promotion = Promotion::findOrFail($validated['promotion_id']);
            $validation = $this->promotionService->validatePromotionUsage(
                $promotion,
                $validated['quantity'],
                $customer
            );

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 400);
            }

            $product = Product::where('StockCode', $validated['stock_code'])->first();
            $originalPrice = $product->{"SalePrice{$customerTier}"};
            $calculation = $promotion->calculateDiscount(
                $validated['quantity'],
                $originalPrice,
                $customerTier
            );

            return response()->json([
                'success' => true,
                'promotion' => $promotion,
                'calculation' => $calculation
            ]);
        }

        // Find best promotion
        $result = $this->promotionService->calculateBestPromotion(
            $validated['stock_code'],
            $validated['quantity'],
            $customer,
            $customerTier
        );

        return response()->json([
            'success' => true,
            'result' => $result
        ]);
    }
}
