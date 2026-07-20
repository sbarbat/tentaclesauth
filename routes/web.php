<?php

use App\Http\Controllers\SocialConnectionController;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Socialite;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp', \App\Mcp\Servers\MCPServer::class)
    ->middleware('auth:sanctum');

Route::view('/', 'welcome')->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/connectors/{provider}/redirect', [SocialConnectionController::class, 'redirect'])->name('connectors.redirect');
    Route::get('/connectors/{provider}/callback', [SocialConnectionController::class, 'callback'])->name('connectors.callback');
    Route::delete('/connectors/{provider}', [SocialConnectionController::class, 'destroy'])->name('connectors.destroy');
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
    Auth::guard('web')->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

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
            'profile_photo_path' => $providerUser->getAvatar(),
            'provider' => $provider,
            'provider_id' => $providerUser->getId(),
        ]
    );

    if (! $user->ownedTeams()->exists()) {
        $teamName = 'Your Team';
        if ($user->firstname) {
            $teamName = explode(' ', $user->firstname, 2)[0]."'s Team";
        }
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => $teamName,
            'personal_team' => true,
        ]));
        if (is_null($user->currentTeam)) {
            $user->switchTeam($user->personalTeam());
        }
    }

    auth()->guard('web')->login($user);

    return redirect()->route('dashboard');
})->middleware('guest')->name('auth.callback');
