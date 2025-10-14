<?php

namespace App\Models;

use App\Traits\HasTax;
use Illuminate\Database\Eloquent\Model;

class OrdersItem extends Model
{
    use HasTax;

    public $table = 'orders_items';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'OrderID',
        'company_id',
        'StockItem',
        'LocationCode',
        'PackageTypeID',
        'discount_type',
        'Quantity',
        'discount',
        'discount_val',
        'UnitPrice',
        'TaxRate',
        'total',
        'PickedQuantity',
        'PickingCompletedWhen',
        'LastEditedBy',
    ];

    /**
     * Autoatically cast attributes to given types
     *
     * @var array
     */
    protected $casts = [
        'UnitPrice'     => 'integer',
        'total'         => 'integer',
        'discount_val'  => 'float',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'OrderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'StockItem', 'StockCode');
    }

    /**
     * Get the location for this order item
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'LocationCode', 'LocationCode');
    }

    /**
     * Get the stock holding for this item at the specified location
     */
    public function stockHolding()
    {
        return $this->hasOne(StockItemHoldings::class, 'StockCode', 'StockItem')
            ->where('LocationCode', $this->LocationCode);
    }

    /**
     * Check if item can be fulfilled from the assigned location
     */
    public function canFulfillFromLocation()
    {
        $stockHolding = StockItemHoldings::where('StockCode', $this->StockItem)
            ->where('LocationCode', $this->LocationCode)
            ->first();

        if (!$stockHolding) {
            return false;
        }

        return $stockHolding->QuantityOnHand >= $this->Quantity;
    }

    /**
     * Get available quantity at the assigned location
     */
    public function getAvailableQuantityAtLocation()
    {
        return StockItemHoldings::where('StockCode', $this->StockItem)
            ->where('LocationCode', $this->LocationCode)
            ->value('QuantityOnHand') ?? 0;
    }


    /**
     * Get the Total Percentage of Invoice Item Taxes
     *
     * @return int
     */
    public function getTotalPercentageOfTaxes()
    {
        $total = 0;
        foreach ($this->taxes as $tax) {
            $total += $tax->tax_type->percent;
        }

        return (int) $total;
    }

    /**
     * Get the Total Percentage of Order Item Taxes with Tax Names
     *
     * @return array
     */
    public function getTotalPercentageOfTaxesWithNames()
    {
        $total = [];
        foreach ($this->taxes as $tax) {
            if (isset($total[$tax->tax_type->name])) {
                $total[$tax->tax_type->name] += $tax->tax_type->percent;
            } else {
                $total[$tax->tax_type->name] = $tax->tax_type->percent;
            }
        }

        return $total;
    }

    /**
     * Scope a query to only include Order Items of a given company.
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param int $company_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByCompany($query, $company_id)
    {
        $query->where('company_id', $company_id);
    }

    /**
     * Scope a query to only include Order Items of a given Order Date.
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param date $from
     * @param date $to
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdersBetween($query, $from, $to)
    {
        $query->whereHas('order', function ($query) use ($from, $to) {
            $query->whereBetween(
                'OrderDate',
                [$from->format('Y-m-d'), $to->format('Y-m-d')]
            );
        });
    }
}
