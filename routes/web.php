<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AddressController;


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

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    // Catálogo: ver para todos los del local, gestionar según permiso
    Route::middleware('permission:catalog.view')->group(function () {
        Route::get('brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('brands/{brand}', [BrandController::class, 'show'])->name('brands.show')->whereNumber('brand');

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show')->whereNumber('category');

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show')->whereNumber('product');
    });

    Route::middleware('permission:catalog.manage')->group(function () {
        Route::resource('brands', BrandController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        //Route::resource('variants', ProductVariantController::class)->only(['store', 'update', 'destroy']);
        Route::get('products/{product}/variants/create', [ProductVariantController::class, 'create'])->name('products.variants.create');
        Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])->name('products.variants.store');
        Route::get('variants/{variant}/edit', [ProductVariantController::class, 'edit'])->name('variants.edit');
        Route::put('variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
    });

    // Pedidos
    Route::middleware('permission:orders.view')->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show')->whereNumber('order');
    });
    
    Route::middleware('permission:orders.manage')->group(function () {
        Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel')->whereNumber('order');
    });

    // Pagos
    Route::middleware('permission:payments.manage')->group(function () {
        Route::resource('payments', PaymentController::class)->only(['store']);
    });

    // Envíos
    Route::middleware('permission:shipments.view')->group(function () {
        Route::get('shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    });
    Route::middleware('permission:shipments.manage')->group(function () {
        Route::resource('shipments', ShipmentController::class)->only(['store', 'update', 'destroy']);
    });

    // Clientes
    Route::middleware('permission:customers.view')->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show')->whereNumber('customer');
    });
    Route::middleware('permission:customers.manage')->group(function () {
        Route::resource('customers', CustomerController::class)->except(['index', 'show']);
    
        Route::get('customers/{customer}/addresses/create', [AddressController::class, 'create'])->name('customers.addresses.create');
        Route::post('customers/{customer}/addresses', [AddressController::class, 'store'])->name('customers.addresses.store');
        Route::get('addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    });

    // Usuarios del local (solo admin)
    Route::middleware('permission:users.manage')->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });
});
