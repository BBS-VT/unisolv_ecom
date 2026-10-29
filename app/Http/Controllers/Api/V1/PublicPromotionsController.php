<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Features;
use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Public, unauthenticated promotions feed.
 *
 * Intended for the marketing website (currently WordPress, soon a Next.js
 * site) to pull "current promotions" for display, with a link back to the
 * matching product on Vantage. Deliberately NOT behind the `auth:api`
 * (Passport) guard used by the on-prem sync endpoints - this is public
 * marketing content, not a POS integration, and the website has no OAuth
 * client credentials of its own.
 *
 * Only safe-to-publish fields are returned: no cost price, supplier info,
 * stock levels, or customer-tier-specific pricing beyond the single
 * "sale price" shown on the promo.
 */
class PublicPromotionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $onlineOnly = $request->boolean('online_only');

        $cacheKey = 'public_promotions_feed:' . ($onlineOnly ? 'online' : 'all');

        $promotions = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($onlineOnly) {
            return Promotion::with('product')
                ->active()
                ->when($onlineOnly, fn ($query) => $query->onlineOnly())
                // Same rule the storefront itself uses (Shop\ProductController)
                // - when backorders are off, don't advertise a promotion for
                // a product that's actually out of stock everywhere.
                ->when(!Features::backordersEnabled(), function ($query) {
                    $query->whereHas('product.stockHolding', function ($q) {
                        $q->selectRaw('StockCode, SUM(QuantityOnHand) as total_quantity')
                            ->groupBy('StockCode')
                            ->havingRaw('total_quantity > 0');
                    });
                })
                ->orderBy('ends_at')
                ->get()
                // A promotion can outlive its product (deleted/discontinued
                // stock code) - skip anything we can't show a product for.
                ->filter(fn (Promotion $promotion) => $promotion->product !== null)
                ->map(fn (Promotion $promotion) => $this->transform($promotion))
                ->values();
        });

        return response()->json([
            'data' => $promotions,
            'generated_at' => now()->toIso8601String(),
        ]);
    }

    protected function transform(Promotion $promotion): array
    {
        $product = $promotion->product;
        $photo = $product->photo; // Spatie Media Library - already gives public URLs

        return [
            'id' => $promotion->id,
            'name' => $promotion->name,
            'description' => $promotion->description,
            'type' => $promotion->type,
            'starts_at' => $promotion->starts_at?->toIso8601String(),
            'ends_at' => $promotion->ends_at?->toIso8601String(),
            'product' => [
                'stock_code' => $product->StockCode,
                'name' => $product->StockItemName,
                'slug' => $product->slug,
                'price' => $product->SellingPrice,
                'sale_price' => $promotion->sale_price_1 ? $promotion->sale_price_1 / 100 : null,
                'image' => $photo?->url,
                'image_thumb' => $photo?->thumbnail,
            ],
            // Where the "click through to Vantage" link should point.
            'url' => route('products.show', $product),
        ];
    }
}
