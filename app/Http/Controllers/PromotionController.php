<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Product;
use App\Imports\PromotionImport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class PromotionController extends Controller
{

    /**
     * Display a listing of promotions.
     */
    public function index(Request $request): View
    {
        $query = Promotion::with('product');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_online_only')) {
            $query->where('is_online_only', $request->boolean('is_online_only'));
        }

        if ($request->filled('location')) {
            $query->where('location_name', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('stock_code', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('StockItemName', 'like', "%{$search}%");
                    });
            });
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $promotions = $query->paginate(25)->withQueryString();

        $statistics = [
            'total' => Promotion::count(),
            'active' => Promotion::where('status', 'active')->count(),
            'scheduled' => Promotion::where('status', 'scheduled')->count(),
            'expired' => Promotion::where('status', 'expired')->count(),
            'online_only' => Promotion::where('is_online_only', true)->count(),
            'imported' => Promotion::where('is_imported', true)->count(),
        ];

        return view('promotions.index', compact('promotions', 'statistics'));
    }

    public function create(): View
    {
        $products = Product::select('StockCode', 'StockItemName', 'SellingPrice', 'SellingPrice2', 'SellingPrice3', 'SellingPrice4' )
            ->orderBy('StockItemName')
            ->get();

        return view('promotions.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePromotionData($request);

        try {
            $promotion = Promotion::create($validated);

            Log::info('Promotion created', [
                'promotion_id' => $promotion->id,
                'admin_user' => auth()->id()
            ]);

            return redirect()->route('promotions.show', $promotion)
                ->with('success', 'Promotion created successfully.');

        } catch (Exception $e) {
            Log::error('Failed to create promotion', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return back()->withInput()
                ->with('error', 'Failed to create promotion: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified promotion
     */
    public function show(Promotion $promotion): View
    {
        $promotion->load(['product', 'usage.customer', 'usage.order']);

        $usageStats = [
            'total_usage' => $promotion->usage_count,
            'unique_customers' => $promotion->usage()->distinct('customer_id')->count(),
            'total_savings' => $promotion->usage()->sum('total_savings_cents'),
            'recent_usage' => $promotion->usage()->with('customer')
                ->latest()
                ->take(10)
                ->get()
        ];

        return view('promotions.show', compact('promotion', 'usageStats'));
    }

    public function edit(Promotion $promotion): View
    {
        $products = Product::select('StockCode', 'StockItemName')
            ->orderBy('StockItemName')
            ->get();

        return view('promotions.edit', compact('promotion', 'products'));
    }

    /**
     * Update the specified promotion
     */
    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $validated = $this->validatePromotionData($request, $promotion);

        try {
            $promotion->update($validated);

            Log::info('Promotion updated', [
                'promotion_id' => $promotion->id,
                'admin_user' => auth()->id()
            ]);

            return redirect()->route('promotions.show', $promotion)
                ->with('success', 'Promotion updated successfully.');

        } catch (Exception $e) {
            Log::error('Failed to update promotion', [
                'error' => $e->getMessage(),
                'promotion_id' => $promotion->id
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update promotion: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified promotion
     */
    public function destroy(Promotion $promotion): RedirectResponse
    {
        try {
            $promotionId = $promotion->id;
            $promotion->delete();

            Log::info('Promotion deleted', [
                'promotion_id' => $promotionId,
                'admin_user' => auth()->id()
            ]);

            return redirect()->route('promotions.index')
                ->with('success', 'Promotion deleted successfully.');

        } catch (Exception $e) {
            Log::error('Failed to delete promotion', [
                'error' => $e->getMessage(),
                'promotion_id' => $promotion->id
            ]);

            return back()->with('error', 'Failed to delete promotion: ' . $e->getMessage());
        }
    }

    public function showImport(): View
    {
        return view('promotions.import');
    }

    /**
     * Handle Excel/CSV import using Laravel Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
            'update_existing' => 'boolean'
        ]);

        try {
            $file = $request->file('import_file');
            $updateExisting = $request->boolean('update_existing', true);

            // Create import instance
            $import = new PromotionImport($updateExisting);

            // Process the import
            Excel::import($import, $file);

            // Get results
            $result = $import->getResults();

            if ($result['successful_rows'] > 0) {
                $message = "Import completed successfully. ";
                $message .= "Processed: {$result['processed_rows']}, ";
                $message .= "Successful: {$result['successful_rows']}, ";
                $message .= "Errors: {$result['error_count']}, ";
                $message .= "Warnings: {$result['warning_count']}";

                $sessionData = [
                    'import_result' => $result,
                    'success' => $message
                ];

                if (!empty($result['errors']) || !empty($result['warnings'])) {
                    $sessionData['import_details'] = [
                        'errors' => $result['errors'],
                        'warnings' => $result['warnings']
                    ];
                }

                Log::info('Promotion Excel import completed', [
                    'batch_id' => $result['batch_id'],
                    'processed_rows' => $result['processed_rows'],
                    'successful_rows' => $result['successful_rows'],
                    'admin_user' => auth()->id()
                ]);

                return redirect()->route('promotions.index')->with($sessionData);
            } else {
                return back()->withInput()
                    ->with('error', 'Import failed: No rows were successfully processed')
                    ->with('import_errors', $result['errors'] ?? []);
            }

        } catch (Exception $e) {
            Log::error('Excel import failed', [
                'error' => $e->getMessage(),
                'admin_user' => auth()->id()
            ]);

            return back()->withInput()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Preview Excel/CSV before import
     */
    public function previewImport(Request $request): JsonResponse
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:10240'
        ]);

        try {
            $file = $request->file('import_file');

            // Read first few rows for preview
            $preview = Excel::toArray(new class {
                use \Maatwebsite\Excel\Concerns\Importable;
            }, $file);

            $rows = $preview[0] ?? [];
            $header = array_shift($rows); // Remove header
            $previewRows = array_slice($rows, 0, 5); // First 5 data rows

            // Basic validation
            $requiredColumns = [1 => 'Location Name', 6 => 'Stock Code'];
            $missingColumns = [];

            foreach ($requiredColumns as $index => $name) {
                if (!isset($header[$index]) || empty(trim($header[$index]))) {
                    $missingColumns[] = $name;
                }
            }

            return response()->json([
                'valid' => empty($missingColumns),
                'row_count' => count($rows),
                'header' => $header,
                'preview_rows' => $previewRows,
                'missing_columns' => $missingColumns,
                'message' => empty($missingColumns)
                    ? 'File structure looks good'
                    : 'Missing required columns: ' . implode(', ', $missingColumns)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'valid' => false,
                'error' => 'Failed to read file: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download sample import template
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $headers = [
            'Location Code',
            'Location Name',
            '', '', '', '',
            'Stock Code',
            '', '',
            'Date From',
            'Date To',
            'Selling Price 1',
            'Selling Price 2',
            'Selling Price 3',
            'Selling Price 4',

        ];

        // Create a sample CSV file
        $filename = 'promotion_import_template.csv';
        $handle = fopen('php://temp', 'w+');

        // Add headers
        fputcsv($handle, $headers);

        // Add sample data row
        $sampleData = [
            '0',
            'Sample Store',
            '', '', '', '',
            'SAMPLE001',
            '', '',
            '2025-08-01',
            '2025-08-31',
            '19.99',
            '18.99',
            '17.99',
            '16.99'
        ];
        fputcsv($handle, $sampleData);

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Test promotion calculation
     */
    public function testCalculation(Request $request): JsonResponse
    {
        $request->validate([
            'promotion_id' => 'required|exists:promotions,id',
            'quantity' => 'required|integer|min:1',
            'customer_tier' => 'required|integer|min:1|max:4'
        ]);

        try {
            $promotion = Promotion::findOrFail($request->promotion_id);
            $product = $promotion->product;

            if (!$product) {
                return response()->json([
                    'error' => 'Product not found for this promotion'
                ], 404);
            }

            if ($request->customer_tier == 1)
                $originalPrice = $product->{"SellingPrice"};
            else {
                $originalPrice = $product->{"SellingPrice{$request->customer_tier}"};
            }
            $result = $promotion->calculateDiscount(
                $request->quantity,
                $originalPrice,
                $request->customer_tier
            );

            return response()->json([
                'success' => true,
                'original_price' => $originalPrice,
                'original_price_formatted' => 'R' . number_format($originalPrice, 2),
                'calculation' => $result
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Bulk update promotion status
     */
    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'promotion_ids' => 'required|array',
            'promotion_ids.*' => 'exists:promotions,id',
            'status' => 'required|in:active,inactive,scheduled,expired'
        ]);

        try {
            $updated = Promotion::whereIn('id', $request->promotion_ids)
                ->update(['status' => $request->status]);

            Log::info('Bulk promotion status update', [
                'count' => $updated,
                'status' => $request->status,
                'admin_user' => auth()->id()
            ]);

            return back()->with('success', "Updated {$updated} promotions to {$request->status} status.");

        } catch (Exception $e) {
            Log::error('Bulk status update failed', [
                'error' => $e->getMessage(),
                'admin_user' => auth()->id()
            ]);

            return back()->with('error', 'Failed to update promotions: ' . $e->getMessage());
        }
    }

    /**
     * Get promotion statistics for dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = [
                'total_promotions' => Promotion::count(),
                'active_promotions' => Promotion::active()->count(),
                'expired_promotions' => Promotion::where('ends_at', '<', now())->count(),
                'scheduled_promotions' => Promotion::where('starts_at', '>', now())->count(),
                'online_only' => Promotion::where('is_online_only', true)->count(),
                'imported_promotions' => Promotion::where('is_imported', true)->count(),
                'total_usage' => \DB::table('promotion_usage')->count(),
                'total_savings' => \DB::table('promotion_usage')->sum('total_savings_cents'),
                'top_promotions' => Promotion::withCount('usage')
                    ->orderBy('usage_count', 'desc')
                    ->take(5)
                    ->get(['id', 'name', 'usage_count']),
                'recent_activity' => Promotion::with('product')
                    ->latest()
                    ->take(10)
                    ->get(['id', 'name', 'stock_code', 'type', 'created_at'])
            ];

            return response()->json($stats);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Validate promotion data
     */
    private function validatePromotionData(Request $request, ?Promotion $promotion = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:date_range,bogo,quantity_break,bonus_quantity,price_break,online_only',
            'status' => 'required|in:active,inactive,scheduled,expired',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'stock_code' => [
                'required',
                'string',
                'exists:products,StockCode'
            ],
            'is_online_only' => 'boolean',
            'customer_tiers' => 'nullable|array',
            'customer_tiers.*' => 'integer|min:1|max:4',
            'sale_price_1' => 'nullable|numeric|min:0',
            'sale_price_2' => 'nullable|numeric|min:0',
            'sale_price_3' => 'nullable|numeric|min:0',
            'sale_price_4' => 'nullable|numeric|min:0',
            'min_quantity' => 'nullable|integer|min:1',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
            'quantity_limit_per_customer' => 'nullable|integer|min:1',
            'usage_limit_total' => 'nullable|integer|min:1'
        ];

        $validated = $request->validate($rules);

        // Convert prices to cents
        foreach (['sale_price_1', 'sale_price_2', 'sale_price_3', 'sale_price_4', 'discount_amount'] as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = intval($validated[$field] * 100);
            }
        }

        // Set defaults
        $validated['is_imported'] = false;
        $validated['is_online_only'] = $validated['is_online_only'] ?? false;

        return $validated;
    }

    public function getProductInfo($stockCode)
    {
        $product = Product::where('StockCode', $stockCode)
            ->select('StockCode', 'StockItemName', 'SellingPrice', 'SellingPrice2', 'SellingPrice3', 'SellingPrice4')
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'stock_code' => $product->StockCode,
            'name' => $product->StockItemName,
            'price1' => $product->SellingPrice,
            'price2' => $product->SellingPrice2,
            'price3' => $product->SellingPrice3,
            'price4' => $product->SellingPrice4,
        ]);
    }
}
