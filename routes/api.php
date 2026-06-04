<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\LogApiRequests;

Route::middleware([LogApiRequests::class])->group(function () {
    Route::get('/weather', [ApiController::class, 'getWeather']);
});
