<?php
use App\Helpers\Features;
use App\Http\Controllers\Shop\DraftOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController as ShopHomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\PageController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\AccountController;

if (Features::ecommerceEnabled()) {

    Route::get('/home', [ShopHomeController::class, 'index'])->name('home');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::get('/terms', [PageController::class, 'terms'])->name('terms');

    // Customer login

    // Product browsing
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/categories/{category:slug}', [ProductController::class, 'category'])->name('categories.show');
    Route::get('/products/recently-viewed', [ProductController::class, 'getRecentlyViewed'])->name('products.recently-viewed');
    Route::get('/products/switch-pack/{productId}', [ProductController::class, 'switchPack'])->name('products.switch-pack');
    Route::post('/products/pack-size/{productId}', [ProductController::class, 'switchPackSize'])
        ->name('products.switch-pack-size');


    // Cart functionality
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('/add', [CartController::class, 'addToCart'])->name('add');
        Route::get('/', [CartController::class, 'showCart'])->name('show');
        Route::post('/update', [CartController::class, 'updateCart'])->name('update');
        Route::post('/remove', [CartController::class, 'removeFromCart'])->name('remove');
        Route::post('/clear', [CartController::class, 'clearCart'])->name('clear');
        Route::get('/mini', [CartController::class, 'getMiniCart'])->name('mini');
    });

    // Protected routes
    Route::middleware(['auth', 'verified'])->group(function () {
        // checkout
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [CheckoutController::class, 'index'])->name('index');
            Route::post('/process', [CheckoutController::class, 'process'])->name('process');
            Route::get('/success/{orderId}', [CheckoutController::class, 'success'])->name('success');

        });

        Route::get('/orders/{order}/print', [OrderController::class, 'print'] )->name('orders.print');

        // Customer account/profile
        Route::prefix('account')->name('account.')->group(function () {
            Route::get('/', [AccountController::class, 'index'])->name('index');
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{orderId}', [OrderController::class, 'show'])->name('orders.show');
            Route::get('/orders/{orderId}/reorder', [OrderController::class, 'reorder'])->name('orders.reorder');
            Route::post('/orders/{orderId}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
            Route::get('/orders/draft', [OrderController::class, 'draft'])->name('orders.draft');
            Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
            Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        });

        //Route::prefix('drafts')->name('drafts.')->group(function () {
        //    Route::get('/', [DraftOrderController::class, 'index'])->name('index');
        //});
    });
}
