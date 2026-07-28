<?php

namespace App\Connectors\Xero;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;

class XeroConnector extends AbstractOAuthConnector
{
    protected array $scopes = ['email', 'openid', 'profile'];


    public static function provider(): string
    {
        return 'xero';
    }

    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        return $connection;
    }
}
