<?php

use App\Http\Controllers\Api\ParseCvController;
use App\Http\Controllers\Api\SearchProductsController;
use Illuminate\Support\Facades\Route;

Route::post('/cv/parse', ParseCvController::class);
Route::get('/products/search', SearchProductsController::class);
