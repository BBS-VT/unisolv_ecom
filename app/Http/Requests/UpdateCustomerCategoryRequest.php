<?php


namespace App\Http\Requests;

use App\Models\CustomerCategory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateCustomerCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('customer_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'CustomerCategoryName' => [
                'required',
            ],
        ];
    }
}

