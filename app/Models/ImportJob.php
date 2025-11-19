<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'filename',
        'total_rows',
        'processed_rows',
        'successful_rows',
        'failed_rows',
        'items_updated',
        'company_id',
        'imported_by',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    /**
     * Get the user who started this import
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    /**
     * Get all stock transactions from this import
     */
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'reference_id')
            ->where('reference_type', 'ImportJob');
    }

    /**
     * Calculate the progress percentage
     *
     * @return float
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_rows <= 0) {
            return 0;
        }

        return min(100, round(($this->processed_rows / $this->total_rows) * 100, 2));
    }

    /**
     * Get duration in human readable format
     */
    public function getDurationAttribute()
    {
        if (!$this->started_at) {
            return null;
        }

        $end = $this->completed_at ?? now();
        return $this->started_at->diffForHumans($end, true);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => '<span class="badge bg-secondary"><i class="mdi mdi-clock-outline me-1"></i>Pending</span>',
            self::STATUS_PROCESSING => '<span class="badge bg-primary"><i class="mdi mdi-sync me-1 mdi-spin"></i>Processing</span>',
            self::STATUS_COMPLETED => '<span class="badge bg-success"><i class="mdi mdi-check-circle me-1"></i>Completed</span>',
            self::STATUS_FAILED => '<span class="badge bg-danger"><i class="mdi mdi-alert-circle me-1"></i>Failed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Update the progress of the import
     *
     * @param int $processedRows
     * @param int $successfulRows
     * @param int $failedRows
     * @return void
     */
    public function updateProgress(int $processedRows, int $successfulRows = null, int $failedRows = null)
    {
        $data = [
            'processed_rows' => $processedRows,
            'status' => self::STATUS_PROCESSING,
        ];

        if ($successfulRows !== null) {
            $data['successful_rows'] = $successfulRows;
        }

        if ($failedRows !== null) {
            $data['failed_rows'] = $failedRows;
        }

        $this->update($data);
    }

    /**
     * Increment items updated counter
     */
    public function incrementItemsUpdated()
    {
        $this->increment('items_updated');
    }

    /**
     * Mark the import as completed
     *
     * @return void
     */
    public function markAsCompleted()
    {
        $this->update([
            'processed_rows' => $this->total_rows,
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark the import as failed
     *
     * @param string $errorMessage
     * @return void
     */
    public function markAsFailed(string $errorMessage)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Scope for recent imports
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope for in-progress imports
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }
}
