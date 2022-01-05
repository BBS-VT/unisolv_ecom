<?php


namespace App\Http\Requests;

use App\Models\Order;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            /*'CustomerID' => [
                'required',
            ],
            'SalesPersonID' => [
                'required',
            ],
            'OrderDate' => [
                'required',
            ],
            'CustomerPurchaseOrderNumber' => [
                'required',
            ],
            'LastEditedBy' => [
                'required',
            ],*/
        ];
    }
}
