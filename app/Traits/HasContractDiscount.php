<?php

namespace App\Traits;

use App\Models\OrdersItem;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContractDiscount
{
    /** Check whether model has Contract Discount
     *
     * @return boolean
     */
    public function hasContractDiscount()
    {
        $matchCustomer = $request->customer_id;
    }
}

