<?php
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::apiResource('products', ProductController::class);
});