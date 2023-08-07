<div class="modal fade bd-example-modal-lg" id="displayDeal" tabindex="-1" role="dialog" aria-labelledby="displayDealLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="displayDealLabel">{{ trans('global.view') }} {{ trans('cruds.deal.title_singular') }}</h6>
                <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="la la-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <label for="DealDescription" class="col-sm-4 col-form-label text-left required">{{ trans('cruds.deal.fields.description') }}</label>
                            <div class="col-lg-8">
                                <input class="form-control" type="hidden" value="{{ $deal->id ??  '' }}" >
                                <input class="form-control" type="text" value="{{ $deal->DealDescription ??  '' }}" >
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="example-text-input" class="col-sm-2 form-label align-self-center mb-lg-0 text-end">{{ trans('cruds.deal.fields.product') }}</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" value="{{ intval( ltrim($deal->products->StockCode ?? '', '0')) }} &nbsp; &ndash; &nbsp; {{ $deal->products->StockItemName ?? '' }}">
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
{{--                                    @foreach($deal->CustomerID as $key => $item)--}}
                                        <span class="badge badge-info">{{ $deal->CustomerID->CustomerName ?? '' }}</span>
{{--                                    @endforeach--}}

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label for="DiscountAmount" >{{ trans('cruds.deal.fields.discount') }}</label>
                                    <input class="form-control" type="text" value="{{ old('DiscountAmount', isset($deal) ? $deal->DiscountAmount : '') }}"
                                       id="DiscountAmount" name="DiscountAmount">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label for="DiscountPercentage" >{{ trans('cruds.deal.fields.discountperc') }}</label>
                                    <div class="input-group">
                                        <input type="text" id="DiscountPercentage" name="DiscountPercentage" class="form-control" placeholder="">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fa fa-percentage"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group bootstrap-select-1">
                                    <label for="UnitPrice" >{{ trans('cruds.deal.fields.unitprice') }}</label>
                                    <input class="form-control" type="text" value="{{ isset($deal) ? $deal->DiscountAmount : '' }}"
                                           id="UnitPrice" name="UnitPrice">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="StartDate" class="col-sm-6 text-left col-form-label">{{ trans('cruds.deal.fields.startdate') }}</label>
                                    <div class="col-sm-12">
                                        <input class="form-control" type="date" value="{{ date('Y-m-d') }}" id="StartDate" name="StartDate">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="EndDate" class="col-sm-6 col-form-label text-left">{{ trans('cruds.deal.fields.enddate') }}</label>
                                    <div class="col-sm-12">
                                        <input class="form-control" type="date" value="{{ date('Y-m-d') }}" id="EndDate" name="EndDate">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>

</div>
