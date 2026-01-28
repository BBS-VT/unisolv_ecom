<?php

namespace App\Http\Requests\Settings\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    public function rules()
    {
        return [
            'b2b_ecommerce_enabled' => 'boolean',
            'ecommerce_guest_checkout' => 'boolean',
            'ecommerce_public_prices' => 'boolean',
            'ecommerce_show_product_images' => 'boolean',
            'ecommerce_backorders' => 'boolean',
            'ecommerce_require_approval' => 'boolean',
            'ecommerce_show_stock' => 'boolean',
            'ecommerce_min_order_amount' => 'numeric|min:0',
            'ecommerce_products_per_page' => 'integer|min:1|max:100',
            'ecommerce_new_customer_requires_approval' => 'boolean',
            'sales_locations' => 'boolean',
            'ecommerce_delivery_enabled' => 'boolean',
            'ecommerce_collection_hours_weekday' => 'nullable|string|max:255',
            'ecommerce_collection_hours_saturday' => 'nullable|string|max:255',
            'ecommerce_collection_hours_sunday' => 'nullable|string|max:255',
            'ecommerce_processing_time' => 'nullable|string|max:255',
        ];
    }
    protected function prepareForValidation()
    {
        $rules = $this->rules();

        foreach ($rules as $key => $rule) {
            // Only handle boolean fields for checkbox conversion
            $isBooleanField = is_string($rule) && str_contains($rule, 'boolean');

            if ($isBooleanField) {
                // Set values of checked checkboxes from 'on' to 1
                if ($this->input($key) === 'on') {
                    $this->merge([
                        $key => 1,
                    ]);
                }

                // Add unposted checkbox values as 0 (unchecked)
                if (!$this->has($key)) {
                    $this->merge([
                        $key => 0,
                    ]);
                }
            }
        }
    }

}
