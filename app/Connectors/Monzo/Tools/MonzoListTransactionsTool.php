<?php

namespace App\Connectors\Monzo\Tools;

use App\Connectors\ConnectorTool;
use App\Connectors\Monzo\MonzoConnector;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Title;

#[Title('List Monzo Account Transactions')]
class MonzoListTransactionsTool extends ConnectorTool
{
    public function connector(): string
    {
        return MonzoConnector::provider();
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'account_id' => $schema->string()
                ->description('The ID of the Monzo account to fetch the transactions for. You can obtain this ID by using the "List Monzo Accounts" tool.')
                ->required(),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'account_id' => 'required|string',
        ]);

        $connection = $this->connectionFor($request);

        if (! $connection) {
            return $this->notConnectedResponse();
        }

        $response = Http::withToken($connection->access_token)
            ->get('https://api.monzo.com/transactions', [
                'account_id' => $validated['account_id'],
            ]);

        return Response::json($response->json() ?? []);
    }
}
