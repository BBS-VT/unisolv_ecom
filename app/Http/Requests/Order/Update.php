<?php


namespace App\Http\Requests\Order;

use App\Models\Order;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class Update extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'customer_id'   => [
                'required',
            ],
            'products.*'    => [
                'integer'
            ],
            'products'      => [
                'array',
            ],
        ];
    }
}
