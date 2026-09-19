<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1');

Route::post('refresh-token', [AuthController::class, 'refreshToken'])
    ->middleware('throttle:10,1');

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me'])
        ->middleware('scopes:profile:read');
    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware('scopes:products:read')->group(function () {
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);
    });

    Route::post('products', [ProductController::class, 'store'])
        ->middleware('scopes:products:create');

    Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])
        ->middleware('scopes:products:update');

    Route::delete('products/{product}', [ProductController::class, 'destroy'])
        ->middleware('scopes:products:delete');
});
