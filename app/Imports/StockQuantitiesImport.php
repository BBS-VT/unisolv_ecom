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
    protected $totalRows = 0;
    protected $processedRows = 0;

    /**
     * @param int $importJobId
     */
    public function __construct(int $importJobId)
    {
        $this->importJobId = $importJobId;
    }

    /**
     * @ return int
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
            BeforeImport::class => function(BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                // Extract the number from the first sheet (index 0)
                $this->totalRows = isset($totalRows['Worksheet']) ? $totalRows['Worksheet'] : (isset($totalRows['Sheet1']) ? $totalRows['Sheet1'] : 0);
                $this->totalRows = max(0, $this->totalRows - 1); // Subtract header row

                // Update total rows in import job
                ImportJob::where('id', $this->importJobId)->update([
                    'total_rows' => $this->totalRows,
                ]);
            },
            AfterImport::class => function(AfterImport $event) {
                // Mark as completed in the import tracker
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->totalRows);
                }
            },
            AfterSheet::class => function(AfterSheet $event) {
                // Update progress after each sheet
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->processedRows);
                }
            },
        ];
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $chunks = $rows->chunk(250);

        foreach ($chunks as $chunk) {
            $holdings = [];

            foreach ($chunk as $row) {
                if (isset($row[0]) && $row[0]) {
                    $stockCode = trim($row[0]);

                    $holdings[] = [
                        'StockCode'         => $stockCode,
                        'LocationCode'      => $row[104] ?? '0000',
                        'QuantityOnHand'    => $row[10] ?? 0,
                        'BinLocation'       => $row[6] ?? '',
                        'LastCostPrice'     => $row[25] ?? 0,
                        'ReorderLevel'      => $row[16] ?? 0,
                        'TargetStockLevel'  => $row[18] ?? 0,
                        'LastEditedBy'      => Auth::check() ? Auth::id() : 1,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];

                    $this->processedRows++;
                }
            }

            if (!empty($holdings)) {
                // Batch insert the holdings
                DB::table('stock_item_holdings')->insert($holdings);
            }

            // Update progress every 1000 rows
            if ($this->processedRows % 1000 === 0) {
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->processedRows);
                }
            }
        }

    }
}
