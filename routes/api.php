<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
Route::post('/products/upload-file', [App\Http\Controllers\Api\ProductController::class, 'upload']);