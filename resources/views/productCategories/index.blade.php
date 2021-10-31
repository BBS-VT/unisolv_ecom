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

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ trans('cruds.productCategory.title_singular') }} {{ trans('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">

                            @can('product_category_create')
                                <a href="{{ route("product-categories.create") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }} {{ trans('cruds.productCategory.title_singular') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div><!--end card-header-->
                <div class="card-body">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th width="10"></th>
                            <th>{{ trans('cruds.productCategory.fields.id') }}</th>
                            <th>{{ trans('cruds.productCategory.fields.name') }}</th>
                            <th>{{ trans('cruds.productCategory.fields.status') }}</th>
                            <th width="150">&nbsp;</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($productCategories as $key => $productCategory)
                            <tr data-entry-id="{{ $productCategory->id }}">
                                <td></td>
                                <td>{{ $productCategory->id ?? '' }}</td>
                                <td>{{ $productCategory->StockGroupName ?? '' }}</td>
                                <td>
                                    @if($productCategory->status==1)
                                        <a class="updateCategoryStatus" id="category-{{ $productCategory->id }}" category_id="{{ $productCategory->id }}"
                                           href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to Disable Department">
                                            <span class="badge badge-success">Active</span>
                                        </a>
                                    @else
                                        <a class="updateCategoryStatus" id="category-{{ $productCategory->id }}" category_id="{{ $productCategory->id }}"
                                           href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to Enable Department">
                                            <span class="badge badge-danger">Inactive</span>
                                        </a>
                                    @endif
                                <td>
                                    @can('product_category_show')
                                        <a href="{{ route('product-categories.show', $productCategory->id) }}" data-toggle="tooltip"
                                           title="{{ trans('global.view') }} {{ trans('cruds.productCategory.title_singular') }}"
                                           data-placement="top">
                                            <i class="las dripicons-preview text-info font-18" >
                                            </i>
                                        </a>
                                        &nbsp;
                                    @endcan
                                    @can('customer_edit')
                                        <a href="{{ route('product-categories.edit', $productCategory->id) }}" data-toggle="tooltip"
                                           title="{{ trans('global.edit') }} {{ trans('cruds.productCategory.title_singular') }}"
                                           data-placement="top">
                                            <i class="las dripicons-document-edit text-info font-18"></i>
                                        </a>
                                    @endcan
                                    @can('customer_delete')
                                        <form action="{{ route('product-categories.destroy', $productCategory->id) }}" method="POST"
                                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ trans('global.delete') }} {{ trans('cruds.productCategory.title_singular') }}">
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

<script>
    $(function () {
        let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
        @can('product_category_delete')
        let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
        let deleteButton = {
            text: deleteButtonTrans,
            url: "{{ route('product-categories.massDestroy') }}",
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
        $('.datatable-ProductCategory:not(.ajaxTable)').DataTable({ buttons: dtButtons })
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
    })
</script>

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

@endpush
