<div class="row">
    <div class="col-lg-12">
        <div class="mb-3 row required">
            <label for="accountType" class="col-sm-3 form-label align-self-center mb-lg-0 text-end" required>
                {{ __('cruds.customerCategory.fields.account_type') }}
            </label>
            <div class="col-sm-9">
                <input name="accountType" id="accountType" type="text" class="form-control" placeholder="{{ __('cruds.customerCategory.fields.account_type') }}"
                       value="{{ $customerCategory->AccountType }}" >
            </div>

        </div>
        <div class="mb-3 row">
            <label for="customerCategoryname" class="col-sm-3 form-label align-self-center mb-lg-0 text-end">
                {{ __('cruds.customerCategory.fields.category_name') }}
            </label>
            <div class="col-sm-9">
                <input class="form-control" name="customerCategoryname" type="text" value="{{ $customerCategory->CustomerCategoryName }}"
                       placeholder="{{ __('cruds.customerCategory.fields.category_name') }}" >
            </div>
        </div>

    </div>
</div>
