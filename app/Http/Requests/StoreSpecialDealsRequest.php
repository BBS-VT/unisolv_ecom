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

    /**
     * Prepare the data for validation.
     * Convert empty strings and "0" to null for foreign key fields
     */
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

    /**
     * Helper to convert empty strings, "0", or 0 to null
     */
    private function convertEmptyToNull($value)
    {
        if ($value === '' || $value === '0' || $value === 0 || is_null($value)) {
            return null;
        }
        return $value;
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'DealDescription.required' => 'Please enter a description for this deal.',
            'StockItemID.exists' => 'The selected product does not exist.',
            'StockGroupID.exists' => 'The selected department does not exist.',
            'CustomerID.exists' => 'The selected customer does not exist.',
            'BuyingGroupID.exists' => 'The selected buying group does not exist.',
            'CustomerCategoryID.exists' => 'The selected customer group does not exist.',
            'EndDate.after_or_equal' => 'The end date must be on or after the start date.',
        ];
    }
}
