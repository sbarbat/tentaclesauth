<?php

namespace App\Connectors\Github;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;

class GithubConnector extends AbstractOAuthConnector
{
    protected array $scopes = ['user', 'repo', 'project'];


    public static function provider(): string
    {
        return 'github';
    }

    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        return $connection;
    }
}
