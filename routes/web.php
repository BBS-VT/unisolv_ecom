<?php

use App\Http\Controllers\CustomerBalanceController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockItemHoldingsController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
    //return view('welcome');
});

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
   Route::get('/dashboard', 'DashboardController@index')->name('home');
   Route::get('/dashboard/sales/chart-data', [DashboardController::class, 'getChartData'])
       ->name('dashboard.sales.chart-data');
   Route::get('/sales', 'DashboardController@sales')->name('sales.dashboard');



   Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');

   // Customers
   Route::delete('customers/destroy', 'CustomersController@massDestroy')->name('customers.massDestroy');

   Route::resource('customers', 'CustomersController');
   Route::post('update-customer-status', 'CustomersController@updateCustomerStatus');
   Route::get('customer-lookup', 'CustomersController@lookup')->name('customers.lookup');
   Route::get('generateStoreEan', 'CustomersController@generateStoreEan')->name('customers.storeid');
   Route::post('importBalances', 'CustomerBalanceController@importExcel')->name('importBalances');
    Route::post('importCustomermaster', 'CustomersController@importExcel')->name('importCustomermaster');
    Route::patch('customers/{customer}/pricing', 'CustomersController@updatePricing')->name('customers.update_pricing');

   // Product Categories
    Route::delete('product-categories/destroy', 'ProductCategoryController@massDestroy')->name('product-categories.massDestroy');
    Route::post('product-categories/media', 'ProductCategoryController@storeMedia')->name('product-categories.storeMedia');
    Route::post('product-categories/ckmedia', 'ProductCategoryController@storeCKEditorImages')->name('product-categories.storeCKEditorImages');
    Route::post('update-category-status', 'ProductCategoryController@updateCategoryStatus');
    Route::post('importCategories', 'ProductCategoryController@importExcel')->name('importProductCategories');
    Route::post('product-categories/update/{id}', 'ProductCategoryController@update')->name('productCategories.update');
    Route::post('product-categories/store', 'ProductCategoryController@store')->name('productCategories.store');
    Route::resource('product-categories', 'ProductCategoryController');

    // Product Tags
    Route::delete('product-tags/destroy','ProductTagController@massDestroy')->name('product-tags.massDestroy');
    Route::resource('product-tags', 'ProductTagController');

    // Products
    //Route::get('products', 'ProductController@index')->name('products.index');
    Route::delete('products/{product}', 'ProductController@destroy')->name('products.destroy');
    //Route::get('products/show', 'ProductController@show')->name('products.show');
    Route::delete('products/destroy', 'ProductController@massDestroy')->name('products.massDestroy');
    Route::post('products/media','ProductController@storeMedia')->name('products.storeMedia');
    Route::post('products/ckmedia', 'ProductController@storeCKEditorImages')->name('products.storeCKEditorImages');
    Route::post('update-product-status', 'ProductController@updateProductStatus');
    Route::resource('products', 'ProductController');
    Route::post('importQuantities', 'StockItemHoldingsController@importExcel')->name('importQuantities');
    //Route::post('importStockmaster', 'ProductController@importExcel')->name('importStockmaster');
    //Route::match(['get', 'post'], 'products/maintain/{id?}', 'ProductController@maintain')->name('products.maintain');
    Route::get('products/product_search', 'ProductController@productSearch')->name('product.search');

    // Orders
    Route::get('orders/create', 'OrdersController@create')->name('orders.create');
    Route::post('orders/create', 'OrdersController@store')->name('orders.store');
    Route::get('orders/download/{order}', 'OrdersController@downloadOrder')->name('orders.download');
    Route::get('/orders/{order}/details', 'OrdersController@show')->name('orders.show');
    Route::get('/orders/{order}/delete', 'OrdersController@delete')->name('orders.delete');
    Route::get('/orders/{tab?}', 'OrdersController@index')->name('orders.index');

    // Special Deals
    Route::delete('deals/destroy', 'SpecialDealsController@massDestroy')->name('deals.massDestroy');
    Route::resource('deals', 'SpecialDealsController');
    Route::get('exportExcel/{type}', 'SpecialDealsController@exportExcel')->name('exportSpecialDeals');
    Route::post('importExcel', 'SpecialDealsController@importExcel')->name('importSpecialDeals');

    // Ajax requests
    Route::get('/ajax/products', 'AjaxController@products')->name('ajax.products');
    Route::get('/ajax/customers', 'AjaxController@customers')->name('ajax.customers');
    Route::get('/ajax/maxdiscount', 'AjaxController@maxdiscount')->name('ajax.maxdiscount');

});

