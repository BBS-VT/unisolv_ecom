<div class="modal fade" id="importQuantities" tabindex="-1" role="dialog" aria-labelledby="importQuantitiesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6 class="modal-title m-0 text-white" id="importQuantitiesLabel">{{ trans('global.import') }} {{ trans('cruds.product.fields.quantity') }}</h6>
                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                </button>
            </div>
            <form action="{{ route('admin.stock-holdings.import') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group">
                        <label for="import_file">Select CSV File</label>
                        <input type="file" name="import_file" class="form-control" required>
                    </div>
                    <div class="alert alert-info">
                        <h6>Instructions:</h6>
                        <p>Upload a CSV file with the following columns:</p>
                        <ul>
                            <li>Stock Code (column A)</li>
                            <li>Quantity on Hand (column K)</li>
                            <li>Bin Location (column G)</li>
                            <li>Last Cost Price (column Z)</li>
                            <li>Reorder Level (column Q)</li>
                            <li>Target Stock Level (column S)</li>
                        </ul>
                        <p>The import will process in the background and can handle large files.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-gradient-danger">Upload and Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
