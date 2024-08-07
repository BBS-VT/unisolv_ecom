<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header bg-dark">
            <h6 class="modal-title m-0 text-white" id="displayBalanceLabel">{{ trans('global.view') }}
                {{ trans('cruds.customer.title') }} {{ trans('cruds.customer.fields.balance') }}
            </h6>
            <button type="button" class="btn-close btn-sm " data-dismiss="modal" aria-label="Close">
                 <span aria-hidden="true"><i class="la la-times text-white"></i></span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 form-label align-self-center mb-lg-0 text-end">{{ trans('global.account') }}</label>
                        <div class="col-sm-10">
                            @if ($displaySubAccount == "1")
                                <input class="form-control" type="text" value="{{ $customer->acc_main }} - {{ $customer->acc_sub }}&nbsp; &nbsp; {{ $customer->CustomerName }}">
                            @else
                                <input class="form-control" type="text" value="{{ $customer->acc_main }} &nbsp; &nbsp; {{ $customer->CustomerName }}" >
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3 row">
                        <label for="example-text-input" class="col-sm-2 form-label align-self-center mb-lg-0 text-end">{{ trans('global.balance_bf') }}</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="{{ number_format($balance_bf, 2, ".", " ") }}" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row pb-1">
                        <div class="col-12 col-lg-6 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table">
                                        @if ($displaySubAccount == "1")
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.current') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerSubBalance->AgedBalance1) ?
                                                    number_format($customer->customerSubBalance->AgedBalance1, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period1') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerSubBalance->AgedBalance2) ?
                                                    number_format($customer->customerSubBalance->AgedBalance2, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period2') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerSubBalance->AgedBalance3) ?
                                                    number_format($customer->customerSubBalance->AgedBalance3, 2, ".", " ") : '0.00' }}</td>

                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period3') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerSubBalance->AgedBalance4) ?
                                                    number_format($customer->customerSubBalance->AgedBalance4, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period4') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerSubBalance->AgedBalance5) ?
                                                    number_format($customer->customerSubBalance->AgedBalance5, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period5') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerSubBalance->AgedBalance6) ?
                                                    number_format($customer->customerSubBalance->AgedBalance6, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                            </tbody>
                                        @else
                                            <tbody>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.current') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerBalance->AgedBalance1) ?
                                                    number_format($customer->customerBalance->AgedBalance1, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period1') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerBalance->AgedBalance2) ?
                                                    number_format($customer->customerBalance->AgedBalance2, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period2') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerBalance->AgedBalance3) ?
                                                    number_format($customer->customerBalance->AgedBalance3, 2, ".", " ") : '0.00' }}</td>

                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period3') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerBalance->AgedBalance4) ?
                                                    number_format($customer->customerBalance->AgedBalance4, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period4') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerBalance->AgedBalance5) ?
                                                    number_format($customer->customerBalance->AgedBalance5, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ trans('global.period5') }}</td>
                                                    <td class="semi-bold">{{ !empty($customer->customerBalance->AgedBalance6) ?
                                                    number_format($customer->customerBalance->AgedBalance6, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                            </tbody>
                                        @endif

                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>

                                        </tr>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ trans('global.credit_available') }}</td>
                                            <td class="semi-bold">{{ !empty($customer->CreditLimit) ?
                                                number_format(($customer->CreditLimit - $balance_total), 2, ".", " ") : '0.00' }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted"></td>
                                            <td class="semi-bold">&nbsp;</td>

                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ trans('global.balance_due') }}</td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ trans('global.balance_arrear') }}</td>
                                            <td class="semi-bold">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">{{ trans('global.balance_total') }}</td>
                                            <td class="semi-bold">{{ !empty($balance_total) ?
                                                number_format(($balance_total), 2, ".", " ") : '0.00' }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-soft-primary btn-sm" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
