<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use \DateTimeInterface;
use App\Traits\HasTax;
use App\Traits\UUIDTrait;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, UUIDTrait, HasTax;

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
        'company_id',
        'StockItemName',
        'StockCode',
        'SupplierID',
        'UnitPackageID',
        'OuterPackageID',
        'TaxRateID',
        'Brand',
        'Size',
        'slug',
        'LeadTimeDays',
        'Packsize',
        'Barcode',
        'AltBarcode',
        'AverageCostPrice',
        'SellingPrice',
        'SellingPrice2',
        'SellingPrice3',
        'SellingPrice4',
        'DiscountPercentage',
        'WeightPerUnit',
        'MarketingComments',
        'SearchDetails',
        'status',
        'is_featured',
        'LastEditedBy',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Automatically cast attributes to given types
     *
     * @var array
     */
    protected $casts = [
        'CostPrice' => 'float',
        'SellingPrice' => 'float'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->StockItemName);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('StockItemName') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->StockItemName);
            }
        });
    }

    public function registerMediaConversions(Media $media = null) : void
    {
        $this->addMediaConversion('thumb')->width('250');
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class);
    }

    public function mainCategories()
    {
        return $this->belongsToMany(ProductCategory::class)
            ->where('ParentID', 0);
    }

    public function subCategories()
    {
        return $this->belongsToMany(ProductCategory::class)
            ->where('ParentID', '>', 0);
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

    public function order_items()
    {
        return $this->hasMany(OrdersItem::class,'StockItem', 'id')->orderBy('StockItemName', 'desc');
    }

    public function specialdeals()
    {
        return $this->hasMany(SpecialDeals::class, 'StockItemID', 'StockCode');
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

    /**
     * Define Relation with Company Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope a query to only include Products of a given company.
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

    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $value = Str::slug($this->StockItemName);
        }

        $this->attributes['slug'] = $this->makeSlugUnique($value);
    }

    protected function makeSlugUnique($slug)
    {
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    public function hasEnoughStock($quantity)
    {
        if (Features::allowBackorders()) {
            return true;
        }

        return $this->stock >= $quantity;
    }

    public function checkMinimumOrderQuantity($quantity)
    {
        if ($this->min_order_qty > 0) {
            return $quantity >= $this->min_order_qty;
        }

        return true;
    }
}

