<?php


namespace App\Imports;

use App\Models\CustomerBalance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Auth;

class CustomerBalanceImport implements ToModel, WithStartRow
{
    /**
     * @return int
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
        return new CustomerBalance([
           'AccMain'        => $row[4],
           'AccSub'         => $row[5],
           'AgedBalance1'   => $row[7],
           'AgedBalance2'   => $row[8],
           'AgedBalance3'   => $row[9],
           'AgedBalance4'   => $row[10],
           'AgedBalance5'   => $row[11],
           'AgedBalance6'   => $row[12],
           'LastEditedBy'   => Auth::user()->id,
        ]);
    }

}
