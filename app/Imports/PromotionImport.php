<?php

namespace App\Imports;

use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Exception;

class PromotionImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    use Importable;

    protected array $errors = [];
    protected array $warnings = [];
    protected array $imported = [];
    protected string $batchId;
    protected bool $updateExisting;
    protected int $processedRows = 0;
    protected int $successfulRows = 0;

    public function __construct(bool $updateExisting = true)
    {
        $this->updateExisting = $updateExisting;
        $this->batchId = Str::uuid()->toString();
    }

    /**
     * Process the collection of rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $this->processedRows++;

            try {
                $this->processRow($row, $index + 2); // +2 for header and 1-based indexing
                $this->successfulRows++;
            } catch (Exception $e) {
                $this->errors[] = "Row {$this->processedRows}: " . $e->getMessage();
                Log::error("Promotion import error on row {$this->processedRows}", [
                    'error' => $e->getMessage(),
                    'row_data' => $row->toArray(),
                    'batch_id' => $this->batchId
                ]);
            }
        }
    }

    /**
     * Process individual row
     */
    protected function processRow(Collection $row, int $rowNumber): void
    {
        // Map CSV columns based on your actual structure
        $data = $this->mapRowData($row);

        // Validate the mapped data
        $this->validateRowData($data, $rowNumber);

        // Check if product exists
        $product = Product::where('StockCode', $data['stock_code'])->first();
        if (!$product) {
            $this->warnings[] = "Row {$rowNumber}: Product with Stock Code '{$data['stock_code']}' not found - skipping";
            return;
        }

        // Build promotion data
        $promotionData = $this->buildPromotionData($data, $product);

        // Check for existing promotion
        $existingPromotion = $this->findExistingPromotion($data['stock_code'], $data['location_code']);

        if ($existingPromotion && !$this->updateExisting) {
            $this->warnings[] = "Row {$rowNumber}: Promotion already exists for {$data['stock_code']} at {$data['location_name']} - skipping";
            return;
        }

        // Create or update promotion
        if ($existingPromotion && $this->updateExisting) {
            $existingPromotion->update($promotionData);
            $promotion = $existingPromotion;
            $this->imported[] = "Updated: {$data['stock_code']} at {$data['location_name']}";
        } else {
            $promotion = Promotion::create($promotionData);
            $this->imported[] = "Created: {$data['stock_code']} at {$data['location_name']}";
        }

        // Update product featured status
        if ($promotion->isActive()) {
            $product->update(['is_featured' => 1]);
        }

        Log::info('Promotion imported via Excel', [
            'promotion_id' => $promotion->id,
            'stock_code' => $data['stock_code'],
            'location' => $data['location_name'],
            'batch_id' => $this->batchId
        ]);
    }

    /**
     * Map CSV row to standardized data array - FIXED for your actual CSV structure
     */
    protected function mapRowData(Collection $row): array
    {
        // Based on your log data, these are the actual column names in your CSV
        return [
            'location_code' => $this->getRowValue($row, 'location', ''),
            'location_name' => $this->getRowValue($row, 'location_name', ''),
            'stock_code' => $this->getRowValue($row, 'stock_code', ''),
            'long_description' => $this->getRowValue($row, 'long_description', ''),
            'date_from' => $this->getRowValue($row, 'date_from', ''),
            'date_to' => $this->getRowValue($row, 'date_to', ''),
            'selling_price_1' => $this->getRowValue($row, 'selling_price_1', 0),
            'selling_price_2' => $this->getRowValue($row, 'selling_price_2', 0),
            'selling_price_3' => $this->getRowValue($row, 'selling_price_3', 0),
            'selling_price_4' => $this->getRowValue($row, 'selling_price_4', 0),
            'fixedbreak_qty' => $this->getRowValue($row, 'fixedbreak_qty', 'F'),
            'price_break_1' => $this->getRowValue($row, 'price_break_1', 0),
            'price_break_2' => $this->getRowValue($row, 'price_break_2', 0),
            'price_break_3' => $this->getRowValue($row, 'price_break_3', 0),
            'bonus_break_qty_1' => $this->getRowValue($row, 'bonus_break_qty_1', 0),
            'bonus_quantity_1' => $this->getRowValue($row, 'bonus_quantity_1', 0),
            'bonus_discount_1' => $this->getRowValue($row, 'bonus_discount_1', 0),
            'bonus_break_qty_2' => $this->getRowValue($row, 'bonus_break_qty_2', 0),
            'bonus_quantity_2' => $this->getRowValue($row, 'bonus_quantity_2', 0),
            'bonus_discount_2' => $this->getRowValue($row, 'bonus_discount_2', 0),
            'bonus_break_qty_3' => $this->getRowValue($row, 'bonus_break_qty_3', 0),
            'bonus_quantity_3' => $this->getRowValue($row, 'bonus_quantity_3', 0),
            'bonus_discount_3' => $this->getRowValue($row, 'bonus_discount_3', 0),
            'quantity_limit' => $this->getRowValue($row, 'quantity_limit', 0),
            'bonus_1_inv_limit' => $this->getRowValue($row, 'bonus_1_inv_limit', 0),
        ];
    }

    /**
     * Safely get row value using column name
     */
    protected function getRowValue(Collection $row, string $columnName, $default = null)
    {
        $value = $row->get($columnName, $default);

        // Handle string values that need trimming
        if (is_string($value)) {
            $value = trim($value);
            // Convert empty strings to null for numeric fields
            if ($value === '' && is_numeric($default)) {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Validate row data - UPDATED for correct field names
     */
    protected function validateRowData(array $data, int $rowNumber): void
    {
        // Debug: Log what we're actually validating
        Log::debug("Validating row {$rowNumber}", [
            'stock_code' => $data['stock_code'],
            'location_name' => $data['location_name'],
            'data_keys' => array_keys($data)
        ]);

        $validator = Validator::make($data, [
            'stock_code' => 'required|string|min:1',
            'location_name' => 'required|string|min:1',
            'selling_price_1' => 'nullable|numeric|min:0',
            'selling_price_2' => 'nullable|numeric|min:0',
            'selling_price_3' => 'nullable|numeric|min:0',
            'selling_price_4' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            Log::error("Validation failed for row {$rowNumber}", [
                'errors' => $errors,
                'data' => $data
            ]);
            throw new Exception("Validation failed: " . implode(', ', $errors));
        }

        // Additional custom validations
        if (empty($data['stock_code']) || trim($data['stock_code']) === '') {
            throw new Exception("Stock Code is required and cannot be empty");
        }

        if (empty($data['location_name']) || trim($data['location_name']) === '') {
            throw new Exception("Location Name is required and cannot be empty");
        }

        if (strlen($data['stock_code']) > 50) {
            throw new Exception("Stock Code too long (max 50 characters)");
        }
    }

    /**
     * Build promotion data array - UPDATED for correct field names
     */
    protected function buildPromotionData(array $data, Product $product): array
    {
        // Parse dates
        $startsAt = $this->parseDate($data['date_from']) ?: now();
        $endsAt = $this->parseDate($data['date_to']) ?: now()->addYears(10);

        // Convert prices to cents
        $sellingPrice1 = $this->parsePriceToCents($data['selling_price_1']);
        $sellingPrice2 = $this->parsePriceToCents($data['selling_price_2']);
        $sellingPrice3 = $this->parsePriceToCents($data['selling_price_3']);
        $sellingPrice4 = $this->parsePriceToCents($data['selling_price_4']);

        // Determine promotion type and build configuration
        $type = 'date_range'; // Default
        $priceBreaks = [];
        $bonusBreaks = [];

        // Check for price breaks
        $priceBreak1 = $this->parsePriceToCents($data['price_break_1']);
        $priceBreak2 = $this->parsePriceToCents($data['price_break_2']);
        $priceBreak3 = $this->parsePriceToCents($data['price_break_3']);

        if ($priceBreak1 > 0 || $priceBreak2 > 0 || $priceBreak3 > 0) {
            $type = 'price_break';
            if ($priceBreak1 > 0) $priceBreaks[] = ['qty' => 1, 'price' => $priceBreak1];
            if ($priceBreak2 > 0) $priceBreaks[] = ['qty' => 10, 'price' => $priceBreak2];
            if ($priceBreak3 > 0) $priceBreaks[] = ['qty' => 50, 'price' => $priceBreak3];
        }

        // Check for bonus quantities
        if ($data['bonus_break_qty_1'] > 0 && $data['bonus_quantity_1'] > 0) {
            $type = 'bonus_quantity';
            $bonusBreaks[] = [
                'break_qty' => intval($data['bonus_break_qty_1']),
                'bonus_qty' => intval($data['bonus_quantity_1']),
                'discount' => floatval($data['bonus_discount_1'])
            ];
        }

        if ($data['bonus_break_qty_2'] > 0 && $data['bonus_quantity_2'] > 0) {
            $bonusBreaks[] = [
                'break_qty' => intval($data['bonus_break_qty_2']),
                'bonus_qty' => intval($data['bonus_quantity_2']),
                'discount' => floatval($data['bonus_discount_2'])
            ];
        }

        if ($data['bonus_break_qty_3'] > 0 && $data['bonus_quantity_3'] > 0) {
            $bonusBreaks[] = [
                'break_qty' => intval($data['bonus_break_qty_3']),
                'bonus_qty' => intval($data['bonus_quantity_3']),
                'discount' => floatval($data['bonus_discount_3'])
            ];
        }

        // Generate promotion name
        $productName = $data['long_description'] ?: $product->ProductName;
        $name = "Imported: {$productName} - {$data['location_name']}";

        return [
            'name' => Str::limit($name, 255),
            'description' => "Imported from POS system for {$data['location_name']}",
            'type' => $type,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'location_code' => (string)$data['location_code'],
            'location_name' => $data['location_name'],
            'is_online_only' => false,
            'is_imported' => true,
            'stock_code' => $data['stock_code'],
            'customer_tiers' => [1, 2, 3, 4], // Apply to all tiers by default
            'sale_price_1' => $sellingPrice1 > 0 ? $sellingPrice1 : null,
            'sale_price_2' => $sellingPrice2 > 0 ? $sellingPrice2 : null,
            'sale_price_3' => $sellingPrice3 > 0 ? $sellingPrice3 : null,
            'sale_price_4' => $sellingPrice4 > 0 ? $sellingPrice4 : null,
            'quantity_type' => strtolower(trim($data['fixedbreak_qty'])) === 'b' ? 'break' : 'fixed',
            'min_quantity' => 1,
            'price_breaks' => !empty($priceBreaks) ? $priceBreaks : null,
            'bonus_breaks' => !empty($bonusBreaks) ? $bonusBreaks : null,
            'quantity_limit_per_customer' => intval($data['quantity_limit']) > 0 ? intval($data['quantity_limit']) : null,
            'usage_limit_total' => intval($data['bonus_1_inv_limit']) > 0 ? intval($data['bonus_1_inv_limit']) : null,
            'import_batch_id' => $this->batchId,
            'last_imported_at' => now()
        ];
    }

    /**
     * Parse price string to cents
     */
    protected function parsePriceToCents($price): int
    {
        if (empty($price) || $price === '0' || $price === '0.00') {
            return 0;
        }

        // Handle numeric values directly
        if (is_numeric($price)) {
            return intval(floatval($price) * 100);
        }

        // Handle string values
        if (is_string($price)) {
            // Remove currency symbols and clean
            $cleaned = preg_replace('/[^\d.-]/', '', $price);
            $float = floatval($cleaned);
            return intval($float * 100);
        }

        return 0;
    }

    /**
     * Parse date string
     */
    protected function parseDate(?string $date): ?Carbon
    {
        if (empty($date)) {
            return null;
        }

        try {
            // Try common date formats
            $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y'];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $date);
                } catch (Exception $e) {
                    continue;
                }
            }

            // Try Carbon's flexible parsing
            return Carbon::parse($date);

        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Find existing promotion
     */
    protected function findExistingPromotion(string $stockCode, string $locationCode): ?Promotion
    {
        return Promotion::where('stock_code', $stockCode)
            ->where('location_code', $locationCode)
            ->where('import_batch_id', '!=', $this->batchId)
            ->where('is_imported', true)
            ->first();
    }

    /**
     * Get chunk size for processing
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Get batch size for database inserts
     */
    public function batchSize(): int
    {
        return 500;
    }

    /**
     * Get import results
     */
    public function getResults(): array
    {
        return [
            'success' => true,
            'batch_id' => $this->batchId,
            'processed_rows' => $this->processedRows,
            'successful_rows' => $this->successfulRows,
            'imported_count' => count($this->imported),
            'error_count' => count($this->errors),
            'warning_count' => count($this->warnings),
            'imported' => $this->imported,
            'errors' => $this->errors,
            'warnings' => $this->warnings
        ];
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get warnings
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * Get imported items
     */
    public function getImported(): array
    {
        return $this->imported;
    }

    /**
     * Get batch ID
     */
    public function getBatchId(): string
    {
        return $this->batchId;
    }
}
