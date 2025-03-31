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
            'CustomerCategoryID'   => $row[34],
            'BuyingGroupID'        => $row[97],
            'StoreEAN'             => $row[71],
            'VatNr'                => $row[90],
            'CreditLimit'          => $row[38],
            'AccountOpenedDate'    => $row[52],
            'PhoneNumber'          => $row[22],
            'FaxNumber'            => $row[23],
            'DeliveryRoute'        => $row[50],
            'GeneralEmailAddress'  => $row[25],
            'DeliveryAddressLine1' => $row[11],
            'DeliveryAddressLine2' => $row[12],
            'DeliveryCity'         => $row[13],
            'DeliveryPostalCode'   => $row[15],
            'PostalAddressLine1'   => $row[4],
            'PostalAddressLine2'   => $row[5],
            'PostalCity'           => $row[6],
            'PostalPostalCode'     => $row[8],
            'CustomerStatus'       => '1',
            'CountryID'            => '202',
            'SalesRepID'           => $row[61],
            'LastEditedBy'         => Auth::user()->id,
            'created_at'           => Carbon::now(),
        ]);

    }

    /*public function model( array $row )
    {
        return new Customer([
            'company_id'           => '1',
            'currency_id'          => '4',
            'acc_main'             => $row[0],
            'acc_sub'              => $row[1],
            'CustomerName'         => $row[2],
            'CustomerCategoryID'   => $row[18],
            'BuyingGroupID'        => $row[21],
            'StoreEAN'             => $row[25],
            'VatNr'                => $row[26],
            'CreditLimit'          => $row[19],
            'AccountOpenedDate'    => $row[22],
            'PhoneNumber'          => $row[14],
            'FaxNumber'            => $row[15],
            'DeliveryRoute'        => $row[21],
            'GeneralEmailAddress'  => $row[17],
            'DeliveryAddressLine1' => $row[9],
            'DeliveryAddressLine2' => $row[10],
            'DeliveryCity'         => $row[11],
            'DeliveryPostalCode'   => $row[13],
            'PostalAddressLine1'   => $row[3],
            'PostalAddressLine2'   => $row[4],
            'PostalCity'           => $row[5],
            'PostalPostalCode'     => $row[7],
            'CustomerStatus'       => '1',
            'CountryID'            => '202',
            'SalesRepID'           => $row[23],
            'LastEditedBy'         => Auth::user()->id,
            'created_at'           => Carbon::now(),
        ]);
    }*/
}
