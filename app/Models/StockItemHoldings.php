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

}
