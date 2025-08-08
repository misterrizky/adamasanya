<?php

use App\Models\User;
use App\Models\UserVerify;
use Illuminate\Support\Str;
use App\Models\Verify;
use App\Notifications\WelcomeNotification;
use function Livewire\Volt\{rules, state};
use Illuminate\Support\Facades\Notification;
use function Laravel\Folio\{middleware, name};

name('verification.verify');
middleware('guest');
// return redirect()->route('verification.error');
?>
@php
if (request('token') != null && request('token') != 'verify') {
    $userVerify = Verify::where('token', request('token'))->first();
    if ($userVerify) {
        $user = User::where('id', $userVerify->user_id)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
        }
    }
    Verify::where('token', request('token'))->delete();
    // return redirect()->route('login');
}
@endphp
<x-layouts.app>
    @volt('verification.verify')
    <div>
        <script>window.location = "/auth/sign-in";</script>
    </div>
    @endvolt
</x-layouts.app>