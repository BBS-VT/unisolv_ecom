<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use \DateTimeInterface;

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
        'StockGroupName',
        'ParentID',
        'LastEditedBy',
        'created_at',
        'updated_at',
        'deleted_at',
        'status',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function specialdeals()
    {
        return $this->hasMany(SpecialDeals::class, 'StockGroupID');
    }

}
