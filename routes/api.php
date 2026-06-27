<?php

use App\Http\Controllers\Api\ParseCvController;
use Illuminate\Support\Facades\Route;

Route::post('/cv/parse', ParseCvController::class);
