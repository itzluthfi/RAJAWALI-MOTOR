@props(['label' => null, 'error' => null, 'mono' => false])

<div>
    @if($label)
        <label class="block text-xs font-semibold text-steel mb-1">{{ $label }}</label>
    @endif
    <input {{ $attributes->merge([
        'class' => 'w-full rounded-md border border-line bg-white px-3 py-2 text-sm text-ink placeholder:text-steel/60 focus:outline-none focus:ring-2 focus:ring-rajawali focus:border-rajawali ' . ($mono ? 'font-mono text-right' : '')
    ]) }} />
    @if($error)
        <p class="mt-1 text-xs text-rajawali">{{ $error }}</p>
    @endif
</div>
