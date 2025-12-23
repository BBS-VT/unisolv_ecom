<?php

namespace App\Models;

use App\Traits\UUIDTrait;
use App\Traits\HasAddresses;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Supplier extends Model
{
    use SoftDeletes, HasAddresses;

    protected $fillable = [
        'acc_main',
        'acc_sub',
        'acc_code',
        'company_id',
        'SupplierName',
        'VatNr',
        'tax_reference',
        'CreditLimit',
        'AccountOpenedDate',
        'StandardDiscountPercentage',
        'IsOnCreditHold',
        'PaymentDays',
        'payment_terms',
        'PhoneNumber',
        'FaxNumber',
        'WebsiteURL',
        'GeneralEmailAddress',
        'bank_name',
        'bank_account',
        'bank_branch',
        'lead_time_days',
        'minimum_order_value',
        'Status',
        'CountryID',
        'currency_id',
        'notes',
        'LastEditedBy',
    ];

    protected $casts = [
        'CreditLimit' => 'decimal:2',
        'StandardDiscountPercentage' => 'decimal:2',
        'minimum_order_value' => 'decimal:2',
        'IsOnCreditHold' => 'boolean',
        'Status' => 'boolean',
        'PaymentDays' => 'integer',
        'lead_time_days' => 'integer',
        'AccountOpenedDate' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->acc_code)) {
                $supplier->acc_code = self::getNextAccountNumber();
            }
        });
    }

    /**
     * Generate next account code
     */
    public static function getNextAccountNumber(): string
    {
        $lastSupplier = static::where('company_id', auth()->user()->company_id ?? 1)
            ->latest('acc_code')
            ->first();

        if (!$lastSupplier) {
            $number = 0;
        } else {
            // Extract numeric part from acc_code (e.g., SUP000001 -> 1)
            $number = (int) preg_replace('/[^0-9]/', '', $lastSupplier->acc_code);
        }

        return 'SUP' . sprintf('%06d', $number + 1);
    }

    /**
     * Get formatted display name
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->acc_code} - {$this->SupplierName}";
    }

    /**
     * Get short postal address for display
     */
    public function getShortAddressAttribute(): string
    {
        $postal = $this->address('postal');

        if (!$postal) {
            return 'No address on file';
        }

        $line1 = $postal->address_line1 ?? '';
        $city = $postal->city ?? '';

        return trim("{$line1}, {$city}");
    }

    /**
     * Check if supplier is active
     */
    public function isActive(): bool
    {
        return $this->Status && !$this->IsOnCreditHold;
    }

    /**
     * Check if supplier is on credit hold
     */
    public function isOnHold(): bool
    {
        return $this->IsOnCreditHold;
    }

    // ==================== Relationships ====================
    /**
     * Supplier belongs to a company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Supplier country
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'CountryID', 'id');
    }

    /**
     * Supplier currency
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    /**
     * Currency code
     */
    public function getCurrencyCodeAttribute(): ?string
    {
        return $this->currency?->code;
    }

    /**
     * User who last edited this supplier
     */
    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'LastEditedBy');
    }

    /**
     * Supplier has many contacts (using morphMany for flexibility)
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    /**
     * Get primary contact
     */
    public function primaryContact()
    {
        return $this->morphOne(Contact::class, 'contactable')
            ->where('is_primary', true);
    }

    /**
     * Supplier has many purchase orders
     */
    //public function purchaseOrders()
    //{
    //    return $this->hasMany(PurchaseOrder::class);
    //}

    /**
     * Products this supplier can provide
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier', 'supplier_id', 'StockCode')
            ->withPivot([
                'supplier_product_code',
                'cost_price',
                'lead_time_days',
                'is_preferred',
                'sort_order'
            ])
            ->withTimestamps();
    }

    /**
     * Get preferred products (where this supplier is marked as preferred)
     */
    public function preferredProducts()
    {
        return $this->products()->wherePivot('is_preferred', true);
    }

    /**
     * Batch receives from this supplier
     */
    //public function batchReceives()
    //{
    //    return $this->hasMany(BatchReceive::class);
    //}

    // ==================== Scopes ====================

    /**
     * Scope: Active suppliers only
     */
    public function scopeActive($query)
    {
        return $query->where('Status', true);
    }

    /**
     * Scope: Inactive suppliers only
     */
    public function scopeInactive($query)
    {
        return $query->where('Status', false);
    }

    /**
     * Scope: Suppliers on credit hold
     */
    public function scopeOnHold($query)
    {
        return $query->where('IsOnCreditHold', true);
    }

    /**
     * Scope: Suppliers not on hold
     */
    public function scopeNotOnHold($query)
    {
        return $query->where('IsOnCreditHold', false);
    }

    /**
     * Scope: Filter by company (multi-tenancy)
     */
    public function scopeForCompany($query, $companyId = null)
    {
        //$companyId = $companyId ?? (auth()->user()->company()->id ?? null);

        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Search suppliers by name or code
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('SupplierName', 'like', "%{$search}%")
                ->orWhere('acc_code', 'like', "%{$search}%")
                ->orWhere('VatNr', 'like', "%{$search}%");
        });
    }

    // ==================== Helper Methods ====================

    /**
     * Get outstanding purchase orders
     */
    public function outstandingPurchaseOrders()
    {
        return $this->purchaseOrders()
            ->whereIn('status', ['pending', 'approved', 'partially_received']);
    }

    /**
     * Calculate total outstanding amount
     */
    public function outstandingAmount(): float
    {
        return $this->outstandingPurchaseOrders()
            ->sum('total_amount') ?? 0;
    }

    /**
     * Check if within credit limit
     */
    public function withinCreditLimit(): bool
    {
        if ($this->CreditLimit <= 0) {
            return true; // No limit set
        }

        return $this->outstandingAmount() < $this->CreditLimit;
    }

    /**
     * Get available credit
     */
    public function availableCredit(): float
    {
        if ($this->CreditLimit <= 0) {
            return PHP_FLOAT_MAX; // Unlimited
        }

        return max(0, $this->CreditLimit - $this->outstandingAmount());
    }
}
