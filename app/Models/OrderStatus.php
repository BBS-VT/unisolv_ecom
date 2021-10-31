<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public $table = 'order_status';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'colour',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id', 'OrderStatusID');
    }
}
