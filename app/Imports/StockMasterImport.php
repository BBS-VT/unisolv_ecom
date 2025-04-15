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
    protected $categoryCache = [];
    protected $errorRows = [];
    protected $importedProductCodes = []; // Track already imported product codes

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
        return 500;
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

                // Pre-load categories into cache to improve performance
                $this->preloadCategories();

                // Pre-load existing product codes to prevent duplicates
                $this->preloadExistingProducts();

                Log::info("Starting product import with {$this->totalRows} rows");
            },
            AfterImport::class => function(AfterImport $event) {
                // Mark as completed in the import tracker
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->totalRows);

                    // Add error information to notes if any
                    if (!empty($this->errorRows)) {
                        $errorSummary = "Errors occurred on " . count($this->errorRows) . " rows: ";
                        $errorSummary .= implode(', ', array_slice($this->errorRows, 0, 10));

                        if (count($this->errorRows) > 10) {
                            $errorSummary .= " and " . (count($this->errorRows) - 10) . " more";
                        }

                        $importJob->notes = $errorSummary;
                        $importJob->save();
                    }

                    Log::info("Product import completed. Processed {$this->processedRows} rows with " . count($this->errorRows) . " errors");
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
     * Preload existing products to avoid duplicates
     */
    protected function preloadExistingProducts()
    {
        // Get all existing product codes
        $existingProducts = DB::table('products')->select('StockCode')->get();
        foreach ($existingProducts as $product) {
            $this->importedProductCodes[$product->StockCode] = true;
        }

        Log::info("Preloaded " . count($this->importedProductCodes) . " existing product codes");
    }

    /**
     * Preload categories into memory to avoid repeated database lookups
     */
    protected function preloadCategories()
    {
        // Get all categories and index them by category_code for quick lookups
        $categories = ProductCategory::all();
        foreach ($categories as $category) {
            $this->categoryCache[$category->CategoryCode] = $category->id;
        }

        Log::info("Preloaded " . count($this->categoryCache) . " categories");
    }

    /**
     * Get category ID from category code using cache
     *
     * @param string $categoryCode
     * @return int|null
     */
    protected function getCategoryId($categoryCode)
    {
        // Return from cache if exists
        if (isset($this->categoryCache[$categoryCode])) {
            return $this->categoryCache[$categoryCode];
        }

        // If not in cache, try to find in database
        $category = ProductCategory::where('CategoryCode', $categoryCode)->first();

        if ($category) {
            // Add to cache for future lookups
            $this->categoryCache[$categoryCode] = $category->id;
            return $category->id;
        }

        return null;
    }

    /**
     * Clean and validate value
     *
     * @param mixed $value
     * @param string $fieldName
     * @param bool $required
     * @return mixed
     */
    protected function sanitizeValue($value, $fieldName = '', $required = false)
    {
        // Trim whitespace if string
        if (is_string($value)) {
            $value = trim($value);
        }

        // Handle empty values
        if ($value === null || $value === '') {
            return $required ? 0 : null; // Return default value if required
        }

        // Special handling for TaxRateID field
        if ($fieldName === 'TaxRateID' && ($value === null || $value === '' || $value === ' ')) {
            // Force default tax ID value instead of letting it be null
            Log::warning("Empty TaxRateID found, using default value 1");
            return 1;
        }

        return $value;
    }

    public function collection(Collection $rows)
    {
        Log::info("Processing chunk of " . count($rows) . " rows. Current progress: {$this->processedRows}/{$this->totalRows}");

        $chunks = $rows->chunk(250); // Further chunk for database insertion

        foreach ($chunks as $chunkIndex => $chunk) {
            $products = [];
            $rowProductCodes = []; // To keep track of product codes in this chunk
            $newProductCodes = []; // Track new product codes in this chunk

            foreach ($chunk as $rowIndex => $row) {
                $rowNumber = $this->processedRows + $rowIndex + 1;

                try {
                    if (isset($row[0]) && $row[0]) { // Check for empty rows
                        $productCode = trim($row[0] ?? '');
                        $categoryCode = trim($row[2] ?? ''); // Get category code from column 2

                        // Skip if we've already imported this product code in this import job
                        // or if it already exists in the database
                        if (isset($this->importedProductCodes[$productCode])) {
                            Log::info("Skipping duplicate product code: {$productCode} (row {$rowNumber})");
                            continue;
                        }

                        // Skip if we've already processed this product code in this chunk
                        if (isset($newProductCodes[$productCode])) {
                            Log::info("Skipping duplicate product code within chunk: {$productCode} (row {$rowNumber})");
                            continue;
                        }

                        // Mark as processed
                        $newProductCodes[$productCode] = true;

                        // Special handling for TaxRateID
                        $taxRateID = $this->sanitizeValue($row[7] ?? null, 'TaxRateID', true);

                        $products[] = [
                            'company_id'         => '1',
                            'StockItemName'      => $row[1] ?? '',
                            'StockCode'          => $productCode,
                            'SupplierID'         => $this->sanitizeValue($row[8]),
                            'TaxRateID'          => $taxRateID,
                            'Size'               => '1',
                            'PackSize'           => $this->sanitizeValue($row[4], 'PackSize', false) ?? '0',
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

                        // Store product code and category code for pivot table insertion
                        if (!empty($categoryCode)) {
                            $rowProductCodes[] = [
                                'product_code' => $productCode,
                                'category_code' => $categoryCode,
                                'row_number' => $rowNumber
                            ];
                        }

                        $this->processedRows++;
                    }
                } catch (\Exception $e) {
                    $this->errorRows[] = $rowNumber;
                    Log::error("Error processing row {$rowNumber}: " . $e->getMessage(), [
                        'row_data' => $row ? json_encode($row) : 'NULL',
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if (!empty($products)) {
                try {
                    // Use updateOrInsert to prevent duplicates
                    foreach ($products as $product) {
                        $stockCode = $product['StockCode'];

                        try {
                            // Check if product already exists
                            $existingProduct = DB::table('products')
                                ->where('StockCode', $stockCode)
                                ->first();

                            if ($existingProduct) {
                                // Update existing product
                                DB::table('products')
                                    ->where('StockCode', $stockCode)
                                    ->update(array_diff_key($product, ['created_at' => true])); // Don't update created_at
                            } else {
                                // Insert new product
                                DB::table('products')->insert($product);
                            }

                            // Mark as imported
                            $this->importedProductCodes[$stockCode] = true;

                        } catch (\Exception $e) {
                            Log::error("Error inserting/updating product {$stockCode}: " . $e->getMessage());
                        }
                    }

                    // Get IDs of inserted products for pivot table
                    foreach ($rowProductCodes as $item) {
                        try {
                            $product = DB::table('products')->where('StockCode', $item['product_code'])->first();
                            if ($product) {
                                $categoryId = $this->getCategoryId($item['category_code']);

                                if ($categoryId) {
                                    // Insert into pivot table
                                    DB::table('product_product_category')->updateOrInsert(
                                        [
                                            'product_id' => $product->id,
                                            'product_category_id' => $categoryId
                                        ]
                                    );
                                } else {
                                    Log::warning("Category not found: {$item['category_code']} for product: {$item['product_code']} (row {$item['row_number']})");
                                }
                            } else {
                                Log::warning("Product not found after insert: {$item['product_code']} (row {$item['row_number']})");
                            }
                        } catch (\Exception $e) {
                            Log::error("Error linking product to category: " . $e->getMessage(), [
                                'product_code' => $item['product_code'],
                                'category_code' => $item['category_code'],
                                'row_number' => $item['row_number']
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    // Log the entire chunk if there's an error
                    $this->errorRows = array_merge($this->errorRows, range(
                        $this->processedRows - count($products) + 1,
                        $this->processedRows
                    ));

                    Log::error("Error inserting product chunk (#{$chunkIndex}): " . $e->getMessage(), [
                        'error' => $e->getMessage(),
                        'first_item' => !empty($products) ? json_encode($products[0]) : 'NULL',
                        'total_items' => count($products)
                    ]);
                }
            }

            if ($this->processedRows % 500 === 0) {
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->processedRows);
                    Log::info("Updated progress: {$this->processedRows}/{$this->totalRows}");
                }
            }
        }
    }
}
