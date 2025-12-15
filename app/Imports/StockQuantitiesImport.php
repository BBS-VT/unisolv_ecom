<?php

namespace App\Imports;

use App\Models\ImportJob;
use App\Models\StockItemHoldings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;

class StockQuantitiesImport implements ToCollection, WithChunkReading, WithStartRow, WithEvents
{
    protected $importJobId;
    protected $companyId;
    protected $totalRows = 0;
    protected $processedRows = 0;
    protected $successfulRows = 0;
    protected $failedRows = 0;
    protected $itemsUpdated = 0;

    /**
     * @param  int  $importJobId
     * @param int|null $companyId
     */
    public function __construct(int $importJobId, ?int $companyId = null)
    {
        $this->importJobId = $importJobId;
        $this->companyId = $companyId;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Register event listeners for Excel import
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                // Extract the number from the first sheet (index 0)
                $this->totalRows = isset($totalRows['Worksheet']) ? $totalRows['Worksheet'] : (isset($totalRows['Sheet1']) ? $totalRows['Sheet1'] : 0);
                $this->totalRows = max(0, $this->totalRows - 1); // Subtract header row

                // Update total rows in import job
                ImportJob::where('id', $this->importJobId)->update([
                    'total_rows' => $this->totalRows,
                    'status' => ImportJob::STATUS_PROCESSING,
                ]);

                Log::info("Stock import started", [
                    'import_job_id' => $this->importJobId,
                    'total_rows' => $this->totalRows
                ]);
            },

            AfterImport::class => function (AfterImport $event) {
                // Mark as completed in the import tracker
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->update([
                        'processed_rows' => $this->processedRows,
                        'successful_rows' => $this->successfulRows,
                        'failed_rows' => $this->failedRows,
                        'items_updated' => $this->itemsUpdated,
                    ]);
                    $importJob->markAsCompleted();

                    Log::info("Stock import completed", [
                        'import_job_id' => $this->importJobId,
                        'processed' => $this->processedRows,
                        'successful' => $this->successfulRows,
                        'failed' => $this->failedRows,
                        'items_updated' => $this->itemsUpdated
                    ]);
                }
            },

            AfterSheet::class => function (AfterSheet $event) {
                // Update progress after each sheet
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress(
                        $this->processedRows,
                        $this->successfulRows,
                        $this->failedRows
                    );
                }
            },
        ];
    }

    /**
     * @param  Collection  $rows
     */
    public function collection(Collection $rows)
    {
        $importJob = ImportJob::find($this->importJobId);
        $userId = Auth::check() ? Auth::id() : ($importJob->imported_by ?? 1);

        $companyId = $this->companyId ?? $importJob->company_id;

        foreach ($rows as $index => $row) {
            try {
                // Validate that we have a stock code
                if (!isset($row[0]) || !$row[0]) {
                    $this->failedRows++;
                    $this->processedRows++;
                    continue;
                }

                $stockCode = trim($row[0]);
                $locationCode = $row[109] ?? '0000';
                $quantityOnHand = $row[10] ?? 0;

                // Use the updateFromImport method which logs transactions
                $wasUpdated = StockItemHoldings::updateFromImport(
                    $stockCode,
                    $locationCode,
                    $quantityOnHand,
                    $userId,
                    'ImportJob',
                    $this->importJobId,
                    "Import: {$importJob->filename}",
                    $companyId
                );

                // Also update other fields that don't affect quantity
                // Use updateOrCreate to handle these additional fields
                StockItemHoldings::where('StockCode', $stockCode)
                    ->where('LocationCode', $locationCode)
                    ->update([
                        'BinLocation' => $row[6] ?? '',
                        'LastCostPrice' => $row[25] ?? 0,
                        'ReorderLevel' => $row[16] ?? 0,
                        'TargetStockLevel' => $row[17] ?? 0,
                        'LastEditedBy' => $userId,
                        'updated_at' => now(),
                    ]);

                if ($wasUpdated) {
                    $this->itemsUpdated++;
                }

                $this->successfulRows++;

            } catch (\Exception $e) {
                $this->failedRows++;

                Log::error("Stock import row failed", [
                    'import_job_id' => $this->importJobId,
                    'row_number' => $index + 2, // +2 because of 0-index and header row
                    'stock_code' => $stockCode ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }

            $this->processedRows++;

            // Update progress every 500 rows (more frequent for better UX)
            if ($this->processedRows % 500 === 0) {
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->update([
                        'processed_rows' => $this->processedRows,
                        'successful_rows' => $this->successfulRows,
                        'failed_rows' => $this->failedRows,
                        'items_updated' => $this->itemsUpdated,
                    ]);
                }
            }
        }
    }
}
