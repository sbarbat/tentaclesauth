<?php

namespace App\Mcp\Tools\Facebook;

use App\Mcp\Tools\ConnectorTool;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

class GetFacebookPostStatsTool extends ConnectorTool
{
    protected string $name = 'facebook-get-post-stats';

    protected string $description = 'Fetch engagement stats (likes, comments, shares) for a single Facebook post.';

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

        $stats = $this->connector->getPostStats($connection, $validated['post_id']);

        return Response::json($stats);
    }
}
