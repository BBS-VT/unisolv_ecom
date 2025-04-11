<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;

class ProductCategoryImport implements ToModel, WithStartRow, WithValidation
{
    use Importable;
    protected $lastEditedBy;
    protected $rowCount = 0;
    protected $successCount = 0;

    public function __construct($lastEditedBy)
    {
        $this->lastEditedBy = $lastEditedBy;

        \Log::info('Starting ProductCategory import', ['user_id' => $this->lastEditedBy]);
    }

    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     *
     * @return ProductCategory
     */
    public function model(array $row)
    {
        $this->rowCount++;

        if (empty($row[0]) || !isset($row[1])) {
            Log::warning('Skipping empty row', ['row_num' => $this->rowCount, 'data' => json_encode($row)]);
            return null;
        }

        Log::info('Processing row', [
            'row_num' => $this->rowCount,
            'category_code' => $row[0] ?? 'N/A',
            'description' => $row[1] ?? 'N/A'
        ]);

        $categoryCode = trim($row[0]) ;
        $description = trim($row[1]) ;

        if (!preg_match('/^\d+$/', $categoryCode)) {
            Log::warning('Invalid category code format', [
                'row_num' => $this->rowCount,
                'category_code' => $categoryCode
            ]);

        }

        $mainCategoryCode = substr($categoryCode, 0, 2) . '00';

        Log::info('Derived main category', [
            'row_num' => $this->rowCount,
            'category_code' => $categoryCode,
            'main_category_code' => $mainCategoryCode
        ]);

        try {
            // Create or find main category
            $mainCategory = ProductCategory::firstOrCreate(
                ['CategoryCode' => $mainCategoryCode],
                [
                    'StockGroupName' => $description,
                    'ParentID' => null,
                    'status' => 1,
                    'LastEditedBy' => $this->lastEditedBy,
                    'created_at' => Carbon::now(),
                ]
            );

            Log::info('Main category processed', [
                'category_id' => $mainCategory->id,
                'category_code' => $mainCategory->CategoryCode,
                'was_created' => $mainCategory->wasRecentlyCreated
            ]);

            // If it's a subcategory (not the main category)
            if ($mainCategoryCode !== $categoryCode) {
                $subCategory = ProductCategory::firstOrCreate(
                    ['CategoryCode' => $categoryCode],
                    [
                        'StockGroupName' => $description,
                        'ParentID'       => $mainCategory->id,
                        'status'         => 1,
                        'LastEditedBy'   => $this->lastEditedBy,
                        'created_at'     => Carbon::now(),
                    ]
                );

                Log::info('Sub category processed', [
                    'category_id' => $subCategory->id,
                    'category_code' => $subCategory->CategoryCode,
                    'parent_id' => $mainCategory->id,
                    'was_created' => $subCategory->wasRecentlyCreated
                ]);

                $this->successCount++;
                return $subCategory;
            }

            $this->successCount++;
            return $mainCategory;

        } catch (\Exception $e) {
            Log::error('Error processing category', [
                'row_num' => $this->rowCount,
                'category_code' => $categoryCode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '0' => 'required',  // Category code is required
            '1' => 'required',  // Description is required
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '0.required' => 'Category code is required',
            '1.required' => 'Description is required',
        ];
    }

    /**
     * Get import statistics
     */
    public function getStats(): array
    {
        return [
            'total_rows' => $this->rowCount,
            'successful_imports' => $this->successCount
        ];
    }
}

