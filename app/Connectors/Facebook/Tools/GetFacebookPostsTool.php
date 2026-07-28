<?php

namespace App\Connectors\Facebook\Tools;

use App\Connectors\ConnectorTool;
use App\Connectors\Facebook\FacebookConnector;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

class GetFacebookPostsTool extends ConnectorTool
{
    protected string $name = 'facebook-get-posts';

    protected string $description = 'Fetch the most recent posts published on the connected Facebook page.';

    public function connector(): string
    {
        return FacebookConnector::provider();
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'limit' => $schema->integer()
                ->description('The maximum number of posts to return.')
                ->default(25),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        $connection = $this->connectionFor($request);

        if (! $connection) {
            return $this->notConnectedResponse();
        }

        $limit = $validated['limit'] ?? 25;

        $response = Http::withToken($connection->access_token)
            ->get("https://graph.facebook.com/v19.0/{$connection->provider_account_id}/posts", [
                'limit' => $limit,
                'fields' => 'id,message,created_time,permalink_url',
            ])
            ->throw();

        return Response::json($response->json('data', []));        
    }
}
