@extends('layouts.app')

@push('style')
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

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
                            <h4 class="card-title">{{ trans('cruds.user.title_singular') }} {{ trans('global.list') }}</h4>
                        </div>
                        <div class="col-auto align-self-center">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.add') }} {{ trans('cruds.user.title_singular') }}
                            </a>
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-sm btn-outline-primary">
                                <i data-feather="plus-circle" class="align-self-center icon-xs"></i>
                                {{ trans('global.add') }} {{ trans('cruds.role.title_singular') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap " style="border-collapse: collapse; border-spacing: 0; width:100%">
                        <thead>
                        <tr>
                            <th width="5"></th>
                            <th>{{ trans('cruds.user.fields.id') }}</th>
                            <th>{{ trans('cruds.user.fields.fullname') }}</th>
                            <th>{{ trans('cruds.user.fields.email') }}</th>
                            <th>{{ trans('cruds.user.fields.repcode') }}</th>
                            <th>{{ trans('cruds.user.fields.roles') }}</th>
                            <th>&nbsp;</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach($users as $key => $user)
                                <tr data-entry-id="{{ $user->id }}">
                                    <td></td>
                                    <td>{{ $user->id ?? '' }}</td>
                                    <td>{{ $user->FullName ?? '' }}</td>
                                    <td>{{ $user->email ?? '' }}</td>
                                    <td>{{ $user->RepCode ?? '' }}</td>
                                    <td>
                                        @foreach($user->roles as $key => $item)
                                            <span class="badge badge-info">{{ $item->title }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @can('user_show')
                                            <a href="{{ route('admin.users.show', $user->id) }}" data-toggle="tooltip"
                                               title="{{ trans('global.view') }} {{ trans('cruds.user.title_singular') }}"
                                               data-placement="top">
                                                <i class="las dripicons-preview text-info font-18"></i>
                                            </a>
                                        @endcan
                                        &nbsp;
                                        @can('user_edit')
                                            <a href="{{ route('admin.users.edit', $user->id) }}" data-toggle="tooltip"
                                               title="{{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }}"
                                               data-placement="top">
                                                <i class="las dripicons-document-edit text-info font-18"></i>
                                            </a>
                                        @endcan
                                        &nbsp;
                                        @can('user_delete')
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                  onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button aria-expanded="false" class="text-danger font-18" style="border:none; background: none;" type="submit"
                                                        data-toggle="tooltip" data-placement="top"
                                                        title="{{ trans('global.delete') }} {{ trans('cruds.user.title_singular') }}">
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

    <script>
        $(document).ready(function () {
            $('#datatable-buttons').DataTable({
                dom: 'Bfrtip',
                lengthMenu: [
                    [ 10, 25, 50, -1 ],
                    [ '10 rows', '25 rows', '50 rows', 'Show all']
                ],
                buttons: [
                   'copy', 'csv', 'excel', 'pdf', 'print', 'pageLength'
                ],
               "order": [[ 1, "asc" ]],
            });
        });
    </script>
@endpush
