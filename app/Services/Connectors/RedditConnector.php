<?php

namespace App\Services\Connectors;

use App\Models\SocialConnection;

class RedditConnector extends AbstractOAuthConnector
{
    public function provider(): string
    {
        return 'reddit';
    }

    public function refreshToken(SocialConnection $connection): SocialConnection
    {
        throw new \RuntimeException('Reddit connector not yet implemented.');
    }

    public function getPosts(SocialConnection $connection, int $limit = 25): array
    {
        throw new \RuntimeException('Reddit connector not yet implemented.');
    }

    public function getPostStats(SocialConnection $connection, string $postId): array
    {
        throw new \RuntimeException('Reddit connector not yet implemented.');
    }

    public function setWebhook(SocialConnection $connection, string $callbackUrl): bool
    {
        throw new \RuntimeException('Reddit connector not yet implemented.');
    }

    public function removeWebhook(SocialConnection $connection): bool
    {
        throw new \RuntimeException('Reddit connector not yet implemented.');
    }
}
