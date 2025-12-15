<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportJob;
use App\Models\StockTransaction;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    /**
     * Show stock transaction history
     */
    public function index(Request $request)
    {
        $query = StockTransaction::with(['product', 'location', 'user']);

        // Filter by product
        if ($request->filled('stock_code')) {
            $query->forProduct($request->stock_code);
        }

        // Filter by location
        if ($request->filled('location_code')) {
            $query->forLocation($request->location_code);
        }

        // Filter by transaction type
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $transactions = $query->latest()->paginate(50);

        // Get data for filters
        $transactionTypes = [
            'order' => 'Order',
            'return' => 'Return',
            'adjustment' => 'Adjustment',
            'transfer' => 'Transfer',
            'import' => 'Import',
            'initial' => 'Initial'
        ];

        $locations = Location::orderBy('LocationName')->get();

        return view('admin.stock-transactions.index', compact('transactions', 'transactionTypes', 'locations'));
    }

    /**
     * Show transaction history for a specific product
     */
    public function productHistory($stockCode)
    {
        $product = Product::where('StockCode', $stockCode)->firstOrFail();

        $transactions = StockTransaction::with(['location', 'user'])
            ->forProduct($stockCode)
            ->latest()
            ->paginate(50);

        return view('admin.stock-transactions.product', compact('product', 'transactions'));
    }

    /**
     * Show import job details with stock changes
     */
    public function importDetails($importJobId)
    {
        $importJob = ImportJob::with(['user'])->findOrFail($importJobId);

        // Paginate transactions separately to avoid loading all into memory
        $transactions = $importJob->stockTransactions()
            ->with(['product', 'location'])
            ->orderBy('created_at', 'desc')
            ->paginate(100);
        //$importJob = ImportJob::with(['user', 'stockTransactions.product', 'stockTransactions.location'])
        //    ->findOrFail($importJobId);

        return view('admin.imports.details', compact('importJob', 'transactions'));
    }

    /**
     * Show import status/progress page
     */
    public function importStatus()
    {
        $recentImports = ImportJob::with('user')
            ->recent(20)
            ->get();

        $inProgressImports = ImportJob::with('user')
            ->inProgress()
            ->get();

        return view('admin.imports.status', compact('recentImports', 'inProgressImports'));
    }

    /**
     * API endpoint for real-time import progress
     */
    public function importProgress($importJobId)
    {
        $importJob = ImportJob::findOrFail($importJobId);

        return response()->json([
            'id' => $importJob->id,
            'status' => $importJob->status,
            'progress_percentage' => $importJob->progress_percentage,
            'processed_rows' => $importJob->processed_rows,
            'total_rows' => $importJob->total_rows,
            'successful_rows' => $importJob->successful_rows,
            'failed_rows' => $importJob->failed_rows,
            'items_updated' => $importJob->items_updated,
        ]);
    }

}
