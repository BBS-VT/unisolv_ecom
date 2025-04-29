<!-- _balance_redesigned.blade.php -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="displayBalanceLabel">
                <i data-feather="bar-chart-2" class="icon-xs me-1"></i>
                {{ __('global.account') }} {{ __('global.balance') }} {{ __('global.detail') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="text-muted mb-1 small">{{ __('global.account') }}</p>
                                        <h5 class="mb-0">
                                            @if ($displaySubAccount == "1")
                                                {{ $customer->acc_main }} - {{ $customer->acc_sub }}
                                            @else
                                                {{ $customer->acc_main }}
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="text-muted mb-1 small">{{ __('global.customer_name') }}</p>
                                        <h5 class="mb-0">{{ $customer->CustomerName }}</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <p class="text-muted mb-1 small">{{ __('global.balance_bf') }}</p>
                                        <h5 class="mb-0 text-warning">{{ number_format($balance_bf, 2, ".", " ") }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <p class="text-muted mb-1 small">{{ __('global.credit_limit') }}</p>
                                        <h5 class="mb-0">{{ !empty($customer->CreditLimit) ? number_format($customer->CreditLimit, 2, ".", " ") : '0.00' }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{ __('global.aging_analysis') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-muted" style="width: 30%">{{ __('global.period') }}</th>
                                                <th class="text-end">{{ __('global.amount') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if ($displaySubAccount == "1")
                                                <tr>
                                                    <td class="text-muted">{{ __('global.current') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerSubBalance->AgedBalance1) ?
                                                        number_format($customer->customerSubBalance->AgedBalance1, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period1') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerSubBalance->AgedBalance2) ?
                                                        number_format($customer->customerSubBalance->AgedBalance2, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period2') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerSubBalance->AgedBalance3) ?
                                                        number_format($customer->customerSubBalance->AgedBalance3, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period3') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerSubBalance->AgedBalance4) ?
                                                        number_format($customer->customerSubBalance->AgedBalance4, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period4') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerSubBalance->AgedBalance5) ?
                                                        number_format($customer->customerSubBalance->AgedBalance5, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period5') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerSubBalance->AgedBalance6) ?
                                                        number_format($customer->customerSubBalance->AgedBalance6, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td class="text-muted">{{ __('global.current') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerBalance->AgedBalance1) ?
                                                        number_format($customer->customerBalance->AgedBalance1, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period1') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerBalance->AgedBalance2) ?
                                                        number_format($customer->customerBalance->AgedBalance2, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period2') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerBalance->AgedBalance3) ?
                                                        number_format($customer->customerBalance->AgedBalance3, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period3') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerBalance->AgedBalance4) ?
                                                        number_format($customer->customerBalance->AgedBalance4, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period4') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerBalance->AgedBalance5) ?
                                                        number_format($customer->customerBalance->AgedBalance5, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.period5') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->customerBalance->AgedBalance6) ?
                                                        number_format($customer->customerBalance->AgedBalance6, 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.balance_total') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($balance_total) ?
                                                            number_format(($balance_total), 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">{{ __('global.credit_available') }}</td>
                                                    <td class="text-end fw-bold">{{ !empty($customer->CreditLimit) ?
                                                            number_format(($customer->CreditLimit - $balance_total), 2, ".", " ") : '0.00' }}</td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <div class="mt-4">
                                                <h6 class="text-muted mb-2">{{ __('global.credit_utilization') }}</h6>
                                                @php
                                                    $utilization = !empty($customer->CreditLimit) && $customer->CreditLimit > 0
                                                        ? min(100, ($balance_total / $customer->CreditLimit) * 100)
                                                        : 0;

                                                    $barColor = 'bg-success';
                                                    if ($utilization > 70) {
                                                        $barColor = 'bg-warning';
                                                    }
                                                    if ($utilization > 90) {
                                                        $barColor = 'bg-danger';
                                                    }
                                                @endphp
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar {{ $barColor }}" role="progressbar"
                                                         style="width: {{ $utilization }}%"
                                                         aria-valuenow="{{ $utilization }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-1">
                                                    <small>0%</small>
                                                    <small>{{ number_format($utilization, 1) }}%</small>
                                                    <small>100%</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($balance_total > 0 && !empty($customer->CreditLimit) && $balance_total > $customer->CreditLimit)
                                <div class="alert alert-warning mt-3 mb-0">
                                    <i data-feather="alert-triangle" class="icon-xs me-1"></i>
                                    {{ __('global.credit_limit_exceeded_warning') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('global.close') }}</button>
            @can('customer_edit')
                <a href="{{ route('customers.edit', $customer->id) }}#credit" class="btn btn-primary">
                    <i data-feather="edit-2" class="icon-xs me-1"></i> {{ __('global.edit_credit_settings') }}
                </a>
            @endcan
        </div>
    </div>
</div>
