<?php

namespace App\Mcp\Servers;

use App\Connectors\ConnectorManager;
use Illuminate\Support\Facades\Log;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Contracts\Transport;

#[Name('M C P Server')]
#[Version('0.0.1')]
#[Instructions('Instructions describing how to use the server and its features.')]
class MCPServer extends Server
{
    private readonly ConnectorManager $connectorsManager;

    protected array $tools = [
        //
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];

    public function __construct(Transport $transport, ConnectorManager $connectorsManager)
    {
        parent::__construct($transport);

        $this->connectorsManager = $connectorsManager;
    }


    protected function boot(): void
    {
        $path = request()->path();
        $connector = str_replace('mcp/', '', $path);

        if ($connector && $connector !== 'mcp' && $this->connectorsManager->driver($connector)) {
            $this->tools = array_merge($this->tools, $this->connectorsManager->driver($connector)->tools());
            Log::debug('MCPServer tools for connector ' . $connector, ['count' => count($this->tools), 'tools' => $this->tools]);
        } else {
            // $this->tools = array_merge($this->tools, $this->connectorsManager->tools());
            Log::debug('MCPServer tools', ['count' => count($this->tools), 'tools' => $this->tools]);
        }
    }
}
