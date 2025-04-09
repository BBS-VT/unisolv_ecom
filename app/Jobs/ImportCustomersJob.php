<?php

namespace App\Jobs;

use App\Imports\CustomerMasterImport;
use App\Models\Customer;
use App\Models\ImportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportCustomersJob implements ShouldQueue
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
        Log::info('Starting customer import process', ['filePath' => $this->filePath, 'importJobId' => $this->importJobId]);

        $importJob = ImportJob::findOrFail($this->importJobId);
        $importJob->update(['status' => ImportJob::STATUS_PROCESSING, 'started_at' => now()]);

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            Customer::truncate();

            $importer = new CustomerMasterImport($this->importJobId);
            Excel::import($importer, storage_path('app/' . $this->filePath));

            // Dispatch a separate job for cleanup operations
            CleanupCustomersData::dispatch($this->importJobId);

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            Storage::delete($this->filePath);

            Log::info('Customer import completed successfully');

        } catch (\Exception $e) {
            Log::error('Customer import failed', [
                'error' => $e->getMessage(),
                'file' => $this->filePath,
                'trace' => $e->getTraceAsString()
            ]);

            $importJob->markAsFailed($e->getMessage());

            throw $e;
        }







    }
}
