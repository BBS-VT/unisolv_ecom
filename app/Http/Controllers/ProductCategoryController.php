<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProductCategoryRequest;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Imports\ProductCategoryImport;
use App\Models\Product;
use App\Models\ProductCategory;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelType;

class ProductCategoryController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('product_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productCategories = ProductCategory::all();

        return view('productCategories.index', compact('productCategories'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('productCategories.create');
    }

    public function store(StoreProductCategoryRequest $request)
    {
        $productCategory = ProductCategory::create($request->all());

        if ($request->input('photo', false)) {
            $productCategory->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $productCategory->id]);
        }

        return redirect()->route('product-categories.index');
    }

    public function edit(ProductCategory $productCategory)
    {
        abort_if(Gate::denies('product_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('productCategories.edit', compact('productCategory'));
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $productCategory->update($request->all());

        if ($request->input('photo', false)) {
            if (!$productCategory->photo || $request->input('photo') !== $productCategory->photo->file_name) {
                $productCategory->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        }elseif ($productCategory->photo) {
            $productCategory->photo->delete();
        }

        return redirect()->route('product-categories.index');
    }

    public function show(ProductCategory $productCategory)
    {
        abort_if(Gate::denies('product_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('productCategories.show', compact('productCategory'));
    }

    public function destroy(ProductCategory $productCategory)
    {
        abort_if(Gate::denies('product_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductCategoryRequest $request)
    {
        ProductCategory::whereIn('id', request('ids'))->delete();

        return response(null, response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('product_category_create') && Gate::denies('product_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ProductCategory();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media', 'public');

        return response()->json(['id' => $media-id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    public function updateCategoryStatus(Request $request)
    {
        if($request->ajax()) {
            $data = $request->all();

            if($data['status'] == "Active"){
                $status = 0;
            } else {
                $status = 1;
            }

            ProductCategory::where('id',$data['category_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'category_id'=>$data['category_id']]);
        }
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');

        // Check extension and MIME type
        if ($file->getClientOriginalExtension() !== 'csv') {
            return back()->with('error', 'The uploaded file must have a .csv extension.');
        }

        try {
            // Import categories with explicit file type
            $userId = Auth::id();
            Excel::import(new ProductCategoryImport($userId), $file->getPathname(), null, ExcelType::CSV);
            return back()->with('success', 'Categories imported successfully.');
        } catch (\Exception $e) {
            \Log::error('Error during import', ['message' => $e->getMessage()]);
            return back()->with('error', 'Error during import: ' . $e->getMessage());
        }

    }
}
