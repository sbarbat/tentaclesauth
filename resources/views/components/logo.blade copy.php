@props(['white' => false, 'size' => 'md'])

@php
$sizes = [
    'md' => ['container' => 'w-8 h-8', 'dot' => 'w-3.5 h-3.5', 'text' => 'text-lg'],
    'lg' => ['container' => 'w-10 h-10', 'dot' => 'w-4.5 h-4.5', 'text' => 'text-xl'],
    'xl' => ['container' => 'w-12 h-12', 'dot' => 'w-5.5 h-5.5', 'text' => 'text-2xl'],
];
$s = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex items-center gap-2.5">
    <div class="relative {{ $s['container'] }} flex items-center justify-center">
        <div class="absolute inset-0 rounded-full border border-violet-400/40 orbit-ring"></div>
        <div class="{{ $s['dot'] }} rounded-full bg-gradient-to-br from-violet-400 to-cyan-400"></div>
    </div>
    <span class="{{ $s['text'] }} font-semibold tracking-tight {{ $white ? 'text-white' : '' }}">Orbit <span class="{{ $white ? 'text-white/70' : 'text-violet-400' }}">PT</span></span>
</div>
