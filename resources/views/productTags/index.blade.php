@extends('layouts.master')

@push('styles')
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

@endpush

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <div class="row">
                            <div class="col">
                                <h4 class="page-title">{{ trans('cruds.productTag.title') }}</h4>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ trans('global.home') }}</a></li>
                                    <li class="breadcrumb-item active">{{ trans('cruds.productTag.title') }} {{ trans('global.list') }}</li>
                                </ol>
                            </div>
                            <div class="col-auto align-self-center">

                                @can('product_tag_create')
                                    <a href="{{ route("product-tags.create") }}" class="btn btn-sm btn-outline-primary">
                                        <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                        {{ trans('global.add') }} {{ trans('cruds.productTag.title_singular') }}
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
                        <div class="card-header">
                            <h4 class="card-title">{{ trans('cruds.productTag.title_singular') }} {{ trans('global.list') }}</h4>
                            <p class="text-muted mb-0">
                            </p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class=" table table-bordered table-striped table-hover datatable datatable-ProductTag">
                                    <thead>
                                    <tr>
                                        <th width="10">

                                        </th>
                                        <th>
                                            {{ trans('cruds.productTag.fields.id') }}
                                        </th>
                                        <th>
                                            {{ trans('cruds.productTag.fields.name') }}
                                        </th>
                                        <th >
                                            &nbsp;
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($productTags as $key => $productTag)
                                        <tr data-entry-id="{{ $productTag->id }}">
                                            <td>

                                            </td>
                                            <td>
                                                {{ $productTag->id ?? '' }}
                                            </td>
                                            <td>
                                                {{ $productTag->name ?? '' }}
                                            </td>
                                            <td>
                                                @can('product_tag_show')
                                                    <a  href="{{ route('product-tags.show', $productTag->id) }}" data-bs-toggle="tooltip"
                                                    title="{{ trans('global.view') }} {{ trans('cruds.productTag.title_singular') }}"
                                                    data-bs-placement="top">
                                                        <i class="las dripicons-preview text-info font-18"></i>
                                                    </a>
                                                @endcan
                                                    &nbsp;
                                                @can('product_tag_edit')
                                                    <a href="{{ route('product-tags.edit', $productTag->id) }}" data-bs-toggle="tooltip"
                                                       title="{{ trans('global.edit') }} {{ trans('cruds.productTag.title_singular') }}"
                                                       data-bs-placement="top">
                                                        <i class="las dripicons-document-edit text-info font-18"></i>
                                                    </a>
                                                @endcan
                                                    &nbsp;
                                                @can('product_tag_delete')
                                                    <form action="{{ route('product-tags.destroy', $productTag->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                title="{{ trans('global.delete') }} {{ trans('cruds.productTag.title_singular') }}">
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
            </div>
        </div>
    </div>

@endsection

@push('scripts')

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
            @can('product_tag_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('product-tags.massDestroy') }}",
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
            $('.datatable-ProductTag:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
    </script>

@endpush
