<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('home');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'authenticate']);

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::resource('variants', \App\Http\Controllers\Admin\ProductVariantController::class)->only(['store', 'update', 'destroy']);
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
        Route::resource('addresses', \App\Http\Controllers\Admin\AddressController::class)->only(['store', 'update', 'destroy']);
    });
