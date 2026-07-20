<?php

namespace App\Services\Connectors;

use App\Contracts\SocialConnectorInterface;
use InvalidArgumentException;

class SocialConnectorManager
{
    /**
     * Resolve the connector implementation registered for the given
     * provider key (e.g. "facebook", "instagram").
     */
    public function driver(string $provider): SocialConnectorInterface
    {
        $config = config("connectors.providers.{$provider}");

        if (! $config) {
            throw new InvalidArgumentException("Unknown social connector provider [{$provider}].");
        }

        return app($config['class']);
    }

    /**
     * Get the list of provider keys and labels available for connection.
     *
     * @return array<string, string>
     */
    public function available(): array
    {
        return collect(config('connectors.providers'))
            ->map(fn (array $provider) => $provider['label'])
            ->all();
    }
}
