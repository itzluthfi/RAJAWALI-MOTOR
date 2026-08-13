@props(['href' => '#', 'icon', 'active' => false, 'badge' => null, 'badgeColor' => 'bg-rajawali text-white'])

<a href="{{ $href }}"
   :data-tooltip="!sidebarTerbuka ? @js(trim((string)$slot)) : null"
   @if($badge !== null && $badge !== '' && (int)$badge > 0)
       :data-tooltip-badge="!sidebarTerbuka ? @js((string)$badge) : null"
       data-tooltip-badge-color="{{ $badgeColor }}"
   @endif
   {{ $attributes->merge([
    'class' => 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 relative group '
        . ($active ? 'bg-rajawali text-white shadow-md font-bold' : 'text-steel hover:bg-canvas hover:text-ink')
]) }}>
    <x-icon :name="$icon" class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" />
    <span x-show="sidebarTerbuka" class="truncate flex-1 text-xs font-semibold" x-cloak>{{ $slot }}</span>
    
    @if($badge !== null && $badge !== '' && (int)$badge > 0)
        <span x-show="sidebarTerbuka" class="px-2 py-0.5 text-[10px] font-extrabold rounded-full transition-transform group-hover:scale-105 {{ $active ? 'bg-white text-rajawali font-black' : $badgeColor }}" x-cloak>
            {{ $badge }}
        </span>
        <span x-show="!sidebarTerbuka" class="w-2.5 h-2.5 rounded-full absolute top-2 right-2 border-2 border-surface {{ $badgeColor }}" x-cloak></span>
    @endif
</a>
