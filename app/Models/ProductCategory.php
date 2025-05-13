<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use \DateTimeInterface;
use Illuminate\Support\Str;

class ProductCategory extends Model implements HasMedia
{

    use SoftDeletes, InteractsWithMedia;

    protected $appends = [
        'photo',
    ];

    public $table = 'product_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'ParentID',
        'CategoryCode',
        'StockGroupName',
        'slug',
        'status',
        'LastEditedBy',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->StockGroupName);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('StockGroupName') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->StockGroupName);
            }
        });
    }

    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $value = Str::slug($this->StockGroupName);
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)
            ->orWhere('id', $value)
            ->firstOrFail();
    }

    public function registerMediaConversions(Media $media = null) : void
    {
        $this->addMediaConversion('thumb')->width(50)->height(50);
    }

    public function getPhotoAttribute()
    {
        $file = $this->getMedia('photo')->last();

        if ($file) {
            $file->url      = $file->getUrl();
            $file->thumbail = $file->getUrl('thumb');
        }

        return $file;
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'ParentID');
        //$parentCode = $this->getParentCode();
        //if (!$parentCode) {
        //    return null;
        //}
        //return self::where('CategoryCode', $parentCode)->first();
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'ParentID');
        //if (strlen($this->CategoryCode) != 4 || substr($this->CategoryCode, 2) != '00') {
        //    return collect([]);
        //}

        //$parentPrefix = substr($this->CategoryCode, 0, 2);
        //return self::where('CategoryCode', 'like', $parentPrefix . '%')
        //    ->where('CategoryCode', '!=', $this->CategoryCode)
        //    ->orderBy('CategoryCode')
        //    ->get();
    }

    public function scopeMainCategories($query)
    {
        return $query->whereNull('ParentID');
    }

    public function scopeSubcategories($query, $parentCode)
    {
        return $query->where('ParentID', $parentCode);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function specialdeals()
    {
        return $this->hasMany(SpecialDeals::class, 'StockGroupID');
    }

    public function getParentCode()
    {
        if (strlen($this->CategoryCode) !=4) {
            return null;
        }

        $parentCode = substr($this->CategoryCode, 0, 2) . '00';

        if ($this->CategoryCode == $parentCode) {
            return null;
        }

        return $parentCode;
    }

    public function isParent()
    {
        return strlen($this->CategoryCode) == 4 && substr($this->CategoryCode, 2) == '00';
    }

    public function isChild()
    {
        return strlen($this->CategoryCode) == 4 && substr($this->CategoryCode, 2) != '00';
    }

}
