<?php

namespace App\Http\Controllers;

use App\Models\CustomerBalance;
use Illuminate\Http\Request;
use DB;
use Gate;
use App\Imports\CustomerBalanceImport;
use Symfony\Component\HttpFoundation\Response;

class CustomerBalanceController extends Controller
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('customer_balance_import'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        CustomerBalance::truncate();

        \Excel::import(new CustomerBalanceImport,$request->import_file);

        DB::statement('UPDATE customer_balances SET AccMain = TRIM(AccMain)');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        return redirect()->back()->with('success', 'File imported successfully');
    }
}
