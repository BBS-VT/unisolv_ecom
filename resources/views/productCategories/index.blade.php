@extends('layouts.master')

@push('styles')
    <link href="{{ URL::asset('build/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .column-content {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table-card {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
        }
    </style>
@endpush

@section('content')
    <div class="mx-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">{{ __('cruds.productCategory.title') }}</h2>
                <p class="text-muted mb-0">{{ __('Manage product departments') }}</p>
            </div>
            <div class="d-flex gap-2">
                @can('product_category_create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#productCategoryModal">
                        <i class="fas fa-plus-circle me-1"></i>
                        {{ trans('global.add') }} {{ trans('cruds.productCategory.title_singular') }}
                    </button>
                @endcan
                @can('productCategory_import')
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#importCategorymaster">
                        <i class="fas fa-upload me-1"></i>
                        {{ trans('global.import') }}
                    </button>
                @endcan
            </div>
        </div>

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <strong>{{ __('global.success') }}!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="column-content">
            <div class="table-card">
                <div class="card-body p2">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover table-striped dt-responsive nowrap w-100">
                            <thead class="table-light">
                            <tr>
                                <th>{{ __('cruds.productCategory.fields.category_code') }}</th>
                                <th>{{ __('cruds.productCategory.fields.name') }}</th>
                                <th>{{ __('Main Department') }}</th>
                                <th>{{ __('Sales Location') }}</th>
                                <th>{{ trans('cruds.productCategory.fields.status') }}</th>
                                <th width="120" class="text-end">{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($productCategories as $key => $productCategory)
                                <tr data-entry-id="{{ $productCategory->id }}">
                                    <td>
                                        <span
                                            class="badge bg-secondary">{{ $productCategory->CategoryCode ?? '' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $productCategory->StockGroupName ?? '' }}</strong>
                                    </td>
                                    <td>
                                        @if($productCategory->parent)
                                            <span
                                                class="text-muted">{{ $productCategory->parent->StockGroupName }}</span>
                                        @else
                                            <span class="text-muted fst-italic">{{ __('None') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($productCategory->location_id)
                                            <span
                                                class="badge bg-info">{{ $productCategory->location->LocationName }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('All Locations') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input updateCategoryStatus"
                                                   type="checkbox"
                                                   id="category-{{ $productCategory->id }}"
                                                   data-category-id="{{ $productCategory->id }}"
                                                   {{ $productCategory->status == 1 ? 'checked' : '' }}
                                                   data-bs-toggle="tooltip"
                                                   title="{{ $productCategory->status == 1 ? 'Click to Disable' : 'Click to Enable' }}">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @can('customer_edit')
                                            <button class="btn btn-sm btn-outline-primary edit-category"
                                                    data-id="{{ $productCategory->id }}"
                                                    data-category-code="{{ $productCategory->CategoryCode }}"
                                                    data-stock-group-name="{{ $productCategory->StockGroupName }}"
                                                    data-parent-id="{{ $productCategory->ParentID }}"
                                                    data-location-id="{{ $productCategory->location_id }}"
                                                    data-bs-toggle="tooltip"
                                                    title="{{ trans('global.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endcan
                                        @can('customer_delete')
                                            <form
                                                action="{{ route('product-categories.destroy', $productCategory->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"
                                                        type="submit"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ trans('global.delete') }}">
                                                    <i class="fas fa-trash"></i>
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

    {{-- Import Modal --}}
    <div class="modal fade" id="importCategorymaster" tabindex="-1" aria-labelledby="importCategorymasterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importCategorymasterLabel">
                        <i class="fas fa-upload me-2"></i>
                        {{ trans('global.import') }} {{ trans('cruds.productCategory.title') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('importProductCategories') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">{{ __('Select CSV File') }}</label>
                            <input type="file"
                                   class="form-control"
                                   id="csv_file"
                                   name="csv_file"
                                   accept=".csv"
                                   required>
                            <small class="text-muted">{{ __('Upload a CSV file with category data') }}</small>
                        </div>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>{{ __('CSV Format:') }}</strong> CategoryCode, StockGroupName, ParentID, LocationID
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('global.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>
                            {{ __('Import File') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade" id="productCategoryModal" tabindex="-1" aria-labelledby="productCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="categoryForm" method="POST" action="{{ route('productCategories.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="productCategoryModalLabel">
                            <i class="fas fa-folder me-2"></i>
                            <span id="modalTitle">{{ __('Add') }}</span> {{ __('cruds.productCategory.title_singular') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="categoryId">

                        <div class="mb-3">
                            <label for="categoryCode" class="form-label">
                                {{ __('cruds.productCategory.fields.category_code') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('CategoryCode') is-invalid @enderror"
                                   name="CategoryCode"
                                   id="categoryCode"
                                   required>
                            @error('CategoryCode')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="categoryName" class="form-label">
                                {{ __('cruds.productCategory.fields.name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('StockGroupName') is-invalid @enderror"
                                   id="categoryName"
                                   name="StockGroupName"
                                   required>
                            @error('StockGroupName')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parentCategory" class="form-label">{{ __('Main Department') }}</label>
                            <select class="form-select @error('ParentID') is-invalid @enderror"
                                    id="parentCategory"
                                    name="ParentID">
                                <option value="">{{ __('None') }}</option>
                                @foreach ($productCategories as $productCategory)
                                    <option value="{{ $productCategory->id }}">
                                        {{ $productCategory->StockGroupName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ParentID')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Select a parent department if this is a subcategory') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="location_id" class="form-label">{{ __('Sales Location') }}</label>
                            <select class="form-select @error('location_id') is-invalid @enderror"
                                    id="location_id"
                                    name="location_id">
                                <option value="">{{ __('All Locations (Available Everywhere)') }}</option>
                                @foreach(\App\Models\Location::shopLocations()->get() as $location)
                                    <option value="{{ $location->id }}">
                                        {{ $location->LocationName }} {{ __('Only') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                {{ __('Leave as "All Locations" if this department should be available at every store/branch.') }}<br>
                                {{ __('Select a specific location if this department is unique to one storefront.') }}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('global.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            {{ __('global.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#datatable').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search departments..."
                }
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Edit Category
            $('.edit-category').on('click', function() {
                const categoryId = $(this).data('id');
                const categoryCode = $(this).data('category-code');
                const stockGroupName = $(this).data('stock-group-name');
                const parentId = $(this).data('parent-id');
                const locationId = $(this).data('location-id');

                // Update modal title
                $('#modalTitle').text('{{ __("Edit") }}');

                // Populate form fields
                $('#categoryId').val(categoryId);
                $('#categoryCode').val(categoryCode);
                $('#categoryName').val(stockGroupName);
                $('#parentCategory').val(parentId || '');
                $('#location_id').val(locationId || '');

                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('productCategoryModal'));
                modal.show();
            });

            // Reset form when modal is hidden
            $('#productCategoryModal').on('hidden.bs.modal', function () {
                $('#modalTitle').text('{{ __("Add") }}');
                $('#categoryForm')[0].reset();
                $('#categoryId').val('');
            });

            // Form submission
            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const categoryId = formData.get('id');
                const url = categoryId ? `/product-categories/update/${categoryId}` : '/product-categories/store';

                fetch(url, {
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
                            // Show success message
                            alert(categoryId ? 'Category updated successfully' : 'Category added successfully');
                            window.location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Something went wrong'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            });

            // Update Category Status
            $('.updateCategoryStatus').on('change', function() {
                const categoryId = $(this).data('category-id');
                const isActive = $(this).is(':checked');

                fetch(`/product-categories/update-status/${categoryId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        status: isActive ? 1 : 0
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update tooltip
                            $(this).attr('title', isActive ? 'Click to Disable' : 'Click to Enable');

                            // Show brief notification
                            const message = isActive ? 'Category enabled' : 'Category disabled';
                            console.log(message);
                        } else {
                            // Revert checkbox on error
                            $(this).prop('checked', !isActive);
                            alert('Error updating status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert checkbox on error
                        $(this).prop('checked', !isActive);
                        alert('An error occurred. Please try again.');
                    });
            });
        });
    </script>
@endpush
