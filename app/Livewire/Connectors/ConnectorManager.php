<?php

namespace App\Livewire\Connectors;

use App\Connectors\ConnectorManager as Connectors;
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
        return app(Connectors::class)->available();
    }

    #[Computed]
    public function connections()
    {
        return Auth::user()->currentTeam->oAuthConnections()->get()->keyBy('provider');
    }

    public function disconnect(string $provider): void
    {
        $team = Auth::user()->currentTeam;

        Gate::authorize('update', $team);

        $connection = $team->oAuthConnections()->where('provider', $provider)->first();

        if ($connection) {
            app(Connectors::class)->driver($provider)->disconnect($connection);
        }

        unset($this->connections);
    }

    public function render(): View
    {
        return view('livewire.connectors.connector-manager');
    }
}
