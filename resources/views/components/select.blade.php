@props(['label' => null])

<div>
    @if($label)
        <label class="block text-xs font-semibold text-steel mb-1">{{ $label }}</label>
    @endif
    <select {{ $attributes->merge([
        'class' => 'w-full rounded-md border border-line bg-white px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-rajawali focus:border-rajawali'
    ]) }}>
        {{ $slot }}
    </select>
</div>
