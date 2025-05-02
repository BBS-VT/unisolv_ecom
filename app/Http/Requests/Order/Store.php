<?php


namespace App\Http\Requests\Order;

use App\Models\Order;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class Store extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {

        return [
            'order_number' => 'required|unique:orders,OrderNumber',
            'order_date' => 'required|date',
            'customer_id' => 'required|exists:customers,acc_code',
        ];

        /*if (is_array($this->product)) {
            // Make sure the length of product array is the same with other attributes of arrays
            $max_length = count($this->product);
            return [
                'order_number' => 'required|unique:orders,OrderNumber',
                'order_date' => 'required|date',
                'customer_id' => 'required|exists:customers,acc_code',
                'sub_total' => 'required',
                'grand_total' => 'required',
                'notes' => 'nullable|string',
                'private_notes' => 'nullable|string',

                'total_taxes' => 'sometimes|array|min:0',

                'product' => 'required|array|max:'.$max_length,
                'product.*' => 'required',

                'quantity' => 'required|array|max:'.$max_length,
                'quantity.*' => 'required|numeric|min:0',

                'price' => 'required|array|max:'.$max_length,
                'price.*' => 'required',

                'total' => 'required|array|max:'.$max_length,
                'total.*' => 'required',

                'taxes' => 'sometimes|required|array|max:'.$max_length,
                'taxes.*' => 'sometimes|required|array',

                'discount' => 'sometimes|required|array|max:'.$max_length,
                'discount.*' => 'sometimes|required',
            ];
        }

        return [
            'product' => 'required|array',
        ];*/
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'order_number.unique' => __('messages.order_exists'),
            'product.required' => __('messages.please_select_a_product'),
        ];
    }
}
