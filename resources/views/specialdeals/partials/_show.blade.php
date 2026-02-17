<div class="modal fade" id="dealModal" tabindex="-1" aria-labelledby="dealModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="dealModalLabel">
                    <i class="las la-eye me-2" id="modal-icon"></i>
                    <span id="modal-title-text">{{ trans('global.view') }} {{ trans('cruds.deal.title_singular') }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="dealForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="deal_id" name="deal_id">

                    <!-- Deal Description -->
                    <div class="mb-3">
                        <label class="form-label fw-bold required" id="description-label">{{ trans('cruds.deal.fields.description') }}</label>
                        <input class="form-control" type="text" id="deal_name" name="DealDescription" required>
                    </div>

                    <!-- Product Selection Section -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ trans('cruds.deal.fields.product_selection') }} <small class="text-muted edit-mode-only" style="display: none;">(Select one)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ trans('cruds.deal.fields.product') }}</label>
                                    <!-- View mode input -->
                                    <input class="form-control view-mode-only" type="text" id="deal_product_display" readonly>
                                    <!-- Edit mode select -->
                                    <select class="select2-ajax form-control edit-mode-only"
                                            id="StockItemID"
                                            name="StockItemID"
                                            data-exclusive-group="product"
                                            data-ajax-url="{{ route('ajax.products.search') }}"
                                            data-placeholder="Search for a product..."
                                            style="display: none;">
                                        <option value="">-- search for a product --</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ trans('cruds.deal.fields.department') }}</label>
                                    <!-- View mode input -->
                                    <input class="form-control view-mode-only" type="text" id="deal_department_display" readonly>
                                    <!-- Edit mode select -->
                                    <select class="select2 form-control edit-mode-only"
                                            id="StockGroupID"
                                            name="StockGroupID"
                                            data-exclusive-group="product"
                                            style="display: none;">
                                        <option value="">-- select a department --</option>
                                        @foreach($categories ?? [] as $id => $category)
                                            <option value="{{ $id }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Selection Section -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ trans('cruds.deal.fields.customer_selection') }} <small class="text-muted edit-mode-only" style="display: none;">(Select one)</small></h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ trans('cruds.deal.fields.buygroup') }}</label>
                                    <!-- View mode input -->
                                    <input class="form-control view-mode-only" type="text" id="deal_buygroup_display" readonly>
                                    <!-- Edit mode select -->
                                    <select class="select2 form-control edit-mode-only"
                                            id="BuyingGroupID"
                                            name="BuyingGroupID"
                                            data-exclusive-group="customer"
                                            style="display: none;">
                                        <option value="">-- select a buying group --</option>
                                        @foreach($buyinggroups ?? [] as $id => $buyingroup)
                                            <option value="{{ $id }}">{{ $buyingroup }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ trans('cruds.deal.fields.customergroup') }}</label>
                                    <!-- View mode input -->
                                    <input class="form-control view-mode-only" type="text" id="deal_customergroup_display" readonly>
                                    <!-- Edit mode select -->
                                    <select class="select2 form-control edit-mode-only"
                                            id="CustomerCategoryID"
                                            name="CustomerCategoryID"
                                            data-exclusive-group="customer"
                                            style="display: none;">
                                        <option value="">-- select a customer group --</option>
                                        @foreach($customergroups ?? [] as $id => $customergroup)
                                            <option value="{{ $id }}">{{ $customergroup }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ trans('cruds.deal.fields.customer') }}</label>
                                    <!-- View mode input -->
                                    <input class="form-control view-mode-only" type="text" id="deal_customer_display" readonly>
                                    <!-- Edit mode select -->
                                    <select class="select2-ajax form-control edit-mode-only"
                                            id="CustomerID"
                                            name="CustomerID"
                                            data-exclusive-group="customer"
                                            data-ajax-url="{{ route('ajax.customers') }}"
                                            data-placeholder="Search for a customer..."
                                            style="display: none;">
                                        <option value="">-- search for a customer --</option>
                                    </select>
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
                                    <!-- View mode -->
                                    <div class="input-group view-mode-only">
                                        <span class="input-group-text">R</span>
                                        <input class="form-control" type="text" id="deal_amount_display" readonly>
                                    </div>
                                    <!-- Edit mode -->
                                    <div class="input-group edit-mode-only" style="display: none;">
                                        <span class="input-group-text">R</span>
                                        <input class="form-control" type="text" id="DiscountAmount" name="DiscountAmount" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ trans('cruds.deal.fields.discountperc') }}</label>
                                    <!-- View mode -->
                                    <div class="input-group view-mode-only">
                                        <input type="text" id="deal_percentage_display" class="form-control" readonly>
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <!-- Edit mode -->
                                    <div class="input-group edit-mode-only" style="display: none;">
                                        <input type="text" id="DiscountPercentage" name="DiscountPercentage" class="form-control" placeholder="0.00">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ trans('cruds.deal.fields.unitprice') }}</label>
                                    <!-- View mode -->
                                    <div class="input-group view-mode-only">
                                        <span class="input-group-text">R</span>
                                        <input class="form-control" type="text" id="deal_unit_price_display" readonly>
                                    </div>
                                    <!-- Edit mode -->
                                    <div class="input-group edit-mode-only" style="display: none;">
                                        <span class="input-group-text">R</span>
                                        <input class="form-control" type="text" id="UnitPrice" name="UnitPrice" placeholder="0.00">
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
                                    <!-- View mode -->
                                    <input class="form-control view-mode-only" type="date" id="deal_start_date_display" readonly>
                                    <!-- Edit mode -->
                                    <input class="form-control edit-mode-only" type="date" id="StartDate" name="StartDate" style="display: none;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ trans('cruds.deal.fields.enddate') }}</label>
                                    <!-- View mode -->
                                    <input class="form-control view-mode-only" type="date" id="deal_end_date_display" readonly>
                                    <!-- Edit mode -->
                                    <input class="form-control edit-mode-only" type="date" id="EndDate" name="EndDate" style="display: none;">
                                    <input type="hidden" name="LastEditedBy" id="LastEditedBy" value="{{ auth()->user()->id }}"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-primary edit-mode-only" style="display: none;">
                        <i class="fas fa-save me-1"></i>{{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
