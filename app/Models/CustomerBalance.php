<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBalance extends Model
{
    public $table = 'customer_balances';

    protected $primaryKey = 'AccMain';
    protected $keyType = 'string';

    protected $fillable = [
        'AccMain',
        'AccSub',
        'AccCode',
        'AgedBalance1',
        'AgedBalance2',
        'AgedBalance3',
        'AgedBalance4',
        'AgedBalance5',
        'AgedBalance6',
        'LastEditedBy'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'acc_main', 'AccMain');
    }
}
