<?php

namespace App\Connectors;

use App\Connectors\ConnectorToolInterface;
use App\Connectors\OAuthConnectorInterface;
use App\Models\OAuthConnection;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

/**
 * Base class for MCP tools backed by a social connector. Resolves the
 * authenticated team's `OAuthConnection` for the tool's provider and
 * only registers the tool when such a connection exists.
 */
abstract class ConnectorTool extends Tool implements ConnectorToolInterface
{
    /**
     * Cached connector instance resolved from the connector class name.
     */
    protected ?OAuthConnectorInterface $connectorInstance = null;

    /**
     * Only expose the tool when the current team is connected to the provider.
     */
    public function shouldRegister(Request $request): bool
    {
        return $this->connectionFor($request) !== null;
    }

    /**
     * Resolve the social connection for the authenticated user's current team.
     */
    protected function connectionFor(Request $request): ?OAuthConnection
    {
        $team = $request->user()?->currentTeam;

        if (! $team) {
            return null;
        }

        // TODO: Check if is active
        return $team->oAuthConnections()
            ->where('provider', $this->getConnector()->provider())
            ->first();
    }

    /**
     * Resolve the connector instance this tool is tied to.
     */
    protected function getConnector(): OAuthConnectorInterface
    {
        return $this->connectorInstance ??= app($this->connector());
    }

    /**
     * Error response used when the team has not connected the provider yet.
     */
    protected function notConnectedResponse(): Response
    {
        return Response::error(
            "Your team has not connected {$this->getConnector()->provider()} yet. Connect it first, then try again."
        );
    }
}
