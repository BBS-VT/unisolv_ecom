<?php

namespace App\Imports;

use App\Models\ImportJob;
use App\Models\Product;
use App\Models\ProductCategory;
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

class StockMasterImport implements ToCollection, WithChunkReading, WithStartRow, WithEvents
{
    protected $importJobId;
    protected $totalRows = 0;
    protected $processedRows = 0;
    protected $successfulRows = 0;
    protected $failedRows = 0;
    protected $categoryCache = [];
    protected $errorRows = [];
    protected $importedProductCodes = [];

    public function __construct(int $importJobId)
    {
        $this->importJobId = $importJobId;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                $this->totalRows = isset($totalRows['Worksheet']) ? $totalRows['Worksheet'] : (isset($totalRows['Sheet1']) ? $totalRows['Sheet1'] : 0);
                $this->totalRows = max(0, $this->totalRows - 1);

                ImportJob::where('id', $this->importJobId)->update([
                    'total_rows' => $this->totalRows,
                    'status' => ImportJob::STATUS_PROCESSING,
                ]);

                $this->preloadCategories();
                $this->preloadExistingProducts();

                Log::info("Starting product import", [
                    'import_job_id' => $this->importJobId,
                    'total_rows' => $this->totalRows
                ]);
            },

            AfterImport::class => function(AfterImport $event) {
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    // Update final counts
                    $importJob->update([
                        'processed_rows' => $this->processedRows,
                        'successful_rows' => $this->successfulRows,
                        'failed_rows' => $this->failedRows,
                    ]);

                    // Add error summary if needed
                    if (!empty($this->errorRows)) {
                        $errorSummary = "Errors on rows: " . implode(', ', array_slice($this->errorRows, 0, 20));
                        if (count($this->errorRows) > 20) {
                            $errorSummary .= " and " . (count($this->errorRows) - 20) . " more";
                        }
                        $importJob->update(['error_message' => $errorSummary]);
                    }

                    // Mark as completed
                    $importJob->markAsCompleted();

                    Log::info("Product import completed", [
                        'import_job_id' => $this->importJobId,
                        'processed' => $this->processedRows,
                        'successful' => $this->successfulRows,
                        'failed' => $this->failedRows
                    ]);
                }
            },

            AfterSheet::class => function(AfterSheet $event) {
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

    protected function preloadExistingProducts()
    {
        $existingProducts = DB::table('products')->select('StockCode')->get();
        foreach ($existingProducts as $product) {
            $this->importedProductCodes[$product->StockCode] = true;
        }
        Log::info("Preloaded " . count($this->importedProductCodes) . " existing product codes");
    }

    protected function preloadCategories()
    {
        $categories = ProductCategory::all();
        foreach ($categories as $category) {
            $this->categoryCache[$category->CategoryCode] = [
                'id' => $category->id,
                'parent_id' => $category->ParentID,
                'name' => $category->StockGroupName
            ];
        }
        Log::info("Preloaded " . count($this->categoryCache) . " categories");
    }

    /**
     * Parse category code into main and sub category codes
     * Example: "0101" -> main: "0100", sub: "0101"
     */
    protected function parseCategoryCode($categoryCode)
    {
        if (!$categoryCode || strlen($categoryCode) !== 4) {
            return ['main' => null, 'sub' => null];
        }

        // First 2 digits are main category, pad with 00
        $mainCode = substr($categoryCode, 0, 2) . '00';

        return [
            'main' => $mainCode,
            'sub' => $categoryCode
        ];
    }

    /**
     * Get category IDs from the 4-digit category code
     * Returns both main and sub category IDs
     */
    protected function getCategoryIds($categoryCode)
    {
        $parsed = $this->parseCategoryCode($categoryCode);
        $categoryIds = [];

        // Get main category ID
        if ($parsed['main'] && isset($this->categoryCache[$parsed['main']])) {
            $categoryIds[] = $this->categoryCache[$parsed['main']]['id'];
        }

        // Get sub category ID (only if different from main)
        if ($parsed['sub'] && $parsed['sub'] !== $parsed['main'] && isset($this->categoryCache[$parsed['sub']])) {
            $categoryIds[] = $this->categoryCache[$parsed['sub']]['id'];
        }

        return array_unique($categoryIds);
    }

    protected function sanitizeValue($value, $fieldName = '', $required = false)
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        if ($value === null || $value === '') {
            return $required ? 0 : null;
        }

        if ($fieldName === 'TaxRateID' && ($value === null || $value === '' || $value === ' ')) {
            Log::warning("Empty TaxRateID found, using default value 1");
            return 1;
        }

        return $value;
    }

    public function collection(Collection $rows)
    {
        Log::info("Processing chunk", [
            'rows_in_chunk' => count($rows),
            'current_progress' => $this->processedRows,
            'total_rows' => $this->totalRows
        ]);

        $chunks = $rows->chunk(250);

        foreach ($chunks as $chunkIndex => $chunk) {
            $products = [];
            $productCategories = []; // Store product-category relationships
            $newProductCodes = [];

            foreach ($chunk as $rowIndex => $row) {
                $rowNumber = $this->processedRows + 1;

                try {
                    if (isset($row[0]) && $row[0]) {
                        $productCode = trim($row[0] ?? '');
                        $categoryCode = trim($row[2] ?? '');

                        // Skip duplicates
                        if (isset($this->importedProductCodes[$productCode]) || isset($newProductCodes[$productCode])) {
                            Log::info("Skipping duplicate product: {$productCode} (row {$rowNumber})");
                            $this->processedRows++;
                            continue;
                        }

                        $newProductCodes[$productCode] = true;
                        $taxRateID = $this->sanitizeValue($row[7] ?? null, 'TaxRateID', true);

                        $products[] = [
                            'company_id'         => '1',
                            'StockItemName'      => $row[1] ?? '',
                            'StockCode'          => $productCode,
                            'SupplierID'         => $this->sanitizeValue($row[8]),
                            'TaxRateID'          => $taxRateID,
                            'Size'               => '1',
                            'Packsize'           => $this->sanitizeValue($row[4], 'Packsize', false) ?? '0',
                            'refer_code'         => $this->sanitizeValue($row[9]),
                            'Barcode'            => $this->sanitizeValue($row[11]),
                            'AltBarcode'         => $this->sanitizeValue($row[13]),
                            'AverageCostPrice'   => $this->sanitizeValue($row[24], 'AverageCostPrice', false) ?? 0,
                            'SellingPrice'       => $this->sanitizeValue($row[31], 'SellingPrice', false) ?? 0,
                            'SellingPrice2'      => $this->sanitizeValue($row[32], 'SellingPrice2', false) ?? 0,
                            'SellingPrice3'      => $this->sanitizeValue($row[33], 'SellingPrice3', false) ?? 0,
                            'SellingPrice4'      => $this->sanitizeValue($row[34], 'SellingPrice4', false) ?? 0,
                            'SearchDetails'      => $this->sanitizeValue($row[30]),
                            'DiscountPercentage' => $this->sanitizeValue($row[44], 'DiscountPercentage', false) ?? 0,
                            'status'             => '1',
                            'LastEditedBy'       => Auth::check() ? Auth::id() : 1,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ];

                        // Store category relationship if category code exists
                        if (!empty($categoryCode)) {
                            $productCategories[] = [
                                'product_code' => $productCode,
                                'category_code' => $categoryCode,
                                'row_number' => $rowNumber
                            ];
                        }

                        $this->successfulRows++;
                    }
                } catch (\Exception $e) {
                    $this->errorRows[] = $rowNumber;
                    $this->failedRows++;
                    Log::error("Error processing row {$rowNumber}", [
                        'error' => $e->getMessage(),
                        'row_data' => $row ? json_encode(array_slice($row->toArray(), 0, 5)) : 'NULL'
                    ]);
                }

                $this->processedRows++;
            }

            // Insert/Update products
            if (!empty($products)) {
                try {
                    foreach ($products as $product) {
                        $stockCode = $product['StockCode'];

                        try {
                            DB::table('products')->updateOrInsert(
                                ['StockCode' => $stockCode],
                                array_diff_key($product, ['StockCode' => true])
                            );

                            $this->importedProductCodes[$stockCode] = true;

                        } catch (\Exception $e) {
                            Log::error("Error upserting product {$stockCode}", [
                                'error' => $e->getMessage()
                            ]);
                            $this->failedRows++;
                        }
                    }

                    // Now handle category relationships
                    foreach ($productCategories as $item) {
                        try {
                            $product = DB::table('products')
                                ->where('StockCode', $item['product_code'])
                                ->first();

                            if (!$product) {
                                Log::warning("Product not found after insert: {$item['product_code']}");
                                continue;
                            }

                            // Get both main and sub category IDs
                            $categoryIds = $this->getCategoryIds($item['category_code']);

                            if (empty($categoryIds)) {
                                Log::warning("No categories found for code: {$item['category_code']} (product: {$item['product_code']})");
                                continue;
                            }

                            // CRITICAL: Delete existing relationships first to prevent duplicates
                            DB::table('product_product_category')
                                ->where('product_id', $product->id)
                                ->delete();

                            // Insert new relationships
                            foreach ($categoryIds as $categoryId) {
                                DB::table('product_product_category')->insert([
                                    'product_id' => $product->id,
                                    'product_category_id' => $categoryId
                                ]);

                                Log::debug("Linked product {$item['product_code']} to category ID {$categoryId}");
                            }

                        } catch (\Exception $e) {
                            Log::error("Error linking product to category", [
                                'product_code' => $item['product_code'],
                                'category_code' => $item['category_code'],
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    Log::error("Error processing product chunk", [
                        'chunk_index' => $chunkIndex,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update progress periodically
            if ($this->processedRows % 500 === 0) {
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress(
                        $this->processedRows,
                        $this->successfulRows,
                        $this->failedRows
                    );
                }
            }
        }
    }
}
