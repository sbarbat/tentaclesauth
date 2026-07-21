<?php

namespace App\Services\Connectors;

use App\Mcp\Tools\Facebook\GetFacebookPostStatsTool;
use App\Mcp\Tools\Facebook\GetFacebookPostsTool;
use App\Models\SocialConnection;
use Illuminate\Support\Facades\Http;

class FacebookConnector extends AbstractOAuthConnector
{
    public function provider(): string
    {
        return 'facebook';
    }

    public function mcpTools(): array
    {
        return [
            new GetFacebookPostsTool($this),
            new GetFacebookPostStatsTool($this),
        ];
    }

    public function refreshToken(SocialConnection $connection): SocialConnection
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

    public function getPosts(SocialConnection $connection, int $limit = 25): array
    {
        $response = Http::withToken($connection->access_token)
            ->get("https://graph.facebook.com/v19.0/{$connection->provider_account_id}/posts", [
                'limit' => $limit,
                'fields' => 'id,message,created_time,permalink_url',
            ])
            ->throw();

        return $response->json('data', []);
    }

    public function getPostStats(SocialConnection $connection, string $postId): array
    {
        $response = Http::withToken($connection->access_token)
            ->get("https://graph.facebook.com/v19.0/{$postId}", [
                'fields' => 'likes.summary(true),comments.summary(true),shares',
            ])
            ->throw();

        return $response->json() ?? [];
    }

    public function setWebhook(SocialConnection $connection, string $callbackUrl): bool
    {
        $response = Http::withToken($connection->access_token)
            ->post("https://graph.facebook.com/v19.0/{$connection->provider_account_id}/subscribed_apps", [
                'subscribed_fields' => 'feed',
            ]);

        if ($response->successful()) {
            $connection->forceFill([
                'webhook_id' => $connection->provider_account_id,
                'meta' => array_merge($connection->meta ?? [], ['webhook_callback_url' => $callbackUrl]),
            ])->save();

            return true;
        }

        return false;
    }

    public function removeWebhook(SocialConnection $connection): bool
    {
        $response = Http::withToken($connection->access_token)
            ->delete("https://graph.facebook.com/v19.0/{$connection->provider_account_id}/subscribed_apps");

        if ($response->successful()) {
            $connection->forceFill(['webhook_id' => null])->save();

            return true;
        }

        return false;
    }
}
