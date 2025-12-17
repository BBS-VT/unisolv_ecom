<?php

use App\Http\Controllers\Admin\BuyingGroupController;
use App\Http\Controllers\Admin\CustomerCategoryController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\PackageTypeController;
use App\Http\Controllers\Admin\Settings\TaxTypeController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\SpecialDealsController;
use Illuminate\Support\Facades\Route;
use App\Livewire\SalesOrder\OrderForm;
use App\Helpers\Features;
use App\Http\Controllers\Admin\StockTransactionController;
use App\Http\Controllers\CustomerBalanceController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UsersController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\Shop\HomeController as ShopHomeController;
use App\Http\Controllers\StockItemHoldingsController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\Settings\AccountController as AccountSettingController;
use App\Http\Controllers\Admin\Settings\CompanyController as CompanySettingController;
use App\Http\Controllers\Admin\Settings\CustomerController as CustomerSettingController;
use App\Http\Controllers\Admin\Settings\EcommerceSettingsController;
use App\Http\Controllers\Admin\Settings\OrderController as OrderSettingController;
use App\Http\Controllers\Admin\Settings\PreferenceController as PreferenceSettingController;
use App\Http\Controllers\Admin\Settings\ProductController as ProductSettingController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (Features::ecommerceEnabled()) {
    Route::get('/', [ShopHomeController::class, 'index']);
} else
{
    Route::get('/', function () {
        return view('auth.login');

    });
}

//Route::get('/catalog', 'HomeController@index')->name('catalog');
//Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.detail');
//Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth','customer']], function () {
    Route::get('/customer/dashboard', [CustomerDashboardController::class, 'index'])->name('customers.dashboard');
    Route::get('/profile', [CustomerProfileController::class, 'show'])->name('customer.profile');
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::put('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('customer.profile.password');
});

