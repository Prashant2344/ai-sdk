<?php

use App\Ai\Agents\SalesCoach;
use App\Support\AiUsageReporter;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParseCvPageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/cv', [ParseCvPageController::class, 'index'])->name('cv.index');
Route::post('/cv', [ParseCvPageController::class, 'store'])->name('cv.parse');


Route::get('/test', function (AiUsageReporter $usageReporter) {
    $response = (new SalesCoach)->prompt('Analyze this sales transcript...');

    return response()->json($usageReporter->wrap(
        ['text' => (string) $response],
        $usageReporter->track($response),
    ));
});