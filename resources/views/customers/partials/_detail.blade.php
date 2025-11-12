<div class="row">
    <div class="col-lg-4 b-round border-gray">
        <div class="card">
            <div class="card-body">
                <div class="text-muted">{{ trans('cruds.customer.fields.name') }}</div>
                <h4 class="bold no-margin pb-2">{{ $customer->CustomerName  }}</h4>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="text-muted">{{ trans('global.account') }}</div>
                        @if($displaySubAccount)
                            <h4 class="bold no-margin">{{ $customer->acc_main }} - {{ $customer->acc_sub }}</h4>
                        @else
                            <h4 class="bold no-margin">{{ $customer->acc_main }} </h4>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted">{{ trans('cruds.customer.fields.opened_date') }}</div>
                        <h4 class="bold no-margin">{{ $customer->AccountOpenedDate }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="text-muted">{{ trans('cruds.customer.fields.vat_nr') }}</div>
                        <h4 class="bold no-margin">{{ $customer->VatNr ?? 'No VAT nr' }}</h4>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted">{{ trans('cruds.customer.fields.store_ean') }}</div>
                        <h4 class="bold no-margin">{{ $customer->StoreEAN }}</h4>
                    </div>
                </div>
                <h5 class="text-muted">{{ trans('global.contacts') }}</h5>
                <div class="m-lg-1">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="text-muted">{{ trans('global.phone') }}</td>
                                <td class="semi-bold phone">{{ $customer->PhoneNumber ?? '' }}</td>
                                <td><i class="fa fa-phone"></i></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ trans('global.fax') }}</td>
                                <td class="semi-bold phone">{{ $customer->FaxNumber ?? '' }}</td>
                                <td><i class="fa fa-fax"></i></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ trans('global.email') }}</td>
                                <td class="semi-bold">{{ $customer->GeneralEmailAddress ?? '' }}</td>
                                <td><i class="fas fa-at"></i></td>
                            </tr>
                            <tr>
                                <td class="text-muted">{{ trans('global.website') }}</td>
                                <td class="semi-bold">{{ $customer->WebsiteURL ?? '' }}</td>
                                <td><i class="fa fa-globe"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="row pb-1">
            <div class="col-12 col-lg-6 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="text-end">
                            <div class="p-l-15 p-b-xs p-t-sm text-warning">
                                 <div class="font-light m-b-0 h3">{{ number_format($balance_bf, 2, ".", " ") }}</div>
                                <h4 class="semi-bold no-margin text-uppercase card-title text-warning">{{ trans('global.balance_bf') }}</h4>
                                {{--                        <span class="mini-description ng-binding">0 Sales</span>--}}
                            </div>
                            <div class="wrapper-popover" >
                                <i class="intro-questionmark" data-original-title="" title=""></i>
                                <i class="fa fa-info-circle" aria-hidden="true" style="color:#1C75BC" data-bs-toggle="tooltip" data-bs-placement="bottom" data-original-title="Total outstanding balance brought forward"></i>
                                <div class="popover-go"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12 col-lg-6 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="text-end">
                            <div class="p-l-15 p-b-xs p-t-sm">
                                <div class="font-light m-b-0 h3">
                                    @if($displaySubAccount == "1")
                                        {{ !empty($customer->customerSubBalance->AgedBalance1) ? number_format($customer->customerSubBalance->AgedBalance1, 2, ".", " ") : '0.00' }}
                                    @else
                                        {{ !empty($customer->customerBalance->AgedBalance1) ? number_format($customer->customerBalance->AgedBalance1, 2, ".", " ") : '0.00' }}
                                    @endif
                                </div>
                                <h4 class="semi-bold no-margin text-uppercase card-title">{{ trans('global.current') }}</h4>
                                {{--<span class="mini-description ng-binding">0 Invoices</span>--}}
                            </div>
                            <div class="wrapper-popover ">
                                <i class="intro-questionmark" data-original-title="" title=""></i>
                                <i class="fa fa-info-circle" aria-hidden="true" style="color:#1C75BC" data-bs-toggle="tooltip" data-bs-placement="bottom" data-original-title="Total invoice qty and value of this customer that has not been paid"></i>
                                <div class="popover-go"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-12 col-lg-6 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="text-end">
                            <div class="p-l-15 p-b-xs p-t-sm text-danger">
                                <div class="font-light m-b-0 h3">{{ number_format($overdue_balance, 2, ".", " ") }}</div>
                                <h4 class="semi-bold no-margin text-uppercase card-title text-danger">{{ trans('global.overdue') }}</h4>
                                {{--<span class="mini-description ">0 Invoices</span>--}}
                            </div>
                            <div class="wrapper-popover" >
                                <i class="intro-questionmark" data-original-title="" title=""></i>
                                <i class="fa fa-info-circle" aria-hidden="true" style="color:#1C75BC" data-bs-toggle="tooltip" data-bs-placement="bottom" data-original-title="Total invoice qty and value of this customer that has exceeded the payment due date"></i>
                                <div class="popover-go"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 col-xl">
                <div class="card">
                    <div class="card-body">
                        <div class="text-end p-t-20 p-r-20">
                            <div class="p-l-15 p-b-xs p-t-sm text-success">
                                <div class="font-light m-b-0 h3">0.00</div>
                                <h4 class="semi-bold no-margin all-caps text-success card-title">{{ trans('global.last_paid') }}</h4>
                                {{--<span class="mini-description ng-binding">1 Invoices</span>--}}
                            </div>
                            <div class="wrapper-popover" >
                                <i class="intro-questionmark" data-original-title="" title=""></i>
                                <i class="fa fa-info-circle" aria-hidden="true" style="color:#1C75BC" data-bs-toggle="tooltip" data-bs-placement="bottom" data-original-title="Last payment amount"></i>
                                <div class="popover-go"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 g stretch-card">
                <div class="card">
                    <ul class="nav nav-tabs nav-tabs-line" id="addressTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-uppercase" id="postal-tab" data-bs-toggle="tab" href="#postal" role="tab"
                               aria-controls="postal" aria-selected="true">{{ trans('global.postal_address') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="delivery-tab" data-bs-toggle="tab" href="#delivery" role="tab"
                               aria-controls="delivery" aria-selected="false">{{ trans('global.delivery_address') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="addressTab">
                        <div class="tab-pane fade show active" id="postal" role="tabpanel" aria-labelledby="postal-tab">
                            <div class="m-auto">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">{{ trans('cruds.customer.fields.address_1') }}</td>
                                            <td>{{ $customer->PostalAddressLine1 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ trans('cruds.customer.fields.address_2') }}</td>
                                            <td>{{ $customer->PostalAddressLine2 }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ trans('cruds.customer.fields.city') }}</td>
                                            <td>{{ $customer->PostalCity }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ trans('cruds.customer.fields.postal_code') }}</td>
                                            <td>{{ $customer->PostalPostalCode }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade show" id="delivery" role="tabpanel" aria-labelledby="delivery-tab">
                            <div class="m-auto">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td class="text-muted">{{ trans('cruds.customer.fields.address_1') }}</td>
                                        <td>{{ $customer->DeliveryAddressLine1 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ trans('cruds.customer.fields.address_2') }}</td>
                                        <td>{{ $customer->DeliveryAddressLine2 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ trans('cruds.customer.fields.city') }}</td>
                                        <td>{{ $customer->DeliveryCity }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">{{ trans('cruds.customer.fields.postal_code') }}</td>
                                        <td>{{ $customer->DeliveryPostalCode }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @include('customers.partials._pricing_settings')
            </div>
            <div class="col-lg-5  stretch-card">
                <div class="card">
                    <div class="float-end mb-1">
                        <a class="dropdown-item text-info text-end" data-bs-toggle="modal" data-bs-target="#displayBalance" href="#">
                            <i data-feather="list" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                            <span class="bold">{{ trans('global.view') }} {{ trans('global.detail_balance') }}</span>
                        </a>
                    </div>
                    <table class="table">
                        <tbody>
                        <tr>
                            <td class="text-muted">{{ trans('global.trade_discount') }}</td>
                            <td class="semi-bold">{{ $customer->StandardDiscountPercentage ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ trans('global.credit_limit') }}</td>
                            <td class="semi-bold">{{ $customer->CreditLimit ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('cruds.customer.fields.delivery') }}</td>
                            <td class="semi-bold">{{ $customer->DeliveryRoute  ?? '' }}</td>

                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('cruds.customer.fields.salerep') }}</td>
                            <td class="semi-bold">{{ $customer->salesrep->PreferredName ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('cruds.customer.fields.contract') }}</td>
                            <td class="semi-bold">{{ $customer->BuyingGroupID ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('cruds.customer.fields.type') }}</td>
                            <td class="semi-bold">{{ $customer->CustomerCategoryID ?? '' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('cruds.customer.fields.price_level') }}</td>
                            <td class="semi-bold">{{ $customer->price_level ?? '1' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('cruds.customer.fields.discount_allowed') }}</td>
                            <td class="semi-bold">
                                @if($customer->discount_allowed == 0)
                                    <span class="badge badge-soft-success">{{ __('global.yes') }}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{ __('global.no') }}</span>
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal fade bd-example-modal-lg" id="displayBalance" tabindex="-1" role="dialog" aria-labelledby="displayBalanceLabel" aria-hidden="true">
                    @include('customers.partials._balance')
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="text-sm-left text-muted">{{ trans('global.last_edit') }} {{ $customer->lastedited->PreferredName }} on {{ $customer->updated_at }}</div>
</div>