Route::group(['middleware' => ['auth']], function () {
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('home');
   Route::get('/dashboard/sales/chart-data', [DashboardController::class, 'getChartData'])
       ->name('dashboard.sales.chart-data');
   Route::get('/sales', [DashboardController::class, 'sales'])->name('sales.dashboard');


   Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');

   // Customers
   Route::delete('customers/destroy', [CustomersController::class, 'massDestroy'])->name('customers.massDestroy');

   Route::resource('customers', CustomersController::class);
   Route::post('update-customer-status', [CustomersController::class, 'updateCustomerStatus']);
   Route::get('customer-lookup', [CustomersController::class, 'lookup'])->name('customers.lookup');
   Route::get('generateStoreEan', [CustomersController::class, 'generateStoreEan'])->name('customers.storeid');
   Route::post('importBalances', [CustomerBalanceController::class, 'importExcel'])->name('importBalances');
    Route::post('importCustomermaster', [CustomersController::class, 'importExcel'])->name('importCustomermaster');
    Route::patch('customers/{customer}/pricing', [CustomersController::class, 'updatePricing'])->name('customers.update_pricing');

   // Product Categories
    Route::delete('product-categories/destroy', [ProductCategoryController::class, 'massDestroy'])->name('product-categories.massDestroy');
    Route::post('product-categories/media', [ProductCategoryController::class, 'storeMedia'])->name('product-categories.storeMedia');
    Route::post('product-categories/ckmedia', [ProductCategoryController::class, 'storeCKEditorImages'])->name('product-categories.storeCKEditorImages');
    Route::post('update-category-status', [ProductCategoryController::class, 'updateCategoryStatus']);
    Route::post('importCategories', [ProductCategoryController::class, 'importExcel'])->name('importProductCategories');
    Route::post('product-categories/update/{id}', [ProductCategoryController::class, 'update'])->name('productCategories.update');
    Route::post('product-categories/store', [ProductCategoryController::class, 'store'])->name('productCategories.store');
    Route::resource('product-categories', ProductCategoryController::class);

    // Product Tags
    //Route::delete('product-tags/destroy','ProductTagController@massDestroy')->name('product-tags.massDestroy');
    //Route::resource('product-tags', 'ProductTagController');

    // Products
    Route::get('products/search', [AjaxController::class, 'search'])->name('products.search');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('products/destroy', [ProductController::class, 'massDestroy'])->name('products.massDestroy');
    Route::post('products/media',[ProductController::class, 'storeMedia'])->name('products.storeMedia');
    Route::post('products/ckmedia', [ProductController::class, 'storeCKEditorImages'])->name('products.storeCKEditorImages');
    Route::post('update-product-status', [ProductController::class, 'updateProductStatus']);

    Route::resource('products', ProductController::class);
    Route::post('importQuantities', [StockItemHoldingsController::class, 'importExcel'])->name('importQuantities');
    //Route::get('products/product_search', [ProductController::class, 'productSearch'])->name('product.search');
    Route::delete('products/media/{media}', [ProductController::class, 'destroyMedia'])
        ->name('products.destroyMedia');
    Route::post('products/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('products.adjust-stock');


    // Promotionss
    Route::prefix('promotions')->name('promotions.')->group(function () {
        // Imports
        Route::get('import', [PromotionController::class, 'showImport'])->name('import');
        Route::post('import', [PromotionController::class, 'import'])->name('import.process');
        Route::post('import/preview', [PromotionController::class, 'previewImport'])->name('import.preview');
        Route::get('download-template', [PromotionController::class, 'downloadTemplate'])
            ->name('download-template');

        // Bulk operations
        Route::post('bulk-status', [PromotionController::class, 'bulkUpdateStatus'])->name('bulk-status');

        Route::post('test-calculation', [PromotionController::class, 'testCalculation'])
            ->name('test-calculation');
        Route::get('statistics', [PromotionController::class, 'statistics'])
            ->name('statistics');
        Route::get('{promotion}/analytics', [PromotionController::class, 'analytics'])
            ->name('analytics');
    });
    Route::resource('promotions', PromotionController::class);
    Route::get('/promotions/product/{stockCode}', [PromotionController::class, 'getProductInfo'])
        ->name('promotions.product-info');

    // Orders
    Route::get('orders/create', OrderForm::class)->name('orders.create');
    Route::get('orders/{orderId}/edit', OrderForm::class)->name('orders.edit');
    //Route::get('orders/create', 'OrdersController@create')->name('orders.create');
    //Route::post('orders/create', 'OrdersController@store')->name('orders.store');
    Route::get('orders/download/{order}', [OrdersController::class, 'downloadOrder'])->name('orders.download');
    Route::get('/orders/{order}/details', [OrdersController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/delete', [OrdersController::class, 'delete'])->name('orders.delete');
    Route::get('/orders/{order}/print/order', [OrdersController::class, 'printInvoice'])
        ->name('print.order');
    Route::get('/orders/{order}/print/picklist', [OrdersController::class, 'printPickList'])
        ->name('print.picklist');
    Route::get('/orders/{order}/print/packing-slip', [OrdersController::class, 'printPackingSlip'])
        ->name('print.packing-slip');
    Route::get('/orders/{tab?}', [OrdersController::class, 'index'])->name('orders.index');

    // Special Deals
    Route::delete('deals/destroy', [SpecialDealsController::class, 'massDestroy'])->name('deals.massDestroy');
    Route::resource('deals', SpecialDealsController::class);
    Route::get('exportExcel/{type}', [SpecialDealsController::class, 'exportExcel'])->name('exportSpecialDeals');
    Route::post('importExcel', [SpecialDealsController::class, 'importExcel'])->name('importSpecialDeals');

    // Ajax requests
    Route::get('/ajax/products', [AjaxController::class, 'products'])->name('ajax.products');
    Route::get('/ajax/customers', [AjaxController::class, 'customers'])->name('ajax.customers');
    //Route::get('/ajax/maxdiscount', 'AjaxController@maxdiscount')->name('ajax.maxdiscount');


});

Route::group(['prefix' => 'admin', 'as' => 'admin.',  'middleware' => ['auth']], function () {

    // landing page
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Permissions
    Route::delete('permissions/destroy', [PermissionController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', PermissionController::class);

    // locations
    Route::group(['prefix' => 'locations', 'as' => 'locations.'], function () {
        Route::get('/', [LocationController::class, 'index'])->name('index');
        Route::get('/generate-code', [LocationController::class, 'generateCode'])->name('generate-code');
        Route::get('/create', [LocationController::class, 'create'])->name('create');
        Route::post('/', [LocationController::class, 'store'])->name('store');
        Route::get('/{location}', [LocationController::class, 'show'])->name('show');
        Route::get('/{location}/edit', [LocationController::class, 'edit'])->name('edit');
        Route::put('/{location}', [LocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
        Route::post('/{location}/toggle-status', [LocationController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{location}/set-default', [LocationController::class, 'setDefault'])->name('set-default');
    });

    // Roles
    Route::delete('roles/destroy', [RoleController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RoleController::class);

    // Users
    Route::delete('users/destroy', [AdminUserController::class, 'massDestroy'])->name('users.massDestroy');
    Route::resource('users', AdminUserController::class);

    // BuyingGroup
    Route::delete('buying-group/destroy', [BuyingGroupController::class, 'massDestroy'])->name('buying-group.massDestroy');
    Route::resource('buying-group', BuyingGroupController::class);

    // Customer Group
    Route::resource('customer-category', CustomerCategoryController::class);

    // Stock Transactions
    Route::get('/stock-transactions', [StockTransactionController::class, 'index'])
        ->name('stock-transactions.index');
    Route::get('/stock-transactions/product/{stockCode}', [StockTransactionController::class, 'productHistory'])
        ->name('stock-transactions.product');

    // Import routes
    Route::post('imports/process', [ProductController::class, 'importExcel'])
        ->name('imports.process');
    Route::get('imports/status', [StockTransactionController::class, 'importStatus'])
        ->name('imports.status');
    Route::get('/imports/{importJob}/details', [StockTransactionController::class, 'importDetails'])
        ->name('imports.details');
    Route::get('/imports/{importJob}/progress', [StockTransactionController::class, 'importProgress'])
        ->name('imports.progress');
    Route::get('imports/check-progress/{importJobId}', [ProductController::class, 'checkImportProgress'])
        ->name('imports.check-progress');
    Route::get('imports/product_template', [ProductController::class, 'importTemplate'])->name('imports.download-template');

    Route::post('stock-holdings/import', [StockItemHoldingsController::class, 'importExcel'])
        ->name('stock-holdings.import');
    Route::post('stock-holdings/link-products', [StockItemHoldingsController::class, 'linkProductsToQuantities'])
        ->name('stock-holdings.link-products');
    Route::get('stock-holdings/import-status', [StockItemHoldingsController::class, 'showImportStatus'])
        ->name('stock-holdings.import-status');
    Route::post('customer-balances/import', [CustomerBalanceController::class, 'importExcel'])
        ->name('customer-balances.import');
    Route::get('stock-holdings/download-template', [StockItemHoldingsController::class, 'downloadTemplate'])->name('stock-holdings.download-template');

    // Order Status
    Route::resource('orderstatus', OrderStatusController::class);

    // Package Types
    Route::resource('packagetype', PackageTypeController::class);

});

Route::group(['prefix' => 'settings', 'as' => 'settings.',  'namespace' => 'Admin\Settings','middleware' => ['auth']], function () {

    // Settings>Account Settings
    Route::get('/account', [AccountSettingController::class, 'index'])->name('account');
    Route::post('/account', [AccountSettingController::class, 'update'])->name('account.update');

    // Settings>Company Settings
    Route::get('/company', [CompanySettingController::class, 'index'])->name('company');
    Route::post('/company', [CompanySettingController::class, 'update'])->name('company.update');
    Route::post('/company/collection-address', [CompanySettingController::class, 'updateCollectionAddress'])->name('company.ecommerce.collection-address');

    // Settings>Order Settings
    Route::get('/order', [OrderSettingController::class, 'index'])->name('order');
    Route::post('/order', [OrderSettingController::class, 'update'])->name('order.update');

    // Settings>Preferences
    Route::get('/preferences', [PreferenceSettingController::class, 'index'])->name('preferences');
    Route::post('/preferences', [PreferenceSettingController::class, 'update'])->name('preferences.update');

    // Settings>Product Settings
    Route::get('/product', [ProductSettingController::class, 'index'])->name('product');
    Route::post('/product', [ProductSettingController::class, 'update'])->name('product.update');

    // Settings>Customer Settings
    Route::get('/customer', [CustomerSettingController::class, 'index'])->name('customer');
    Route::post('/customer', [CustomerSettingController::class, 'update'])->name('customer.update');

    // Settings>Ecommerce Settings
    Route::get('/ecommerce', [EcommerceSettingsController::class, 'index'])->name('ecommerce');
    Route::post('/ecommerce', [EcommerceSettingsController::class, 'update'])->name('ecommerce.update');

    // Settings>Tax Types
    Route::get('/tax-types', [TaxTypeController::class, 'index'])->name('tax_types');
    Route::get('/tax-types/create', [TaxTypeController::class, 'create'])->name('tax_types.create');
    Route::post('/tax-types/create', [TaxTypeController::class, 'store'])->name('tax_types.store');
    Route::get('/tax-types/{tax_type}/edit', [TaxTypeController::class, 'edit'])->name('tax_types.edit');
    Route::post('/tax-types/{tax_type}/edit', [TaxTypeController::class, 'update'])->name('tax_types.update');
    Route::delete('/tax-types/{tax_type}/delete', [TaxTypeController::class, 'destroy'])->name('tax_types.destroy');
});

Route::group(['prefix' => '/portal/{customer}', 'as' => 'customer_portal.', 'namespace' => 'CustomerPortal', 'middleware' => ['auth']], function () {
   // Dashboard
    Route::get('/', [CustomerDashboardController::class, 'index']);
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

});
