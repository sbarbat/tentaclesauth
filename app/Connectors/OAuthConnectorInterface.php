<?php

namespace App\Connectors;

use App\Models\OAuthConnection;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Mcp\Server\Tool;

/**
 * Contract that must be implemented by every social media network
 * connector (e.g. Facebook, Instagram, TikTok, X, Reddit).
 *
 * Implementations encapsulate OAuth authentication, content retrieval,
 * stats retrieval, and webhook management for a single provider.
 */
interface OAuthConnectorInterface
{
    /**
     * The provider key this connector is registered under (e.g. "facebook").
     */
    public static function provider(): string;

    /**
     * Redirect the user to the provider's OAuth consent screen.
     */
    public function redirect(): RedirectResponse;

    /**
     * Handle the OAuth callback, exchange the code for tokens, and
     * persist a `OAuthConnection` record linked to the given team.
     */
    public function connect(Team $team, Request $request): OAuthConnection;

    /**
     * Handle an error response from the provider's OAuth flow.
     */
    public function error(Team $team, Request $request): string;

    /**
     * Refresh an expired (or soon to expire) access token and persist
     * the updated credentials on the connection.
     */
    public function refreshToken(OAuthConnection $connection): OAuthConnection;

    /**
     * Disconnect the team from the provider, revoking tokens where
     * supported by the provider's API.
     */
    public function disconnect(OAuthConnection $connection): bool;

    /**
     * Get all MCP tool instances connected to this connector.
     *
     * @return array<int, Tool>
     */
    public function tools(): array;

    /**
     * Compile the OAuth scopes required by this connector and its tools.
     *
     * @return array<int, string>
     */
    public function scopes(): array;
}
