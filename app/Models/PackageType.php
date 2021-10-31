<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageType extends Model
{
    public $table = 'package_types';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'PackageTypeName',
        'LastEditedBy'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'StockCode', 'StockCode');
    }
}
