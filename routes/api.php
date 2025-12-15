<?php

use App\Http\Controllers\StockItemHoldingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PackSizeController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1\Admin\Auth'], function() {
    Route::post('login', 'AuthController@login');
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    Route::post('stock-quantities/import', [StockItemHoldingsController::class, 'importFromApi']);
});


Route::group(['prefix' => 'v1', 'as' => 'api.admin.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    // Product Categories
    Route::post('product-categories/media', 'ProductCategoryApiController@storeMedia')->name('product-categories.storeMedia');
    Route::apiResource('product-categories', 'ProductCategoryApiController');

    // Product Tags
    Route::apiResource('product-tags', 'ProductTagApiController');

    // Products
    Route::post('products/media', 'ProductApiController@storeMedia')->name('products.storeMedia');

    // Products with pack size information
    Route::get('/products/{stockCode}/pack-sizes', [PackSizeController::class, 'getPackSizeVariations']);
    Route::get('/products/{stockCode}/pack-size-hierarchy', [PackSizeController::class, 'getPackSizeHierarchy']);

    // Stock operations
    Route::post('/stock/check-availability', [PackSizeController::class, 'checkStockAvailability']);
    Route::post('/stock/allocation', [PackSizeController::class, 'calculateStockAllocation']);
    Route::post('/stock/recommendations', [PackSizeController::class, 'getRecommendedPackSizes']);


    Route::apiResource('products', 'ProductApiController');

});


