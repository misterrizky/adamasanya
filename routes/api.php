<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PushSubscriptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/check-auth', function () {
    return response()->json(['authenticated' => auth()->check()]);
})->middleware('auth');
Route::post('/api/push-subscribe', [PushSubscriptionController::class, 'store'])->middleware('auth:api');