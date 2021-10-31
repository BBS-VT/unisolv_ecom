<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use \DateTimeInterface;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    public $table = 'products';

    protected $appends = [
        'photo'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'StockItemName',
        'StockCode',
        'SupplierID',
        'UnitPackageID',
        'OuterPackageID',
        'TaxRateID',
        'Brand',
        'Size',
        'LeadTimeDays',
        'Packsize',
        'Barcode',
        'CostPrice',
        'SellingPrice',
        'DiscountPercentage',
        'WeightPerUnit',
        'MarketingComments',
        'SearchDetails',
        'status',
        'LastEditedBy',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null) : void
    {
        $this->addMediaConversion('thumb')->width('250');
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function tags()
    {
        return $this->belongsToMany(ProductTag::class);
    }

    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();

        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
        }

        return $file;
    }

    public function orderItems()
    {
        return $this->hasMany(OrdersItem::class,'StockItem', 'id');
    }

    public function specialdeals()
    {
        return $this->hasMany(SpecialDeals::class, 'StockItemID', 'id');
    }

    public function stockHolding()
    {
        return $this->hasOne(StockItemHoldings::class, 'StockCode', 'StockCode');
    }

    public function packageType()
    {
        return $this->hasOne(PackageType::class, 'id', 'UnitPackageID');
    }

    public function casepackageType()
    {
        return $this->hasOne(PackageType::class, 'id', 'OuterPackageID');
    }
}

