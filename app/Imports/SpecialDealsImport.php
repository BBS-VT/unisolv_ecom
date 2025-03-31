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
            'StockItemID'           => $row[4],
            'CustomerID'            => $row[0],
            'BuyingGroupID'         => $row[3],
            'CustomerCategoryID'    => $row[3],
            /*'StockGroupID'          => $row[4],*/
            'DealDescription'       => $row[3],
            /*'StartDate'             => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8]),
            'EndDate'               => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[9]),*/
            'StartDate'             => \Carbon\Carbon::createFromFormat('Y-m-d', $row[7]),
            'EndDate'               => \Carbon\Carbon::createFromFormat('Y-m-d', $row[8]),
            'DiscountAmount'        => $row[11],
            'DiscountPercentage'    => $row[12],
            'UnitPrice'             => $row[10],
            'LastEditedBy'          => Auth::user()->id,
        ]);
    }

}
