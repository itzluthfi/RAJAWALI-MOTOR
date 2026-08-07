@props(['label' => null, 'error' => null, 'tampilkanOpsional' => true])

@php
    $isRequired = $attributes->has('required') && $attributes->get('required') !== false;
@endphp

<div>
    @if($label)
        <label class="block text-xs font-semibold text-steel mb-1">
            {{ $label }}
            @if($isRequired)
                <span class="text-rajawali font-bold text-xs ml-0.5" title="Wajib diisi">*</span>
            @elseif($tampilkanOpsional && $label !== 'Status')
                <span class="text-[10px] text-steel/70 font-normal ml-1">(Opsional)</span>
            @endif
        </label>
    @endif
    <select {{ $attributes->merge([
        'class' => 'w-full rounded-md border bg-white px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 ' . 
            ($error ? 'border-rajawali focus:ring-rajawali/30 focus:border-rajawali' : 'border-line focus:ring-rajawali focus:border-rajawali')
    ]) }}>
        {{ $slot }}
    </select>
    @if($error)
        <p class="mt-1 text-xs font-medium text-rajawali flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ $error }}</span>
        </p>
    @endif
</div>
