<?php

namespace App\Http\Controllers;

use App\Models\StockItemHoldings;
use Illuminate\Http\Request;
use DB;
use Gate;
use App\Imports\StockQuantitiesImport;
use Symfony\Component\HttpFoundation\Response;
use Session;

class StockItemHoldingsController extends Controller
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('stock_quantityImport'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        StockItemHoldings::truncate();

        \Excel::import(new StockQuantitiesImport,$request->import_file);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        \Session::put('success', 'File imported successfully');

        return back();
    }
}
