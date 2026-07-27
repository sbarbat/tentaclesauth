<?php

namespace App\Livewire\Mcp;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ServerSetup extends Component
{
    /**
     * The name given to API keys created for MCP server access.
     */
    private const TOKEN_NAME = 'MCP Server';

    /**
     * The plain-text API key, shown to the user once after creation.
     */
    public ?string $plainTextToken = null;

    /**
     * The absolute URL of the application's MCP server endpoint.
     */
    #[Computed]
    public function mcpUrl(): string
    {
        return url('/mcp');
    }

    /**
     * Whether the current team has at least one connected social account.
     */
    #[Computed]
    public function hasConnections(): bool
    {
        return Auth::user()->currentTeam->oAuthConnections()->exists();
    }

    /**
     * The API keys the user has created for MCP server access.
     *
     * @return Collection<int, PersonalAccessToken>
     */
    #[Computed]
    public function tokens(): Collection
    {
        return Auth::user()->tokens()
            ->where('name', self::TOKEN_NAME)
            ->latest()
            ->get();
    }

    /**
     * The MCP client configuration snippet, pre-filled with the server
     * URL and the freshly generated API key when one is on screen.
     */
    #[Computed]
    public function clientConfig(): string
    {
        return (string) json_encode(
            [
                'mcpServers' => [
                    (string) str(config('app.name'))->slug() => [
                        'command' => 'npx',
                        'args' => [
                            'mcp-remote',
                            $this->mcpUrl,
                            '--header',
                            'Authorization: Bearer '.($this->plainTextToken ?? '<your-api-key>'),
                        ],
                    ],
                ],
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Create a new API key for MCP server access and display it once.
     */
    public function createToken(): void
    {
        if (! $this->hasConnections) {
            return;
        }

        $this->plainTextToken = Auth::user()->createToken(self::TOKEN_NAME)->plainTextToken;

        unset($this->tokens);
    }

    /**
     * Revoke one of the user's MCP API keys.
     */
    public function revokeToken(int $tokenId): void
    {
        Auth::user()->tokens()->whereKey($tokenId)->delete();

        unset($this->tokens);
    }

    public function render(): View
    {
        return view('livewire.mcp.server-setup');
    }
}
