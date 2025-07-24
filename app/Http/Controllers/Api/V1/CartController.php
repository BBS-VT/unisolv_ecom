<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @group Front page
 *
 * APIs for front page
 */
class CartController extends Controller
{

    /**
     * Get cart data in JSON format
     */
    public function getCartJson()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $cart = Session::get('cart', []);
        $cartTotal = $this->getCartTotal();
        $tax = $cartTotal * 0.1; // Assuming 10% tax
        $orderTotal = $cartTotal + $tax;

        return response()->json([
            'success' => true,
            'cart' => [
                'items' => $cart,
                'item_count' => array_sum(array_column($cart, 'quantity')),
                'subtotal' => $cartTotal,
                'tax' => $tax,
                'total' => $orderTotal
            ]
        ]);
    }
}
