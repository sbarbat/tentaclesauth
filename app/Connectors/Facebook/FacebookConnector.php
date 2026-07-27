<?php

namespace App\Connectors\Facebook;

use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;
use Illuminate\Support\Facades\Http;

class FacebookConnector extends AbstractOAuthConnector
{
    public static function provider(): string
    {
        return 'facebook';
    }

    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        $response = Http::get('https://graph.facebook.com/v19.0/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'fb_exchange_token' => $connection->access_token,
        ])->throw();

        $connection->forceFill([
            'access_token' => $response->json('access_token'),
            'token_expires_at' => now()->addSeconds($response->json('expires_in', 0)),
        ])->save();

        return $connection;
    }
}
