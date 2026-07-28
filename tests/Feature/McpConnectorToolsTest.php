<?php

use App\Connectors\ConnectorManager;
use App\Connectors\ConnectorToolInterface;
use App\Connectors\Facebook\FacebookConnector;
use App\Connectors\Facebook\Tools\GetFacebookPostStatsTool;
use App\Connectors\Facebook\Tools\GetFacebookPostsTool;
use App\Mcp\Servers\MCPServer;
use App\Models\OAuthConnection;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('connector manager collects the mcp tools provided by connectors', function () {
    $tools = app(ConnectorManager::class)->tools();

    expect($tools)->toHaveCount(9)
        ->and(collect($tools)->map->name()->all())->toContain(
            'facebook-get-post-stats',
            'facebook-get-posts',
            'get-facebook-me-tool',
            'facebook-get-user-family',
            'facebook-get-id-for-business',
            'facebook-get-my-photos',
            'monzo-list-accounts-tool',
            'monzo-list-transactions-tool',
            'monzo-read-balance-tool',
        );
});

test('facebook tools declare the facebook connector and scopes', function () {
    $postsTool = new GetFacebookPostsTool;
    $statsTool = new GetFacebookPostStatsTool;

    expect($postsTool)
        ->toBeInstanceOf(ConnectorToolInterface::class)
        ->connector()->toBe('facebook')
        ->scopes()->toBe(['pages_read_engagement'])
        ->and($statsTool)
        ->connector()->toBe('facebook')
        ->scopes()->toBe(['pages_read_engagement']);
});

test('facebook connector compiles scopes from its tools', function () {
    $connector = new FacebookConnector;

    expect($connector->scopes())->toBe(['pages_read_engagement', 'user_relationships']);
});

test('facebook get posts tool returns the connected pages posts', function () {
    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'data' => [
                ['id' => '123', 'message' => 'Hello world'],
            ],
        ]),
    ]);

    $user = User::factory()->withPersonalTeam()->create();

    OAuthConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostsTool,
        ['limit' => 5],
    );

    $response->assertOk()->assertSee('Hello world');
});

test('facebook get post stats tool returns engagement stats', function () {
    Http::fake([
        'graph.facebook.com/*' => Http::response([
            'id' => '123',
            'likes' => ['summary' => ['total_count' => 42]],
        ]),
    ]);

    $user = User::factory()->withPersonalTeam()->create();

    OAuthConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostStatsTool,
        ['post_id' => '123'],
    );

    $response->assertOk()->assertSee('42');
});

test('facebook get post stats tool requires a post id', function () {
    $user = User::factory()->withPersonalTeam()->create();

    OAuthConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostStatsTool,
    );

    $response->assertHasErrors();
});

test('connector tools are registered for teams without a connection but return a not connected error', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostsTool,
    );

    $response->assertHasErrors(['Your team has not connected facebook yet. Connect it first, then try again.']);
});
