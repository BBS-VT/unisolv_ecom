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
use Log;

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

    /*public function store(StoreProductCategoryRequest $request)
    {
        $productCategory = ProductCategory::create($request->all());

        if ($request->input('photo', false)) {
            $productCategory->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $productCategory->id]);
        }

        return redirect()->route('product-categories.index');
    }*/

    public function store(Request $request)
    {
        $request->validate([
            'CategoryCode' => 'required|string|max:4',
            'StockGroupName' => 'required|string|max:255',
            'ParentID' => 'nullable|exists:product_categories,id',
        ]);

        ProductCategory::updateOrCreate(
            ['id' => $request->id],
            [
                'CategoryCode' => $request->CategoryCode,
                'StockGroupName' => $request->StockGroupName,
                'ParentID' => $request->ParentID,
                'location_id' => $request->location_id,
                'LastEditedBy' => auth()->id(),
                'status' => 1,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Category created successfully.']);
    }

    public function edit(ProductCategory $productCategory)
    {
        abort_if(Gate::denies('product_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('productCategories.edit', compact('productCategory'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'CategoryCode' => 'required|string|max:4',
            'StockGroupName' => 'required|string|max:255',
            'ParentID' => 'nullable|exists:product_categories,id',
            'location_id' => 'nullable|exists:locations,LocationCode',
       ]);

        $category = ProductCategory::findOrFail($id);

        $category->update([
            'CategoryCode' => $request->CategoryCode,
            'StockGroupName' => $request->StockGroupName,
            'ParentID' => $request->ParentID,
            'location_id' => $request->location_id,
            'LastEditedBy' => auth()->id(),
            'status' => 1,
        ]);


        return response()->json(['success' => true, 'message' => 'Category updated successfully.']);
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
            'csv_file' => 'required|file|mimes:csv,txt|max:20480',
        ]);

        $file = $request->file('csv_file');

        \Log::info('Import file received', [
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
        ]);

        // Check extension and MIME type
        $validMimeTypes = [
            'text/csv',
            'text/plain',
            'application/csv',
            'application/vnd.ms-excel',
            'text/comma-separated-values',
            'text/x-comma-separated-values',
        ];

        if (!in_array($file->getMimeType(), $validMimeTypes) && $file->getClientOriginalExtension() !== 'csv') {
            \Log::warning('Invalid file type', ['mime_type' => $file->getMimeType()]);
            return back()->with('error', 'The uploaded file must be a CSV file.');
        }

        try {
            // Read first few lines of the file to debug content
            $fileHandle = fopen($file->getPathname(), 'r');
            $sampleLines = [];
            for ($i = 0; $i < 5; $i++) {
                $line = fgets($fileHandle);
                if ($line === false) break;
                $sampleLines[] = $line;
            }
            fclose($fileHandle);

            \Log::info('File sample content', ['sample_lines' => $sampleLines]);

            // Import categories with explicit file type
            $userId = Auth::id();
            $import = new ProductCategoryImport($userId);
            $import->import($file, null, \Maatwebsite\Excel\Excel::CSV);

            // Get import statistics
            $stats = $import->getStats();
            \Log::info('Import completed', $stats);

            // Return appropriate message based on stats
            if ($stats['successful_imports'] > 0) {
                return back()->with('success', "Categories imported successfully ({$stats['successful_imports']} of {$stats['total_rows']} rows processed).");
            } else {
                return back()->with('warning', "Import completed, but no categories were imported. Please check the log for details.");
            }
        }
        catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            \Log::error('Validation error during import', [
                'errors' => $e->failures(),
                'message' => $e->getMessage()
            ]);

            // Get the first few validation errors to show the user
            $failures = $e->failures();
            $errorMessages = [];

            foreach (array_slice($failures, 0, 3) as $failure) {
                $errorMessages[] = "Row {$failure->row()}: {$failure->errors()[0]}";
            }

            if (count($failures) > 3) {
                $errorMessages[] = "... and " . (count($failures) - 3) . " more errors.";
            }

            return back()->with('error', 'Validation errors: ' . implode(', ', $errorMessages));
        }
        catch (\Exception $e) {
            \Log::error('Error during import', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error during import: ' . $e->getMessage());
        }

    }
}
