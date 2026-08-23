@props(['name', 'title' => null, 'wide' => false])

<div
    x-data="{ open: false }"
    x-on:buka-modal.window="if ($event.detail && $event.detail.name === '{{ $name }}') { open = true; $nextTick(() => setTimeout(() => { const inp = $el.querySelector('input:not([type=hidden]), select, textarea'); if(inp) { inp.focus(); if (inp.select) inp.select(); } }, 60)); }"
    x-on:tutup-modal.window="if (!$event.detail || $event.detail.name === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div
        x-show="open"
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-ink/40"
        x-on:click="open = false"
    ></div>

    <div
        x-show="open"
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-surface rounded-md shadow-lg border border-line w-full {{ $wide ? 'max-w-3xl' : 'max-w-md' }} max-h-[90vh] overflow-y-auto"
    >
        @if($title)
            <div class="flex items-center justify-between px-5 py-3 border-b border-line">
                <h3 class="font-display font-semibold text-base">{{ $title }}</h3>
                <button type="button" x-on:click="open = false" aria-label="Tutup" class="text-steel hover:text-ink">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>
        @endif
        <div class="p-5">
            {{ $slot }}
        </div>
    </div>
</div>
