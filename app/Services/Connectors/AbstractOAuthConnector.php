<?php

namespace App\Services\Connectors;

use App\Contracts\OAuthConnectorInterface;
use App\Contracts\ProvidesMcpTools;
use App\Models\SocialConnection;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

/**
 * Shared OAuth plumbing for provider connectors built on top of
 * Laravel Socialite. Provider-specific classes only need to implement
 * the content/stats/webhook methods from `OAuthConnectorInterface`.
 */
abstract class AbstractOAuthConnector implements OAuthConnectorInterface, ProvidesMcpTools
{
    /**
     * The Socialite driver name used by this connector, if it differs
     * from the provider key (e.g. "x" uses the "twitter" driver).
     */
    protected string $driver;

    protected array $scopes;

    public function __construct()
    {
        $this->driver = $this->driver ?? $this->provider();
    }

    public function redirect(): RedirectResponse
    {
        return Socialite::driver($this->driver)
            ->scopes($this->scopes ?? [])
            ->stateless()
            ->redirect();
    }

    public function connect(Team $team, Request $request): SocialConnection
    {
        /** @var SocialiteUser $socialiteUser */
        $socialiteUser = Socialite::driver($this->driver)
            ->stateless()
            ->user();

        return SocialConnection::updateOrCreate(
            [
                'team_id' => $team->id,
                'provider' => $this->provider(),
            ],
            [
                'provider_account_id' => $socialiteUser->getId(),
                'provider_account_name' => $socialiteUser->getName() ?? $socialiteUser->getNickname(),
                'access_token' => $socialiteUser->token,
                'token_expires_at' => $socialiteUser->expiresIn
                    ? now()->addSeconds($socialiteUser->expiresIn)
                    : null,
                'refresh_token' => $socialiteUser->refreshToken ?? null,
                'refresh_token_expires_at' => $socialiteUser->refreshExpiresIn
                    ? now()->addSeconds($socialiteUser->refreshExpiresIn)
                    : null,
                'error_code' => null,
                'error_message' => null,
            ]
        );
    }

    public function error(Team $team, Request $request): string
    {
        $error = $request->input('error_message') ?? $request->input('error_code');

        return "Connection Failed: ${error}";
    }

    public function disconnect(SocialConnection $connection): bool
    {
        return (bool) $connection->delete();
    }

    /**
     * Connectors expose no MCP tools by default. Override in provider
     * connectors to return the tool instances for that provider.
     */
    public function mcpTools(): array
    {
        return [];
    }
}
