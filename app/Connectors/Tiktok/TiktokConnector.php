<?php

namespace App\Connectors\Tiktok;

use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;
use Illuminate\Support\Facades\Http;

class TiktokConnector extends AbstractOAuthConnector
{
    protected array $scopes = [
        'user.info.basic',
        'user.info.stats',
        'video.list',
        'video.publish',
        'video.data',
    ];

    public static function provider(): string
    {
        return 'tiktok';
    }

    // Should refresh every 365 days
    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        $response = Http::get('https://open.tiktokapis.com/v2/oauth/token/', [
            'client_key' => config('services.tiktok.client_id'),
            'client_secret' => config('services.tiktok.client_secret'),
            'grant_type' => 'refresh_token',
            'refresh_token' => $connection->refresh_token,
        ])->throw();

        $connection->forceFill([
            'access_token' => $response->json('access_token'),
            'token_expires_at' => now()->addSeconds($response->json('expires_in', 0)),
            'refresh_token' => $response->json('refresh_token'),
            'refresh_token_expires_at' => now()->addSeconds($response->json('refresh_expires_in', 0)),
        ])->save();

        return $connection;
    }
}
