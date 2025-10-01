<?php

namespace App\Http\Controllers\Shop;

use App\Helpers\PricingHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Helpers\Features;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        $query = Product::query()
            ->where('status', true)
            ->forOnline()
            ->with(['packageType', 'stockHolding']);

        // Only show products with stock if backorders are disabled
        if (!Features::backordersEnabled()) {
            $query->whereHas('stockHolding', function ($q) {
                $q->selectRaw('StockCode, SUM(QuantityOnHand) as total_quantity')
                    ->groupBy('StockCode')
                    ->havingRaw('total_quantity > 0');
            });
        }

        // Category filter
        if ($request->has('categories') && !empty($request->categories)) {
            $categoryIds = is_array($request->categories) ? $request->categories : explode(',', $request->categories);
            $query->whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('product_categories.id', $categoryIds);
            });
        }

        // Price filter
        if ($request->has('price_range') && !empty($request->price_range)) {
            $range = explode('-', $request->price_range);
            $min = $range[0] ?? 0;
            $max = $range[1] ?? null;

            // Apply price filter based on customer's price level
            $customer = auth()->user()?->customer;
            $priceLevel = $customer->price_level ?? 1;
            $priceField = 'SellingPrice' . ($priceLevel > 1 ? $priceLevel : '');

            $query->where($priceField, '>=', $min);
            if ($max) {
                $query->where($priceField, '<=', $max);
            }
        }

        // Custom price range
        if (($request->has('price_min') && !empty($request->price_min)) ||
            ($request->has('price_max') && !empty($request->price_max))) {

            $customer = auth()->user()?->customer;
            $priceLevel = $customer->price_level ?? 1;
            $priceField = 'SellingPrice' . ($priceLevel > 1 ? $priceLevel : '');

            if ($request->price_min) {
                $query->where($priceField, '>=', $request->price_min);
            }
            if ($request->price_max) {
                $query->where($priceField, '<=', $request->price_max);
            }
        }

        // Availability filter
        if ($request->has('availability') && !empty($request->availability)) {
            $availability = is_array($request->availability) ? $request->availability : explode(',', $request->availability);

            $query->where(function($q) use ($availability) {
                foreach ($availability as $status) {
                    switch ($status) {
                        case 'in_stock':
                            $q->orWhereHas('stockHolding', function($sq) {
                                $sq->where('QuantityOnHand', '>', 10);
                            });
                            break;
                        case 'low_stock':
                            $q->orWhereHas('stockHolding', function($sq) {
                                $sq->whereBetween('QuantityOnHand', [1, 10]);
                            });
                            break;
                        case 'backorder':
                            if (Features::backordersEnabled()) {
                                $q->orWhereHas('stockHolding', function($sq) {
                                    $sq->where('QuantityOnHand', '<=', 0);
                                });
                            }
                            break;
                    }
                }
            });
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('StockItemName', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('StockCode', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('MarketingComments', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Sorting
        switch ($request->get('sort', 'relevance')) {
            case 'price_low_high':
                $customer = auth()->user()?->customer;
                $priceLevel = $customer->price_level ?? 1;
                $priceField = 'SellingPrice' . ($priceLevel > 1 ? $priceLevel : '');
                $query->orderBy($priceField, 'asc');
                break;
            case 'price_high_low':
                $customer = auth()->user()?->customer;
                $priceLevel = $customer->price_level ?? 1;
                $priceField = 'SellingPrice' . ($priceLevel > 1 ? $priceLevel : '');
                $query->orderBy($priceField, 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('StockItemName', 'asc');
        }

        // Get categories for filter sidebar
        $categories = ProductCategory::withCount(['products' => function ($q) {
            $q->where('status', true);
        }])
            ->where('status', true)
            ->having('products_count', '>', 0)
            ->orderBy('StockGroupName', 'asc')
            ->get();

        $products = $query->paginate(Features::productsPerPage());

        $products->getCollection()->transform(function ($product) {
            $product->pricing = PricingHelper::getProductPricing($product);
            return $product;
        });


        return view('shop.products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->orWhere('id', $slug)
            ->with([ 'stockHolding', 'categories','referredProduct', 'referringProducts'])
            ->firstOrFail();

        // Add pricing information to the product
        $product->pricing = PricingHelper::getProductPricing($product);

        // Get pack size family if this product is part of one
        $packSizeFamily = collect();
        $selectedPackSize = $product;

        if ($product->refer_code || $product->referringProducts->count() > 0) {
            // Get the complete pack size family
            $packSizeFamily = $product->packSizeFamily()
                ->with(['stockHolding'])
                ->get()
                ->sortByDesc('Packsize');

            //dd($packSizeFamily);
            // Add pricing information to each pack size
            $packSizeFamily = $packSizeFamily->map(function ($packProduct) {
                $packProduct->pricing = PricingHelper::getProductPricing($packProduct);
                return $packProduct;
            });

            // Calculate unit prices for comparison
            $packSizeFamily = $packSizeFamily->map(function ($packProduct) use ($packSizeFamily) {
                if ($packProduct->pricing && $packProduct->pricing['show_prices'] && $packProduct->Packsize > 0) {
                    $packProduct->unit_price = $packProduct->pricing['price'] / $packProduct->Packsize;
                    $packProduct->savings_per_unit = null;

                    // Calculate savings compared to the smallest pack size
                    $baseUnit = $packSizeFamily->where('Packsize', 1)->first();
                    if ($baseUnit && $baseUnit->pricing && $baseUnit->pricing['show_prices']) {
                        $baseUnitPrice = $baseUnit->pricing['price'];
                        $thisUnitPrice = $packProduct->unit_price;
                        if ($thisUnitPrice < $baseUnitPrice) {
                            $packProduct->savings_per_unit = $baseUnitPrice - $thisUnitPrice;
                            $packProduct->savings_percentage = round((($baseUnitPrice - $thisUnitPrice) / $baseUnitPrice) * 100, 1);
                        }
                    }
                }
                return $packProduct;
            });
        }

        // Get related products (excluding pack size family members)
        $excludeIds = $packSizeFamily->pluck('id')->toArray();
        $excludeIds[] = $product->id;

        // Get related products
        $relatedProducts = Product::whereHas('categories', function($query) use ($product) {
            $query->whereIn('id', $product->categories->pluck('id'));
        })
            ->whereNotIn('id', $excludeIds)
            ->where('status', true)
            ->take(4)
            ->get();

        // Add pricing to related products
        $relatedProducts->transform(function ($relatedProduct) {
            $relatedProduct->pricing = PricingHelper::getProductPricing($relatedProduct);
            return $relatedProduct;
        });

        return view('shop.products.show', compact('product', 'relatedProducts', 'packSizeFamily', 'selectedPackSize'));
    }

    public function switchPack($productId)
    {
        $product = Product::findOrFail($productId);
        return redirect()->route('shop.products.show', $product->slug ?? $product->id);
    }

    public function switchPackSize($productId)
    {
        $product = Product::with(['stockHolding'])->findOrFail($productId);
        $product->pricing = PricingHelper::getProductPricing($product);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->StockItemName,
                'stock_code' => $product->StockCode,
                'pack_size' => $product->Packsize ?? 1,
                'stock_quantity' => $product->stockHolding?->QuantityOnHand ?? 0,
                'pricing' => $product->pricing,
                'weight' => $product->WeightPerUnit,
                'dimensions' => $product->dimensions,
                'url' => route('shop.products.show', $product->slug ?? $product->id)
            ]
        ]);
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
            $product->pricing = PricingHelper::getProductPricing($product);
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

    public function getRecentlyViewed(Request $request)
    {
        $productIds = $request->input('product_ids', []);

        if (empty($productIds)) {
            return response()->json(['products' => []]);
        }

        $products = Product::whereIn('id', $productIds)
            ->where('status', true)
            ->get()
            ->map(function ($product) {
                $pricing = PricingHelper::getProductPricing($product);

                $priceHtml = '';
                if ($pricing['show_prices']) {
                    $price = $pricing['price'];
                    $whole = floor($price);
                    $fraction = sprintf('%02d', ($price - $whole) * 100);
                    $priceHtml = sprintf(
                        '<div class="amazon-price mt-2"><span class="amazon-price-whole">%s%s</span><span class="amazon-price-fraction">%s</span></div>',
                        config('app.currency', 'R'),
                        number_format($whole, 0),
                        $fraction
                    );
                }
                return [
                    'id' => $product->id,
                    'name' => $product->StockItemName,
                    'url' => route('shop.products.show', $product->slug ?? $product->id),
                    'image' => $product->photo ? $product->photo->thumbnail : 'https://dummyimage.com/300x300/cccccc/000000.png&text=No+Image',
                    'price_html' => $priceHtml
                ];
            });

        return response()->json(['products' => $products]);
    }
}
