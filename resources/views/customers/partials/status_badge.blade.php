@if($customer->CustomerStatus == 1)
    <span class="badge bg-success-subtle text-success">{{ __('global.active') }}</span>
@else
    <span class="badge bg-danger-subtle text-danger">{{ __('global.inactive') }}</span>
@endif
@if($customer->IsOnCreditHold == 1)
    <span class="badge bg-warning-subtle text-warning">{{ __('global.credit_hold') }}</span>
@endif
