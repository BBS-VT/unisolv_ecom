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
     * Update the progress of the import
     *
     * @param int $processedRows
     * @return void
     */
    public function updateProgress(int $processedRows)
    {
        $this->update([
            'processed_rows' => $processedRows,
            'status' => self::STATUS_PROCESSING,
        ]);
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
}
