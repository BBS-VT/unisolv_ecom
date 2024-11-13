<?php

namespace App\Imports;

use App\Models\SpecialDeals;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Auth;

class SpecialDealsImport implements ToModel, WithStartRow
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
        return new SpecialDeals([
            'StockItemID'           => $row[0],
            'CustomerID'            => $row[1],
            'BuyingGroupID'         => $row[2],
            'CustomerCategoryID'    => $row[3],
            'StockGroupID'          => $row[4],
            'DealDescription'       => $row[5],
            /*'StartDate'             => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8]),
            'EndDate'               => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[9]),*/
            'StartDate'             => \Carbon\Carbon::createFromFormat('Y-m-d', $row[6]),
            'EndDate'               => \Carbon\Carbon::createFromFormat('Y-m-d', $row[7]),
            'DiscountAmount'        => $row[8],
            'DiscountPercentage'    => $row[9],
            'UnitPrice'             => $row[10],
            'LastEditedBy'          => Auth::user()->id,
        ]);
    }

}
