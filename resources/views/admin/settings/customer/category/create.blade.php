@extends('layouts.app', ['page' => 'settings'])

@section('title', __('global.create_new_category'))

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">

        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-2 col-sm-3">
        @include('admin.settings._aside', ['tab' => 'customers'])
    </div>
    <div class="col-xl-10 col-sm-9">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h4 class="card-title">{{ __('global.add') }} {{ __('cruds.customerCategory.title') }}</h4>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-primary">
                            <i data-feather="arrow-left-circle" class="align-self-center icon-xs"></i>
                            {{ __('global.back_to_list') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.customer-category.store') }}" method="POST">
                    @include('layouts._form_errors')
                    @csrf

                    @include('admin.settings.customer.category._form')

                    <div class="form-group text-right mt-4">
                        <button type="submit" class="btn btn-danger">{{ __('global.save') }}</button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

{{--@push('custom-scripts')
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
            @can('customer_category_delete')
            let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
            let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.customer-category.massDestroy') }}",
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
@endpush--}}
