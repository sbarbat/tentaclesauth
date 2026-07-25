<?php

namespace App\Mcp\Servers;

use App\Services\Connectors\ConnectorManager;
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
    protected array $tools = [
        //
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];

    public function __construct(Transport $transport, ConnectorManager $connectors)
    {
        parent::__construct($transport);

        $this->tools = array_merge($this->tools, $connectors->tools());
        dd($this->tools);
    }
}
