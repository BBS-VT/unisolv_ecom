@extends('layouts.app')

@push('style')
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                @if($message = Session::get('success'))
                    <div class="alert alert-info alert-dismissible fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">x</span>
                        </button>
                        <strong>{{ trans('global.success') }}</strong> {{ $message }}
                    </div>
                @endif
                {!! Session::forget('success') !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">{{ trans('cruds.product.title_singular') }} {{ trans('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <button type="button" class="btn btn-sm btn-outline-dark">Active</button>
                                <button type="button" class="btn btn-sm btn-outline-dark">Inactive</button>
                                <button type="button" class="btn btn-sm btn-outline-dark">All</button>
                            </div>
                            @can('product_create')
                                <a href="{{ route("products.create") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }} {{ trans('cruds.product.title_singular') }}
                                </a>
                                <a href="{{ route("product-tags.create") }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }} {{ trans('cruds.productTag.title_singular') }}
                                </a>
                            @endcan
                            @can('product_import')
                                <ul class="list-unstyled float-right mb-0">
                                    <li class="dropdown">
                                        <a href="#" class="btn btn-sm btn-outline-danger dropdown-toggle arrow-none waves-light waves-effect"
                                            data-toggle="dropdown" role="button" aria-haspopup="false" aria-expanded="false">
                                            <i data-feather="upload" class="align-self-center icon-xs"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" data-toggle="modal" data-target="#importStockmaster" href="#">
                                                <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                                {{ trans('global.import') }} {{ trans('cruds.product.title') }}
                                            </a>
                                            <a class="dropdown-item" data-toggle="modal" data-target="#importQuantities" href="#">
                                                <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                                {{ trans('global.import') }} {{ trans('cruds.product.fields.quantity') }}
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th width="10">&nbsp;</th>
                                <th>{{ trans('cruds.product.fields.sku') }}</th>
                                <th>{{ trans('cruds.product.fields.name') }} </th>
                                <th>{{ trans('cruds.product.fields.barcode') }}</th>
                                <th>{{ trans('cruds.product.fields.price') }}</th>
                                <th>{{ trans('cruds.product.fields.category') }}</th>
<!--                            <th>{{ trans('cruds.product.fields.tag') }}</th>-->
                                <th>{{ trans('cruds.product.fields.quantity') }}</th>
                                <th>{{ trans('cruds.product.fields.status') }}</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $key => $product)
                            <tr data-entry-id="{{ $product->id }}">
                                <td></td>
                                <td>{{ $product->StockCode ?? '' }}</td>
                                <td>{{ $product->StockItemName ?? '' }}</td>
                                <td>
                                    {{ $product->Barcode ?? '' }}
                                    {{ $product->AltBarcode ?? '' }}
                                </td>
{{--                                <td>{{ $product->SellingPrice ?? '' }}</td>--}}
                                <td>{{ number_format($product->SellingPrice, 2, '.', ' ') }}</td>
                                <td>
                                    @foreach($product->categories as $key => $item)
                                        <span class="badge badge-info">{{ $item->StockGroupName }}</span>
                                    @endforeach
                                </td>
<!--                                <td>
                                    @foreach($product->tags as $key => $item)
                                        <span class="badge badge-info">{{ $item->name }}</span>
                                    @endforeach
                                </td>-->
                                <td>{{ $product->stockHolding->QuantityOnHand ?? '' }}</td>
                                <td>
                                    @if($product->status==1)
                                        <a class="updateProductStatus" id="product-{{ $product->id }}" product_id="{{ $product->id }}"
                                           href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to Disable Product">
                                            <span class="badge badge-success">Active</span>
                                        </a>
                                    @else
                                        <a class="updateProductStatus" id="product-{{ $product->id }}" product_id="{{ $product->id }}"
                                           href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="Click to Enable Product">
                                            <span class="badge badge-danger">Inactive</span>
                                        </a>
                                    @endif
                                </td>
                                <td class="mx-auto">
                                    @can('product_show')
                                        <a href="{{ route('products.show', $product->id) }}" data-toggle="tooltip"
                                           title="{{ trans('global.view') }} {{ trans('cruds.product.title_singular') }}"
                                           data-placement="top">
                                            <i class="las dripicons-preview text-info font-18"></i>
                                        </a>
                                    @endcan
                                    &nbsp;
                                    @can('product_edit')
                                        <a href="{{ route('products.edit', $product->id) }}" data-toggle="tooltip"
                                           title="{{ trans('global.edit') }} {{ trans('cruds.product.title_singular') }}"
                                           data-placement="top">
                                            <i class="las dripicons-document-edit text-info font-18"></i>
                                        </a>
                                    @endcan
                                    &nbsp;
                                    @can('product_delete')
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                              onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ trans('global.delete') }} {{ trans('cruds.product.title_singular') }}">
                                                <i class="dripicons-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="modal fade" id="importQuantities" tabindex="-1" role="dialog" aria-labelledby="importQuantitiesLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h6 class="modal-title m-0 text-white" id="importQuantitiesLabel">{{ trans('global.import') }} {{ trans('cruds.product.fields.quantity') }}</h6>
                                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                    </button>
                                </div>
                                <form action="{{ route('importQuantities') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                        <div class="row">
                                            <input type="file" id="input-file-now" name="import_file" class="dropify">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-gradient-danger">Import File</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="importStockmaster" tabindex="-1" role="dialog" aria-labelledby="importStockmaster" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h6 class="modal-title m-0 text-white" id="importStockmasterLabel">{{ trans('global.import') }} {{ trans('cruds.product.title') }}</h6>
                                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                                    </button>
                                </div>
                                <form action="{{ route('importStockmaster') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                        <div class="row">
                                            <input type="file" id="input-file-now" name="import_file" class="dropify">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-gradient-danger">Import File</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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

    <script src="{{ asset('plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.form-upload.init.js') }}"></script>

    <script>

        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('product_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('products.massDestroy') }}",
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
            $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
@endpush





