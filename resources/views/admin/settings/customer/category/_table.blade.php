@if($customerCategories->count() > 0)
    <div class="table-responsive" data-toggle="lists">
        <table class="table table-xl mb-0 thread-border-top-0 table-striped">
            <thead>
                <tr>
                    <th>{{ __('cruds.customerCategory.fields.account_type') }}</th>
                    <th>{{ __('cruds.customerCategory.fields.category_name') }}</th>
                    <th class="w-30">{{ __('global.actions') }}</th>
                </tr>
            </thead>
            <tbody class="lists" id="customer_categories">
                @foreach($customerCategories as $key => $customerCategory)
                    <tr data-entry-id="{{ $customerCategory->id }}">
                        <td>
                            {{ $customerCategory->AccountType ?? '' }}
                        </td><td>
                            {{ $customerCategory->CustomerCategoryName ?? '' }}
                        </td>
                        <td>
                            @can('customer_category_edit')
                                <a href="{{ route('admin.customer-category.edit', $customerCategory->id) }}" data-toggle="tooltip"
                                   title="{{ trans('global.edit') }} {{ trans('cruds.customerCategory.title_singular') }}"
                                   data-placement="top">
                                    <i class="las dripicons-document-edit text-info font-18"></i>
                                </a>
                            @endcan
                            &nbsp;
                            @can('customer_category_delete')
                                <form action="{{ route('admin.customer-category.destroy', $customerCategory->id) }}" method="POST"
                                      onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                            data-toggle="tooltip" data-placement="top"
                                            title="{{ trans('global.delete') }} {{ trans('cruds.customerCategory.title_singular') }}">
                                        <i class="dripicons-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row card-body pagination-light justify-content-center text-center">
        {{ $customerCategories->links() }}
    </div>
@else
    <div class="row justify-content-center card-body pb-0 pt-5">
        <i data-feather="alert-triangle" class="icon-xl"></i>
    </div>
    <div class="row justify-content-center card-body pb-5">
        <p class="h4">{{ __('global.no_customer_categories_yet') }}</p>
    </div>
@endif
