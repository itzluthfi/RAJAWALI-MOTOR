@props([
    'variant' => 'secondary',
    'as' => 'button',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-md text-sm font-medium px-3.5 py-2 transition duration-150 active:scale-[0.98] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rajawali focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none';

    $variants = [
        'primary' => 'bg-rajawali text-white hover:bg-rajawali-dark shadow-sm',
        'secondary' => 'bg-white text-ink border border-line hover:bg-canvas shadow-sm',
        'ghost' => 'text-steel hover:text-ink hover:bg-canvas',
        'danger' => 'bg-white text-rajawali border border-rajawali/30 hover:bg-rajawali/5',
        'success' => 'bg-lunas text-white hover:bg-lunas/90 shadow-sm',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['secondary']);
@endphp

@if($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
