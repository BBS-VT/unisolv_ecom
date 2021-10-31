<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    public $table = 'customer_categories';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'CustomerCategoryName',
        'ValidFrom',
        'ValidTo',
        'LastEditedBy',
    ];

    public function lastedited()
    {
        return $this->hasOne('User', 'LastEditedBy');
    }

    public function specialdeals()
    {
        return $this->hasMany(SpecialDeals::class, 'CustomerCategoryID');
    }
}
