<?php

use Illuminate\Support\Facades\Route;
use Sobhansgh\MellatApi\Http\Controllers\MellatController;

Route::group(config('hiveweb-mellat-api.route'), function () {
    Route::post('/pay', [MellatController::class, 'pay']);
    Route::post('/verify', [MellatController::class, 'verify']);
    Route::match(['GET','POST'],'/callback', [MellatController::class, 'callback']);
});
