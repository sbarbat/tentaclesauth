<?php

namespace App\Connectors;

/**
 * Contract for MCP tools that are backed by an OAuth connector.
 *
 * Implementations declare which connector they use and which OAuth scopes
 * are required for the connector to fulfil the tool's requests. Connectors
 * collect all tools that point to them and merge their scopes before
 * redirecting the user to the provider's OAuth consent screen.
 */
interface ConnectorToolInterface
{
    /**
     * The connector class this tool uses.
     */
    public function connector(): string;

    /**
     * The OAuth scopes required on the connector for this tool to work.
     *
     * @return array<int, string>
     */
    public function scopes(): array;
}
