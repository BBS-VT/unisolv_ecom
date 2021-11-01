<div class="row">
    <div class="col">
        <div class="form-group required">
            <label for="name">{{ __('cruds.taxType.fields.name') }}</label>
            <input name="name" type="text" class="form-control" placeholder="{{ __('cruds.taxType.fields.name') }}" value="{{ $tax_type->name }}" required>
        </div>
    </div>
    <div class="col">
        <div class="form-group required">
            <label for="percent">{{ __('cruds.taxType.fields.percent') }}</label>
            <input name="percent" type="number" class="form-control" placeholder="{{ __('cruds.taxType.fields.percent') }}" value="{{ $tax_type->percent }}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="description">{{ __('cruds.taxType.fields.description') }}</label>
            <textarea name="description" class="form-control" placeholder="{{ __('cruds.taxType.fields.description') }}" rows="4">{{ $tax_type->description }}</textarea>
        </div>
    </div>
</div>
