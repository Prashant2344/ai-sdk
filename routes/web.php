<?php

use App\Http\Controllers\ParseCvPageController;
use Illuminate\Support\Facades\Route;
use App\Ai\Agents\SalesCoach;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cv', [ParseCvPageController::class, 'index'])->name('cv.index');
Route::post('/cv', [ParseCvPageController::class, 'store'])->name('cv.parse');


Route::get('/test', function () {
    $response = (new SalesCoach)
    ->prompt('Analyze this sales transcript...');

    return (string) $response;
});