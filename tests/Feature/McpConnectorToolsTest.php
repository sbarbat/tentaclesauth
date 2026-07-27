<?php

use App\Contracts\ConnectorToolInterface;
use App\Mcp\Servers\MCPServer;
use App\Models\OAuthConnection;
use App\Models\User;
use App\Connectors\ConnectorManager;
use App\Connectors\Facebook\Tools\GetFacebookPostStatsTool;
use App\Connectors\Facebook\Tools\GetFacebookPostsTool;
use App\Connectors\FacebookConnector;
use Illuminate\Support\Facades\Http;

test('connector manager collects the mcp tools provided by connectors', function () {
    $tools = app(ConnectorManager::class)->tools();

    expect($tools)->toHaveCount(2)
        ->and(collect($tools)->map->name()->all())->toBe([
            'facebook-get-post-stats',
            'facebook-get-posts',
        ]);
});

test('facebook tools declare the facebook connector and scopes', function () {
    $postsTool = new GetFacebookPostsTool;
    $statsTool = new GetFacebookPostStatsTool;

    expect($postsTool)
        ->toBeInstanceOf(ConnectorToolInterface::class)
        ->connector()->toBe(FacebookConnector::class)
        ->scopes()->toBe(['pages_read_engagement'])
        ->and($statsTool)
        ->connector()->toBe(FacebookConnector::class)
        ->scopes()->toBe(['pages_read_engagement']);
});

test('facebook connector compiles scopes from its tools', function () {
    $connector = new FacebookConnector;

    expect($connector->scopes())->toBe(['pages_read_engagement']);
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

test('connector tools are not registered when the team has no connection for the provider', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostsTool,
    );

    $response->assertHasErrors(['Tool [facebook-get-posts] not found.']);
});
