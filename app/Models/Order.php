<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Order extends Model
{
    public $table = 'orders';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'CustomerID',
        'SalesPersonID',
        'PickedByPersonID',
        'ContactPersonID',
        'BackorderOrderID',
        'OrderStatusID',
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

    public function orderItems()
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

    public function getSubTotalAmount()
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
}
