<?php

namespace App\Http\Controllers;

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
                    $primaryBarcode = $product->Barcode ? $product->Barcode : 'N/A';
                    $alternateBarcode = $product->AltBarCode ? $product->AltBarCode : 'N/A';

                    return "
                        <div>Barcode: {$primaryBarcode}</div>
                        <div>Alt: {$alternateBarcode}</div>
                    ";
                })
                ->addColumn('prices', function ($product) {
                    $currency = auth()->user()->currentCompany()->currency; // Fetch system-wide currency

                    $sellingPrice1 = number_format($product->SellingPrice ?? 0, 2);
                    $sellingPrice2 = number_format($product->SellingPrice2 ?? 0, 2);
                    $sellingPrice3 = number_format($product->SellingPrice3 ?? 0, 2);

                    return "
                        <div>1: $sellingPrice1</div>
                        <div>2: $sellingPrice2</div>
                        <div>3: $sellingPrice3</div>
                    ";
                })
                ->addColumn('costPrices', function ($product) {
                    $averageCostPrice = number_format($product->AverageCostPrice ?? 0, 2); // Format to 2 decimals
                    $lastCostPrice = number_format($product->LastCostPrice ?? 0, 2);

                    return "
                        <div>Avg: $averageCostPrice</div>
                        <div>Last: $lastCostPrice</div>
                    ";
                })
                ->addColumn('quantity_on_hand', function ($product) {
                    return $product->QuantityOnHand ?? 0;
                })
                ->addColumn('action', function ($product) {
                    // Adding both 'View' and 'Edit' buttons
                    $viewButton = '<a href="'.route('products.show',
                            $product->id).'" class="btn btn-sm btn-outline-primary"><i class="dripicons-preview"></i></a>';
                    $editButton = '<a href="'.route('products.edit',
                            $product->id).'" class="btn btn-sm btn-outline-warning"><i class="dripicons-document-edit"></i></a>';

                    // Delete button with SweetAlert trigger
                    $deleteButton = '<a href="javascript:void(0)" data-id="'.$product->id.'" class="btn btn-sm btn-outline-danger delete-product"><i class="dripicons-trash"></i></a>';

                    return $viewButton.' '.$editButton.' '.$deleteButton;
                })
                ->filterColumn('barcodes', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        $q->where('products.Barcode', 'like', "%{$keyword}%")
                            ->orWhere('products.AltBarCode', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['barcodes','prices','costPrices','action'])
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
        $product = Product::create($request->all());
        $product->categories()->sync($request->input('categories', []));
        $product->tags()->sync($request->input('tags', []));


        if ($request->input('photo', false)) {
            $product->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $product->id]);
        }

        return redirect()->route('products.index');

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

        $categories = ProductCategory::all()->pluck('StockGroupName', 'id');
        $packagetypes = PackageType::all()->pluck('PackageTypeName', 'id');
        $tags = ProductTag::all()->pluck('name', 'id');

        $product->load('categories', 'tags', 'packageType', 'stockHolding');

        //dd($product);
        return view('products.edit', compact('categories', 'tags', 'product', 'packagetypes'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());
        $product->categories()->sync($request->input('categories', []));
        $product->tags()->sync($request->input('tags', []));
        //$product->packageType()->sync($request->input('packageType', []));

        if ($request->input('photo', false)) {
            if (!$product->photo || $request->input('photo') !== $product->photo->file_name) {
                $product->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }

        } elseif ($product->photo) {
            $product->photo->delete();
        }

        return redirect()->route('products.index');

    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = ProductCategory::all()->pluck('StockGroupName', 'id');
        $packagetypes = PackageType::all()->pluck('PackageTypeName', 'id');
        $tags = ProductTag::all()->pluck('name', 'id');

        $product->load('categories', 'tags', 'packageType', 'stockHolding');

        //dd($product);
        return view('products.show', compact('product', 'categories', 'tags', 'packagetypes'));
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

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_create') && Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Product();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media', 'public');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);

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
