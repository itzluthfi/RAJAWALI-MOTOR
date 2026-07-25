@props(['href' => '#', 'icon', 'active' => false])

<a href="{{ $href }}"
   :title="!sidebarTerbuka ? '{{ $slot }}' : ''"
   {{ $attributes->merge([
    'class' => 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition duration-150 relative group '
        . ($active ? 'bg-rajawali text-white shadow-sm' : 'text-steel hover:bg-canvas hover:text-ink')
]) }}>
    <x-icon :name="$icon" class="w-4 h-4 shrink-0" />
    <span x-show="sidebarTerbuka" class="truncate" x-cloak>{{ $slot }}</span>
    <div x-show="!sidebarTerbuka" class="absolute left-full ml-2 px-2.5 py-1 bg-ink text-white text-xs font-medium rounded-md opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-50 shadow-xl transition-opacity duration-150" x-cloak>
        {{ $slot }}
    </div>
</a>
