<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\Product;
use App\Models\Customer;
use App\Models\PromotionUsage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PromotionCalculationService
{
    /**
     * Calculate the best available promotion for a product and customer
     */
    public function calculateBestPromotion(
        string $stockCode,
        int $quantity,
        ?Customer $customer = null,
        ?int $customerTier = null
    ): array
    {
        $customerTier = $customerTier ?? ($customer->price_level ?? 1);
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return $this->noPromotionResult($stockCode, $quantity);
        }

        $originalPrice = $product->{"SalePrice{$customerTier}"};

        $promotions = $this->getApplicablePromotions($stockCode, $customerTier, $customer);

        if ($promotions->isEmpty()) {
            return $this->noPromotionResult($stockCode, $quantity, $originalPrice);
        }

        $bestPromotion = null;
        $bestSavings = 0;
        $bestCalculation = null;

        foreach ($promotions as $promotion) {
            $calculation = $promotion->calculateDiscount($quantity, $originalPrice, $customerTier);

            if ($calculation['applicable'] && $calculation['total_savings'] > $bestSavings) {
                $bestSavings = $calculation['total_savings'];
                $bestPromotion = $promotion;
                $bestCalculation = $calculation;
            }
        }

        if (!$bestPromotion) {
            return $this->noPromotionResult($stockCode, $quantity, $originalPrice);
        }

        return [
            'has_promotion' => true,
            'promotion' => $bestPromotion,
            'calculation' => $bestCalculation,
            'original_price_per_item' => $originalPrice,
            'discounted_price_per_item' => $bestCalculation['discounted_price'],
            'savings_per_item' => $bestCalculation['savings_per_item'],
            'total_original_price' => $originalPrice * $quantity,
            'total_discounted_price' => $bestCalculation['discounted_price'] * $quantity,
            'total_savings' => $bestCalculation['total_savings'],
            'bonus_quantity' => $bestCalculation['bonus_quantity'],
            'savings_percentage' => $originalPrice > 0 ? round(($bestCalculation['total_savings'] / ($originalPrice * $quantity)) * 100, 2) : 0,
            'message' => $bestCalculation['message']
        ];
    }

    /**
     * Calculate promotions for multiple cart items
     */
    public function calculateCartPromotions(array $cartItems, ?Customer $customer = null): array
    {
        $customerTier = $customer->price_level ?? 1;
        $results = [];
        $totalSavings = 0;
        $totalOriginalPrice = 0;
        $totalDiscountedPrice = 0;
        $totalBonusQuantity = 0;

        foreach ($cartItems as $item) {
            $stockCode = $item['stock_code'];
            $quantity = $item['quantity'];

            $promotionResult = $this->calculateBestPromotion($stockCode, $quantity, $customer, $customerTier);

            $results[$stockCode] = $promotionResult;
            $totalSavings += $promotionResult['total_savings'] ?? 0;
            $totalOriginalPrice += $promotionResult['total_original_price'] ?? 0;
            $totalDiscountedPrice += $promotionResult['total_discounted_price'] ?? 0;
            $totalBonusQuantity += $promotionResult['bonus_quantity'] ?? 0;
        }

        return [
            'items' => $results,
            'summary' => [
                'total_original_price' => $totalOriginalPrice,
                'total_discounted_price' => $totalDiscountedPrice,
                'total_savings' => $totalSavings,
                'total_bonus_quantity' => $totalBonusQuantity,
                'overall_savings_percentage' => $totalOriginalPrice > 0 ? round(($totalSavings / $totalOriginalPrice) * 100, 2) : 0,
                'items_with_promotions' => collect($results)->where('has_promotion', true)->count(),
                'total_items' => count($results)
            ]
        ];
    }

    /**
     * Record promotion usage after successful order
     */
    public function recordPromotionUsage(array $cartItems, ?Customer $customer = null, ?int $orderId = null): Collection
    {
        $usageRecords = collect();

        if (!$customer) {
            return $usageRecords;
        }

        foreach ($cartItems as $item) {
            $promotionResult = $this->calculateBestPromotion(
                $item['stock_code'],
                $item['quantity'],
                $customer
            );

            if ($promotionResult['has_promotion']) {
                $promotion = $promotionResult['promotion'];
                $calculation = $promotionResult['calculation'];

                $discountDetails = [
                    'original_price' => $promotionResult['original_price_per_item'],
                    'discounted_price' => $promotionResult['discounted_price_per_item'],
                    'total_savings' => $promotionResult['total_savings'],
                    'bonus_quantity' => $promotionResult['bonus_quantity'],
                    'customer_tier' => $customer->price_level ?? 1
                ];

                $usage = $promotion->recordUsage(
                    $customer->id,
                    $item['quantity'],
                    $discountDetails,
                    $orderId
                );

                $usageRecords->push($usage);

                Log::info('Promotion usage recorded', [
                    'promotion_id' => $promotion->id,
                    'customer_id' => $customer->id,
                    'order_id' => $orderId,
                    'stock_code' => $item['stock_code'],
                    'savings' => $promotionResult['total_savings']
                ]);
            }
        }

        return $usageRecords;
    }

    /**
     * Get applicable promotions for a product and customer
     */
    private function getApplicablePromotions(string $stockCode, int $customerTier, ?Customer $customer = null): Collection
    {
        $query = Promotion::active()
            ->forProduct($stockCode)
            ->forCustomerTier($customerTier);

        $promotions = $query->get();

        // Filter out promotions that have reached usage limits
        return $promotions->filter(function ($promotion) use ($customer) {
            if ($promotion->hasReachedUsageLimit()) {
                return false;
            }

            if ($customer && !$promotion->canBeUsedByCustomer($customer->id)) {
                return false;
            }

            return true;
        });
    }

    /**
     * Return result when no promotion is applicable
     */
    private function noPromotionResult(string $stockCode, int $quantity, ?int $originalPrice = null): array
    {
        return [
            'has_promotion' => false,
            'promotion' => null,
            'calculation' => null,
            'original_price_per_item' => $originalPrice ?? 0,
            'discounted_price_per_item' => $originalPrice ?? 0,
            'savings_per_item' => 0,
            'total_original_price' => ($originalPrice ?? 0) * $quantity,
            'total_discounted_price' => ($originalPrice ?? 0) * $quantity,
            'total_savings' => 0,
            'bonus_quantity' => 0,
            'savings_percentage' => 0,
            'message' => 'No promotions available'
        ];
    }

    /**
     * Get featured products with active promotions
     */
    public function getFeaturedPromotedProducts(int $limit = 20): Collection
    {
        return Product::with(['promotions' => function ($query) {
            $query->active();
        }])
            ->where('is_featured', 1)
            ->whereHas('promotions', function ($query) {
                $query->active();
            })
            ->take($limit)
            ->get()
            ->map(function ($product) {
                $activePromotions = $product->promotions->where('status', 'active');
                $bestPromotion = $activePromotions->sortByDesc(function ($promotion) use ($product) {
                    $calculation = $promotion->calculateDiscount(1, $product->SalePrice1, 1);
                    return $calculation['savings_per_item'] ?? 0;
                })->first();

                $product->best_promotion = $bestPromotion;
                return $product;
            });
    }

    /**
     * Get promotion summary for a specific product
     */
    public function getProductPromotionSummary(string $stockCode, int $customerTier = 1): array
    {
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return ['error' => 'Product not found'];
        }

        $promotions = $this->getApplicablePromotions($stockCode, $customerTier);
        $originalPrice = $product->{"SalePrice{$customerTier}"};

        $promotionSummaries = $promotions->map(function ($promotion) use ($originalPrice) {
            $calculation = $promotion->calculateDiscount(1, $originalPrice, 1);

            return [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'type' => $promotion->type,
                'message' => $calculation['message'],
                'savings_per_item' => $calculation['savings_per_item'] ?? 0,
                'min_quantity' => $promotion->min_quantity,
                'ends_at' => $promotion->ends_at->format('Y-m-d H:i:s'),
                'is_applicable' => $calculation['applicable'] ?? false
            ];
        });

        return [
            'product' => [
                'stock_code' => $stockCode,
                'name' => $product->ProductName,
                'original_price' => $originalPrice,
                'original_price_formatted' => number_format($originalPrice / 100, 2)
            ],
            'promotions' => $promotionSummaries->values()->all(),
            'has_active_promotions' => $promotionSummaries->where('is_applicable', true)->isNotEmpty()
        ];
    }

    /**
     * Validate promotion rules and constraints
     */
    public function validatePromotionUsage(
        Promotion $promotion,
        int $quantity,
        ?Customer $customer = null
    ): array {
        $errors = [];

        // Check if promotion is active
        if (!$promotion->isActive()) {
            $errors[] = 'Promotion is not currently active';
        }

        // Check minimum quantity
        if ($promotion->min_quantity && $quantity < $promotion->min_quantity) {
            $errors[] = "Minimum quantity of {$promotion->min_quantity} required";
        }

        // Check customer usage limits
        if ($customer && !$promotion->canBeUsedByCustomer($customer->id)) {
            $errors[] = 'Customer has reached usage limit for this promotion';
        }

        // Check total usage limits
        if ($promotion->hasReachedUsageLimit()) {
            $errors[] = 'Promotion has reached its usage limit';
        }

        // Check customer tier eligibility
        if ($customer && $promotion->customer_tiers) {
            $customerTier = $customer->price_level ?? 1;
            if (!in_array($customerTier, $promotion->customer_tiers)) {
                $errors[] = 'Customer tier not eligible for this promotion';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Get promotion analytics data
     */
    public function getPromotionAnalytics(Promotion $promotion): array
    {
        $usage = PromotionUsage::where('promotion_id', $promotion->id);

        $totalUsage = $usage->count();
        $totalSavings = $usage->sum('total_savings_cents');
        $uniqueCustomers = $usage->distinct('customer_id')->count();
        $averageOrderValue = $usage->avg('original_price_cents');

        // Usage over time (last 30 days)
        $dailyUsage = $usage->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_savings_cents) as savings')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top customers by savings
        $topCustomers = $usage->selectRaw('customer_id, SUM(total_savings_cents) as total_savings, COUNT(*) as usage_count')
            ->with('customer:id,name,email')
            ->groupBy('customer_id')
            ->orderBy('total_savings', 'desc')
            ->take(10)
            ->get();

        return [
            'summary' => [
                'total_usage' => $totalUsage,
                'total_savings' => $totalSavings,
                'total_savings_formatted' => ' . number_format($totalSavings / 100, 2)',
                'unique_customers' => $uniqueCustomers,
                'average_order_value' => $averageOrderValue,
                'average_order_value_formatted' => ' . number_format($averageOrderValue / 100, 2)',
                'usage_limit_percentage' => $promotion->usage_limit_total
                    ? round(($totalUsage / $promotion->usage_limit_total) * 100, 2)
                    : null
            ],
            'daily_usage' => $dailyUsage,
            'top_customers' => $topCustomers,
            'performance_metrics' => [
                'conversion_rate' => $uniqueCustomers > 0 ? round(($totalUsage / $uniqueCustomers), 2) : 0,
                'average_savings_per_use' => $totalUsage > 0 ? round($totalSavings / $totalUsage / 100, 2) : 0,
                'days_active' => $promotion->starts_at->diffInDays(min($promotion->ends_at, now())),
                'average_daily_usage' => $promotion->starts_at->diffInDays(now()) > 0
                    ? round($totalUsage / $promotion->starts_at->diffInDays(now()), 2)
                    : $totalUsage
            ]
        ];
    }

    /**
     * Suggest optimal promotion parameters based on product data
     */
    public function suggestPromotionParameters(string $stockCode): array
    {
        $product = Product::where('StockCode', $stockCode)->first();

        if (!$product) {
            return ['error' => 'Product not found'];
        }

        // Get historical sales data (if available)
        // This would integrate with your existing order/sales system

        $suggestions = [
            'date_range' => [
                'recommended_discount_percentage' => [10, 15, 20],
                'optimal_duration_days' => 14,
                'description' => 'Simple percentage discount for broad appeal'
            ],
            'quantity_break' => [
                'recommended_min_quantities' => [3, 5, 10],
                'recommended_discount_percentages' => [5, 10, 15],
                'description' => 'Encourage bulk purchases'
            ],
            'bogo' => [
                'buy_quantities' => [1, 2],
                'get_quantities' => [1],
                'description' => 'Effective for moving inventory quickly'
            ],
            'price_break' => [
                'break_points' => [
                    ['qty' => 1, 'discount' => 0],
                    ['qty' => 5, 'discount' => 5],
                    ['qty' => 10, 'discount' => 10],
                    ['qty' => 25, 'discount' => 15]
                ],
                'description' => 'Tiered pricing for different volume buyers'
            ]
        ];

        // Add product-specific context
        $basePrice = $product->SalePrice1;
        $suggestions['context'] = [
            'product_name' => $product->ProductName,
            'base_price' => $basePrice,
            'base_price_formatted' => ' . number_format($basePrice / 100, 2)',
            'recommended_promotion_types' => ['date_range', 'quantity_break'], // Based on product type
        ];

        return $suggestions;
    }

    /**
     * Check for conflicting promotions
     */
    public function checkPromotionConflicts(array $promotionData, ?int $excludePromotionId = null): array
    {
        $conflicts = [];

        $query = Promotion::where('stock_code', $promotionData['stock_code'])
            ->where('status', 'active')
            ->where(function ($q) use ($promotionData) {
                $q->whereBetween('starts_at', [$promotionData['starts_at'], $promotionData['ends_at']])
                    ->orWhereBetween('ends_at', [$promotionData['starts_at'], $promotionData['ends_at']])
                    ->orWhere(function ($q2) use ($promotionData) {
                        $q2->where('starts_at', '<=', $promotionData['starts_at'])
                            ->where('ends_at', '>=', $promotionData['ends_at']);
                    });
            });

        if ($excludePromotionId) {
            $query->where('id', '!=', $excludePromotionId);
        }

        $conflictingPromotions = $query->get();

        foreach ($conflictingPromotions as $conflicting) {
            $conflicts[] = [
                'promotion_id' => $conflicting->id,
                'promotion_name' => $conflicting->name,
                'conflict_type' => 'date_overlap',
                'starts_at' => $conflicting->starts_at->format('Y-m-d H:i:s'),
                'ends_at' => $conflicting->ends_at->format('Y-m-d H:i:s'),
                'message' => "Overlaps with existing promotion: {$conflicting->name}"
            ];
        }

        return [
            'has_conflicts' => !empty($conflicts),
            'conflicts' => $conflicts,
            'recommendation' => !empty($conflicts)
                ? 'Consider adjusting dates or deactivating conflicting promotions'
                : 'No conflicts detected'
        ];
    }

    /**
     * Get promotion recommendations for a customer
     */
    public function getCustomerPromotionRecommendations(Customer $customer, int $limit = 10): Collection
    {
        $customerTier = $customer->price_level ?? 1;

        // Get customer's purchase history to recommend relevant promotions
        // This would integrate with your order history system

        return Promotion::active()
            ->forCustomerTier($customerTier)
            ->where('is_online_only', true) // Prioritize online promotions
            ->whereDoesntHave('usage', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            })
            ->with('product')
            ->take($limit)
            ->get()
            ->map(function ($promotion) use ($customerTier) {
                $product = $promotion->product;
                if ($product) {
                    $originalPrice = $product->{"SalePrice{$customerTier}"};
                    $calculation = $promotion->calculateDiscount(1, $originalPrice, $customerTier);
                    $promotion->potential_savings = $calculation['savings_per_item'] ?? 0;
                    $promotion->promotion_message = $calculation['message'] ?? '';
                }
                return $promotion;
            })
            ->sortByDesc('potential_savings');
    }

}
