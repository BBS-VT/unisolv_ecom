<div class="card-header ">
    <div class="col-auto align-self-center float-right">
        @can('buying_group_create')
            <a href="{{ route("admin.buying-group.create") }}" class="btn btn-sm btn-gradient-primary">
                <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                {{ trans('global.add') }}&nbsp;{{ trans('global.buying_group') }}
            </a>
        @endcan
    </div>
</div>
<div class="card-body">
    @if($buyingGroups->count() > 0)
        <div class="table-responsive" data-toggle="lists">
            <table class="table table-xl mb-0 thread-border-top-0 table-stiped">
                <thead>
                <tr>
                    <th>{{ __('cruds.buyingGroup.fields.group_name') }}</th>
                    <th>{{ __('cruds.buyingGroup.fields.valid_from') }}</th>
                    <th>{{ __('cruds.buyingGroup.fields.valid_to') }}</th>
                    <th> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($buyingGroups as $key => $buyingGroup)
                    <tr data-entry-id="{{ $buyingGroup->id }}">
                        <td>{{ $buyingGroup->BuyingGroupName ?? '' }}</td>
                        <td>{{ \Carbon\Carbon::parse($buyingGroup->ValidFrom ?? '')->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($buyingGroup->ValidTo ?? '')->format('d/m/Y') }}</td>
                        <td>
                            @can('buying_group_edit')
                                <a href="{{ route('admin.buying-group.edit', $buyingGroup->id) }}" data-toggle="tooltip"
                                   title="{{ trans('global.edit') }} {{ trans('cruds.buyingGroup.title_singular') }}"
                                   data-placement="top">
                                    <i class="las dripicons-document-edit text-info font-18"></i>
                                </a>
                            @endcan
                            @can('buying_group_delete')
                                    <form action="{{ route('admin.buying-group.destroy', $buyingGroup->id) }}" method="POST"
                                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                data-toggle="tooltip" data-placement="top"
                                                title="{{ trans('global.delete') }} {{ trans('cruds.buyingGroup.title_singular') }}">
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
            {{ $buyingGroups->links() }}
        </div>

    @else
        <div class="row justify-content-center card-body pb-0 pt-5">
            <i data-feather="alert-triangle" class="icon-xl"></i>
        </div>
        <div class="row justify-content-center card-body pb-5">
            <p class="h4">{{ __('global.no_buying_groups_yet') }}</p>
        </div>
    @endif
</div>
