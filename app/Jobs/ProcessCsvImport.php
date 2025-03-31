<?php

namespace App\Jobs;

use App\Imports\StockMasterImport;
use App\Models\ImportJob;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProcessCsvImport implements ShouldQueue
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
     * @return void
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
        Log::info('Processing CSV import', ['file' => $this->filePath, 'import_job_id' => $this->importJobId]);

        $importJob = ImportJob::find($this->importJobId);
        $importJob->update(['status' => ImportJob::STATUS_PROCESSING, 'started_at' => now()]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Product::truncate();

            $importer = new StockMasterImport($this->importJobId);
            // use chunked import
            Excel::import($importer, storage_path('app/' . $this->filePath));

            UpdateProductFields::dispatch($this->importJobId);

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            Log::info('CSV import completed successfully');
        } catch (\Exception $e) {
            Log::error('CSV import failed', [
                'file' => $this->filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $importJob->markAsFailed($e->getMessage());

            throw $e;
        }

    }
}
