<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersItem extends Model
{
    public $table = 'orders_items';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'OrderID',
        'StockItem',
        'PackageTypeID',
        'Quantity',
        'UnitPrice',
        'TaxRate',
        'PickedQuantity',
        'PickingCompletedWhen',
        'LastEditedBy',
        ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'OrderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'StockItem', 'id');
    }
}
