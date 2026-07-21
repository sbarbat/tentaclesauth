<?php

namespace App\Services\Connectors;

use App\Contracts\OAuthConnectorInterface;
use App\Contracts\ProvidesMcpTools;
use InvalidArgumentException;
use Laravel\Mcp\Server\Tool;

class ConnectorManager
{
    /**
     * Resolve the connector implementation registered for the given
     * provider key (e.g. "facebook", "instagram").
     */
    public function driver(string $provider): OAuthConnectorInterface
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

    /**
     * Resolve every registered connector instance.
     *
     * @return array<int, OAuthConnectorInterface>
     */
    public function all(): array
    {
        return collect(config('connectors.providers'))
            ->map(fn (array $provider) => app($provider['class']))
            ->all();
    }

    /**
     * Collect all MCP tools exposed by connectors that implement
     * `ProvidesMcpTools`.
     *
     * @return array<int, Tool>
     */
    public function tools(): array
    {
        return collect($this->all())
            ->filter(fn (OAuthConnectorInterface $connector) => $connector instanceof ProvidesMcpTools)
            ->flatMap(fn (ProvidesMcpTools $connector) => $connector->mcpTools())
            ->all();
    }
}
