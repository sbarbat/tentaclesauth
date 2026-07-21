<?php

namespace App\Http\Controllers;

use App\Services\Connectors\ConnectorManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SocialConnectionController extends Controller
{
    public function __construct(protected ConnectorManager $connectors) {}

    /**
     * Redirect the current user to the provider's OAuth consent screen.
     */
    public function redirect(string $provider): RedirectResponse
    {
        $team = Auth::user()->currentTeam;

        Gate::authorize('update', $team);

        return $this->connectors->driver($provider)->redirect();
    }

    /**
     * Handle the OAuth callback and link the connection to the current team.
     */
    public function callback(string $provider, Request $request): RedirectResponse
    {
        $team = Auth::user()->currentTeam;

        Gate::authorize('update', $team);

        if ($request->has('error') || $request->has('error_code')) {
            $error = $this->connectors->driver($provider)->error($team, $request);

            return redirect()->route('dashboard')->with('flash.error', $error);
        }

        $this->connectors->driver($provider)->connect($team, $request);

        return redirect()->route('dashboard')->with('flash.banner', ucfirst($provider).' connected successfully.');
    }

    /**
     * Disconnect the current team from the given provider.
     */
    public function destroy(string $provider): RedirectResponse
    {
        $team = Auth::user()->currentTeam;

        Gate::authorize('update', $team);

        $connection = $team->socialConnections()->where('provider', $provider)->firstOrFail();

        $this->connectors->driver($provider)->disconnect($connection);

        return redirect()->route('dashboard')->with('flash.banner', ucfirst($provider).' disconnected.');
    }
}
