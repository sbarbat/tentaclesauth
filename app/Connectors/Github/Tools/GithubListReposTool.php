<?php

namespace App\Connectors\Github\Tools;

use App\Connectors\ConnectorTool;
use App\Connectors\Github\GithubConnector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Title;

#[Title('List GitHub Repos')]
class GithubListReposTool extends ConnectorTool
{
    public function connector(): string
    {
        return GithubConnector::provider();
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'page' => $schema->integer()
                ->min(1)
                ->default(1)
                ->description('The page number of the repositories to fetch (maximum 100 repositories per page).'),
            'per_page' => $schema->integer()
                ->min(10)
                ->default(100)
                ->max(100)
                ->description('The number of repositories to fetch per page.'),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'page' => 'sometimes|integer',
            'per_page' => 'sometimes|integer|max:100',
        ]);
        $connection = $this->connectionFor($request);

        if (! $connection) {
            return $this->notConnectedResponse();
        }

        $response = Http::withToken($connection->access_token)
            ->get('https://api.github.com/user/repos', [
                'per_page' => $validated['per_page'] ?? 100, // Adjust as needed
                'page' => $validated['page'] ?? 1, // You can implement pagination if needed
            ]);

        return Response::json($response->json() ?? []);
    }
}
