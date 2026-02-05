<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public $table = 'locations';

    protected $primaryKey = 'LocationCode';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'LocationCode',
        'fulfillment_email',
        'LocationName',
        'LocationDescription',
        'Address1',
        'Address2',
        'City',
        'Province',
        'PostalCode',
        'Country',
        'Phone',
        'Email',
        'ContactPerson',
        'IsActive',
        'IsDefault',
        'SortOrder',
        'show_in_shop',
        'shop_sort_order',
        'LastEditedBy'
    ];

    protected $casts = [
        'IsActive' => 'boolean',
        'IsDefault' => 'boolean',
        'SortOrder' => 'integer',
    ];

    /**
     * Get stock holdings at this location
     */
    public function stockHoldings()
    {
        return $this->hasMany(StockItemHoldings::class, 'LocationCode', 'LocationCode');
    }

    /**
     * Scope to get only active locations
     */
    public function scopeActive($query)
    {
        return $query->where('IsActive', true);
    }

    /**
     * Scope to get default location
     */
    public function scopeDefault($query)
    {
        return $query->where('IsDefault', true);
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute()
    {
        $address = collect([
            $this->Address1,
            $this->Address2,
            $this->City,
            $this->Province,
            $this->PostalCode,
            $this->Country
        ])->filter()->implode(', ');

        return $address;
    }

    /**
     * Get display name (LocationCode - LocationName)
     */
    public function getDisplayNameAttribute()
    {
        return $this->LocationCode . ' - ' . $this->LocationName;
    }

    /**
     * Boot method to ensure only one default location
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->IsDefault) {
                // Set all other locations to not default
                self::where('LocationCode', '!=', $model->LocationCode)
                    ->update(['IsDefault' => false]);
            }
        });
    }

    public function scopeShopLocations($query)
    {
        return $query->where('show_in_shop', true)
            ->orderBy('shop_sort_order', 'asc');
    }

    public function categories()
    {
        return $this->hasMany(ProductCategory::class, 'location_code', 'LocationCode');
    }

}
