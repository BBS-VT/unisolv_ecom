<?php

namespace App\Http\Requests\Settings\Preference;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request
     *
     * @return bool
     */
    public function authorize()
    {
        abort_if(Gate::denies('settings_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "currency_id" => 'required|integer',
            "langugage" => 'required|string|max:190',
            "timezone" => 'required|string|max:190',
            "date_format" => 'required|string|max:190',
            "financial_month_starts" => 'required|integer',
            "financial_month_ends" => 'required|integer',
        ];
    }
}
