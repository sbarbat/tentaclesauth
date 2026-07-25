<div>
    <x-action-section>
        <x-slot name="title">
            {{ __('Connect Your AI') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Give your AI assistant access to your connected accounts through your MCP server.') }}
        </x-slot>

        <x-slot name="content">
            @php($enabled = $this->hasConnections)

            @if (! $enabled)
                <div class="rounded-md bg-gray-50 border border-gray-200 p-4 mb-6">
                    <p class="text-sm text-gray-500">
                        {{ __('Connect at least one account above to enable your MCP server.') }}
                    </p>
                </div>
            @endif

            <div @class(['space-y-6', 'opacity-50 pointer-events-none select-none' => ! $enabled]) @if (! $enabled) aria-disabled="true" @endif>
                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ __('1. Your MCP server URL') }}</div>

                    <div class="mt-2 flex items-center gap-2">
                        <x-input type="text" readonly value="{{ $this->mcpUrl }}"
                            class="flex-1 font-mono text-xs bg-gray-50" />

                        <x-secondary-button type="button" x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText(@js($this->mcpUrl)).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                            :disabled="! $enabled">
                            <span x-show="! copied">{{ __('Copy') }}</span>
                            <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                        </x-secondary-button>
                    </div>
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ __('2. Generate an API key') }}</div>

                    @if ($plainTextToken)
                        <div class="mt-2 rounded-md bg-green-50 border border-green-200 p-3">
                            <div class="text-xs font-medium text-green-700">
                                {{ __('Copy this key now — it will only be shown once.') }}
                            </div>

                            <div class="mt-1 flex items-center justify-between gap-2">
                                <code class="text-xs font-mono text-green-900 break-all">{{ $plainTextToken }}</code>

                                <x-secondary-button type="button" x-data="{ copied: false }"
                                    x-on:click="navigator.clipboard.writeText(@js($plainTextToken)).then(() => { copied = true; setTimeout(() => copied = false, 2000); })">
                                    <span x-show="! copied">{{ __('Copy') }}</span>
                                    <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                                </x-secondary-button>
                            </div>
                        </div>
                    @endif

                    <div class="mt-2">
                        <x-button type="button" wire:click="createToken" wire:loading.attr="disabled"
                            :disabled="! $enabled">
                            {{ __('Generate API Key') }}
                        </x-button>
                    </div>

                    @if ($this->tokens->isNotEmpty())
                        <div class="mt-4 space-y-2">
                            @foreach ($this->tokens as $token)
                                <div class="flex items-center justify-between" wire:key="token-{{ $token->id }}">
                                    <div class="text-xs text-gray-500">
                                       API KEY {{ __('Created :date', ['date' => $token->created_at->diffForHumans()]) }}
                                        @if ($token->last_used_at)
                                            -
                                            {{ __('Last used :date', ['date' => $token->last_used_at->diffForHumans()]) }}
                                        @endif
                                    </div>

                                    <x-danger-button wire:click="revokeToken({{ $token->id }})"
                                        wire:loading.attr="disabled" :disabled="! $enabled">
                                        {{ __('Revoke') }}
                                    </x-danger-button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <div class="text-sm font-semibold text-gray-800">{{ __('3. Add it to your AI client') }}</div>

                    <p class="mt-1 text-xs text-gray-500">
                        {{ __('Paste this into your client\'s MCP settings (e.g. Claude Desktop\'s claude_desktop_config.json), then restart the client.') }}
                    </p>

                    <pre class="mt-2 rounded-md bg-gray-800 p-3 text-xs font-mono text-gray-100 overflow-x-auto">{{ $this->clientConfig }}</pre>

                    <div class="mt-2">
                        <x-secondary-button type="button" x-data="{ copied: false }"
                            x-on:click="navigator.clipboard.writeText(@js($this->clientConfig)).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                            :disabled="! $enabled">
                            <span x-show="! copied">{{ __('Copy Config') }}</span>
                            <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </x-slot>
    </x-action-section>
</div>
