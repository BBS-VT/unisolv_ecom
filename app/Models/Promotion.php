<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'starts_at',
        'ends_at',
        'location_code',
        'location_name',
        'is_online_only',
        'is_imported',
        'stock_code',
        'customer_tiers',
        'sale_price_1',
        'sale_price_2',
        'sale_price_3',
        'sale_price_4',
        'quantity_type',
        'min_quantity',
        'discount_percentage',
        'discount_amount',
        'price_breaks',
        'buy_quantity',
        'get_quantity',
        'bonus_breaks',
        'quantity_limit_per_customer',
        'usage_limit_total',
        'usage_count',
        'import_batch_id',
        'last_imported_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'last_imported_at' => 'datetime',
        'is_online_only' => 'boolean',
        'is_imported' => 'boolean',
        'customer_tiers' => 'array',
        'price_breaks' => 'array',
        'bonus_breaks' => 'array',
        'discount_percentage' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'stock_code', 'StockCode');
    }

    public function usage(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeForCustomerTier($query, $priceLevel)
    {
        return $query->where(function ($q) use ($priceLevel) {
            $q->whereJsonContains('customer_tiers', $priceLevel)
                ->orWhereNull('customer_tiers');
        });
    }

    public function scopeForProduct($query, $stockCode)
    {
        return $query->where('stock_code', $stockCode);
    }

    public function scopeOnlineOnly($query)
    {
        return $query->where('is_online_only', true);
    }

    public function scopeImported($query)
    {
        return $query->where('is_imported', true);
    }

    /**
     * Accessors & Mutators
     */
    public function getSalePriceAttribute(): array
    {
        return [
            1 => $this->sale_price_1,
            2 => $this->sale_price_2,
            3 => $this->sale_price_3,
            4 => $this->sale_price_4,
        ];
    }

    public function getPriceForTier($tier): ?int
    {
        return $this->{"sale_price_{$tier}"};
    }

    /**
     * Business Logic Methods
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->starts_at <= now()
            && $this->ends_at >= now();
    }

    public function isExpired(): bool
    {
        return $this->ends_at < now();
    }

    public function isScheduled(): bool
    {
        return $this->starts_at > now();
    }

    public function appliesTo($stockCode, $priceLevel = null): bool
    {
        if ($this->stock_code !== $stockCode) {
            return false;
        }

        if (!$this->isActive()) {
            return false;
        }

        if ($priceLevel && $this->customer_tiers) {
            return in_array($priceLevel, $this->customer_tiers);
        }

        return true;
    }

    public function calculateDiscount($quantity, $originalPricePerItem, $customerTier = 1): array
    {
        if (!$this->isActive()) {
            return [
                'applicable' => false,
                'discounted_price' => $originalPricePerItem,
                'savings_per_item' => 0,
                'total_savings' => 0,
                'bonus_quantity' => 0,
                'message' => 'Promotion not active'
            ];
        }

        return match($this->type) {
            'date_range' => $this->calculateDateRangeDiscount($quantity, $originalPricePerItem, $customerTier),
            'bogo' => $this->calculateBogoDiscount($quantity, $originalPricePerItem),
            'quantity_break' => $this->calculateQuantityBreakDiscount($quantity, $originalPricePerItem),
            'bonus_quantity' => $this->calculateBonusQuantityDiscount($quantity, $originalPricePerItem),
            'price_break' => $this->calculatePriceBreakDiscount($quantity, $originalPricePerItem, $customerTier),
            default => [
                'applicable' => false,
                'discounted_price' => $originalPricePerItem,
                'savings_per_item' => 0,
                'total_savings' => 0,
                'bonus_quantity' => 0,
                'message' => 'Unknown promotion type'
            ]
        };
    }

    private function calculateDateRangeDiscount($quantity, $originalPrice, $customerTier): array
    {
        $promotionPrice = $this->getPriceForTier($customerTier);

        if (!$promotionPrice || $promotionPrice >= $originalPrice) {
            return [
                'applicable' => false,
                'discounted_price' => $originalPrice,
                'savings_per_item' => 0,
                'total_savings' => 0,
                'bonus_quantity' => 0,
                'message' => 'No discount available'
            ];
        }

        $savingsPerItem = $originalPrice - $promotionPrice;

        return [
            'applicable' => true,
            'discounted_price' => $promotionPrice,
            'savings_per_item' => $savingsPerItem,
            'total_savings' => $savingsPerItem * $quantity,
            'bonus_quantity' => 0,
            'message' => "Save " . number_format($savingsPerItem / 100, 2) . " per item"
        ];
    }

    private function calculateBogoDiscount($quantity, $originalPrice): array
    {
        if ($quantity < ($this->buy_quantity ?: 2)) {
            return [
                'applicable' => false,
                'discounted_price' => $originalPrice,
                'savings_per_item' => 0,
                'total_savings' => 0,
                'bonus_quantity' => 0,
                'message' => "Buy {$this->buy_quantity} to get " . ($this->get_quantity ?: 1) . " free"
            ];
        }

        $buyQty = $this->buy_quantity ?: 2;
        $getQty = $this->get_quantity ?: 1;
        $sets = intval($quantity / $buyQty);
        $bonusItems = $sets * $getQty;
        $totalSavings = $bonusItems * $originalPrice;

        return [
            'applicable' => true,
            'discounted_price' => $originalPrice,
            'savings_per_item' => 0,
            'total_savings' => $totalSavings,
            'bonus_quantity' => $bonusItems,
            'message' => "Buy {$buyQty}, Get {$getQty} Free!"
        ];
    }

    private function calculateQuantityBreakDiscount($quantity, $originalPrice): array
    {
        if ($quantity < $this->min_quantity) {
            return [
                'applicable' => false,
                'discounted_price' => $originalPrice,
                'savings_per_item' => 0,
                'total_savings' => 0,
                'bonus_quantity' => 0,
                'message' => "Buy {$this->min_quantity}+ for discount"
            ];
        }

        $discountAmount = 0;
        if ($this->discount_percentage) {
            $discountAmount = intval($originalPrice * ($this->discount_percentage / 100));
        } elseif ($this->discount_amount) {
            $discountAmount = $this->discount_amount;
        }

        $discountedPrice = max(0, $originalPrice - $discountAmount);
        $totalSavings = $discountAmount * $quantity;

        return [
            'applicable' => true,
            'discounted_price' => $discountedPrice,
            'savings_per_item' => $discountAmount,
            'total_savings' => $totalSavings,
            'bonus_quantity' => 0,
            'message' => $this->discount_percentage
                ? "{$this->discount_percentage}% off for {$this->min_quantity}+"
                : "Save " . number_format($discountAmount / 100, 2) . " each for {$this->min_quantity}+"
        ];
    }

    private function calculateBonusQuantityDiscount($quantity, $originalPrice): array
    {
        $bonusQuantity = 0;
        $totalSavings = 0;

        if ($this->bonus_breaks) {
            foreach ($this->bonus_breaks as $break) {
                if ($quantity >= $break['break_qty']) {
                    $sets = intval($quantity / $break['break_qty']);
                    $bonusQuantity += $sets * $break['bonus_qty'];
                    $totalSavings += $bonusQuantity * $originalPrice;
                    break;
                }
            }
        }

        return [
            'applicable' => $bonusQuantity > 0,
            'discounted_price' => $originalPrice,
            'savings_per_item' => 0,
            'total_savings' => $totalSavings,
            'bonus_quantity' => $bonusQuantity,
            'message' => $bonusQuantity > 0
                ? "Get {$bonusQuantity} bonus items free!"
                : "No bonus quantity available"
        ];
    }

    private function calculatePriceBreakDiscount($quantity, $originalPrice, $customerTier): array
    {
        if (!$this->price_breaks) {
            return [
                'applicable' => false,
                'discounted_price' => $originalPrice,
                'savings_per_item' => 0,
                'total_savings' => 0,
                'bonus_quantity' => 0,
                'message' => 'No price breaks configured'
            ];
        }

        $bestPrice = $originalPrice;
        foreach ($this->price_breaks as $break) {
            if ($quantity >= $break['qty'] && $break['price'] < $bestPrice) {
                $bestPrice = $break['price'];
            }
        }

        $savingsPerItem = $originalPrice - $bestPrice;
        $totalSavings = $savingsPerItem * $quantity;

        return [
            'applicable' => $savingsPerItem > 0,
            'discounted_price' => $bestPrice,
            'savings_per_item' => $savingsPerItem,
            'total_savings' => $totalSavings,
            'bonus_quantity' => 0,
            'message' => $savingsPerItem > 0
                ? "Volume discount: " . number_format($savingsPerItem / 100, 2) . " off per item"
                : "No volume discount available"
        ];
    }

    public function recordUsage($customerId, $quantity, $discountDetails, $orderId = null): PromotionUsage
    {
        $this->increment('usage_count');

        return $this->usage()->create([
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'stock_code' => $this->stock_code,
            'quantity_purchased' => $quantity,
            'quantity_discounted' => $quantity,
            'bonus_quantity' => $discountDetails['bonus_quantity'] ?? 0,
            'original_price_cents' => $discountDetails['original_price'] ?? 0,
            'discounted_price_cents' => $discountDetails['discounted_price'] ?? 0,
            'total_savings_cents' => $discountDetails['total_savings'] ?? 0,
            'customer_price_level' => $discountDetails['customer_tier'] ?? 1,
            'promotion_details' => $this->toArray()
        ]);
    }

    public function canBeUsedByCustomer($customerId): bool
    {
        if (!$this->quantity_limit_per_customer) {
            return true;
        }

        $customerUsage = $this->usage()
            ->where('customer_id', $customerId)
            ->sum('quantity_purchased');

        return $customerUsage < $this->quantity_limit_per_customer;
    }

    public function hasReachedUsageLimit(): bool
    {
        if (!$this->usage_limit_total) {
            return false;
        }

        return $this->usage_count >= $this->usage_limit_total;
    }

    /**
     * Update product featured status
     */
    public function updateProductFeaturedStatus(): void
    {
        if ($this->product && $this->isActive()) {
            $this->product->update(['is_featured' => 1]);
        }
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($promotion) {
            $promotion->updateProductFeaturedStatus();
        });

        static::updated(function ($promotion) {
            $promotion->updateProductFeaturedStatus();
        });
    }
}
