<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use Auth;

class CustomerMasterImport implements ToModel, WithStartRow
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
     * @return Customer
     */
    public function model(array $row)
    {
        return new Customer([
            'company_id'           => '1',
            'currency_id'          => '4',
            'acc_main'             => $row[0],
            'acc_sub'              => $row[1],
            'CustomerName'         => $row[2],
            'CustomerCategoryID'   => $row[17],
            'BuyingGroupID'        => $row[25],
            'StoreEAN'             => $row[4],
            'VatNr'                => $row[112],
            'CreditLimit'          => $row[18],
            'AccountOpenedDate'    => $row[19],
            'PhoneNumber'          => $row[7],
            'FaxNumber'            => $row[8],
            'DeliveryRoute'        => $row[5],
            'GeneralEmailAddress'  => $row[10],
            'DeliveryAddressLine1' => $row[33],
            'DeliveryAddressLine2' => $row[34],
            'DeliveryCity'         => $row[36],
            'DeliveryPostalCode'   => $row[37],
            'PostalAddressLine1'   => $row[26],
            'PostalAddressLine2'   => $row[27],
            'PostalCity'           => $row[28],
            'PostalPostalCode'     => $row[30],
            'CustomerStatus'       => '1',
            'CountryID'            => '202',
            'SalesRepID'           => $row[83],
            'LastEditedBy'         => Auth::user()->id,
            'created_at'           => Carbon::now(),
        ]);
    }
}
