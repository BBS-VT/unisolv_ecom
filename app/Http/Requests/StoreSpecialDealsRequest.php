<?php


namespace App\Http\Requests;

use App\Models\SpecialDeals;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreSpecialDealsRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('specialdeal_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            /*'StartDate'         => [
                'required',
            ],
            'EndDate'         => [
                'required',
            ],*/

        ];
    }
}
