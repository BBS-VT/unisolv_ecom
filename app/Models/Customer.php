<?php

namespace App\Models;

use App\Models\Orders;
use App\Models\User;
use App\Models\BuyingGroup;
use App\Models\CustomerCategory;
use App\Traits\UUIDTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Customer extends Model
{
    use SoftDeletes, UUIDTrait;

    public $table = 'customers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'acc_main',
        'acc_sub',
        'acc_code',
        'company_id',
        'CustomerName',
        'BillToCustomerID',
        'CustomerCategoryID',
        'BuyingGroupID',
        'PrimaryContactPersonID',
        'AlternateContactPersonID',
        'StoreEAN',
        'VatNr',
        'CreditLimit',
        'AccountOpenedDate',
        'StandardDiscountPercentage',
        'IsStatementSent',
        'IsOnCreditHold',
        'PriceIndicator',
        'PaymentDays',
        'PhoneNumber',
        'FaxNumber',
        'DeliveryRoute',
        'DeliveryRoutePosition',
        'WebsiteURL',
        'GeneralEmailAddress',
        'DeliveryAddressLine1',
        'DeliveryAddressLine2',
        'DeliveryCity',
        'DeliveryPostalCode',
        'PostalAddressLine1',
        'PostalAddressLine2',
        'PostalCity',
        'PostalPostalCode',
        'CustomerStatus',
        'CountryID',
        'SalesRepID',
        'LastEditedBy',
        'price_level',
        'discount_allowed',
    ];

    public static function getNextCustomerNumber()
    {
        // Get the last created order
        $lastCustomer = Customer::latest('acc_main')->first();

        if (!$lastCustomer) {
            // if there is no existing customers set last acc code to 0,
            // which will result in 1 being created
            $number = 0;
        } else {
            $number = $lastCustomer->acc_main;
        }

        return sprintf('%06d', intval($number) + 1);
    }

    public function billingCustomer()
    {
        return $this->hasOne(Customer::class, 'BillToCustomerID', 'id');
    }

    public function customerCategory()
    {
        return $this->belongsTo(CustomerCategory::class, 'CustomerCategoryID', 'AccountType');
    }

    public function buyingGroup()
    {
        return $this->belongsTo(BuyingGroup::class, 'BuyingGroupID', 'BuyingGroupName');
    }

    //public function primaryContact()
    //{
    //    return $this->hasOne('User', 'PrimaryContactPersonID');
    //}

    public function alternateContact()
    {
        return $this->hasOne('User', 'AlternateContactPersonID');
    }

    public function country()
    {
        return $this->hasOne('Country', 'CountryID');
    }

    public function salesrep()
    {
        return $this->belongsTo(User::class, 'SalesRepID', 'RepCode');
    }

    public function orders()
    {
        return $this->hasMany(Orders::class,);
    }

    public function lastedited()
    {
        return $this->hasOne(User::class, 'id','LastEditedBy');
    }

    public function specialdeal()
    {
        return $this->hasMany(SpecialDeals::class, 'id', 'CustomerID');
    }

    public function customerBalance()
    {
        return $this->hasOne(CustomerBalance::class, 'AccMain', 'acc_main');
    }

    public function customerSubBalance()
    {
        return $this->hasOne(CustomerBalance::class, 'AccCode', 'acc_code');

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

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    /**
     * Customer has many contacts (polymorphic)
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }


    /**
     * Get the primary contact
     */
    public function primaryContact()
    {
        return $this->morphOne(Contact::class, 'contactable')
            ->where('is_primary', true);
    }

    /**
     * Get active contacts only
     */
    public function activeContacts()
    {
        return $this->morphMany(Contact::class, 'contactable')
            ->where('is_active', true);
    }

    public function getCurrencyCodeAttribute()
    {
        return $this->currency->code;
    }

    /**
     * Scope a query to only include Customers of a given company.
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

    public function getEffectivePriceLevelAttribute()
    {
        return in_array($this->price_level, [1, 2, 3, 4]) ? $this->price_level : 1;
    }

    public function shouldApplyContractDiscount(): bool
    {
        return $this->discount_allowed === true;
    }

    public function getRecentOrders($limit = 5)
    {
        return $this->orders()
            ->orderBy('OrderDate', 'desc')
            ->take($limit)
            ->get();
    }

    public function getTotalSpent($months = 12)
    {
        return $this->orders()
            ->where('OrderDate', '>=', now()->subMonths($months))
            ->where('OrderStatusID', '!=', 5) // Exclude cancelled orders
            ->sum('total');
    }

    public function getOrderStats()
    {
        return [
            'total' => $this->orders()->count(),
            'new' => $this->orders()->where('OrderStatusID', 1)->count(),
            'completed' => $this->orders()->where('OrderStatusID', 4)->count(),
            'on_hold' => $this->orders()->where('OrderStatusID', 5)->count(),
        ];
    }

    public function getFormattedCreditLimit(): string
    {
        return \App\Helpers\PricingHelper::formatPrice($this->CreditLimit );
    }

    public function getFullDeliveryAddress(): string
    {
        $address = $this->DeliveryAddressLine1;
        if ($this->DeliveryAddressLine2) {
            $address .= "\n" . $this->DeliveryAddressLine2;
        }
        $address .= "\n{$this->DeliveryCity}, {$this->DeliveryState} {$this->DeliveryPostCode}";

        return $address;
    }

    public function isActive(): bool
    {
        return !$this->IsOnCreditHold;
    }
}
