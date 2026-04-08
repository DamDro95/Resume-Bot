<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::dashboard' );

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('auth.redirect')
    ->where('provider', 'google|linkedin-openid');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('auth.callback')
    ->where('provider', 'google|linkedin-openid');

Route::post('/auth/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->back();
})->name('auth.logout')->middleware('auth');
