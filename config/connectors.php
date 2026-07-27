<?php

use App\Connectors\Facebook\FacebookConnector;
use App\Connectors\Instagram\InstagramConnector;
use App\Connectors\Reddit\RedditConnector;
use App\Connectors\Tiktok\TiktokConnector;
use App\Connectors\X\XConnector;

return [

    /*
    |--------------------------------------------------------------------------
    | Social Network Connectors
    |--------------------------------------------------------------------------
    |
    | Maps each supported social network to the connector class that
    | implements `App\Contracts\OAuthConnectorInterface` for it, along
    | with the underlying Socialite driver name. Some networks require
    | a community Socialite provider package to be installed before
    | their OAuth flow will work (see socialiteproviders.com).
    |
    */

    'providers' => [
        'facebook' => [
            'label' => 'Facebook',
            'driver' => 'facebook',
            'class' => FacebookConnector::class,
        ],

        'instagram' => [
            'label' => 'Instagram',
            'driver' => 'instagram',
            'class' => InstagramConnector::class,
        ],

        'tiktok' => [
            'label' => 'TikTok',
            'driver' => 'tiktok',
            'class' => TiktokConnector::class,
        ],

        'x' => [
            'label' => 'X',
            'driver' => 'twitter-oauth-2',
            'class' => XConnector::class,
        ],

        'reddit' => [
            'label' => 'Reddit',
            'driver' => 'reddit',
            'class' => RedditConnector::class,
        ],
    ],

];
