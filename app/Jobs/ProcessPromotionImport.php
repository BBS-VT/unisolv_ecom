<?php

namespace App\Jobs;

use App\Imports\PromotionImport;
use App\Models\ImportJob;
use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ProcessPromotionImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected int $importJobId;
    protected bool $updateExisting;

    public $timeout = 3600;
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @param int $importJobId
     * @param bool $updateExisting
     */
    public function __construct(string $filePath, int $importJobId, bool $updateExisting = true)
    {
        $this->filePath = $filePath;
        $this->importJobId = $importJobId;
        $this->updateExisting = $updateExisting;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Log::info('Starting Promotion import process', [
            'file' => $this->filePath,
            'import_job_id' => $this->importJobId,
        ]);

        $importJob = ImportJob::findOrFail($this->importJobId);
        $importJob->update(['status' => ImportJob::STATUS_PROCESSING, 'started_at' => now()]);

        try {
            // updateExisting is passed straight through - this is what the
            // web UI's "update existing" checkbox controls, no preview step involved.
            $importer = new PromotionImport($this->updateExisting);

            Excel::import($importer, storage_path('app/' . $this->filePath));

            $result = $importer->getResults();

            // Imported rows are always created as 'active' regardless of their
            // dates, so sweep anything already past its end date to 'expired'.
            $expiredCount = Promotion::markExpiredPromotions();
            if ($expiredCount > 0) {
                Log::info('Marked promotions as expired after API import', [
                    'import_job_id' => $this->importJobId,
                    'expired_count' => $expiredCount,
                ]);
            }

            $importJob->update([
                'total_rows'       => $result['processed_rows'],
                'processed_rows'   => $result['processed_rows'],
                'successful_rows'  => $result['successful_rows'],
                'failed_rows'      => $result['error_count'],
                'status'           => ImportJob::STATUS_COMPLETED,
                'completed_at'     => now(),
            ]);

            Log::info('Promotion import completed', [
                'import_job_id'    => $this->importJobId,
                'batch_id'         => $result['batch_id'],
                'processed_rows'   => $result['processed_rows'],
                'successful_rows'  => $result['successful_rows'],
                'error_count'      => $result['error_count'],
                'warning_count'    => $result['warning_count'],
            ]);
        } catch (Exception $e) {
            Log::error('Promotion import failed', [
                'file' => $this->filePath,
                'import_job_id' => $this->importJobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $importJob->markAsFailed($e->getMessage());

            throw $e; // Re-throw to trigger job retry
        }
    }
}
