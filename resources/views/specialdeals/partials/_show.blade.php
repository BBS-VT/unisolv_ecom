<div class="modal fade bd-example-modal-lg" id="showDealModal" tabindex="-1" role="dialog" aria-labelledby="displayDealLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="displayDealLabel">{{ trans('global.view') }} {{ trans('cruds.deal.title_singular') }}</h6>
                <button type="button" class="close " data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <label for="DealDescription" class="col-sm-4 col-form-label text-start required">{{ trans('cruds.deal.fields.description') }}</label>
                            <div class="col-lg-8">
                                <input class="form-control" type="hidden" id="deal_id" name="deal_id" >
                                <input class="form-control" type="text" id="deal_name" name="deal_name" value="" >
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-sm-2 form-label align-self-center mb-lg-0 text-end">{{ trans('cruds.deal.fields.product') }}</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" value="" id="deal_product" name="deal_product">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label >{{ trans('cruds.deal.fields.department') }}</label>
                                    <input class="form-control" type="text" value="{{ $deal->productCategory->StockGroupName ??  '' }}" >
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label >{{ trans('cruds.deal.fields.buygroup') }}</label>
                                    <input class="form-control" type="text" value="{{ $deal->BuyingGroupID ?? '' }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label >{{ trans('cruds.deal.fields.customergroup') }}</label>
                                    <input class="form-control" type="text" value="{{ $deal->CustomerCategoryID ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group bootstrap-select-1">
                                    <label >{{ trans('cruds.deal.fields.customer') }}</label>
                                    <input class="form-control" type="text" value="" id="deal_customer" name="deal_customer">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label for="DiscountAmount" >{{ trans('cruds.deal.fields.discount') }}</label>
                                    <input class="form-control" type="text" value="" id="deal_amount" name="deal_amount">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label for="DiscountPercentage" >{{ trans('cruds.deal.fields.discountperc') }}</label>
                                    <div class="input-group">
                                        <input type="text" id="deal_percentage" name="deal_percentage" class="form-control" placeholder="">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fa fa-percentage"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label for="UnitPrice" >{{ trans('cruds.deal.fields.unitprice') }}</label>
                                    <input class="form-control" type="text" value="" id="deal_unit_price" name="deal_unit_price">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="StartDate" class="col-sm-6 text-start col-form-label">{{ trans('cruds.deal.fields.startdate') }}</label>
                                    <div class="col-sm-12">
                                        <input class="form-control" type="date" value="" id="deal_start_date" name="deal_start_date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="EndDate" class="col-sm-6 col-form-label text-start">{{ trans('cruds.deal.fields.enddate') }}</label>
                                    <div class="col-sm-12">
                                        <input class="form-control" type="date" value="" id="deal_end_date" name="deal_end_date">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>

</div>
