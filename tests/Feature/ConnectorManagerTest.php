<?php

use App\Livewire\Connectors\ConnectorManager;
use App\Models\SocialConnection;
use App\Models\User;
use Livewire\Livewire;

test('connectors component lists available providers', function () {
    $user = User::factory()->withPersonalTeam()->create();

    Livewire::actingAs($user)
        ->test(ConnectorManager::class)
        ->assertSee('Facebook')
        ->assertSee('Instagram')
        ->assertSee('Not connected');
});

test('connectors component shows connected providers for the current team', function () {
    $user = User::factory()->withPersonalTeam()->create();

    SocialConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
        'provider_account_name' => 'Acme Page',
    ]);

    Livewire::actingAs($user)
        ->test(ConnectorManager::class)
        ->assertSee('Connected as Acme Page');
});

test('team owner can disconnect a provider', function () {
    $user = User::factory()->withPersonalTeam()->create();

    $connection = SocialConnection::factory()->create([
        'team_id' => $user->currentTeam->id,
        'provider' => 'facebook',
    ]);

    Livewire::actingAs($user)
        ->test(ConnectorManager::class)
        ->call('disconnect', 'facebook');

    expect(SocialConnection::find($connection->id))->toBeNull();
});
