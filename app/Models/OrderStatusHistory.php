<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class OrderStatusHistory extends Model
{
    public $table = 'order_status_histories';

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

    public function oldStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'new_status_id');
    }

    public function changedBy()
    {
        return $this->morphTo('changed_by');
    }

    /**
     * Get formatted change description
     */
    public function getChangeDescriptionAttribute()
    {
        $oldName = $this->oldStatus->name ?? 'Unknown';
        $newName = $this->newStatus->name ?? 'Unknown';
        $changedBy = $this->changedBy->name ?? 'System';

        return "Changed from {$oldName} to {$newName} by {$changedBy}";
    }
}
