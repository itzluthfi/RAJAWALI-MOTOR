@props([
    'action' => '',
    'method' => 'GET',
])

<x-card {{ $attributes->merge(['class' => 'mb-4']) }}>
    @if(!empty($action))
        <form action="{{ $action }}" method="{{ $method }}" class="flex flex-wrap items-end gap-3 w-full">
            {{ $slot }}
        </form>
    @else
        <div class="flex flex-wrap items-end gap-3 w-full">
            {{ $slot }}
        </div>
    @endif
</x-card>
