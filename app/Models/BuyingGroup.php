<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyingGroup extends Model
{
    public $table = 'buying_groups';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'BuyingGroupName',
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
        return $this->hasMany(SpecialDeals::class, 'BuyingGroupID');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'BuyingGroupName', 'BuyingGroupID');
    }
}
