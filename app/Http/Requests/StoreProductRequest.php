<?php


namespace App\Http\Requests;

use App\Models\Product;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;

    }

    public function rules()
    {
        return [
            'StockCode' => 'required|string|max:50|unique:products,StockCode',
            'StockItemName' => 'required|string|max:255',
            'TaxRateID' => 'nullable|string|max:10',
            'Barcode' => 'nullable|string|max:50',
            'AltBarCode' => 'nullable|string|max:50',
            'SellingPrice' => 'required|numeric|min:0',
            'SellingPrice2' => 'nullable|numeric|min:0',
            'SellingPrice3' => 'nullable|numeric|min:0',
            'SellingPrice4' => 'nullable|numeric|min:0',
            'AverageCostPrice' => 'nullable|numeric|min:0',
            'LastCostPrice' => 'nullable|numeric|min:0',
            'DiscountPercentage' => 'nullable|numeric|min:0|max:100',
            'MarketingComments' => 'nullable|string|max:1000',
            'Size' => 'nullable|string|max:50',
            'Packsize' => 'nullable|string|min:1|max:9999',
            'QuantityOnHand' => 'nullable|numeric|min:0',

            'refer_code' => [
                'nullable',
                'string',
                'max:50',
                'exists:products,StockCode',
                function ($attribute, $value, $fail) {
                    if ($value && $value === $this->input('StockCode')) {
                        $fail('A product cannot refer to itself.');
                    }
                }
            ],

            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:product_categories,id',
            'subCategories' => 'nullable|array',
            'subCategories.*' => 'integer|exists:product_categories,id',
        ];
    }

    public function messages()
    {
        return [
            'StockCode.required' => 'Stock code is required.',
            'StockCode.unique' => 'This stock code already exists.',
            'StockItemName.required' => 'Product name is required.',
            'SellingPrice.required' => 'Selling price is required.',
            'SellingPrice.min' => 'Selling price must be greater than or equal to 0.',
            'refer_code.exists' => 'The selected refer code does not exist.',
            'pack_size.required' => 'Pack size is required.',
            'pack_size.min' => 'Pack size must be at least 1.',
            'pack_size.max' => 'Pack size cannot exceed 9999.',
        ];
    }
}
