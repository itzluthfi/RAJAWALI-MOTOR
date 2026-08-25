@props(['name', 'title' => null, 'wide' => false, 'maxWidth' => null])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        'full' => 'max-w-full',
        default => ($wide ? 'max-w-3xl' : 'max-w-md'),
    };
@endphp

<div
    x-data="{ open: false }"
    x-on:buka-modal.window="if ($event.detail && $event.detail.name === '{{ $name }}') { open = true; $nextTick(() => setTimeout(() => { const inp = $el.querySelector('input:not([type=hidden]), select, textarea'); if(inp) { inp.focus(); if (inp.select) inp.select(); } }, 60)); }"
    x-on:tutup-modal.window="if (!$event.detail || $event.detail.name === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
    style="display: none;"
>
    <!-- Backdrop Overlay -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-ink/40 backdrop-blur-xs"
        x-on:click="open = false"
    ></div>

    <!-- Modal Card Wrapper -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-surface rounded-xl shadow-2xl border border-line w-full {{ $maxWidthClass }} max-h-[88vh] flex flex-col overflow-hidden"
    >
        @if($title)
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-line shrink-0 bg-surface">
                <h3 class="font-display font-semibold text-base text-ink">{{ $title }}</h3>
                <button type="button" x-on:click="open = false" aria-label="Tutup" class="text-steel hover:text-ink p-1 rounded-lg hover:bg-canvas transition">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>
        @endif

        <!-- Dedicated Scrollable Body -->
        <div class="p-5 flex-1 overflow-y-auto overscroll-contain space-y-4">
            {{ $slot }}
        </div>
    </div>
</div>
