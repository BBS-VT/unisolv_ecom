<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'old_status_id',
        'new_status_id',
        'changed_by_type',
        'changed_by_id',
        'notes',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusName($statusId)
    {
        return match($statusId) {
            1 => 'New',
            2 => 'Downloaded',
            3 => 'Delivery',
            4 => 'Invoiced',
            5 => 'On Hold',
            default => 'Unknown'
        };
    }
}
