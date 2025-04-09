<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\ImportJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;


class CustomerMasterImport implements ToCollection, WithChunkReading, WithStartRow, WithEvents
{
    protected $importJobId;
    protected $totalRows = 0;
    protected $processedRows = 0;

    /**
     * @param int $importJobId
     */
    public function __construct(int $importJobId)
    {
        $this->importJobId = $importJobId;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Register event listeners for Excel import
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                $this->totalRows = isset($totalRows['Worksheet']) ? $totalRows['Worksheet'] : (isset($totalRows['Sheet1']) ? $totalRows['Sheet1'] : 0);
                $this->totalRows = max(0, $this->totalRows - 1);

                ImportJob::where('id', $this->importJobId)->update([
                    'total_rows' => $this->totalRows,
                ]);
            },
            AfterImport::class => function(AfterImport $event) {
                // Mark as completed in the import tracker
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->totalRows);
                }
            },
            AfterSheet::class => function(AfterSheet $event) {
                // Update progress after each sheet
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->processedRows);
                }
            },
        ];
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        $chunks = $rows->chunk(250);

        foreach ($chunks as $chunk) {
            $customers = [];

            foreach ($chunk as $row) {
                if (isset($row[0]) && $row[0]) {
                    $customers[] = [
                        'company_id'           => '1',
                        'currency_id'          => '4',
                        'acc_main'             => $row[0] ?? '',
                        'acc_sub'              => $row[1] ?? '',
                        'CustomerName'         => $row[2] ?? '',
                        'CustomerCategoryID'   => $row[34] ?? '',
                        'BuyingGroupID'        => $row[97] ?? '',
                        'StoreEAN'             => $row[71] ?? '',
                        'VatNr'                => $row[90] ?? '',
                        'CreditLimit'          => $row[38] ?? '',
                        'AccountOpenedDate'    => $row[52] ?? '',
                        'PhoneNumber'          => $row[22] ?? '',
                        'FaxNumber'            => $row[23] ?? '',
                        'DeliveryRoute'        => $row[50] ?? '',
                        'GeneralEmailAddress'  => $row[25] ?? '',
                        'DeliveryAddressLine1' => $row[11] ?? '',
                        'DeliveryAddressLine2' => $row[12] ?? '',
                        'DeliveryCity'         => $row[13] ?? '',
                        'DeliveryPostalCode'   => $row[15] ?? '',
                        'PostalAddressLine1'   => $row[4] ?? '',
                        'PostalAddressLine2'   => $row[5] ?? '',
                        'PostalCity'           => $row[6] ?? '',
                        'PostalPostalCode'     => $row[8] ?? '',
                        'CustomerStatus'       => '1',
                        'CountryID'            => '202',
                        'SalesRepID'           => $this->cleanValue($row[61]),
                        'LastEditedBy'         => Auth::check() ? Auth::id() : 1,
                        'price_level'          => $row[63] ?? '1',
                        'discount_allowed'     => $row[67] ?? 'Y',
                        'created_at'           => now(),
                        'updated_at'           => now(),

                    ];

                    $this->processedRows++;
                }
            }

            if (!empty($customers)) {
                DB::table('customers')->insert($customers);
            }

            if ($this->processedRows % 1000 === 0) {
                // Update progress in the import tracker
                $importJob = ImportJob::find($this->importJobId);
                if ($importJob) {
                    $importJob->updateProgress($this->processedRows);
                }
            }
        }

    }

    private function cleanValue($value) {
        // Convert empty strings, spaces, or null values to proper NULL
        if ($value === null || $value === '' || trim($value) === '') {
            return null;
        }
        return $value;
    }
}
