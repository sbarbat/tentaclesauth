<?php

namespace App\Mcp\Tools\Facebook;

use App\Mcp\Tools\ConnectorTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

class GetFacebookPostsTool extends ConnectorTool
{
    protected string $name = 'facebook-get-posts';

    protected string $description = 'Fetch the most recent posts published on the connected Facebook page.';

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

        $posts = $this->connector->getPosts($connection, $validated['limit'] ?? 25);

        return Response::json($posts);
    }
}
