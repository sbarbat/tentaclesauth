<?php

namespace App\Contracts;

use App\Models\SocialConnection;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Contract that must be implemented by every social media network
 * connector (e.g. Facebook, Instagram, TikTok, X, Reddit).
 *
 * Implementations encapsulate OAuth authentication, content retrieval,
 * stats retrieval, and webhook management for a single provider.
 */
interface SocialConnectorInterface
{
    /**
     * The provider key this connector is registered under (e.g. "facebook").
     */
    public function provider(): string;

    /**
     * Redirect the user to the provider's OAuth consent screen.
     */
    public function redirect(): RedirectResponse;

    /**
     * Handle the OAuth callback, exchange the code for tokens, and
     * persist a `SocialConnection` record linked to the given team.
     */
    public function connect(Team $team, Request $request): SocialConnection;

    /**
     * Handle an error response from the provider's OAuth flow.
     */
    public function error(Team $team, Request $request): string;

    /**
     * Refresh an expired (or soon to expire) access token and persist
     * the updated credentials on the connection.
     */
    public function refreshToken(SocialConnection $connection): SocialConnection;

    /**
     * Disconnect the team from the provider, revoking tokens where
     * supported by the provider's API.
     */
    public function disconnect(SocialConnection $connection): bool;

    /**
     * Fetch the most recent posts published through this connection.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPosts(SocialConnection $connection, int $limit = 25): array;

    /**
     * Fetch engagement/performance stats for a single post.
     *
     * @return array<string, mixed>
     */
    public function getPostStats(SocialConnection $connection, string $postId): array;

    /**
     * Register a webhook with the provider so the application can
     * receive real-time updates (e.g. new comments, mentions).
     */
    public function setWebhook(SocialConnection $connection, string $callbackUrl): bool;

    /**
     * Remove a previously registered webhook.
     */
    public function removeWebhook(SocialConnection $connection): bool;
}
