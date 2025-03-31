<?php

namespace App\Jobs;

use App\Imports\CustomerMasterImport;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use Storage;

class ImportCustomersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        // Truncate the customers table
        Customer::truncate();

        // Import the file
        \Excel::import(new CustomerMasterImport, storage_path('app/'.$filePath));

        // Perform post-import SQL updates
        DB::statement('UPDATE customers SET acc_main = TRIM(acc_main)');
        DB::statement('UPDATE customers SET acc_main = LPAD(acc_main, 6, "0")');
        DB::statement('UPDATE customers SET acc_sub = "000" where acc_sub = "0"');
        DB::statement('UPDATE customers SET acc_code = CONCAT(acc_main, "-", acc_sub)');
        DB::statement('UPDATE customers SET BillToCustomerID = "9999" where BillToCustomerID is NULL');
        DB::statement('UPDATE customers SET BuyingGroupID = NULL where BuyingGroupID  = ""');
        //        DB::statement('UPDATE customers SET BuyingGroupID = "9999" where BuyingGroupID is NULL');
        DB::statement('UPDATE customers SET SalesRepID = "9999" where SalesRepID is NULL');

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Set success message
        //\Session::put('success', 'File imported successfully');

        // Delete the temporary file
        Storage::delete($filePath);
    }
}
