<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\StockMasterImport;
use App\Models\PackageType;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Session;
use DB;

class ProductController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $products = Product::all();

        return view('products.index', compact('products'));

    }

    public function create()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $categories = ProductCategory::all()->pluck('StockGroupName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $salesunits = PackageType::all()->pluck('PackageTypeName', 'id')->prepend(trans('global.pleaseSelect'), '');
        $packageunits = PackageType::all()->pluck('PackageTypeName', 'id')->prepend(trans('global.pleaseSelect'), '');

        $tags = ProductTag::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('products.create', compact('categories', 'tags', 'salesunits', 'packageunits'));
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
            $productdata = array();
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

        return back();

    }

    public function massDestroy(MassDestroyProductRequest $request)
    {
        Product::whereIn('id', request('ids'))->delete();

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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function importExcel(Request $request)
    {
        abort_if(Gate::denies('stock_quantityImport'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Product::truncate();

        \Excel::import(new StockMasterImport,$request->import_file);

        DB::statement('UPDATE products SET Barcode = TRIM(Barcode)');
        DB::statement('UPDATE products SET StockCode = TRIM(StockCode)');
        DB::statement('UPDATE products SET SupplierID = TRIM(SupplierID)');
        DB::statement('UPDATE products SET AltBarcode = TRIM(AltBarcode)');


        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        \Session::put('success', 'File imported successfully');

        return back();
    }

}
