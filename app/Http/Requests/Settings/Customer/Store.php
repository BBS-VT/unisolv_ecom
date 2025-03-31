<?php

namespace App\Http\Requests\Settings\Customer;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'accountType' => 'required|max:5',
        ];
    }
}
