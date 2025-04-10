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

                    $accountOpenedDate = $this->formatDate($row[19] ?? '');

                    $customers[] = [
                        'company_id'           => '1',
                        'currency_id'          => '4',
                        'acc_main'             => $row[0] ?? '',
                        'acc_sub'              => $row[1] ?? '',
                        'CustomerName'         => $row[2] ?? '',
                        'CustomerCategoryID'   => $this->cleanValue($row[17]),
                        'BuyingGroupID'        => $this->cleanValue($row[25]),
                        'StoreEAN'             => $row[4] ?? '',
                        'VatNr'                => $row[112] ?? '',
                        'CreditLimit'          => $this->cleanValue($row[18]),
                        'AccountOpenedDate'    => $accountOpenedDate,
                        'PhoneNumber'          => $row[7] ?? '',
                        'FaxNumber'            => $row[9] ?? '',
                        'DeliveryRoute'        => $row[5] ?? '',
                        'GeneralEmailAddress'  => $row[10] ?? '',
                        'DeliveryAddressLine1' => $row[33] ?? '',
                        'DeliveryAddressLine2' => $row[34] ?? '',
                        'DeliveryCity'         => $row[36] ?? '',
                        'DeliveryPostalCode'   => $row[37] ?? '',
                        'PostalAddressLine1'   => $row[26] ?? '',
                        'PostalAddressLine2'   => $row[27] ?? '',
                        'PostalCity'           => $row[28] ?? '',
                        'PostalPostalCode'     => $row[30] ?? '',
                        'CustomerStatus'       => '1',
                        'CountryID'            => '202',
                        'SalesRepID'           => $this->cleanValue($row[83]),
                        'LastEditedBy'         => Auth::check() ? Auth::id() : 1,
                        'price_level'          => $row[85] ?? '1',
                        'discount_allowed'     => $row[89] ?? 'Y',
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

    private function formatDate($date) {
        if (empty($date) || trim($date) === '') {
            return null;
        }

        try {
            // Check if it's already in Y-m-d format
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date;
            }

            // Try to parse the date
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                return null;
            }

            return date('Y-m-d', $timestamp);
        } catch (\Exception $e) {
            // If any error occurs during parsing, return NULL
            return null;
        }
    }
}
