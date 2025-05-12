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
            'ecommerce_new_customer_requires_approval' => 'boolean'
        ];
    }

    protected function prepareForValidation()
    {
        foreach ($this->rules() as $key => $value) {
            // Set values of checked checkboxes from 'on' to true
            if ($this->$key == 'on') {
                $this->merge([
                    $key => 1,
                ]);
            }

            // Add unposted checkbox values as false
            if (!$this->has($key)) {
                $this->merge([
                    $key => 0,
                ]);
            }
        }
    }
}
