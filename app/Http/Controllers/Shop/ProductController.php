<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Helpers\Features;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected function getProductPricing($product)
    {
        $customer = auth()->user()?->customer;
        $priceLevel = $customer->price_level ?? 1;

        $priceField = 'SellingPrice' . ($priceLevel > 1 ? $priceLevel : '');
        $basePrice = $product->SellingPrice;
        $customerPrice = $priceLevel == 1 ? $basePrice : ($product->$priceField ?? $basePrice);

        $discountPercentage = 0;
        if ($priceLevel > 1 && $basePrice > 0 && $customerPrice < $basePrice) {
            $discountPercentage = round((($basePrice - $customerPrice) / $basePrice) * 100);
        }

        $taxRate = $product->taxType ? $product->taxType->percent : 0;

        return [
            'price' => $customerPrice,
            'base_price' => $basePrice,
            'price_level' => $priceLevel,
            'discount_percentage' => $discountPercentage,
            'tax_rate' => $taxRate,
            'price_ex_tax' => $customerPrice / (1 + ($taxRate / 100)),
            'show_prices' => Features::publicPricesEnabled() || auth()->check(),
        ];
    }
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('status', true)
            ->with(['packageType', 'stockHolding']);

        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('StockItemName', 'like', "%{$search}%")
                    ->orWhere('StockCode', 'like', "%{$search}%")
                    ->orWhere('Barcode', 'like', "%{$search}%");
            });
        }

        // Filter by price
        if ($request->has('min_price')) {
            $query->where('SellingPrice', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('SellingPrice', '<=', $request->input('max_price'));
        }

        // Only show products with stock if required
        if (\App\Models\CompanySetting::getSetting('ecommerce_show_stock', auth()->user()?->currentCompany()?->id) == false) {
            $query->whereHas('stockHolding', function($q) {
                $q->where('QuantityOnHand', '>', 0);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('StockItemName', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('SellingPrice', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('SellingPrice', 'desc');
                break;
            default:
                $query->orderBy('StockItemName', 'asc');
        }

        $products = $query->paginate(Features::productsPerPage());

        $categories = ProductCategory::withCount(['products' => function ($q) {
            $q->where('status', true);
        }])
            ->where('status', true)
            ->having('products_count', '>', 0)
            ->orderBy('StockGroupName')
            ->get();

        return view('shop.products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->orWhere('id', $slug)
            ->with(['packageType', 'stockHolding', 'categories'])
            ->firstOrFail();

        $relatedProducts = Product::whereHas('categories', function($query) use ($product) {
            $query->whereIn('id', $product->categories->pluck('id'));
        })
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->take(4)
            ->get();

        return view('shop.products.show', compact('product', 'relatedProducts'));
    }

    public function category($slug)
    {
        // Find category by slug or ID
        $category = ProductCategory::where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $query = Product::query()
            ->where('status', true)
            ->whereHas('categories', function($q) use ($category) {
                $q->where('product_categories.id', $category->id);
            })
            ->with(['packageType', 'stockHolding']);

        // Apply filters (same as index method)
        if (request()->has('search')) {
            $search = request()->input('search');
            $query->where(function($q) use ($search) {
                $q->where('StockItemName', 'like', "%{$search}%")
                    ->orWhere('StockCode', 'like', "%{$search}%");
            });
        }

        // Price filtering
        if (request()->has('min_price')) {
            $query->where('SellingPrice', '>=', request()->input('min_price'));
        }
        if (request()->has('max_price')) {
            $query->where('SellingPrice', '<=', request()->input('max_price'));
        }

        // Only show products with stock if required
        if (\App\Models\CompanySetting::getSetting('ecommerce_show_stock', auth()->user()?->currentCompany()?->id) == false) {
            $query->whereHas('stockHolding', function($q) {
                $q->where('QuantityOnHand', '>', 0);
            });
        }

        // Sorting
        $sort = request()->input('sort', 'name_asc');
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('StockItemName', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('SellingPrice', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('SellingPrice', 'desc');
                break;
            default:
                $query->orderBy('StockItemName', 'asc');
        }

        $products = $query->paginate(Features::productsPerPage());

        // Get all categories for the filter
        $categories = ProductCategory::withCount(['products' => function ($q) {
            $q->where('status', true);
        }])
            ->where('status', true)
            ->having('products_count', '>', 0)
            ->orderBy('StockGroupName')
            ->get();

        return view('shop.products.category', compact('products', 'categories', 'category'));
    }
}
