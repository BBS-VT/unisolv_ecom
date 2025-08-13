<?php

namespace App\Http\Controllers;

use App\Helpers\Features;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\StockMasterImport;
use App\Jobs\ProcessCsvImport;
use App\Jobs\UpdateProductFields;
use App\Models\ImportJob;
use App\Models\PackageType;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\StockItemHoldings;
use Cache;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Session;
use DB;
use Yajra\DataTables\Facades\DataTables;
use Cknow\Money\Money;

class ProductController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $currentCompany = $user->currentCompany();


        if ($request->ajax()) {

            $cacheKey = "products_company_{$currentCompany->id}";
            $cacheTTL = 3600; // 1 hour

            $query = Product::query()
                    ->where('company_id', $currentCompany->id)
                    ->select([
                        'products.id',
                        'products.StockCode',
                        'products.StockItemName',
                        'products.Barcode',
                        'products.AltBarCode',
                        'products.SellingPrice',
                        'products.SellingPrice2',
                        'products.SellingPrice3',
                        'products.SellingPrice4',
                        'products.AverageCostPrice'
                    ]);

            $query->leftJoin('stock_item_holdings', 'products.StockCode', '=', 'stock_item_holdings.StockCode')
                    ->addSelect([
                        'stock_item_holdings.QuantityOnHand',
                        'stock_item_holdings.LastCostPrice'
                    ]);

            if ($request->has('search') && !empty($request->input('search.value'))) {
                $searchValue = $request->input('search.value');

                $query->where(function ($q) use ($searchValue) {
                    $q->where('products.StockCode', 'like', "%{$searchValue}%")
                        ->orWhere('products.StockItemName', 'like', "%{$searchValue}%")
                        ->orWhere('products.Barcode', 'like', "%{$searchValue}%")
                        ->orWhere('products.AltBarCode', 'like', "%{$searchValue}%");
                });
            }

            if ($request->has('columns')) {
                foreach ($request->input('columns') as $index => $column) {
                    if (isset($column['search']) && !empty($column['search']['value'])) {
                        $columnName = $column['name'];
                        $searchValue = $column['search']['value'];

                        switch ($columnName) {
                            case 'StockCode':
                                $query->where('products.StockCode', 'like', "%{$searchValue}%");
                                break;
                            case 'StockItemName':
                                $query->where('products.StockItemName', 'like', "%{$searchValue}%");
                                break;
                            case 'Barcode':
                                $query->where(function($q) use ($searchValue) {
                                    $q->where('products.Barcode', 'like', "%{$searchValue}%")
                                        ->orWhere('products.AltBarcode', 'like', "%{$searchValue}%");
                                });
                                break;
                            case 'quantity_on_hand':
                                // Handle numeric search for quantity
                                if (is_numeric($searchValue)) {
                                    $query->where('stock_item_holdings.QuantityOnHand', '=', $searchValue);
                                }
                                break;
                        }
                    }
                }
            }


            return DataTables::of($query)
                ->addColumn('barcodes', function ($product) {
                    $primaryBarcode = $product->Barcode ? $product->Barcode : '<span class="text-muted">N/A</span>';
                    $alternateBarcode = $product->AltBarCode ? $product->AltBarCode : '<span class="text-muted">N/A</span>';

                    return "
                        <div class='product-barcode mb-1'>Primary: {$primaryBarcode}</div>
                        <div class='product-barcode'>Alt: {$alternateBarcode}</div>
                    ";
                })
                ->addColumn('prices', function ($product) {
                    //$currency = auth()->user()->currentCompany()->currency()->symbol; // Fetch system-wide currency
                    $currency = 'R';

                    $sellingPrice1 = $product->SellingPrice ? $currency . ' ' . number_format($product->SellingPrice, 2) : '<span class="text-muted">N/A</span>';
                    $sellingPrice2 = $product->SellingPrice2 ? $currency . ' ' . number_format($product->SellingPrice2, 2) : '<span class="text-muted">N/A</span>';
                    $sellingPrice3 = $product->SellingPrice3 ? $currency . ' ' . number_format($product->SellingPrice3, 2) : '<span class="text-muted">N/A</span>';
                    $sellingPrice4 = $product->SellingPrice4 ? $currency . ' ' . number_format($product->SellingPrice4, 2) : '<span class="text-muted">N/A</span>';

                    return "
                    <div><small class='text-muted'>Price 1:</small> <span class='fw-semibold'>{$sellingPrice1}</span></div>
                    <div><small class='text-muted'>Price 2:</small> {$sellingPrice2}</div>
                    <div><small class='text-muted'>Price 3:</small> {$sellingPrice3}</div>
                    <div><small class='text-muted'>Price 4:</small> {$sellingPrice4}</div>
                ";
                })
                ->addColumn('costPrices', function ($product) {
                    //$currency = auth()->user()->currentCompany()->currency()->symbol;
                    $currency = 'R';
                    $averageCostPrice = $product->AverageCostPrice ? $currency . ' ' . number_format($product->AverageCostPrice, 2) : '<span class="text-muted">N/A</span>';
                    $lastCostPrice = $product->LastCostPrice ? $currency . ' ' . number_format($product->LastCostPrice, 2) : '<span class="text-muted">N/A</span>';

                    return "
                        <div><small class='text-muted'>Avg:</small> {$averageCostPrice}</div>
                        <div><small class='text-muted'>Last:</small> {$lastCostPrice}</div>
                    ";
                })
                ->addColumn('quantity_on_hand', function ($product) {
                    $quantity = $product->QuantityOnHand ?? 0;

                    // Add stock level badge
                    if ($quantity > 10) {
                        $badge = '<span class="stock-badge stock-high">In Stock</span>';
                    } elseif ($quantity >= 1 && $quantity <= 10) {
                        $badge = '<span class="stock-badge stock-medium">Low Stock</span>';
                    } else {
                        $badge = '<span class="stock-badge stock-low">Out of Stock</span>';
                    }

                    return "
                        <div class='text-center'>
                            <div class='fw-bold fs-5'>{$quantity}</div>
                            <div class='mt-1'>{$badge}</div>
                        </div>
                    ";
                })
                ->addColumn('action', function ($product) {
                    $actions = '';

                    // View button
                    $actions .= '<a href="'.route('products.show', $product->id).'" class="btn btn-sm btn-outline-primary btn-sm-custom me-1" data-bs-toggle="tooltip" title="View Product">
                        <i class="fas fa-eye"></i>
                    </a>';

                    // Edit button
                    if (auth()->user()->can('product_edit')) {
                        $actions .= '<a href="'.route('products.edit', $product->id).'" class="btn btn-sm btn-outline-warning btn-sm-custom me-1" data-bs-toggle="tooltip" title="Edit Product">
                        <i class="fas fa-edit"></i>
                     </a>';
                    }

                    // E-commerce view button (only if e-commerce is enabled)
                    if (Features::ecommerceEnabled()) {
                        $shopUrl = route('shop.products.show', $product->slug ?? $product->id);
                        $actions .= '<a href="'.$shopUrl.'" target="_blank" class="btn btn-sm btn-outline-info btn-sm-custom me-1" data-bs-toggle="tooltip" title="View in Shop">
                        <i class="fas fa-shopping-cart"></i>
                     </a>';
                    }

                    // Delete button
                    if (auth()->user()->can('product_delete')) {
                        $actions .= '<button type="button" data-id="'.$product->id.'" data-name="'.htmlspecialchars($product->StockItemName).'" class="btn btn-sm btn-outline-danger btn-sm-custom delete-product" data-bs-toggle="tooltip" title="Delete Product">
                        <i class="fas fa-trash"></i>
                     </button>';
                    }

                    return '<div class="btn-group-actions">' . $actions . '</div>';
                })
                ->filterColumn('barcodes', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('products.Barcode', 'like', "%{$keyword}%")
                            ->orWhere('products.AltBarCode', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['barcodes','prices','costPrices','quantity_on_hand', 'action'])
                ->make(true);
        }

        return view('products.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $currentCompany = $user->currentCompany();
        Cache::forget("products_company_{$currentCompany->id}");

        $mainCategories = ProductCategory::where('ParentID', 0 )->where('status', 1)->pluck('StockGroupName', 'id');
        $subCategories = ProductCategory::where('ParentID', '>', 0)->where('status', 1)->get();

        $salesunits = PackageType::all()->pluck('PackageTypeName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $packageunits = PackageType::all()->pluck('PackageTypeName', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('products.create', compact('subCategories', 'mainCategories', 'salesunits', 'packageunits'));
    }

    public function store(StoreProductRequest $request)
    {
        //dd($request->all());
        $user = $request->user();
        $currentCompany = $user->currentCompany();
        $productData = $request->all();
        //\Log::info('Product creation data:', $productData);

        $cleanedBarcode = null;
        $cleanedAltbarcode = null;

        if ($request->has('Barcode') && $request->input('Barcode') !== null) {
            $barcode = $productData['Barcode'];
            $cleanedBarcode = preg_replace('/[^0-9]/', '', $barcode); // Remove non-numeric characters
        }

        if ($request->has('AltBarCode') && $request->input('AltBarCode') !== null) {
            $altBarcode = $productData['AltBarCode'];
            $cleanedAltbarcode = preg_replace('/[^0-9]/', '', $altBarcode); // Remove non-numeric characters
        }

        try {
            $product = Product::create([
                'company_id'    => $currentCompany->id,
                'StockCode'     => $productData['StockCode'],
                'TaxRateID'     => $productData['TaxRateID'],
                'StockItemName' => $productData['StockItemName'],
                'Barcode'       => $cleanedBarcode,
                'AltBarCode'    => $cleanedAltbarcode,
                'SellingPrice'  => $productData['SellingPrice'],
                'SellingPrice2' => $productData['SellingPrice2'] ?? 0,
                'SellingPrice3' => $productData['SellingPrice3'] ?? 0,
                'SellingPrice4' => $productData['SellingPrice4'] ?? 0,
                'AverageCostPrice'   => $productData['AverageCostPrice'] ?? 0,
                'DiscountPercentage' => $productData['DiscountPercentage'] ?? 0,
                'MarketingComments'  => $productData['MarketingComments'] ?? null,
                'Size'          => $productData['Size'] ?? null,
                'Packsize'      => $productData['Packsize'] ?? null,
                'status'        => 1,
                'LastEditedBy'  => $user->id
            ]);
            //\Log::info('Product created with ID:', [$product->id]);

            // Create stock holding record
            $stockHoldingData = [
                'StockCode' => $product->StockCode,
                'LastCostPrice' => $productData['LastCostPrice'] ?? 0,
                'QuantityOnHand' => $productData['QuantityOnHand'] ?? 0,
                'LastEditedBy' => $user->id,
            ];

            $stockHolding = StockItemHoldings::updateOrCreate(
                ['StockCode' => $product->StockCode],
                $stockHoldingData
            );

            //\Log::info('Stock holding created/updated:', $stockHoldingData);

        } catch (\Exception $e) {
            \Log::error('Product creation failed:', [$e->getMessage()]);
            return back()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
        }

        if ($request->hasFile('file')) {
            try {
                $product->addMedia($request->file('file'))->toMediaCollection('photo');
                //\Log::info('Photo uploaded successfully for product ID: ' . $product->id);
            } catch (\Exception $e) {
                \Log::error('Photo upload failed: ' . $e->getMessage());
            }
        } else {
            //\Log::info('No file uploaded - checking for photo input');
            if ($request->input('photo', false)) {
                $photoPath = storage_path('tmp/uploads/' . $request->input('photo'));
                if (file_exists($photoPath)) {
                    $product->addMedia($photoPath)->toMediaCollection('photo');
                    //\Log::info('Photo from tmp uploaded: ' . $photoPath);
                } else {
                    \Log::error('Photo file not found: ' . $photoPath);
                }
            }
        }

        if ($request->has('subCategories') && !empty(array_filter($request->input('subCategories')))) {
            $product->categories()->attach($request->input('subCategories', []));
        } elseif ($request->has('categories')) {
            $product->categories()->sync($request->input('categories'));
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully!');

    }

    public function maintain(Product $product, $id=null)
    {
        if ($id=="") {
            // Add a Product
            abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            $title = "new";
            $product = new Product;
            $productdata = [];
            $categories = ProductCategory::all()->pluck('StockGroupName', 'id')->prepend(trans('global.pleaseSelect'), '');
            $salesunits = PackageType::all()->pluck('PackageTypeName', 'id')->prepend(trans('global.pleaseSelect'), '');
            $packageunits = PackageType::all()->pluck('PackageTypeName', 'id')->prepend(trans('global.pleaseSelect'), '');

            $tags = ProductTag::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        } else {
            // Edit a Product
            abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

            $title = "edit";
            $productdata = Product::find($id);
            $productdata = json_decode(json_encode($productdata), true);
            $categories = ProductCategory::all()->pluck('StockGroupName', 'id');
            $salesunits = PackageType::all()->pluck('PackageTypeName', 'id');
            $packageunits = PackageType::all()->pluck('PackageTypeName', 'id');
            $tags = ProductTag::all()->pluck('name', 'id');

            $product = Product::find($id);
        }

        return view('products.maintain', compact('categories', 'tags', 'salesunits', 'packageunits', 'title', 'product', 'productdata'));
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $currentCompany = $user->currentCompany();
        Cache::forget("products_company_{$currentCompany->id}");

        $mainCategories = ProductCategory::where('ParentID', 0 )->where('status', 1)->pluck('StockGroupName', 'id');
        $subCategories = ProductCategory::where('ParentID', '>', 0)->where('status', 1)->get();

        $packagetypes = PackageType::all()->pluck('PackageTypeName', 'id');


        $product->load('categories',  'packageType', 'stockHolding');

        //dd($product);
        return view('products.edit', compact('subCategories', 'mainCategories', 'product', 'packagetypes'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        $cleanedBarcode = null;
        $cleanedAltbarcode = null;

        if ($request->has('Barcode') && $request->input('Barcode') !== null) {
            $barcode = $request->Barcode;
            $cleanedBarcode = preg_replace('/[^0-9]/', '', $barcode); // Remove non-numeric characters
        }

        if ($request->has('AltBarCode') && $request->input('AltBarCode') !== null) {
            $altBarcode = $request->AltBarCode;
            $cleanedAltbarcode = preg_replace('/[^0-9]/', '', $altBarcode); // Remove non-numeric characters
        }

        $product->update([
            'company_id'    => $currentCompany->id,
            'StockCode'     => $request->StockCode,
            'TaxRateID'     => $request->TaxRateID,
            'StockItemName' => $request->StockItemName,
            'Barcode'       => $cleanedBarcode,
            'AltBarCode'    => $cleanedAltbarcode,
            'SellingPrice'  => $request->SellingPrice,
            'SellingPrice2' => $request->SellingPrice2 ?? 0,
            'SellingPrice3' => $request->SellingPrice3 ?? 0,
            'SellingPrice4' => $request->SellingPrice4 ?? 0,
            'AverageCostPrice'   => $request->AverageCostPrice ?? 0,
            'DiscountPercentage' => $request->DiscountPercentage ?? 0,
            'MarketingComments'  => $request->MarketingComments ?? null,
            'Size'          => $request->Size ?? null,
            'Packsize'      => $request->Packsize ?? null,
            'status'        => 1,
            'LastEditedBy'  => $user->id
        ]);


        if ($product->stockHolding) {
            $product->stockHolding->update([
                'LastCostPrice' => $request->LastCostPrice ?? 0,
                'QuantityOnHand' => $request->QuantityOnHand ?? 0,
            ]);
        }

        if ($request->hasFile('file')) {
            try {
                $product->addMedia($request->file('file'))->toMediaCollection('photo');
                //\Log::info('Photo uploaded successfully for product ID: ' . $product->id);
            } catch (\Exception $e) {
                \Log::error('Photo upload failed: ' . $e->getMessage());
            }
        } else {
            //\Log::info('No file uploaded - checking for photo input');
            if ($request->input('photo', false)) {
                $photoPath = storage_path('tmp/uploads/' . $request->input('photo'));
                if (file_exists($photoPath)) {
                    $product->addMedia($photoPath)->toMediaCollection('photo');
                    //\Log::info('Photo from tmp uploaded: ' . $photoPath);
                } else {
                    \Log::error('Photo file not found: ' . $photoPath);
                }
            }
        }

        if ($request->has('subCategories') && !empty(array_filter($request->input('subCategories')))) {
            $product->categories()->attach($request->input('subCategories', []));
        } elseif ($request->has('categories')) {
            $product->categories()->sync($request->input('categories'));
        }

        return redirect()->route('products.index');

    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->load('categories', 'mainCategories', 'subCategories', 'packageType', 'stockHolding');

        $subCategoryParentIds = $product->subCategories->pluck('ParentID')->unique()->filter();
        $parentCategories = ProductCategory::whereIn('id', $subCategoryParentIds)->get();

        $allMainCategories = $parentCategories->unique('id');

        //dd($parentCategories);
        return view('products.show', compact('product', 'allMainCategories'));
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->delete();

        $user = auth()->user();
        $currentCompany = $user->currentCompany();
        Cache::forget("products_company_{$currentCompany->id}");

        return back();

    }

    public function massDestroy(MassDestroyProductRequest $request)
    {
        Product::whereIn('id', request('ids'))->delete();

        $user = auth()->user();
        $currentCompany = $user->currentCompany();
        Cache::forget("products_company_{$currentCompany->id}");

        return response(null, Response::HTTP_NO_CONTENT);

    }

    public function updateProductStatus(Request $request)
    {
        if($request->ajax()) {
            $data = $request->all();

            if($data['status'] == "Active"){
                $status = 0;
            } else {
                $status = 1;
            }

            Product::where('id',$data['product_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'product_id'=>$data['product_id']]);
        }
    }

    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('stock_quantityImport'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:50000'
        ]);

        if (!$request->hasFile('import_file')) {
            return back()->withErrors(['message' => 'No file was uploaded']);
        }

        $file = $request->file('import_file');
        if (!$file->isValid()) {
            return back()->withErrors(['message' => 'File upload failed: ' . $file->getErrorMessage()]);
        }

        // Store the file for processing
        try {
            $filePath = $file->store('temp');
            $filename = $file->getClientOriginalName();

            // Debug
            \Log::info('File stored at: ' . $filePath);
            \Log::info('Original Filename: ' . $filename);

            $importJob = ImportJob::create([
                'filename' => $filename,
                'total_rows' => 0,
                'processed_rows' => 0,
                'status' => ImportJob::STATUS_PENDING,
                'started_at' => now(),
            ]);

            ProcessCsvImport::dispatch($filePath, $importJob->id);

            Session::put('success', 'File upload successful. Import is being processed in the background.');
            Session::put('import_job_id', $importJob->id);

            return back();
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Error uploading file: ' . $e->getMessage()]);
        }
    }

    public function checkImportProgress($importJobId)
    {
        $importJobId = ImportJob::findOrFail($importJobId);

        return response()->json([
            'status'        => $importJobId->status,
            'progress'      => $importJobId->progress,
            'processed_rows' => $importJobId->processed_rows,
            'total_rows'    => $importJobId->total_rows,
            'error_message' => $importJobId->error_message,
        ]);
    }

    public function showImportStatus()
    {
        $recentImports = ImportJob::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.imports.status', compact('recentImports'));
    }




}
