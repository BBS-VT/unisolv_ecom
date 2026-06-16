<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCustomerBalanceImport;
use App\Models\CustomerBalance;
use App\Models\ImportJob;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CustomerBalanceController extends Controller
{

    /**
     * Process the import of customer balances
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('customer_balance_import'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Validate the uploaded file
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:50000',
        ]);

        // Store the file for processing
        $path = $request->file('import_file')->store('temp');
        $filename = $request->file('import_file')->getClientOriginalName();

        // Create an import job record to track progress
        $importJob = ImportJob::create([
            'filename' => $filename,
            'total_rows' => 0, // Will be updated by the job
            'processed_rows' => 0,
            'status' => ImportJob::STATUS_PENDING,
            'started_at' => now(),
        ]);

        ProcessCustomerBalanceImport::dispatch($path, $importJob->id);

        Session::put('success', 'Customer balance import has started. You can monitor progress on the imports status page.');
        Session::put('import_job_id', $importJob->id);

        return back();
    }

    /**
     * Process the import of customer balances via API (called from on-premise POS sync script)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importFromApi(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:50000',
        ]);

        $importJob = $this->startApiImport($request->file('import_file'), $request->user());

        return response()->json([
            'status'        => 'queued',
            'import_job_id' => $importJob->id,
        ], 202);
    }

    private function startApiImport(UploadedFile $file, $user): ImportJob
    {
        $path     = $file->store('temp');
        $filename = $file->getClientOriginalName();

        $importJob = ImportJob::create([
            'filename'        => $filename,
            'total_rows'      => 0,
            'processed_rows'  => 0,
            'status'          => ImportJob::STATUS_PENDING,
            'imported_by'     => $user?->id,
            'started_at'      => now(),
        ]);

        ProcessCustomerBalanceImport::dispatch($path, $importJob->id);

        return $importJob;
    }

    public function download()
    {

    }
}
