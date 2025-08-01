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
        'delivery_method',
        'preferred_delivery_date',
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
        'preferred_delivery_date' => 'date',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    // protected $with = ['fields'];


    public function items()
    {
        return $this->hasMany(OrdersItem::class, 'OrderID', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustomerID', 'acc_code');
    }

    public function orderstatus()
    {
        return $this->belongsTo(OrderStatus::class, 'OrderStatusID', 'id');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id')->orderBy('changed_at', 'desc');
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
     * Customized strpos helper function for excluding prefix
     * from estimate number
     *
     * @param string $haystack
     * @param string $needle
     * @param int $number
     *
     * @return string
     */
    private function strposX($haystack, $needle, $number)
    {
        if ($number == '1') {
            return strpos($haystack, $needle);
        } elseif ($number > '1') {
            return strpos(
                $haystack,
                $needle,
                $this->strposX($haystack, $needle, $number - 1) + strlen($needle)
            );
        } else {
            return error_log('Error: Value for parameter $number is out of range');
        }
    }

    public function getCurrencyAttribute($value)
    {
        return $this->customer->currency;
    }

    public function getCurrencyCodeAttribute($value)
    {
        return $this->customer->currency->code;
    }

    /**
     * Scope a query to only include Orders of a given company.
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
     * Scope a query to only include Orders of a given customer.
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param int $customer_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByCustomer($query, $customer_id)
    {
        $query->where('customer_id', $customer_id);
    }

    /**
     * Scope a query to only return new Orders
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNew($query)
    {
        $query->where('OrderStatusID', '1');
    }

    /**
     * Scope a query to only return processed Orders
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $active_stats = [
            '2',
            '3',
            '4',
        ];
        $query->whereIn('OrderStatusID', $active_stats);
    }

    /**
     * Scope a query to only return on-hold Orders
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnhold($query)
    {
        $query->where('OrderStatusID', '5');
    }

    /**
     * Scope a query to only return Orders which has OrderDate
     * greater or equal then given date
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param Date $from
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFrom($query, $from)
    {
        $query->where('OrderDate', '>=', $from);
    }

    /**
     * Scope a query to only return Orders which has OrderDate
     * less or equal then given date
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param Date $to
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTo($query, $to)
    {
        $query->where('OrderDate', '<=', $to);
    }


    public function getSubTotalAmount()
    {
        $subtotal_amount = 0;
        foreach ($this->items as $item) {
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
        foreach ($this->items as $item) {
            if(!$item->TaxRateID == "0") {
                $subtotal_amount += $item->total / $item->Quantity * (1 + ($item->TaxRate / 100));
            } else {
                $subtotal_amount += $item->total / $item->Quantity;
            }

        }

        return $subtotal_amount;
    }

    public function getTotalVATAmount()
    {
        $total_vat = 0;
        $total_vatamount = 0;
        foreach ($this->items as $item) {
            $vat = $item->TaxRate;
            $total_vat += $item->total / $item->Quantity;
            $total_vatamount = $total_vat * ($vat / 100);
        }

        return $total_vatamount;
    }

    public function getTotalVATInclAmount()
    {
        $total_vat = 0;
        $total_vatamount = 0;
        foreach ($this->items as $item) {
            //$vat = $item->TaxRate;
            $vat = 15;
            $total_vat += $item->total / $item->Quantity;
            $total_vatamount = $total_vat - ($total_vat / (1+($vat / 100)));
        }

        return $total_vatamount;
    }

    public function getTotalAmount()
    {
        $total_amount = $this->getSubTotalAmount() + $this->getTotalVATAmount();

        return $total_amount;
    }

    public function getIsUrgentAttribute()
    {
        //return $this->priority == 'urgent' || $this->OrderDate && $this->ExpectedDeliveryDate->isToday();
    }

    public function getStatusName(): string
    {
        return match($this->OrderStatusID) {
            1 => 'New',
            2 => 'Downloaded',
            3 => 'Delivery',
            4 => 'Invoiced',
            5 => 'On Hold',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->OrderStatusID) {
            1 => 'bg-primary',
            2 => 'bg-info',
            3 => 'bg-warning',
            4 => 'bg-success',
            5 => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function canBeCancelled(): bool
    {
        return $this->OrderStatusID === 1; // Only "New" orders can be cancelled
    }

    public function canBeReordered(): bool
    {
        return $this->orderItems()->exists();
    }

    public function getFormattedTotal(): string
    {
        return \App\Helpers\PricingHelper::formatPrice($this->total / 100);
    }

    public function getFormattedSubtotal(): string
    {
        return \App\Helpers\PricingHelper::formatPrice($this->sub_total / 100);
    }

    // Scopes for filtering
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('CustomerID', $customerId);
    }

    public function scopeWithStatus($query, $statusId)
    {
        return $query->where('OrderStatusID', $statusId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('OrderDate', '>=', now()->subDays($days));
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('OrderNumber', 'like', "%{$search}%")
                ->orWhere('CustomerPurchaseOrderNumber', 'like', "%{$search}%");
        });
    }

}
