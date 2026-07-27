<?php

namespace App\Connectors\Instagram;

use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramConnector extends AbstractOAuthConnector
{
    public static function provider(): string
    {
        return 'instagram';
    }

    public function connect(Team $team, Request $request): OAuthConnection
    {
        $connection = parent::connect($team, $request);

        $response = Http::get('https://graph.instagram.com/access_token', [
            'grant_type' => 'ig_exchange_token',
            'client_secret' => config('services.instagram.client_secret'),
            'access_token' => $connection->access_token,
        ])->throw();

        $connection->forceFill([
            'access_token' => $response->json('access_token'),
            'token_expires_at' => now()->addSeconds($response->json('expires_in', 0)),
        ])->save();

        return $connection;
    }

    // Should refresh every 60 days
    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        $response = Http::get('https://graph.instagram.com/refresh_access_token', [
            'grant_type' => 'ig_refresh_token',
            'access_token' => $connection->access_token,
        ])->throw();

        $connection->forceFill([
            'access_token' => $response->json('access_token'),
            'token_expires_at' => now()->addSeconds($response->json('expires_in', 0)),
        ])->save();

        return $connection;
    }
}
