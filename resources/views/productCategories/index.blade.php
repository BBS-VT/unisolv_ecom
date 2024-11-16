@extends('layouts.app')

@section('style')
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/dropify/css/dropify.min.css') }}" rel="stylesheet">
@endsection

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
                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#productCategoryModal">
                                    <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                    {{ trans('global.add') }} {{ trans('cruds.productCategory.title_singular') }}
                                </button>
                            @endcan
                            @can('productCategory_import')
                                <ul class="list-unstyled float-right mb-0">
                                    <li class="dropdown">
                                        <a href="#" class="btn btn-sm btn-outline-danger dropdown-toggle arrow-none waves-light waves-effect"
                                           data-toggle="dropdown" role="button" aria-haspopup="false" aria-expanded="false">
                                            <i data-feather="upload" class="align-self-center icon-xs"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" data-toggle="modal" data-target="#importCategorymaster" href="#">
                                                <i data-feather="upload-cloud" class="align-self-center icon-xs icon-dual me-1"></i>&nbsp;
                                                {{ trans('global.import') }} {{ trans('cruds.productCategory.title') }}
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            @endcan
                        </div>
                    </div>
                </div><!--end card-header-->
                <div class="card-body">
                    <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>{{ __('cruds.productCategory.fields.category_code') }}</th>
                            <th>{{ __('cruds.productCategory.fields.name') }}</th>
                            <th>{{ __('Main Department') }}</th>
                            <th>{{ trans('cruds.productCategory.fields.status') }}</th>
                            <th width="150">&nbsp;</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($productCategories as $key => $productCategory)
                            <tr data-entry-id="{{ $productCategory->id }}">
                                <td>{{ $productCategory->CategoryCode ?? '' }}</td>
                                <td>{{ $productCategory->StockGroupName ?? '' }}</td>
                                <td>{{ optional($productCategory->parent)->StockGroupName }}</td>
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
                                    @can('customer_edit')
                                        <button class="btn btn-sm btn-primary-outline edit-category"
                                                data-id="{{ $productCategory->id }}"
                                                data-category-code="{{ $productCategory->CategoryCode }}"
                                                data-stock-group-name="{{ $productCategory->StockGroupName }}"
                                                data-parent-id="{{ $productCategory->ParentID }}">
                                            <i class="las dripicons-document-edit text-info font-18"></i>
                                        </button>
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

    <div class="modal fade" id="importCategorymaster" tabindex="-1" role="dialog" aria-labelledby="importCategorymaster" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title m-0 text-white" id="importCategorymasterLabel">{{ trans('global.import') }} {{ trans('cruds.productCategory.title') }}</h6>
                    <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="la la-times text-white"></i></span>
                    </button>
                </div>
                <form action="{{ route('importProductCategories') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="row">
                            <input type="file" id="input-file-now" name="csv_file" class="dropify">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-gradient-danger">Import File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productCategoryModal" tabindex="-1" aria-labelledby="productCategoryModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="categoryForm" method="POST" action="{{ route('productCategories.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="productCategoryModalLabel">{{ __('Add/Edit')}} {{ __('cruds.productCategory.title_singular') }}</h5>
                        <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="la la-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="categoryId">

                        <div class="mb-3">
                            <label for="categoryCode" class="form-label">{{ __('cruds.productCategory.fields.category_code') }}</label>
                            <input type = "text" class="form-control" name="CategoryCode" id="categoryCode" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">{{ __('cruds.productCategory.fields.name') }}</label>
                            <input type="text" class="form-control" id="categoryName" name="StockGroupName" required>
                        </div>

                        <div class="mb-3">
                            <label for="parentCategory" class="form-label">{{ __('Main Department')}}</label>
                            <select class="form-select select2" id="parentCategory" name="ParentID">
                                <option value="">None</option>
                                @foreach ($productCategories as $key => $productCategory)
                                    <option value="{{ $productCategory->id }}">{{ $productCategory->StockGroupName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('global.cancel')}}</button>
                        <button type="submit" class="btn btn-danger">{{ __('global.save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-category');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.dataset.id;
                    const categoryCode = this.dataset.categoryCode;
                    const stockGroupName = this.dataset.stockGroupName;
                    const parentId = this.dataset.parentId;

                    // Populate the modal fields
                    document.getElementById('categoryId').value = categoryId;
                    document.getElementById('categoryCode').value = categoryCode;
                    document.getElementById('categoryName').value = stockGroupName;
                    document.getElementById('parentCategory').value = parentId || '';

                    // show the modal
                    const modal = new bootstrap.Modal(document.getElementById('productCategoryModal'));
                    modal.show();
                });
            });

            // Form submission with AJAX
            const editCategoryForm = document.getElementById('categoryForm');
            editCategoryForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                const id = formData.get('id');

                fetch(`/product-categories/update/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Category updated successfully');
                            window.location.reload();
                        } else {
                            alert('Error updating category: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });

        document.getElementById('categoryForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('/product-categories/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Category added successfully');
                        window.location.reload();
                    } else {
                        alert('Error adding category: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        $(function () {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)


            $.extend(true, $.fn.dataTable.defaults,{
                order: [[ 1, 'desc']],
                pageLength: 30,
            });
            $('.datatable-Customer:not(.ajaxTable)').DataTable({ buttons: dtButtons })
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        })
    </script>

    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.datatable.init.js') }}"></script>
    <script src="{{ asset('plugins/dropify/js/dropify.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.form-upload.init.js') }}"></script>

@endsection
