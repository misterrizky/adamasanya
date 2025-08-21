<?php

use App\Models\User;
use Livewire\Livewire;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\ChatController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MidtransController;
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
Route::group(['domain' => 'https://staging.adamasanya.com'], function () {
    Route::post('/payment/webhook', [MidtransController::class, 'handleWebhook'])
        ->name('payment.webhook')
        ->withoutMiddleware(['csrf']);
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
        return redirect()->route('home');
    });
    Route::get('migrate', function() {
        Artisan::call('migrate');
        return redirect()->route('home');
    });
    Route::get('seed', function() {
        Artisan::call('db:seed');
        return redirect()->route('home');
    });
    Route::get('storage-link', function() {
        Artisan::call('storage:link');
        return redirect()->route('home');
    });
    Route::get('maintenance', function() {
        Artisan::call('down');
    });
    Route::get('live', function() {
        Artisan::call('up');
    });
});
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/update', $handle);
});
Livewire::setScriptRoute(function ($handle) {
    return Route::get('/adamasanya.js', $handle);
});