<?php

use App\Http\Controllers\StockItemHoldingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PackSizeController;
use App\Http\Controllers\Api\V1\Admin\Auth\AuthController;
use App\Http\Controllers\Api\V1\Admin\PermissionsApiController;
use App\Http\Controllers\Api\V1\Admin\RolesApiController;
use App\Http\Controllers\Api\V1\Admin\UsersApiController;
use App\Http\Controllers\Api\V1\Admin\ProductApiController;
use App\Http\Controllers\Api\V1\Admin\ProductCategoryApiController;



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

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], function () {
    Route::post('stock-quantities/import', [StockItemHoldingsController::class, 'importFromApi']);


});


/*Route::group(['prefix' => 'v1', 'as' => 'api.admin.', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', PermissionsApiController::class);

    // Roles
    Route::apiResource('roles', RolesApiController::class);

    // Users
    Route::apiResource('users', UsersApiController::class);

    // Product Categories
    Route::post('product-categories/media', ProductCategoryApiController::class, 'storeMedia')->name('product-categories.storeMedia');
    Route::apiResource('product-categories', ProductCategoryApiController::class);

    // Products
    Route::post('products/media', ProductApiController::class, 'storeMedia')->name('products.storeMedia');

    // Products with pack size information
    Route::get('/products/{stockCode}/pack-sizes', [PackSizeController::class, 'getPackSizeVariations']);
    Route::get('/products/{stockCode}/pack-size-hierarchy', [PackSizeController::class, 'getPackSizeHierarchy']);

    // Stock operations
    Route::post('/stock/check-availability', [PackSizeController::class, 'checkStockAvailability']);
    Route::post('/stock/allocation', [PackSizeController::class, 'calculateStockAllocation']);
    Route::post('/stock/recommendations', [PackSizeController::class, 'getRecommendedPackSizes']);


    Route::apiResource('products', ProductApiController::class);

});*/


