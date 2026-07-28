<div>
    <x-action-section>
        <x-slot name="title">
            {{ __('Connectors') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Connect your team\'s social media accounts to schedule posts and pull stats.') }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-6">
                @foreach ($this->providers as $provider => $label)
                    @php($connection = $this->connections->get($provider))

                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-semibold text-gray-800">{{ $label }}</div>

                            @if ($connection)
                                <div class="text-xs text-gray-500">
                                    {{ __('Connected as :name', ['name' => $connection->provider_account_name ?? $connection->provider_account_id]) }}
                                    -
                                    {{ __('Token expires in :date', ['date' => ($connection->refresh_token_expires_at ?? $connection->token_expires_at ?? now())->diffForHumans()]) }}
                                </div>
                            @else
                                <div class="text-xs text-gray-400">{{ __('Not connected') }}</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($connection)
                                <x-secondary-button wire:click="refresh('{{ $provider }}')"
                                    wire:loading.attr="disabled">
                                    {{ __('Refresh') }}
                                </x-secondary-button>

                                <x-danger-button wire:click="disconnect('{{ $provider }}')"
                                    wire:loading.attr="disabled">
                                    {{ __('Disconnect') }}
                                </x-danger-button>
                            @else
                                <a href="{{ route('connectors.redirect', $provider) }}">
                                    <x-button type="button">
                                        {{ __('Connect') }}
                                    </x-button>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-slot>
    </x-action-section>
</div>
