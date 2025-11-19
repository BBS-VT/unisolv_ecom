<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'StockCode',
        'LocationCode',
        'transaction_type',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
        'company_id'
    ];

    protected $casts = [
        'quantity_change' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'StockCode', 'StockCode');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'LocationCode', 'LocationCode');
    }

    public function user()
    {
        return $this->belongsTo(Company::class);
    }

    public function reference()
    {
        if ($this->reference_type && $this->reference_id) {
            $modelClass= "App\Models\\{$this->reference_type}";
            if (class_exists($modelClass)) {
                return $modelClass::find($this->reference_id);
            }
        }
        return null;
    }

    /**
     * Scope for filtering by product
     */
    public function scopeForProduct($query, $stockCode)
    {
        return $query->where('StockCode', $stockCode);
    }

    /**
     * Scope for filtering by location
     */
    public function scopeForLocation($query, $locationCode)
    {
        return $query->where('LocationCode', $locationCode);
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope for date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
