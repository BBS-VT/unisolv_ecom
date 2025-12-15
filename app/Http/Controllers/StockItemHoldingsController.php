<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\User;
use App\Models\ImportJob;
use App\Jobs\ProcessStockQuantitiesImport;
use App\Models\StockItemHoldings;
use Symfony\Component\HttpFoundation\Response;
use DB;
use Gate;
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

        $importJob = $this->startImport(
            $request->file('import_file'),
            $request->user(),
            true // withSession
        );

        return back();
    }

    public function importFromApi(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:50000',
        ]);

        $user = $request->user();
        //if (!$user->companies()->where('company_id', $request->company_id)->exists()) {
        //    return response()->json(['error' => 'Unauthorized company access'], 403);
        //}
        $companyId = 1;

        $importJob = $this->startImportWithCompany(
            $request->file('import_file'),
            $user,
            $companyId,
            false // no session
        );

        return response()->json([
            'status'        => 'queued',
            'import_job_id' => $importJob->id,
        ], 202);
    }

    private function startImportWithCompany(UploadedFile $file, ?User $user, int $companyId, bool $withSession = true): ImportJob
    {
        $path     = $file->store('temp');
        $filename = $file->getClientOriginalName();

        $importJob = ImportJob::create([
            'filename'        => $filename,
            'total_rows'      => 0,
            'processed_rows'  => 0,
            'successful_rows' => 0,
            'failed_rows'     => 0,
            'items_updated'   => 0,
            'company_id'      => $companyId,
            'imported_by'     => $user?->id,
            'status'          => ImportJob::STATUS_PENDING,
            'started_at'      => now(),
        ]);

        ProcessStockQuantitiesImport::dispatch($path, $importJob->id, $companyId);

        if ($withSession) {
            Session::put('success', 'Stock quantities import has started. You can monitor progress on the imports status page.');
            Session::put('import_job_id', $importJob->id);
        }

        return $importJob;
    }

    private function startImport(UploadedFile $file, ?User $user, bool $withSession = true): ImportJob
    {
        $path     = $file->store('temp');
        $filename = $file->getClientOriginalName();

        $companyId = null;
        $importedBy = null;

        if ($user) {
            $importedBy = $user->id;

            // Try to get current company, but handle if it returns null
            $currentCompany = $user->currentCompany();
            if ($currentCompany) {
                $companyId = $currentCompany->id;
            } else {
                // Fallback: get the first company_id from the pivot table
                $companyId = $user->companies()->value('company_id');
            }
        }

        $importJob = ImportJob::create([
            'filename'        => $filename,
            'total_rows'      => 0,
            'processed_rows'  => 0,
            'successful_rows' => 0,
            'failed_rows'     => 0,
            'items_updated'   => 0,
            'company_id'      => $companyId,
            'imported_by'     => $importedBy,
            'status'          => ImportJob::STATUS_PENDING,
            'started_at'      => now(),
        ]);

        ProcessStockQuantitiesImport::dispatch($path, $importJob->id, $companyId);

        if ($withSession) {
            Session::put('success', 'Stock quantities import has started. You can monitor progress on the imports status page.');
            Session::put('import_job_id', $importJob->id);
        }

        return $importJob;
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
