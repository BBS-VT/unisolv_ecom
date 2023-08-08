<?php

namespace App\Http\Requests\Settings\Customer;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /*
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    public function rules()
    {
        return [
            'display_subaccount'    => 'required|boolean',
        ];
    }

    /*
     * Prepare the data for validation
     */
    protected function prepareForValidation()
    {
        foreach ($this->rules() as $key => $value) {
            // Set values of checked checkboxes from 'on' to true
            if ($this->$key == 'on') {
                $this->merge([
                    $key => 1,
                ]);
            }

            // Add unposted checkbox values as false
            if (!$this->has($key)) {
                $this->merge([
                    $key => 0
                ]);
            }
        }
    }
}
