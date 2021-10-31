<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItemHoldings extends Model
{
    public $table = 'stock_item_holdings';

    protected $primaryKey = 'StockCode';
    protected $keyType = 'string';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'StockCode',
        'QuantityOnHand',
        'BinLocation',
        'LastStocktakeQuantity',
        'LastCostPrice',
        'ReorderLevel',
        'TargetStockLevel',
        'LastEditedBy'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'StockCode', 'StockCode');
    }
}
