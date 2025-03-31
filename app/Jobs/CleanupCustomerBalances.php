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
class CleanupCustomerBalances implements ShouldQueue
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
        Log::info('Starting post-import cleanup for customer balances', ['import_job_id' => $this->importJobId]);

        try {
            // Disable foreign key checks at the beginning
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            // Process in batches to prevent timeouts
            $this->processBatchedUpdates($importJob);

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            // Mark import as completed
            $importJob->markAsCompleted();

            Log::info('Customer balance cleanup completed successfully');

        } catch (\Exception $e) {

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            Log::error('Customer balance cleanup failed', [
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
        $totalRecords = DB::table('customer_balances')->count();
        $batches = ceil($totalRecords / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $offset = $i * $batchSize;

            $ids = DB::table('customer_balances')
                ->select('id')
                ->offset($offset)
                ->limit($batchSize)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            // Update AccMain - trim whitespace
            DB::table('customer_balances')
                ->whereIn('id', $ids)
                ->update(['AccMain' => DB::raw('TRIM(AccMain)')]);

            // Update AccMain - pad with leading zeros
            DB::table('customer_balances')
                ->whereIn('id', $ids)
                ->update(['AccMain' => DB::raw('LPAD(AccMain, 6, "0")')]);

            // Update AccSub - set to 000 if it's 0
            DB::table('customer_balances')
                ->whereIn('id', $ids)
                ->where('AccSub', '0')
                ->update(['AccSub' => '000']);

            // Update AccSub - pad with leading zeros
            DB::table('customer_balances')
                ->whereIn('id', $ids)
                ->update(['AccSub' => DB::raw('LPAD(AccSub, 3, "0")')]);

            // Update AccCode - concatenate AccMain and AccSub
            DB::table('customer_balances')
                ->whereIn('id', $ids)
                ->update(['AccCode' => DB::raw('CONCAT(AccMain, "-", AccSub)')]);

            // Update progress
            $progressPercentage = min(90 + ($i / $batches * 10), 99);
            $processedRows = min($importJob->total_rows, (int)($importJob->total_rows * $progressPercentage / 100));
            $importJob->updateProgress($processedRows);


            usleep(100000);
        }
    }
}
