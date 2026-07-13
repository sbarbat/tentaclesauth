<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;
use App\Models\User;

Route::view('/', 'welcome')->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/login', function () {
    return view('login');
})->middleware('guest')->name('login');

Route::get('/logout', function () {
    auth()->logout();
    return redirect()->route('home');
})->middleware('auth:sanctum')->name('logout');

Route::get('/auth/{provider}', function ($provider) {
    return Socialite::driver($provider)->redirect();
})->middleware('guest')->name('auth.redirect');

Route::get('/auth/{provider}/callback', function ($provider) {
    $providerUser = Socialite::driver($provider)->stateless()->user();
    $user = User::updateOrCreate(
        ['email' => $providerUser->getEmail()],
        [
            'firstname' => $providerUser->user['given_name'],
            'lastname' => $providerUser->user['family_name'],
            'avatar' => $providerUser->getAvatar(),
            'provider' => $provider,
            'provider_id' => $providerUser->getId(),
        ]
    );

    // if ($user->wasRecentlyCreated) {
    //     UserCreated::dispatch($user);
    // }

    auth()->login($user);
    return redirect()->route('telegram.connect');
})->middleware('guest')->name('auth.callback');