Route::group(['prefix' => 'admin', 'as' => 'admin.',  'namespace' => 'Admin','middleware' => ['auth']], function () {

    // landing page
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionController');

    // Roles
    Route::delete('roles/destroy', 'RoleController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RoleController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // BuyingGroup
    Route::delete('buying-group/destroy', 'BuyingGroupController@massDestroy')->name('buying-group.massDestroy');
    Route::resource('buying-group', 'BuyingGroupController');

    // Customer Group
    Route::resource('customer-category', 'CustomerCategoryController');

    // Import routes
    Route::post('imports/process', [ProductController::class, 'importExcel'])
        ->name('imports.process');
    Route::get('imports/status', [ProductController::class, 'showImportStatus'])
        ->name('imports.status');
    Route::get('imports/check-progress/{importJobId}', [ProductController::class, 'checkImportProgress'])
        ->name('imports.check-progress');

    Route::post('stock-holdings/import', [StockItemHoldingsController::class, 'importExcel'])
        ->name('stock-holdings.import');
    Route::post('stock-holdings/link-products', [StockItemHoldingsController::class, 'linkProductsToQuantities'])
        ->name('stock-holdings.link-products');
    Route::get('stock-holdings/import-status', [StockItemHoldingsController::class, 'showImportStatus'])
        ->name('stock-holdings.import-status');
    Route::post('customer-balances/import', [CustomerBalanceController::class, 'importExcel'])
        ->name('customer-balances.import');


    // Order Status
    Route::resource('orderstatus', 'OrderStatusController');

    // Package Types
    Route::resource('packagetype', 'PackageTypeController');

});

Route::group(['prefix' => 'settings', 'as' => 'settings.',  'namespace' => 'Admin\Settings','middleware' => ['auth']], function () {

    // Settings>Account Settings
    Route::get('/account', 'AccountController@index')->name('account');
    Route::post('/account', 'AccountController@update')->name('account.update');

    // Settings>Company Settings
    Route::get('/company', 'CompanyController@index')->name('company');
    Route::post('/company', 'CompanyController@update')->name('company.update');

    // Settings>Order Settings
    Route::get('/order', 'OrderController@index')->name('order');
    Route::post('/order', 'OrderController@update')->name('order.update');

    // Settings>Preferences
    Route::get('/preferences', 'PreferenceController@index')->name('preferences');
    Route::post('/preferences', 'PreferenceController@update')->name('preferences.update');

    // Settings>Product Settings
    Route::get('/product', 'ProductController@index')->name('product');
    Route::post('/product', 'ProductController@update')->name('product.update');

    // Settings>Customer Settings
    Route::get('/customer', 'CustomerController@index')->name('customer');
    Route::post('/customer', 'CustomerController@update')->name('customer.update');

    // Settings>Ecommerce Settings
    Route::get('/ecommerce', 'EcommerceSettingsController@index')->name('ecommerce');
    Route::post('/ecommerce', 'EcommerceSettingsController@update')->name('ecommerce.update');

    // Settings>Tax Types
    Route::get('/tax-types', 'TaxTypeController@index')->name('tax_types');
    Route::get('/tax-types/create', 'TaxTypeController@create')->name('tax_types.create');
    Route::post('/tax-types/create', 'TaxTypeController@store')->name('tax_types.store');
    Route::get('/tax-types/{tax_type}/edit', 'TaxTypeController@edit')->name('tax_types.edit');
    Route::post('/tax-types/{tax_type}/edit', 'TaxTypeController@update')->name('tax_types.update');
    Route::get('/tax-types/{tax_type}/delete', 'TaxTypeController@delete')->name('tax_types.delete');
});

Route::group(['prefix' => '/portal/{customer}', 'as' => 'customer_portal.', 'namespace' => 'CustomerPortal', 'middleware' => ['auth']], function () {
   // Dashboard
    Route::get('/', 'DashboardController@index');
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

});
