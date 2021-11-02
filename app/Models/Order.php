<?php


namespace App\Models;

use App\Traits\HasTax;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Customer;

class Order extends Model
{
    use HasTax, UUIDTrait;

    public $table = 'orders';

    protected $dates = [
        'created_at',
        'updated_at',
        'OrderDate',
    ];

    protected $fillable = [
        'CustomerID',
        'company_id',
        'SalesPersonID',
        'PickedByPersonID',
        'ContactPersonID',
        'BackorderOrderID',
        'OrderStatusID',
        'discount_type',
        'discount_val',
        'sub_total',
        'total',
        'tax_per_item',
        'discount_per_item',
        'Authorisation',
        'OrderNumber',
        'OrderDate',
        'ExpectedDeliveryDate',
        'CustomerPurchaseOrderNumber',
        'Comments',
        'DeliveryInstructions',
        'InternalComments',
        'PickingCompletedWhen',
        'LastEditedBy',
        'created_at',
        'updated_at',
    ];

    /**
     * Automatically cast attributes to given types
     *
     * @var array
     */
    protected $casts = [
        'total' => 'integer',
        'tax' => 'integer',
        'sub_total' => 'integer',
        'discount' => 'float',
        'discount_val' => 'integer',
        'tax_per_item' => 'boolean',
        'discount_per_item' => 'boolean',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['fields'];


    public function items()
    {
        return $this->hasMany(OrdersItem::class, 'OrderID', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'acc_main');
    }

    public function orderstatus()
    {
        return $this->belongsTo(OrderStatus::class, 'OrderStatusID', 'id');
    }

    /**
     * Define Relation with Company Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function salesperson()
    {
        return $this->hasOne(User::class, 'id', 'SalesPersonID');
    }

    public function lastedited()
    {
        return $this->belongsTo(User::class, 'LastEditedBy', 'id');
    }

    public static function getNextOrderNumber()
    {
        // Get the last created order
        $lastOrder = Order::latest()->first();

        if (!$lastOrder) {
            // if there is no existing orders set last order to 0,
            // which will result in 1 being created
            $number = 0;
        } else {
            //$number = explode("-", $lastOrder->OrderNumber);
            $number = $lastOrder->OrderNumber;
        }

        return sprintf('%06d', intval($number) + 1);
    }

    /**
     * Get total percentage of taxes
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
     * Get the Total Percentage of Order Taxes
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

    /*public function getSubTotalAmount()
    {
        $subtotal_amount = 0;
        foreach ($this->orderItems as $item) {
            if(!$item->TaxRateID == "0") {
                $subtotal_amount += $item->UnitPrice * $item->Quantity * (1 + ($item->TaxRate / 100));
            } else {
                $subtotal_amount += $item->UnitPrice * $item->Quantity;
            }
        }

        return $subtotal_amount;
    }

    public function getSubTotalAmountIncl()
    {
        $subtotal_amount = 0;
        foreach ($this->orderItems as $item) {
            if(!$item->TaxRateID == "0") {
                $subtotal_amount += $item->UnitPrice * $item->Quantity * (1 + ($item->TaxRate / 100));
            } else {
                $subtotal_amount += $item->UnitPrice * $item->Quantity;
            }

        }

        return $subtotal_amount;
    }

    public function getTotalVATAmount()
    {
        $total_vat = 0;
        $total_vatamount = 0;
        foreach ($this->orderItems as $item) {
            $vat = $item->TaxRate;
            $total_vat += $item->UnitPrice * $item->Quantity;
            $total_vatamount = $total_vat * ($vat / 100);
        }

        return $total_vatamount;
    }

    public function getTotalVATInclAmount()
    {
        $total_vat = 0;
        $total_vatamount = 0;
        foreach ($this->orderItems as $item) {
            $vat = $item->TaxRate;
            $total_vat += $item->UnitPrice * $item->Quantity;
            $total_vatamount = $total_vat - ($total_vat / (1+($vat / 100)));
        }

        return $total_vatamount;
    }

    public function getTotalAmount()
    {
        $total_amount = $this->getSubTotalAmount() + $this->getTotalVATAmount();

        return $total_amount;
    }*/


}
