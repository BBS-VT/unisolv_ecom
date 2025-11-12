@if($product_units->count() > 0)
    <div class="table-responsive" data-bs-toggle="lists">
        <table class="table table-xl mb-0 thead-border-top-0 table-striped">
            <thead>
                <tr>
                    <th>{{ __('global.name') }}</th>
                    <th class="w-30">{{ __('global.actions') }}</th>
                </tr>
            </thead>
            <tbody class="lists" id="product_units">
                @foreach($product_units as $product_unit)
                    <tr>
                        <td class="h6">
                            <a href="{{ route('admin.packagetype.edit', $product_unit->id) }}">
                                <strong class="h6">
                                    {{ $product_unit->PackageTypeName }}
                                </strong>
                            </a>
                        </td>
                        <td class="h6">
                            <a href="{{ route('admin.packagetype.edit', $product_unit->id) }}" class="btn text-primary">
                                <i data-feather="edit" class="align-self-center icon-xs"></i>
                                {{ __('global.edit') }}
                            </a>
                            <a href="{{ route('admin.packagetype.destroy', $product_unit->id) }}" class="btn text-danger delete-confirm">
                                <i data-feather="trash" class="align-self-center icon-xs"></i>
                                {{ __('global.delete') }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row card-body pagination-light justify-content-center text-center">
        {{ $product_units->links() }}
    </div>

@else
    <div class="row justify-content-center card-body pb-0 pt-5">
        <i data-feather="alert-triangle" class="icon-xl"></i>
    </div>
    <div class="row justify-content-center card-body pb-5">
        <p class="h4">{{ __('global.no_product_units_yet') }}</p>
    </div>
@endif
