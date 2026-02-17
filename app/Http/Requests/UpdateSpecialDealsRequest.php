<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecialDealsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'DealDescription' => 'required|string|max:255',
            'StockItemID' => 'nullable|exists:products,StockCode',
            'StockGroupID' => 'nullable|exists:product_categories,id',
            'CustomerID' => 'nullable|exists:customers,acc_main',
            'BuyingGroupID' => 'nullable|exists:buying_groups,id',
            'CustomerCategoryID' => 'nullable|exists:customer_categories,AccountType',
            'DiscountAmount' => 'nullable|numeric|min:0',
            'DiscountPercentage' => 'nullable|numeric|min:0|max:100',
            'UnitPrice' => 'nullable|numeric|min:0',
            'StartDate' => 'required|date',
            'EndDate' => 'required|date|after_or_equal:StartDate',
            'LastEditedBy' => 'required|exists:users,id',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'StockItemID' => $this->convertEmptyToNull($this->StockItemID),
            'StockGroupID' => $this->convertEmptyToNull($this->StockGroupID),
            'CustomerID' => $this->convertEmptyToNull($this->CustomerID),
            'BuyingGroupID' => $this->convertEmptyToNull($this->BuyingGroupID),
            'CustomerCategoryID' => $this->convertEmptyToNull($this->CustomerCategoryID),
            'DiscountAmount' => $this->convertEmptyToNull($this->DiscountAmount),
            'DiscountPercentage' => $this->convertEmptyToNull($this->DiscountPercentage),
            'UnitPrice' => $this->convertEmptyToNull($this->UnitPrice),
        ]);
    }

    private function convertEmptyToNull($value)
    {
        if ($value === '' || $value === '0' || $value === 0 || is_null($value)) {
            return null;
        }
        return $value;
    }
}
