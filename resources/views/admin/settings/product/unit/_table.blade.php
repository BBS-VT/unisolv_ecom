@if($product_units->count() > 0)


@else
    <div class="row justify-content-center card-body pb-0 pt-5">
        <i data-feather="alert-triangle" class="icon-xl"></i>
    </div>
    <div class="row justify-content-center card-body pb-5">
        <p class="h4">{{ __('global.no_product_units_yet') }}</p>
    </div>
@endif
