<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessStockQuantitiesImport;
use App\Models\ImportJob;
use App\Models\StockItemHoldings;
use Illuminate\Http\Request;
use DB;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Session;

class StockItemHoldingsController extends Controller
{

    /**
     * Process the import of stock quantities
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('stock_quantityImport'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:50000',
        ]);

        $path = $request->file('import_file')->store('temp');
        $filename = $request->file('import_file')->getClientOriginalName();

        // Create an import job record to track progress
        /*$importJob = ImportJob::create([
            'filename' => $filename,
            'total_rows' => 0, // Will be updated by the job
            'processed_rows' => 0,
            'status' => ImportJob::STATUS_PENDING,
            'started_at' => now(),
        ]);*/
        $importJob = ImportJob::create([
            'job_id' => $jobId,
            'filename' => $filename,
            'total_rows' => 0, // Will be updated in BeforeImport event
            'processed_rows' => 0,
            'successful_rows' => 0,
            'failed_rows' => 0,
            'items_updated' => 0,
            'company_id' => auth()->user()->currentCompany()->id,
            'imported_by' => auth()->id(),
            'status' => ImportJob::STATUS_PENDING,
            'started_at' => now(),
        ]);

        ProcessStockQuantitiesImport::dispatch($path, $importJob->id);

        Session::put('success', 'Stock quantities import has started. You can monitor progress on the imports status page.');
        Session::put('import_job_id', $importJob->id);

        return back();
    }

    /**
     * Display the import status page
     *
     * @return \Illuminate\View\View
     */
    public function showImportStatus()
    {
        $recentImports = ImportJob::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.imports.status', compact('recentImports'));
    }

    /**
     * Process linking products to stock quantities after import
     * This can be called after the import is complete to link by stock code
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function linkProductsToQuantities()
    {
        abort_if(Gate::denies('stock_quantityImport'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Create a job to handle this process or do it directly if not too resource-intensive
        $updated = DB::update("
            UPDATE stock_item_holdings h
            INNER JOIN products p ON h.StockCode = p.StockCode
            SET h.product_id = p.id
            WHERE h.product_id IS NULL
        ");

        Session::put('success', "Updated $updated stock quantity records with product IDs.");

        return back();
    }

    public function downloadTemplate()
    {
        //
    }
}
