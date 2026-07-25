@props(['label'])

<div class="pt-3 first:pt-0">
    <p x-show="sidebarTerbuka" class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-steel/70" x-cloak>{{ $label }}</p>
    <div class="space-y-1">
        {{ $slot }}
    </div>
</div>
