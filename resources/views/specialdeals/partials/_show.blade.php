<div class="modal fade" id="showDealModal" tabindex="-1" aria-labelledby="displayDealLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="displayDealLabel">
                    <i class="las la-eye me-2"></i>{{ trans('global.view') }} {{ trans('cruds.deal.title_singular') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deal_id" name="deal_id">

                <!-- Deal Description -->
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ trans('cruds.deal.fields.description') }}</label>
                    <input class="form-control" type="text" id="deal_name" name="deal_name" readonly>
                </div>

                <!-- Product Selection Section -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">{{ trans('cruds.deal.fields.product_selection') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('cruds.deal.fields.product') }}</label>
                                <input class="form-control" type="text" id="deal_product" name="deal_product" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('cruds.deal.fields.department') }}</label>
                                <input class="form-control" type="text" id="deal_department" name="deal_department" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Selection Section -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">{{ trans('cruds.deal.fields.customer_selection') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('cruds.deal.fields.buygroup') }}</label>
                                <input class="form-control" type="text" id="deal_buygroup" name="deal_buygroup" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('cruds.deal.fields.customergroup') }}</label>
                                <input class="form-control" type="text" id="deal_customergroup" name="deal_customergroup" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ trans('cruds.deal.fields.customer') }}</label>
                                <input class="form-control" type="text" id="deal_customer" name="deal_customer" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">{{ trans('cruds.deal.fields.pricing_section') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('cruds.deal.fields.discount') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input class="form-control" type="text" id="deal_amount" name="deal_amount" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('cruds.deal.fields.discountperc') }}</label>
                                <div class="input-group">
                                    <input type="text" id="deal_percentage" name="deal_percentage" class="form-control" readonly>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ trans('cruds.deal.fields.unitprice') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input class="form-control" type="text" id="deal_unit_price" name="deal_unit_price" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validity Period Section -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">{{ trans('cruds.deal.fields.validity_period') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('cruds.deal.fields.startdate') }}</label>
                                <input class="form-control" type="date" id="deal_start_date" name="deal_start_date" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ trans('cruds.deal.fields.enddate') }}</label>
                                <input class="form-control" type="date" id="deal_end_date" name="deal_end_date" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
