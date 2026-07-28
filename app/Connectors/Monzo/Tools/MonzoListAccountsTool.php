<?php

namespace App\Connectors\Monzo\Tools;

use App\Connectors\ConnectorTool;
use App\Connectors\Monzo\MonzoConnector;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Title;

#[Title('List Monzo Accounts Information')]
class MonzoListAccountsTool extends ConnectorTool
{
    public function connector(): string
    {
        return MonzoConnector::provider();
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
            ->get('https://api.monzo.com/accounts');

        return Response::json($response->json() ?? []);
    }
}
