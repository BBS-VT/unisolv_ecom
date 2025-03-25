<div class="modal fade" id="importQuantities" tabindex="-1" role="dialog" aria-labelledby="importQuantitiesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6 class="modal-title m-0 text-white" id="importQuantitiesLabel">{{ trans('global.import') }} {{ trans('cruds.product.fields.quantity') }}</h6>
                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                </button>
            </div>
            <form action="{{ route('importQuantities') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="row">
                        <input type="file" id="input-file-now" name="import_file" class="dropify">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-gradient-danger">Import File</button>
                </div>
            </form>
        </div>
    </div>
</div>
