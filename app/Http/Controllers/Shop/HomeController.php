<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('StockGroupName')->get();

        $query = Product::query();

        // Apply filters based on request parameters
        if ($request->has('category') && $request->category) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('SellingPrice', [$request->min_price, $request->max_price]);
        }

        if ($request->has('search') && $request->search) {
            $query->where('StockItemName', 'like', '%' . $request->search . '%')
                ->orWhere('MarketingComments', 'like', '%' . $request->search . '%');
        }

        $products = $query->with('categories')
            ->orderBy('StockItemName')
            ->paginate(9);

        foreach ($products as $product) {
            $product->formatted_price = number_format($product->UnitPrice, 2);
        }

        return view('home', compact('categories', 'products'));
    }
}
