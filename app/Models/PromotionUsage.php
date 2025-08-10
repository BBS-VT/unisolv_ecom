<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PromotionUsage extends Model
{
    use HasFactory;

    protected $table = 'promotion_usages';

    protected $fillable = [
        'promotion_id',
        'customer_id',
        'order_id',
        'stock_code',
        'quantity_purchased',
        'quantity_discounted',
        'bonus_quantity',
        'original_price_cents',
        'discounted_price_cents',
        'total_savings_cents',
        'customer_price_level',
        'promotion_details'
    ];

    protected $casts = [
        'promotion_details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'stock_code', 'StockCode');
    }

    /**
     * Accessors
     */
    public function getOriginalPriceFormattedAttribute(): string
    {
        return '$' . number_format($this->original_price_cents / 100, 2);
    }

    public function getDiscountedPriceFormattedAttribute(): string
    {
        return '$' . number_format($this->discounted_price_cents / 100, 2);
    }

    public function getTotalSavingsFormattedAttribute(): string
    {
        return '$' . number_format($this->total_savings_cents / 100, 2);
    }

    public function getSavingsPercentageAttribute(): float
    {
        if ($this->original_price_cents == 0) {
            return 0;
        }

        return round((($this->original_price_cents - $this->discounted_price_cents) / $this->original_price_cents) * 100, 2);
    }

    /**
     * Scopes
     */
    public function scopeForPromotion($query, $promotionId)
    {
        return $query->where('promotion_id', $promotionId);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForProduct($query, $stockCode)
    {
        return $query->where('stock_code', $stockCode);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeWithSavings($query)
    {
        return $query->where('total_savings_cents', '>', 0);
    }
}
