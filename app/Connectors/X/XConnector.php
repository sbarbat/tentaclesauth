<?php

namespace App\Connectors\X;

use App\Connectors\AbstractOAuthConnector;
use App\Models\OAuthConnection;

class XConnector extends AbstractOAuthConnector
{
    protected static string $driver = 'twitter';

    public static function provider(): string
    {
        return 'x';
    }

    public function refreshToken(OAuthConnection $connection): OAuthConnection
    {
        throw new \RuntimeException('X connector not yet implemented.');
    }

}
