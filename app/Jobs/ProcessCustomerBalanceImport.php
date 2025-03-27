<?php

namespace App\Jobs;

use App\Imports\CustomerBalanceImport;
use App\Models\CustomerBalance;
use App\Models\ImportJob;
use Illuminate\Bus\Queueable;
use App\Jobs\CleanupCustomerBalances;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProcessCustomerBalanceImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $importJobId;

    public $timeout = 3600;
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @param int $importJobId
     */
    public function __construct(string $filePath, int $importJobId)
    {
        $this->filePath = $filePath;
        $this->importJobId = $importJobId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Starting Customer Balance import process', ['file' => $this->filePath, 'import_job_id' => $this->importJobId]);

        $importJob = ImportJob::findOrFail($this->importJobId);
        $importJob->update(['status' => ImportJob::STATUS_PROCESSING, 'started_at' => now()]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            CustomerBalance::truncate();

            // Create an instance of the importer with progress tracking
            $importer = new CustomerBalanceImport($this->importJobId);

            // Use the chunked import
            Excel::import($importer, storage_path('app/' . $this->filePath));

            // Dispatch a separate job for the cleanup operations
            CleanupCustomerBalances::dispatch($this->importJobId);

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            Log::info('Customer Balance import completed successfully');

        } catch (\Exception $e) {
            Log::error('Customer Balance import failed', [
                'file' => $this->filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $importJob->markAsFailed($e->getMessage());

            throw $e; // Re-throw to trigger job retry
        }
    }
}
