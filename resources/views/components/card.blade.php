@props(['padded' => true])

<div {{ $attributes->merge(['class' => 'bg-surface border border-line rounded-md shadow-sm ' . ($padded ? 'p-4' : '')]) }}>
    {{ $slot }}
</div>
