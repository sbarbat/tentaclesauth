<?php

namespace App\Connectors\Monzo;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;

class MonzoConnector extends AbstractOAuthConnector
{
    public static function provider(): string
    {
        return 'monzo';
    }

    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        return $connection;
    }
}
