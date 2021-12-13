<?php


namespace App\Imports;

use App\Models\StockItemHoldings;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Auth;

class StockQuantitiesImport implements ToModel, WithStartRow
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
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new StockItemHoldings([
            'StockCode'         => $row[0],
            'QuantityOnHand'    => $row[10],
            'BinLocation'       => $row[6],
            'LastCostPrice'     => $row[25],
            'ReorderLevel'      => $row[16],
            'TargetStockLevel'  => $row[18],
            'LastEditedBy'      => Auth::user()->id,
        ]);
    }
}
