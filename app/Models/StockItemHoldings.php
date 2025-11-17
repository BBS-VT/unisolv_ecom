<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItemHoldings extends Model
{
    public $table = 'stock_item_holdings';

    protected $primaryKey = 'id';
    protected $keyType = 'int';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'StockCode',
        'LocationCode',
        'QuantityOnHand',
        'BinLocation',
        'LastStocktakeQuantity',
        'LastCostPrice',
        'ReorderLevel',
        'TargetStockLevel',
        'LastEditedBy'
    ];

    protected $casts = [
        'QuantityOnHand' => 'decimal:2',
        'LastStocktakeQuantity' => 'decimal:2',
        'LastCostPrice' => 'decimal:4',
        'ReorderLevel' => 'decimal:2',
        'TargetStockLevel' => 'decimal:2',
    ];

    /**
     * Get the product that this stock holding belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'StockCode', 'StockCode');
    }

    /**
     * Get the location for this stock holding
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'LocationCode', 'LocationCode');
    }

    /**
     * Scope to filter by location
     */
    public function scopeForLocatoin($query, $locationCode)
    {
        return $query->where('LocationCode', $locationCode);
    }

    /**
     * Scope to filter by product
     */
    public function scopeForProduct($query, $stockCode)
    {
        return $query->where('StockCode', $stockCode);
    }

    /**
     * Get total quantity for a product across all locations
     */
    public static function getTotalQuantityForProduct($stockCode)
    {
        return self::where('StockCode', $stockCode)->sum('QuantityOnHand');
    }

    /**
     * Get quantity for a specific product at a specific location
     */
    public static function getQuantityAtLocation($stockCode, $locationCode)
    {
        return self::where('StockCode', $stockCode)
            ->where('LocationCode', $locationCode)
            ->value('QuantityOnHand') ?? 0;
    }

    /**
     * Check if product is below reorder level at this location
     */
    public function isBelowReorderLevel()
    {
        return $this->QuantityOnHand <= $this->ReorderLevel;
    }

    /**
     * Get the composite key for unique identification
     */
    public function getCompositeKeyAttribute()
    {
        return $this->StockCode . '-' . $this->LocationCode;
    }

    /**
     * Ensure unique constraint on StockCode + LocationCode combination
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ensure we don't create duplicate StockCode + LocationCode combinations
            $exists = self::where('StockCode', $model->StockCode)
                ->where('LocationCode', $model->LocationCode)
                ->exists();

            if ($exists) {
                throw new \Exception("Stock holding already exists for product {$model->StockCode} at location {$model->LocationCode}");
            }
        });
    }

    /**
     * Reduce stock quantity for an order
     *
     * @param string $stockCode
     * @param string $locationCode
     * @param float $quantity
     * @param int|null $userId
     * @return bool
     * @throws \Exception
     */
    public static function reduceStock($stockCode, $locationCode, $quantity, $userId = null)
    {
        $holding = self::where('StockCode', $stockCode)
            ->where('LocationCode', $locationCode)
            ->lockForUpdate() // Prevent race conditions
            ->first();

        if (!$holding) {
            throw new \Exception("No stock holding found for product {$stockCode} at location {$locationCode}");
        }

        // This should never happen due to cart validation, but check anyway
        if ($holding->QuantityOnHand < $quantity) {
            throw new \Exception("Insufficient stock at checkout. Available: {$holding->QuantityOnHand}, Requested: {$quantity}. This indicates a race condition or cart validation issue.");
        }

        $oldQuantity = $holding->QuantityOnHand;
        $holding->QuantityOnHand -= $quantity;
        $holding->LastEditedBy = $userId ?? auth()->id();
        $holding->save();

        \Log::info("Stock reduced for order", [
            'stock_code' => $stockCode,
            'location' => $locationCode,
            'quantity_reduced' => $quantity,
            'previous_quantity' => $oldQuantity,
            'new_quantity' => $holding->QuantityOnHand,
            'user_id' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Increase stock quantity (for order cancellations, returns, etc.)
     *
     * @param string $stockCode
     * @param string $locationCode
     * @param float $quantity
     * @param int|null $userId
     * @return bool
     */
    public static function increaseStock($stockCode, $locationCode, $quantity, $userId = null)
    {
        $holding = self::where('StockCode', $stockCode)
            ->where('LocationCode', $locationCode)
            ->lockForUpdate()
            ->first();

        if (!$holding) {
            // Create a new stock holding if it doesn't exist
            $holding = self::create([
                'StockCode' => $stockCode,
                'LocationCode' => $locationCode,
                'QuantityOnHand' => $quantity,
                'LastEditedBy' => $userId ?? auth()->id()
            ]);

            \Log::info("New stock holding created", [
                'stock_code' => $stockCode,
                'location' => $locationCode,
                'quantity' => $quantity
            ]);
        } else {
            $holding->QuantityOnHand += $quantity;
            $holding->LastEditedBy = $userId ?? auth()->id();
            $holding->save();

            \Log::info("Stock increased", [
                'stock_code' => $stockCode,
                'location' => $locationCode,
                'quantity_added' => $quantity,
                'new_quantity' => $holding->QuantityOnHand
            ]);
        }

        return true;
    }

}
