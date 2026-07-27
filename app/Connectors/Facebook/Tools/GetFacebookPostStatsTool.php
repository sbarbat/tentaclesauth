<?php

namespace App\Connectors\Facebook\Tools;

use App\Connectors\ConnectorTool;
use App\Connectors\Facebook\FacebookConnector;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

class GetFacebookPostStatsTool extends ConnectorTool
{
    protected string $name = 'facebook-get-post-stats';

    protected string $description = 'Fetch engagement stats (likes, comments, shares) for a single Facebook post.';

    public function connector(): string
    {
        return FacebookConnector::provider();
    }

    /**
     * @return array<int, string>
     */
    public function scopes(): array
    {
        return [
            'pages_read_engagement',
        ];
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'post_id' => $schema->string()
                ->description('The ID of the Facebook post to fetch stats for.')
                ->required(),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'post_id' => 'required|string',
        ]);

        $connection = $this->connectionFor($request);

        if (! $connection) {
            return $this->notConnectedResponse();
        }

        $response = Http::withToken($connection->access_token)
            ->get("https://graph.facebook.com/v19.0/{$postId}", [
                'fields' => 'likes.summary(true),comments.summary(true),shares',
            ])
            ->throw();

        return Response::json($response->json() ?? []);
    }

}
