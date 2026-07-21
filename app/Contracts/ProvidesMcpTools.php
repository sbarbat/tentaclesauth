<?php

namespace App\Contracts;

use Laravel\Mcp\Server\Tool;

/**
 * Contract for connectors that expose MCP tools. Implementations return
 * tool instances which are registered on the application's MCP server,
 * giving AI clients access to the provider's API through the connector.
 */
interface ProvidesMcpTools
{
    /**
     * Get the MCP tool instances this connector exposes.
     *
     * @return array<int, Tool>
     */
    public function mcpTools(): array;
}
