<?php

namespace App\Jobs;

use App\Models\ImportJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupCustomersData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importJobId;

    public $timeout = 1800; // 30 minutes timeout
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param int $importJobId
     * @return void
     */
    public function __construct(int $importJobId)
    {
        $this->importJobId = $importJobId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $importJob = ImportJob::findOrFail($this->importJobId);
        Log::info('Starting post-import cleanup for customers data', ['import_job_id' => $this->importJobId]);

        try {
            // Disable foreign key checks at the beginning
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Process in batches to prevent timeouts
            $this->processBatchedUpdates($importJob);

            // Re-enable foreign key checks at the end
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            // Mark import as completed
            $importJob->markAsCompleted();

            Log::info('Customer data cleanup completed successfully');

        } catch (\Exception $e) {
            // Make sure to re-enable foreign key checks even on error
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            Log::error('Customer data cleanup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $importJob->markAsFailed($e->getMessage());

            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Process database updates in batches
     *
     * @param ImportJob $importJob
     * @return void
     */
    private function processBatchedUpdates(ImportJob $importJob)
    {
        $batchSize = 5000;
        $totalRecords = DB::table('customers')->count();
        $batches = ceil($totalRecords / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $offset = $i * $batchSize;

            $ids = DB::table('customers')
                ->select('id')
                ->offset($offset)
                ->limit($batchSize)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            // Basic field cleaning
            DB::table('customers')
                ->whereIn('id', $ids)
                ->update(['acc_main' => DB::raw('TRIM(acc_main)')]);

            DB::table('customers')
                ->whereIn('id', $ids)
                ->update(['acc_main' => DB::raw('LPAD(acc_main, 6, "0")')]);

            // Special cases
            DB::table('customers')
                ->whereIn('id', $ids)
                ->where('acc_sub', '0')
                ->update(['acc_sub' => '000']);

            // Field combinations
            DB::table('customers')
                ->whereIn('id', $ids)
                ->update(['acc_code' => DB::raw('CONCAT(acc_main, "-", acc_sub)')]);

            //  Null handling
            DB::table('customers')
                ->whereIn('id', $ids)
                ->whereNull('BillToCustomerID')
                ->update(['BillToCustomerID' => '9999']);

            DB::table('customers')
                ->whereIn('id', $ids)
                ->where('BuyingGroupID', '')
                ->update(['BuyingGroupID' => null]);

            DB::table('customers')
                ->whereIn('id', $ids)
                ->where(function ($query) {
                    $query->whereNull('SalesRepID')
                        ->orWhere('SalesRepID',  '')
                        ->orWhere('SalesRepID',  ' ');
                })
                ->update(['SalesRepID' => '9999']);

            // Update progress
            $progressPercentage = min(90 + ($i / $batches * 10), 99); // From 90% to 99%
            $processedRows = min($importJob->total_rows, (int)($importJob->total_rows * $progressPercentage / 100));
            $importJob->updateProgress($processedRows);

            usleep(100000); // 100ms pause
        }
    }
}
