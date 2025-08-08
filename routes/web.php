<?php

use Livewire\Livewire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\ChatController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\PushSubscriptionController;

Route::get('/', function () {
    if(Auth::check()){
        $user = \App\Models\User::role(['Onboarding'])
        ->where('id', Auth::id())
        ->first();
        if($user){
            return redirect()->route('onboarding');
        }else{
            return redirect()->route('home');
        }
    }else{
        return redirect()->route('home');
    }
});

Route::post('/payment/webhook', [MidtransController::class, 'midtransCallback'])->name('payment.webhook');

// Manual status check (untuk admin)
Route::get('/payment/{payment}/status', [MidtransController::class, 'checkStatus'])
    ->middleware('auth')
    ->name('payment.status');
    Route::get('/auth/callback', function () {
        $githubUser = Socialite::driver('github')->user();
    
        $user = User::updateOrCreate([
            'github_id' => $githubUser->id,
        ], [
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'github_token' => $githubUser->token,
            'github_refresh_token' => $githubUser->refreshToken,
        ]);
    
        Auth::login($user);
    
        return redirect('/dashboard');
    });
    Route::get('/messages/{thread_id}', [ChatController::class, 'show'])->name('messages.show');
    Route::post('/push-subscribe', [PushSubscriptionController::class, 'store'])->middleware('auth');
    Route::get('optimize', function() {
        Artisan::call('optimize:clear');
        return redirect()->route('app.home');
    });
    Route::get('migrate', function() {
        Artisan::call('migrate');
        return redirect()->route('app.home');
    });
    Route::get('seed', function() {
        Artisan::call('db:seed');
        return redirect()->route('app.home');
    });
    Route::get('storage-link', function() {
        Artisan::call('storage:link');
        return redirect()->route('app.home');
    });
    Route::get('maintenance', function() {
        Artisan::call('down');
    });
    Route::get('live', function() {
        Artisan::call('up');
    });
    Route::post('/midtrans/webhook', [App\Http\Controllers\Payment\MidtransController::class, 'handleWebhook']);
Route::group(['domain' => 'https://staging.adamasanya.com'], function () {
    // routes/web.php
});
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/update', $handle);
});
Livewire::setScriptRoute(function ($handle) {
    return Route::get('/adamasanya.js', $handle);
});
Route::controller(App\Http\Controllers\Auth\GoogleController::class)->group(function(){
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});