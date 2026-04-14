<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/products/{product}', [ShopController::class, 'show'])->name('shop.products.show');

Route::get('/shop/cart', [CartController::class, 'index'])->name('shop.cart');
Route::post('/shop/cart/add/{product}', [CartController::class, 'add'])->name('shop.cart.add');
Route::post('/shop/cart/update', [CartController::class, 'update'])->name('shop.cart.update');
Route::post('/shop/cart/remove/{product}', [CartController::class, 'remove'])->name('shop.cart.remove');
Route::post('/shop/cart/clear', [CartController::class, 'clear'])->name('shop.cart.clear');
Route::post('/shop/cart/checkout', [CartController::class, 'checkout'])->name('shop.cart.checkout');
