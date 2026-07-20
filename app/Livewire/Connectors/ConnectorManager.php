<?php

namespace App\Livewire\Connectors;

use App\Services\Connectors\SocialConnectorManager;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ConnectorManager extends Component
{
    #[Computed]
    public function providers(): array
    {
        return app(SocialConnectorManager::class)->available();
    }

    #[Computed]
    public function connections()
    {
        return Auth::user()->currentTeam->socialConnections()->get()->keyBy('provider');
    }

    public function disconnect(string $provider): void
    {
        $team = Auth::user()->currentTeam;

        Gate::authorize('update', $team);

        $connection = $team->socialConnections()->where('provider', $provider)->first();

        if ($connection) {
            app(SocialConnectorManager::class)->driver($provider)->disconnect($connection);
        }

        unset($this->connections);
    }

    public function render(): View
    {
        return view('livewire.connectors.connector-manager');
    }
}
