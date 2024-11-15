<?php

namespace App\Imports;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ProductCategoryImport implements ToModel, WithStartRow
{
    protected $lastEditedBy;

    public function __construct($lastEditedBy)
    {
        $this->lastEditedBy = $lastEditedBy;
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

        $categoryCode = $row[0] ;
        $description = $row[1] ;

        $mainCategoryCode = substr($categoryCode, 0, 2) . '00';

        $mainCategory = ProductCategory::firstOrCreate(
            ['CategoryCode'   => $mainCategoryCode],
            [
                'StockGroupName' => $description,
                'ParentID'       => null,
                'status'         => 1,
                'LastEditedBy'   => $this->lastEditedBy,
                'created_at'     => Carbon::now(),
            ]
        );

        if ($mainCategoryCode !== $categoryCode) {
            return ProductCategory::firstOrCreate(
                ['CategoryCode' => $categoryCode],
                [
                    'StockGroupName' => $description,
                    'ParentID'       => $mainCategory->id,
                    'status'         => 1,
                    'LastEditedBy'   => $this->lastEditedBy,
                    'created_at'     => Carbon::now(),
                ]
            );
        }

        return $mainCategory;
    }
}

