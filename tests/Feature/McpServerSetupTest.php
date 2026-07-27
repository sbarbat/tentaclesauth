<?php

use App\Livewire\Mcp\ServerSetup;
use App\Models\OAuthConnection;
use App\Models\User;
use Livewire\Livewire;

test('mcp server setup is disabled when the team has no connections', function () {
    $user = User::factory()->withPersonalTeam()->create();

    Livewire::actingAs($user)
        ->test(ServerSetup::class)
        ->assertSee('Connect at least one account above to enable your MCP server.')
        ->call('createToken');

    expect($user->tokens()->count())->toBe(0);
});

test('mcp server setup shows the server url and client config when the team has connections', function () {
    $user = User::factory()->withPersonalTeam()->create();

    OAuthConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    Livewire::actingAs($user)
        ->test(ServerSetup::class)
        ->assertSee(url('/mcp'))
        ->assertSee('mcpServers')
        ->assertDontSee('Connect at least one account above to enable your MCP server.');
});

test('user can generate an api key for the mcp server', function () {
    $user = User::factory()->withPersonalTeam()->create();

    OAuthConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $component = Livewire::actingAs($user)
        ->test(ServerSetup::class)
        ->call('createToken');

    $plainTextToken = $component->get('plainTextToken');

    expect($plainTextToken)->not->toBeNull()
        ->and($user->tokens()->where('name', 'MCP Server')->count())->toBe(1);

    $component->assertSee($plainTextToken);
});

test('user can revoke an api key', function () {
    $user = User::factory()->withPersonalTeam()->create();

    OAuthConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    $token = $user->createToken('MCP Server')->accessToken;

    Livewire::actingAs($user)
        ->test(ServerSetup::class)
        ->call('revokeToken', $token->id);

    expect($user->tokens()->count())->toBe(0);
});

test('generated api keys authenticate against the mcp endpoint', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $this->postJson('/mcp')->assertUnauthorized();

    $token = $user->createToken('MCP Server')->plainTextToken;

    $response = $this->withToken($token)->postJson('/mcp');

    expect($response->status())->not->toBe(401);
});
