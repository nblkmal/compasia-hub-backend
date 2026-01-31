<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'I\'m alive',
    ]);
});
Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
Route::post('/products/upload-file', [App\Http\Controllers\Api\ProductController::class, 'upload']);
Route::get('/products/logs', [App\Http\Controllers\Api\ProductController::class, 'logs']);