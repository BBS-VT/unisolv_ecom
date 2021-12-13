<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Auth;

class StockMasterImport implements ToModel, WithStartRow
{
    /**
     * @ return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     *
     * @return Product
     */
    public function model(array $row)
    {
        return new Product([
            'company_id'         => '1',
            'StockItemName'      => $row[1],
            'StockCode'          => $row[0],
            'SupplierID'         => $row[8],
            'TaxRateID'          => $row[7],
            'Size'               => '1',
            'PackSize'           => $row[4],
            'Barcode'            => $row[11],
            'CostPrice'          => $row[24],
            'SellingPrice'       => $row[31],
            'SearchDetails'      => $row[30],
            'DiscountPercentage' => $row[44],
            'status'             => '1',
            'LastEditedBy'       => Auth::user()->id,
        ]);
    }
}
