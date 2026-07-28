<?php

namespace App\Connectors;

use App\Models\OAuthConnection;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Laravel\Mcp\Server\Tool;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

/**
 * Shared OAuth plumbing for provider connectors built on top of
 * Laravel Socialite. Provider-specific classes only need to implement
 * the content/stats/webhook methods from `OAuthConnectorInterface`.
 */
abstract class AbstractOAuthConnector implements OAuthConnectorInterface
{
    /**
     * The Socialite driver name used by this connector, if it differs
     * from the provider key (e.g. "x" uses the "twitter" driver).
     */
    protected string $driver;

    /**
     * Base OAuth scopes required by the connector regardless of which
     * tools are connected to it.
     *
     * @var array<int, string>
     */
    protected array $scopes = [];

    /**
     * Cached tool instances discovered for this connector.
     *
     * @var array<int, Tool>|null
     */
    protected ?array $toolInstances = null;

    public function __construct()
    {
        $reflection = new ReflectionClass(static::class);
        $defaults = $reflection->getDefaultProperties();

        $this->driver = $defaults['driver'] ?? static::provider();
    }

    public function redirect(): RedirectResponse
    {
        $randomState = bin2hex(random_bytes(16));

        Cache::put("oauth_state_{$randomState}", true, now()->addMinutes(5));
        
        return Socialite::driver($this->driver)
            ->scopes($this->scopes())
            ->with(['state' => $randomState])
            ->redirect();
    }

    public function connect(Team $team, Request $request): OAuthConnection
    {
        $state = $request->input('state');
        $stateValue = Cache::get("oauth_state_{$state}");
        if (! $stateValue) {
            throw new \Exception('Invalid or expired OAuth state.');
        }
        Cache::forget("oauth_state_{$state}");

        /** @var SocialiteUser $socialiteUser */
        $socialiteUser = Socialite::driver($this->driver)
            ->stateless()
            ->user();

        return OAuthConnection::updateOrCreate(
            [
                'team_id' => $team->id,
                'provider' => static::provider(),
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

        return "Connection Failed: {$error}";
    }

    public function disconnect(OAuthConnection $connection): bool
    {
        return (bool) $connection->delete();
    }

    /**
     * Get all MCP tool instances connected to this connector.
     *
     * Tools are auto-discovered from the `Tools` subdirectory of the
     * connector's class namespace, then filtered to only those whose
     * `connector()` method points back to this connector class.
     *
     * @return array<int, Tool>
     */
    public function tools(): array
    {
        return $this->toolInstances ??= $this->discoverTools();
    }

    /**
     * Compile the OAuth scopes required by this connector.
     *
     * Merges the connector's base scopes with the scopes required by
     * every tool connected to it, removing duplicates.
     *
     * @return array<int, string>
     */
    public function scopes(): array
    {
        $toolScopes = collect($this->tools())
            ->flatMap(fn (Tool $tool) => $tool instanceof ConnectorToolInterface
                ? $tool->scopes()
                : [])
            ->all();

        return array_values(array_unique(array_merge($this->scopes, $toolScopes)));
    }

    /**
     * Discover tool classes in the connector's `Tools` subdirectory.
     *
     * @return array<int, Tool>
     */
    protected function discoverTools(): array
    {
        $reflection = new ReflectionClass(static::class);
        $directory = dirname($reflection->getFileName()).'/Tools';

        if (! is_dir($directory)) {
            return [];
        }

        $namespace = $reflection->getNamespaceName().'\\Tools';
        $tools = [];
        $finder = new Finder;
        $finder->files()->in($directory)->name('*.php')->depth(0);
        $finder->sortByName();

        foreach ($finder as $file) {
            $class = $namespace.'\\'.$file->getBasename('.php');

            if (! class_exists($class)) {
                Log::debug("Tool class {$class} does not exist and will be ignored.");

                continue;
            }

            $instance = new $class;

            if (! $instance instanceof ConnectorToolInterface) {
                Log::debug("Tool {$class} does not implement ConnectorToolInterface and will be ignored.");

                continue;
            }

            if ($instance->connector() !== static::provider()) {
                Log::warning("Tool {$class} does not point to connector ".static::provider().' and will be ignored.');

                continue;
            }

            $tools[] = $instance;
        }

        return $tools;
    }
}
