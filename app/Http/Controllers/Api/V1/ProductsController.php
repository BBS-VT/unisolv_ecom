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
class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::when(request()->input('category'), function ($query) {
            $query->whereHas('categories', function ($query) {
                $query->where('id', request()->input('category'));
            });
        })
            ->paginate(6);

        return new ProductResource($products);
    }
}
