<?php

namespace App\Models;

use App\Traits\HasTax;
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
        'company_id',
        'Quantity',
        'UnitPrice',
        'TaxRate',
        'discount_type',
        'discount_val',
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
        'Quantity'      => 'float',
        'discount_val'  => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'OrderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'StockItem', 'id');
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
