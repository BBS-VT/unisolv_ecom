<?php

namespace App\Http\Requests\Settings\Location;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'LocationCode' => 'required|string|max:10|unique:locations,LocationCode',
            'fullfillment_email' => 'nullable|string|email|max:255',
            'LocationName' => 'required|string|max:100',
            'LocationDescription' => 'nullable|string|max:500',
            'Address1' => 'nullable|string|max:255',
            'Address2' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:100',
            'Province' => 'nullable|string|max:100',
            'PostalCode' => 'nullable|string|max:20',
            'Country' => 'nullable|string|max:100',
            'Phone' => 'nullable|string|max:50',
            'Email' => 'nullable|email|max:255',
            'ContactPerson' => 'nullable|string|max:255',
            'IsActive' => 'boolean',
            'IsDefault' => 'boolean',
            'SortOrder' => 'nullable|integer|min:0|max:999',
        ];
    }

    public function attributes()
    {
        return [
            'LocationCode' => __('global.location_code'),
            'LocationName' => __('global.location_name'),
            'LocationDescription' => __('global.description'),
            'Address1' => __('global.address_line_1'),
            'Address2' => __('global.address_line_2'),
            'City' => __('global.city'),
            'Province' => __('global.province'),
            'PostalCode' => __('global.postal_code'),
            'Country' => __('global.country'),
            'Phone' => __('global.phone'),
            'Email' => __('global.email'),
            'ContactPerson' => __('global.contact_person'),
            'IsActive' => __('global.active'),
            'IsDefault' => __('global.default'),
            'SortOrder' => __('global.sort_order'),
        ];
    }

    public function messages()
    {
        return [
            'LocationCode.unique' => __('validation.location_code_exists'),
            'LocationCode.max' => __('validation.location_code_max_length'),
            'Email.email' => __('validation.email_invalid'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'LocationCode' => strtoupper($this->LocationCode),
            'IsActive' => $this->has('IsActive') ? true : false,
            'IsDefault' => $this->has('IsDefault') ? true : false,
            'SortOrder' => $this->SortOrder ?? 0,
        ]);
    }
}
