<?php

namespace App\Imports;

use App\Models\ImportJob;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;

class StockMasterImport implements ToCollection, WithChunkReading, WithStartRow, WithEvents
{
    protected $importJobId;
    protected $totalRows = 0;
    protected $processedRows = 0;
    protected $categoryCache = [];

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
     * @ return int
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

                $this->preloadCategories();
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
     * Preload categories into memory for faster access
     */
    protected function preloadCategories()
    {
        $categories = ProductCategory::all();
        foreach ($categories as $category) {
            $this->categoryCache[$category->CategoryCode] = $category->id;
        }

    }

    /**
     * Get category ID from category code using cache
     *
     * @param string $categoryCode
     * @return int|null
     */
    protected function getCategoryId($categoryCode)
    {
        if (isset($this->categoryCache[$categoryCode])) {
            return $this->categoryCache[$categoryCode];
        }

        $category = ProductCategory::where('CategoryCode', $categoryCode)->first();

        if ($category) {
            $this->categoryCache[$categoryCode] = $category->id;
            return $category->id;
        }

        return null;
    }

    /**
     * @param  Collection  $rows
     * @return void
     */

    public function collection(Collection $rows)
    {
        $chunks = $rows->chunk(250); // Further chunk for database insertion
        $productIdMap = [];

        foreach ($chunks as $chunk) {
            $products = [];
            $rowProductCodes = [];

            $productCode = $row[0] ?? '';
            $categoryCode = $row[2] ?? '';

            foreach ($chunk as $row) {
                if (isset($row[0]) && $row[0]) { // Check for empty rows
                    $products[] = [
                        'company_id'         => '1',
                        'StockItemName'      => $row[1] ?? '',
                        'StockCode'          => $productCode,
                        'SupplierID'         => $row[8] ?? '',
                        'TaxRateID'          => $row[7] ?? '',
                        'Size'               => '1',
                        'PackSize'           => $row[4] ?? '0',
                        'Barcode'            => $row[11] ?? '',
                        'AltBarcode'         => $row[13] ?? '',
                        'AverageCostPrice'   => $row[24] ?? 0,
                        'SellingPrice'       => $row[31] ?? 0,
                        'SellingPrice2'      => $row[32] ?? 0,
                        'SellingPrice3'      => $row[33] ?? 0,
                        'SellingPrice4'      => $row[34] ?? 0,
                        'SearchDetails'      => $row[30] ?? '',
                        'DiscountPercentage' => $row[44] ?? 0,
                        'status'             => '1',
                        'LastEditedBy'       => Auth::check() ? Auth::id() : 1,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];

                    if (!empty($categoryCode)) {
                        $rowProductCodes[] = [
                            'product_code' => $productCode,
                            'category_code' => $categoryCode,
                        ];
                    }

                    $this->processedRows++;
                }
            }

            if (!empty($products)) {
                DB::table('products')->insert($products);

                // Get ID's of inserted products
                foreach ($rowProductCodes as $item) {
                    $product = DB::table('products')->where('StockCode', $item['product_code'])->first();
                    if ($product) {
                        $categoryId = $this->getCategoryId($item['category_code']);

                        if ($categoryId) {
                            DB::table('product_product_category')->updateOrInsert(
                                [
                                    'product_id' => $product->id,
                                    'product_category_id' => $categoryId,
                                ],
                            );
                        }
                    }
                }
            }

            if ($this->processedRows % 1000 === 0) {
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->processedRows);
                }
            }
        }
    }

}
