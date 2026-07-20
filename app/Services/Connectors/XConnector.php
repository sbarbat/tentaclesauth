<?php

namespace App\Services\Connectors;

use App\Models\SocialConnection;

class XConnector extends AbstractSocialConnector
{
    protected string $driver = 'twitter';

    public function provider(): string
    {
        return 'x';
    }

    public function refreshToken(SocialConnection $connection): SocialConnection
    {
        throw new \RuntimeException('X connector not yet implemented.');
    }

    public function getPosts(SocialConnection $connection, int $limit = 25): array
    {
        throw new \RuntimeException('X connector not yet implemented.');
    }

    public function getPostStats(SocialConnection $connection, string $postId): array
    {
        throw new \RuntimeException('X connector not yet implemented.');
    }

    public function setWebhook(SocialConnection $connection, string $callbackUrl): bool
    {
        throw new \RuntimeException('X connector not yet implemented.');
    }

    public function removeWebhook(SocialConnection $connection): bool
    {
        throw new \RuntimeException('X connector not yet implemented.');
    }
}
