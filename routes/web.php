<?php
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UsersController;
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
Route::get('/catalog', 'HomeController@index')->name('catalog');
//Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth']], function () {
   Route::get('/dashboard', 'DashboardController@index')->name('home');
   Route::get('/sales', 'DashboardController@sales')->name('salesHome');

   // Customers
   Route::delete('customers/destroy', 'CustomersController@massDestroy')->name('customers.massDestroy');
   Route::resource('customers', 'CustomersController');
   Route::post('update-customer-status', 'CustomersController@updateCustomerStatus');
   Route::get('customer-lookup', 'CustomersController@lookup')->name('customers.lookup');
   Route::get('generateStoreEan', 'CustomersController@generateStoreEan')->name('customers.storeid');
   Route::post('importBalances', 'CustomerBalanceController@importExcel')->name('importBalances');

   // Product Categories
    Route::delete('product-categories/destroy', 'ProductCategoryController@massDestroy')->name('product-categories.massDestroy');
    Route::post('product-categories/media', 'ProductCategoryController@storeMedia')->name('product-categories.storeMedia');
    Route::post('product-categories/ckmedia', 'ProductCategoryController@storeCKEditorImages')->name('product-categories.storeCKEditorImages');
    Route::post('update-category-status', 'ProductCategoryController@updateCategoryStatus');
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
    Route::post('importStockmaster', 'ProductController@importExcel')->name('importStockmaster');
    //Route::match(['get', 'post'], 'products/maintain/{id?}', 'ProductController@maintain')->name('products.maintain');
    Route::get('products/product_search', 'ProductController@productSearch')->name('product.search');

    // Orders
    Route::get('orders/create', 'OrdersController@create')->name('orders.create');
    Route::post('orders/create', 'OrdersController@store')->name('orders.store');
    Route::delete('orders/destroy', 'OrdersController@massDestroy')->name('orders.massDestroy');
    //Route::resource('orders', 'OrdersController');
    Route::get('orders/download/{order}', 'OrdersController@downloadOrder')->name('orders.download');
    //Route::get('orders/create_step1', 'OrdersController@createStepOne')->name('orders.create.step.one');
    //Route::post('orders/create_step1', 'OrdersController@postCreateStepOne')->name('orders.create.step.one.post');
    //Route::get('orders/create_step2', 'OrdersController@createStepTwo')->name('orders.create.step.two');
    //Route::post('orders/create-step-two', 'OrdersController@postcreateStepTwo')->name('orders.create.step.two.post');
    Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');
    Route::delete('orders/{order}', 'OrdersController@destroy')->name('orders.destroy');
    Route::post('orders/getprice/{product_id}', 'OrdersController@getprice')->name('orders.getprice');
    Route::get('/orders/{tab?}', 'OrdersController@index')->name('orders.index');

    // Special Deals
    Route::delete('deals/destroy', 'SpecialDealsController@massDestroy')->name('deals.massDestroy');
    Route::resource('deals', 'SpecialDealsController');
    Route::get('exportExcel/{type}', 'SpecialDealsController@exportExcel')->name('exportSpecialDeals');
    Route::post('importExcel', 'SpecialDealsController@importExcel')->name('importSpecialDeals');

    // Ajax requests
    Route::get('/ajax/products', 'AjaxController@products')->name('ajax.products');
    Route::get('/ajax/customers', 'AjaxController@customers')->name('ajax.customers');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.',  'namespace' => 'Admin','middleware' => ['auth']], function () {

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

    // Customer Groups
    Route::delete('customer-category/destroy', 'CustomerCategoryController@massDestroy')->name('customer-category.massDestroy');
    Route::resource('customer-category', 'CustomerCategoryController');

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

    // Settings>Preferences
    Route::get('/preferences', 'PreferenceController@index')->name('preferences');
    Route::post('/preferences', 'PreferenceController@update')->name('preferences.update');

    // Settings>Product Settings
    Route::get('/product', 'ProductController@index')->name('product');
    Route::post('/product', 'ProductController@update')->name('product.update');

    // Settings>Tax Types
    Route::get('/tax-types', 'TaxTypeController@index')->name('tax_types');
    Route::get('/tax-types/create', 'TaxTypeController@create')->name('tax_types.create');
    Route::post('/tax-types/create', 'TaxTypeController@store')->name('tax_types.store');
    Route::get('/tax-types/{tax_type}/edit', 'TaxTypeController@edit')->name('tax_types.edit');
    Route::post('/tax-types/{tax_type}/edit', 'TaxTypeController@update')->name('tax_types.update');
    Route::get('/tax-types/{tax_type}/delete', 'TaxTypeController@delete')->name('tax_types.delete');
});
