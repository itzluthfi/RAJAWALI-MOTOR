@props(['status' => 'default'])

@php
    $map = [
        'lunas' => 'bg-lunas/10 text-lunas',
        'tempo' => 'bg-marka/20 text-[#8a6404]',
        'batal' => 'bg-batal/15 text-steel',
        'proses' => 'bg-rajawali/10 text-rajawali',
        'default' => 'bg-line/60 text-steel',
    ];
    $classes = 'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs font-semibold ' . ($map[$status] ?? $map['default']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>
