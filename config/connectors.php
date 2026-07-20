<?php

use App\Services\Connectors\FacebookConnector;
use App\Services\Connectors\InstagramConnector;
use App\Services\Connectors\RedditConnector;
use App\Services\Connectors\TiktokConnector;
use App\Services\Connectors\XConnector;

return [

    /*
    |--------------------------------------------------------------------------
    | Social Network Connectors
    |--------------------------------------------------------------------------
    |
    | Maps each supported social network to the connector class that
    | implements `App\Contracts\SocialConnectorInterface` for it, along
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
