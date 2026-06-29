<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

// ===== Públicos: catálogo =====
Route::get('products', [CatalogController::class, 'products']);
Route::get('products/featured', [CatalogController::class, 'featuredProducts']);
Route::get('products/{slug}', [CatalogController::class, 'product']);
Route::get('categories', [CatalogController::class, 'categories']);
Route::get('brands', [CatalogController::class, 'brands']);

// ===== Webhooks =====
Route::post('webhooks/mercadopago', [\App\Http\Controllers\Api\WebhookController::class, 'mercadopago']);

// ===== Auth =====
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// ===== Protegidos (Storefront / API Key) =====
Route::middleware('store.api')->group(function () {
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/status/{code}', [OrderController::class, 'showPublic']);
    Route::get('payment-methods', [PaymentController::class, 'getPaymentMethods']);
    Route::post('create-preference', [PaymentController::class, 'createPreference']);
});

// ===== Protegidos (Sanctum) =====
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    //Route::post('orders', [OrderController::class, 'store']);
});