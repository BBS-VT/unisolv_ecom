<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Imports\StockQuantitiesImport;
use App\Models\StockItemHoldings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
class ProcessStockQuantitiesImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $importJobId;
    protected $companyId;

    public $timeout = 3600;
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @param int $importJobId
     * @param int|null $companyId
     * @return void
     *
     */
    public function __construct(string $filePath, int $importJobId, ?int $companyId = null)
    {
        $this->filePath = $filePath;
        $this->importJobId = $importJobId;
        $this->companyId = $companyId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Starting Stock Quantities import process', [
            'file' => $this->filePath,
            'import_job_id' => $this->importJobId,
            'company_id' => $this->companyId
        ]);

        $importJob = ImportJob::findOrFail($this->importJobId);
        $importJob->update(['status' => ImportJob::STATUS_PROCESSING, 'started_at' => now()]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            StockItemHoldings::truncate();

            // Create an instance of the importer with progress tracking
            $importer = new StockQuantitiesImport($this->importJobId, $this->companyId);

            Excel::import($importer, storage_path('app/' . $this->filePath));

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            // Mark import as completed
            $importJob->markAsCompleted();

            Log::info('Stock Quantities import completed successfully');

        } catch (\Exception $e) {
            Log::error('Stock Quantities import failed', [
                'file' => $this->filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $importJob->markAsFailed($e->getMessage());

            throw $e;
        }
    }
}
