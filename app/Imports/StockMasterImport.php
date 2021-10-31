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
        return 4;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Product([
            'StockItemName' => $row[1],
            'StockCode'     => $row[0],
            'SupplierID'    => $row[2],
            'TaxRateID'     => $row[7],
            'Size'          => '1',
            'PackSize'      => $row[4],
            'Barcode'       => $row[11],
            'CostPrice'     => $row[24],
            'SellingPrice'  => $row[31],
            'SearchDetails' => $row[30],
            'status'        => '1',
            'LastEditedBy'  => Auth::user()->id,
        ]);
    }
}
