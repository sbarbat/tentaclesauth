<?php

namespace App\Connectors\Facebook\Tools;

use App\Connectors\ConnectorTool;
use App\Connectors\Facebook\FacebookConnector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

class GetFacebookMyPhotosTool extends ConnectorTool
{
    protected string $name = 'facebook-get-my-photos';

    protected string $description = 'Fetch the authenticated Facebook user\'s photos.';

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
            // No input required for fetching the authenticated user's basic information.
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $connection = $this->connectionFor($request);

        if (! $connection) {
            return $this->notConnectedResponse();
        }

        $response = Http::withToken($connection->access_token)
            ->get('https://graph.facebook.com/me/photos', [
                'fields' => 'id,name,images',
            ]);

        return Response::json($response->json() ?? []);
    }
}
