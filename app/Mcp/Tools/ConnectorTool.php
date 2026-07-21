<?php

namespace App\Mcp\Tools;

use App\Contracts\OAuthConnectorInterface;
use App\Models\SocialConnection;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

/**
 * Base class for MCP tools backed by a social connector. Resolves the
 * authenticated team's `SocialConnection` for the tool's provider and
 * only registers the tool when such a connection exists.
 */
abstract class ConnectorTool extends Tool
{
    public function __construct(
        protected OAuthConnectorInterface $connector,
    ) {}

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
    protected function connectionFor(Request $request): ?SocialConnection
    {
        $team = $request->user()?->currentTeam;

        if (! $team) {
            return null;
        }

        return $team->socialConnections()
            ->where('provider', $this->connector->provider())
            ->first();
    }

    /**
     * Error response used when the team has not connected the provider yet.
     */
    protected function notConnectedResponse(): Response
    {
        return Response::error(
            "Your team has not connected {$this->connector->provider()} yet. Connect it first, then try again."
        );
    }
}
