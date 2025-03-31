<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateProductFields implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800;

    public function handle()
    {
        Log::info('Starting product field updates');

        try {
            // Run the updates in smaller batches to prevent timeouts
            $this->updateFieldInBatches('Barcode');
            $this->updateFieldInBatches('StockCode');
            $this->updateFieldInBatches('SupplierID');
            $this->updateFieldInBatches('AltBarcode');

            Log::info('Product field updates completed successfully');
        } catch (\Exception $e) {
            Log::error('Product field updates failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Update a field in batches to prevent timeouts
     *
     * @param string $fieldName
     * @return void
     */
    private function updateFieldInBatches(string $fieldName)
    {
        $batchSize = 5000;
        $totalProducts = DB::table('products')->count();
        $batches = ceil($totalProducts / $batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $offset = $i * $batchSize;

            $productIds = DB::table('products')
                ->select('id')
                ->offset($offset)
                ->limit($batchSize)
                ->pluck('id');

            if ($productIds->isEmpty()) {
                break;
            }

            DB::table('products')
                ->whereIn('id', $productIds)
                ->update([
                    $fieldName => DB::raw("TRIM($fieldName)"),
                    'updated_at' => now()
                ]);
        }
    }
}
