<?php

use App\Mcp\Servers\MCPServer;
use App\Mcp\Tools\Facebook\GetFacebookPostStatsTool;
use App\Mcp\Tools\Facebook\GetFacebookPostsTool;
use App\Models\SocialConnection;
use App\Models\User;
use App\Services\Connectors\ConnectorManager;
use App\Services\Connectors\FacebookConnector;
use Illuminate\Support\Facades\Http;

test('connector manager collects the mcp tools provided by connectors', function () {
    $tools = app(ConnectorManager::class)->tools();

    expect($tools)->toHaveCount(2)
        ->and(collect($tools)->map->name()->all())->toBe([
            'facebook-get-posts',
            'facebook-get-post-stats',
        ]);
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

    SocialConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostsTool(new FacebookConnector),
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

    SocialConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostStatsTool(new FacebookConnector),
        ['post_id' => '123'],
    );

    $response->assertOk()->assertSee('42');
});

test('facebook get post stats tool requires a post id', function () {
    $user = User::factory()->withPersonalTeam()->create();

    SocialConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostStatsTool(new FacebookConnector),
    );

    $response->assertHasErrors();
});

test('connector tools are not registered when the team has no connection for the provider', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $response = MCPServer::actingAs($user)->tool(
        new GetFacebookPostsTool(new FacebookConnector),
    );

    $response->assertHasErrors(['Tool [facebook-get-posts] not found.']);
});
