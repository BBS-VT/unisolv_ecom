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

        $this->applyFilters($query, $request);

        $products = $query->paginate(Features::productsPerPage());

        $products->getCollection()->transform(function ($product) {
            $product->pricing = $this->getProductPricing($product);
            return $product;
        });

        $categories = $this->getCategories();

        return view('shop.products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->orWhere('id', $slug)
            ->with([ 'stockHolding', 'categories'])
            ->firstOrFail();

        // Add pricing information to the product
        $product->pricing = $this->getProductPricing($product);

        // Get related products
        $relatedProducts = Product::whereHas('categories', function($query) use ($product) {
            $query->whereIn('id', $product->categories->pluck('id'));
        })
            ->where('id', '!=', $product->id)
            ->where('status', true)
            ->take(4)
            ->get();

        // Add pricing to related products
        $relatedProducts->transform(function ($relatedProduct) {
            $relatedProduct->pricing = $this->getProductPricing($relatedProduct);
            return $relatedProduct;
        });

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

        $this->applyFilters($query, request());

        $products = $query->paginate(Features::productsPerPage());

        $products->getCollection()->transform(function ($product) {
            $product->pricing = $this->getProductPricing($product);
            return $product;
        });

        $categories = $this->getCategories();

        return view('shop.products.category', compact('products', 'categories', 'category'));
    }

    protected function applyFilters($query, Request $request)
    {
        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('StockItemName', 'like', "%{$search}%")
                    ->orWhere('StockCode', 'like', "%{$search}%")
                    ->orWhere('Barcode', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('categories')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->whereIn('id', $request->categories);
            });
        }

        // Price filtering
        if ($request->has('min_price')) {
            $query->where('SellingPrice', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('SellingPrice', '<=', $request->input('max_price'));
        }

        // Stock filter
        if ($request->has('in_stock_only')) {
            $query->whereHas('stockHolding', function($q) {
                $q->where('QuantityOnHand', '>', 0);
            });
        }

        // Only show products with stock if setting requires it
        if (!Features::backordersEnabled()) {
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
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('StockItemName', 'asc');
        }
    }

    protected function getCategories()
    {
        return ProductCategory::withCount(['products' => function ($q) {
            $q->where('status', true);
        }])
            ->where('status', true)
            ->having('products_count', '>', 0)
            ->orderBy('StockGroupName')
            ->get();
    }
}
