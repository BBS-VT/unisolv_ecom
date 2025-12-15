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
     * @param  string  $referenceType
     * @param  int|null  $referenceId
     * @param  string|null  $notes
     * @return bool
     * @throws \Exception
     */
    public static function reduceStock(
        $stockCode,
        $locationCode,
        $quantity, $userId = null,
        $referenceType = 'Order',
        $referenceId = null,
        $notes = null
        )
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

        $oldQuantity = (float) $holding->QuantityOnHand;
        $quantityToReduce = (float) $quantity;
        $newQuantity = $oldQuantity - $quantityToReduce;

        $updated = self::where('StockCode', $stockCode)
            ->where('LocationCode', $locationCode)
            ->update([
                'QuantityOnHand' => $newQuantity,
                'LastEditedBy' => $userId ?? auth()->id(),
                'updated_at' => now()
            ]);

        if (!$updated) {
            throw new \Exception("Failed to update stock for {$stockCode} at {$locationCode}");
        }

        StockTransaction::create([
            'StockCode' => $stockCode,
            'LocationCode' => $locationCode,
            'transaction_type' => strtolower($referenceType),
            'quantity_change' => -$quantityToReduce, // Negative for reduction
            'quantity_before' => $oldQuantity,
            'quantity_after' => $newQuantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
            'company_id' => $holding->product->company_id ?? auth()->user()->currentCompany()->id
        ]);

        \Log::info("Stock reduced for order", [
            'stock_code' => $stockCode,
            'location' => $locationCode,
            'quantity_reduced' => $quantityToReduce,
            'previous_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'user_id' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Increase stock quantity (for order cancellations, returns, etc.)
     */
    public static function increaseStock(
        $stockCode,
        $locationCode,
        $quantity,
        $userId = null,
        $referenceType = 'Adjustment',
        $referenceId = null,
        $notes = null
    ) {
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

            // Log the transaction
            StockTransaction::create([
                'StockCode' => $stockCode,
                'LocationCode' => $locationCode,
                'transaction_type' => 'initial',
                'quantity_change' => $quantity,
                'quantity_before' => 0,
                'quantity_after' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes ?? 'Initial stock holding created',
                'user_id' => $userId ?? auth()->id(),
                'company_id' => $holding->product->company_id ?? auth()->user()->currentCompany()->id
            ]);

            \Log::info("New stock holding created", [
                'stock_code' => $stockCode,
                'location' => $locationCode,
                'quantity' => $quantity
            ]);
        } else {
            $oldQuantity = (float) $holding->QuantityOnHand;
            $quantityToAdd = (float) $quantity;
            $newQuantity = $oldQuantity + $quantityToAdd;

            // Update stock holding
            $updated = self::where('StockCode', $stockCode)
                ->where('LocationCode', $locationCode)
                ->update([
                    'QuantityOnHand' => $newQuantity,
                    'LastEditedBy' => $userId ?? auth()->id(),
                    'updated_at' => now()
                ]);

            if (!$updated) {
                throw new \Exception("Failed to update stock for {$stockCode} at {$locationCode}");
            }

            // Log the transaction
            \App\Models\StockTransaction::create([
                'StockCode' => $stockCode,
                'LocationCode' => $locationCode,
                'transaction_type' => strtolower($referenceType),
                'quantity_change' => $quantityToAdd, // Positive for increase
                'quantity_before' => $oldQuantity,
                'quantity_after' => $newQuantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'user_id' => $userId ?? auth()->id(),
                'company_id' => $holding->product->company_id ?? auth()->user()->currentCompany()->id
            ]);

            \Log::info("Stock increased", [
                'stock_code' => $stockCode,
                'location' => $locationCode,
                'quantity_added' => $quantityToAdd,
                'new_quantity' => $newQuantity
            ]);
        }

        return true;
    }

    /**
     * Update stock from CSV import and log the transaction
     *
     * @param string $stockCode
     * @param string $locationCode
     * @param float $newQuantity
     * @param int|null $userId
     * @param string|null $importReference (e.g., filename)
     * @return bool
     */
    public static function updateFromImport(
        $stockCode,
        $locationCode,
        $newQuantity,
        $userId = null,
        $referenceType = 'ImportJob',
        $referenceId = null,
        $notes = null
    ) {
        $holding = self::where('StockCode', $stockCode)
            ->where('LocationCode', $locationCode)
            ->lockForUpdate()
            ->first();

        $oldQuantity = $holding ? (float) $holding->QuantityOnHand : 0;
        $newQuantity = (float) $newQuantity;
        $quantityChange = $newQuantity - $oldQuantity;

        // Skip if no change
        if ($quantityChange == 0) {
            return false;
        }

        if (!$holding) {
            // Create new stock holding
            $holding = self::create([
                'StockCode' => $stockCode,
                'LocationCode' => $locationCode,
                'QuantityOnHand' => $newQuantity,
                'LastEditedBy' => $userId ?? auth()->id()
            ]);
        } else {
            // Update existing holding
            self::where('StockCode', $stockCode)
                ->where('LocationCode', $locationCode)
                ->update([
                    'QuantityOnHand' => $newQuantity,
                    'LastEditedBy' => $userId ?? auth()->id(),
                    'updated_at' => now()
                ]);
        }

        // Log the transaction
        StockTransaction::create([
            'StockCode' => $stockCode,
            'LocationCode' => $locationCode,
            'transaction_type' => 'import',
            'quantity_change' => $quantityChange,
            'quantity_before' => $oldQuantity,
            'quantity_after' => $newQuantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
            'company_id' => $companyId ?? $holding->product->company_id ?? 1
        ]);

        return true;
    }

}
