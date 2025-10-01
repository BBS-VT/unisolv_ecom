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
        'refer_code',
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
        'SellingType',
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
        'SellingPrice' => 'float',
        'Packsize'  => 'integer',
        'status' => 'boolean',
    ];

    const SELLING_TYPE_INSTORE = 'instore';
    const SELLING_TYPE_ONLINE = 'online';
    const SELLING_TYPE_BOTH = 'both';

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
     * Get the referred product (parent/larger pack size)
     */
    public function referredProduct()
    {
        return $this->belongsTo(Product::class, 'refer_code', 'StockCode');
    }

    /**
     * Get products that refer to this product (children/smaller pack sizes)
     */
    public function referringProducts()
    {
        return $this->hasMany(Product::class, 'refer_code', 'StockCode');
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

    public function getVatExclusivePrice($inclusivePrice)
    {
        if (!$inclusivePrice) return null;

        $vatRate = $this->taxRate ? $this->taxRate->rate / 100 : 0.15;

        return $inclusivePrice / (1 + $vatRate);
    }

    public function getSellingPriceExclAttribute()
    {
        return $this->getVatExclusivePrice($this->SellingPrice);
    }

    public function getSellingPrice2ExclAttribute()
    {
        return $this->getVatExclusivePrice($this->SellingPrice2);
    }

    public function getSellingPrice3ExclAttribute()
    {
        return $this->getVatExclusivePrice($this->SellingPrice3);
    }

    public function getSellingPrice4ExclAttribute()
    {
        return $this->getVatExclusivePrice($this->SellingPrice4);
    }

    public function getQuantityOnHandAttribute()
    {
        return $this->stockHolding ? $this->stockHolding->QuantityOnHand : 0;
    }

    public function packSizeFamily()
    {
        $root = $this->getRootProduct();

        return Product::where(function($query) use ($root) {
            $query->where('StockCode', $root->StockCode)
                ->orWhere('refer_code', $root->StockCode);
        })->orderBy('Packsize', 'desc');
    }

    public function getRootProduct(): Product
    {
        $current = $this;

        while ($current->refer_code && $current->referringProduct) {
            $current = $current->referringProduct;
        }

        return $current;
    }

    /**
     * Get the base unit product (smallest pack size, usually singles)
     */
    public function getBaseUnitProduct(): Product
    {
        return $this->packSizeFamily()
            ->orderBy('Packsize', 'asc')
            ->first();
    }

    /**
     * Calculate total available quantity in base units
     */
    public function getTotalBaseUnitsAttribute(): int
    {
        $total = 0;

        $this->packSizeFamily()->with('stockHolding')->get()->each(function($product) use (&$total) {
            if ($product->stockHolding) {
                $total += $product->stockHolding->QuantityOnHand * $product->Packsize;
            }
        });

        return $total;
    }

    /**
     * Check if enough stock is available across all pack sizes
     */
    public function hasStockForQuantity(int $requestedQuantity): bool
    {
        return $this->getTotalBaseUnitsAttribute() >= ($requestedQuantity * $this->Packsize);
    }

    /**
     * Get available quantity for this specific pack size
     */
    public function getAvailablePacksAttribute(): int
    {
        if (!$this->stockHolding) {
            return 0;
        }

        return $this->stockHolding->QuantityOnHand;
    }

    /**
     * Calculate how many packs can be made from available base units
     */
    public function getMaxPacksFromBaseUnitsAttribute(): int
    {
        return intval($this->getTotalBaseUnitsAttribute() / $this->Packsize);
    }

    /**
     * Scope to get products with their pack size families
     */
    public function scopeWithPackSizeFamily($query)
    {
        return $query->with([
            'referredProduct',
            'referringProducts',
            'stockHolding'
        ]);
    }

    /**
     * Scope to get only root products (largest pack sizes)
     */
    public function scopeRootProducts($query)
    {
        return $query->whereNull('refer_code');
    }

    /**
     * Scope to get products by pack size family
     */
    public function scopeInPackSizeFamily($query, string $stockCode)
    {
        return $query->where(function($q) use ($stockCode) {
            $q->where('StockCode', $stockCode)
                ->orWhere('refer_code', $stockCode);
        });
    }

    /**
     * Get all available selling types
     *
     * @return array
     */
    public static function getSellingTypes()
    {
        return [
            self::SELLING_TYPE_INSTORE => __('global.instore_only'),
            self::SELLING_TYPE_ONLINE => __('global.online_only'),
            self::SELLING_TYPE_BOTH => __('global.instore_and_online'),
        ];
    }

    /**
     * Get the display label for the current selling type
     *
     * @return string
     */
    public function getSellingTypeLabel()
    {
        $types = self::getSellingTypes();
        return $types[$this->SellingType] ?? __('global.unknown');
    }

    /**
     * Scope to filter products for in-store selling
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForInstore($query)
    {
        return $query->whereIn('SellingType', [self::SELLING_TYPE_INSTORE, self::SELLING_TYPE_BOTH]);
    }

    /**
     * Scope to filter products for online selling
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForOnline($query)
    {
        return $query->whereIn('SellingType', [self::SELLING_TYPE_ONLINE, self::SELLING_TYPE_BOTH]);
    }

    /**
     * Check if product is available in-store
     *
     * @return bool
     */
    public function isAvailableInstore()
    {
        return in_array($this->SellingType, [self::SELLING_TYPE_INSTORE, self::SELLING_TYPE_BOTH]);
    }

    /**
     * Check if product is available online
     *
     * @return bool
     */
    public function isAvailableOnline()
    {
        return in_array($this->SellingType, [self::SELLING_TYPE_ONLINE, self::SELLING_TYPE_BOTH]);
    }

    /**
     * Get a badge HTML for the selling type
     *
     * @return string
     */
    public function getSellingTypeBadge()
    {
        $badges = [
            self::SELLING_TYPE_INSTORE => '<span class="badge bg-info"><i class="bx bx-store me-1"></i>' . __('global.instore_only') . '</span>',
            self::SELLING_TYPE_ONLINE => '<span class="badge bg-success"><i class="bx bx-globe me-1"></i>' . __('global.online_only') . '</span>',
            self::SELLING_TYPE_BOTH => '<span class="badge bg-primary"><i class="bx bx-infinite me-1"></i>' . __('global.instore_and_online') . '</span>',
        ];

        return $badges[$this->SellingType] ?? '';
    }

}

