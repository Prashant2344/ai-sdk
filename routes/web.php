<?php

use Illuminate\Support\Facades\Route;
use App\Ai\Agents\SalesCoach;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function () {
    $response = (new SalesCoach)
    ->prompt('Analyze this sales transcript...');

    return (string) $response;
});