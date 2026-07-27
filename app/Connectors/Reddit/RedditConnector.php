<?php

namespace App\Connectors\Reddit;

use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;

class RedditConnector extends AbstractOAuthConnector
{
    public static function provider(): string
    {
        return 'reddit';
    }

    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        throw new \RuntimeException('Reddit connector not yet implemented.');
    }
}
