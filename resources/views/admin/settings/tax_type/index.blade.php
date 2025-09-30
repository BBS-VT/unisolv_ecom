@extends('layouts.master', ['page' => 'settings'])

@section('title', __('cruds.taxType.title'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">{{ __('Settings') }}</h4>
                <div class="page-title-right"></div>
            </div>
        </div>
    </div>

    <div class="d-xl-flex">
        <div class="w-100">
            <div class="d-md-flex">
                <div class="card filemanager-sidebar me-md-2">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-4">
                                @include('admin.settings._aside', ['tab' => 'tax_types'])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-100">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0">{{ __('cruds.taxType.title') }}</h5>
                                </div>
                                <div class="col-auto">
                                    @can('settings_create')
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createTaxTypeModal">
                                            <i class="bx bx-plus-circle me-1"></i>
                                            {{ __('global.add') }} {{ __('cruds.taxType.title_singular') }}
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if($tax_types->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>{{ __('cruds.taxType.fields.name') }}</th>
                                            <th>{{ __('cruds.taxType.fields.percent') }}</th>
                                            <th>{{ __('cruds.taxType.fields.description') }}</th>
                                            <th class="text-center" style="width: 120px;">{{ __('global.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($tax_types as $tax_type)
                                            <tr>
                                                <td>
                                                    <strong>{{ $tax_type->name }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary">{{ $tax_type->percent }}%</span>
                                                </td>
                                                <td>
                                                    @if($tax_type->description)
                                                        <small class="text-muted">{{ Str::limit($tax_type->description, 60) }}</small>
                                                    @else
                                                        <small class="text-muted fst-italic">{{ __('global.no_description') }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        @can('settings_edit')
                                                            <button type="button" class="btn btn-outline-success"
                                                                    title="{{ __('global.edit') }}"
                                                                    onclick="editTaxType({{ $tax_type->id }})">
                                                                <i class="bx bx-edit-alt"></i>
                                                            </button>
                                                        @endcan

                                                        @can('settings_delete')
                                                            <button type="button" class="btn btn-outline-danger"
                                                                    title="{{ __('global.delete') }}"
                                                                    onclick="deleteTaxType({{ $tax_type->id }}, '{{ $tax_type->name }}')">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if($tax_types->hasPages())
                                    <div class="row mt-3">
                                        <div class="col">
                                            {{ $tax_types->links() }}
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="bx bx-receipt display-1 text-muted opacity-25"></i>
                                    <h5 class="mt-3 text-muted">{{ __('global.no_tax_types_yet') }}</h5>
                                    <p class="text-muted">{{ __('messages.add_first_tax_type') }}</p>
                                    @can('settings_create')
                                        <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createTaxTypeModal">
                                            <i class="bx bx-plus-circle me-1"></i>
                                            {{ __('global.add') }} {{ __('cruds.taxType.title_singular') }}
                                        </button>
                                    @endcan
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Include Modals --}}
    @include('admin.settings.tax_type._modals')

    @push('scripts')
        <script>
            function editTaxType(taxTypeId) {
                fetch(`{{ route('settings.tax_types.edit', '') }}/${taxTypeId}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('editTaxTypeModalContent').innerHTML = html;
                        const modal = new bootstrap.Modal(document.getElementById('editTaxTypeModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('{{ __("global.error_loading_data") }}');
                    });
            }

            function deleteTaxType(taxTypeId, taxTypeName) {
                if (confirm(`{{ __('global.are_you_sure_delete') }} "${taxTypeName}"?`)) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('settings/tax-types') }}/${taxTypeId}`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endpush
@endsection
