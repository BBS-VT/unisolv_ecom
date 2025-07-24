<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Helpers\Features;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        /*$categories = ProductCategory::withCount(['products' => function ($query) {
            $query->where('status', true);
            }])
            ->where('status', true)
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->take(8)
            ->get();

        $featuredProductsQuery = Product::query()
            ->where('status', true)
            ->where(function($query) {
                $query->where('is_featured', true)
                    ->orWhereHas('categories', function($q) {
                        $q->where('is_featured', true);
                });
            });

        if (\App\Models\CompanySetting::getSetting('ecommerce_show_stock', auth()->user()?->currentCompany()?->id) == false) {
            $featuredProductsQuery->whereHas('stockHolding', function($query) {
                $query->where('QuantityOnHand', '>', 0);
            });
        }

        $featuredProducts = $featuredProductsQuery->take(8)->get();*/

        return view('shop.home.index', compact('categories', 'featuredProducts'));
    }
}
