@extends('layouts.app')

@push('style')
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="row">
                    <div class="col">
                        <h4 class="page-title">{{ trans('cruds.buyingGroup.title') }}</h4>
                    </div>
                    <div class="col-auto align-self-center">
                        @can('buying_group_create')
                            <a href="{{ route("admin.buying-group.create") }}" class="btn btn-sm btn-soft-primary">
                                <i class="fas fa-plus mr-2"></i>
                                {{ trans('global.add') }}&nbsp;{{ trans('global.buying_group') }}
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0;">
                        <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.buyingGroup.fields.group_name') }}</th>
                            <th>{{ trans('cruds.buyingGroup.fields.valid_from') }}</th>
                            <th>{{ trans('cruds.buyingGroup.fields.valid_to') }}</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($buyingGroups as $key => $buyingGroup)
                            <tr data-entry-id="{{ $buyingGroup->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $buyingGroup->BuyingGroupName ?? '' }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($buyingGroup->ValidFrom ?? '')->format('d/m/Y') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($buyingGroup->ValidTo ?? '')->format('d/m/Y') }}
                                </td>
                                <td>
                                    @can('buying_group_edit')
                                        <a href="{{ route('admin.buying-group.edit', $buyingGroup->id) }}" data-toggle="tooltip"
                                           title="{{ trans('global.edit') }} {{ trans('cruds.buyingGroup.title_singular') }}"
                                           data-placement="top">
                                            <i class="las dripicons-document-edit text-info font-18"></i>
                                        </a>
                                    @endcan
                                    &nbsp;
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
            </div>

        </div>
    </div>
@endsection

@push('custom-scripts')
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('/plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/pages/jquery.datatable.init.js') }}"></script>

    <script>
        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('buying_group_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.buying-group.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                    var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).data('entry-id')
                    });
                    if (ids.length === 0) {
                        alert('{{ trans('global.datatables.zero_selected') }}')
                        return
                    }
                    if (confirm('{{ trans('global.areYouSure') }}')) {
                        $.ajax({
                            headers: {'x-csrf-token': _token},
                            method: 'POST',
                            url: config.url,
                            data: { ids: ids, _method: 'DELETE' }})
                            .done(function () { location.reload() })
                    }
                }
            }
            dtButtons.push(deleteButton)
            @endcan
            $.extend(true, $.fn.dataTable.defaults, {
                order: [[ 1, 'desc' ]],
                pageLength: 25,
            });
            $('.datatable-Order:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
    </script>
@endpush
