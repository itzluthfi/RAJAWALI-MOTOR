@props(['icon' => 'inbox', 'judul' => 'Belum ada data', 'keterangan' => ''])

<div class="flex flex-col items-center justify-center py-16 text-center">
    <x-icon :name="$icon" class="w-8 h-8 text-steel/50 mb-3" />
    <p class="font-medium text-ink">{{ $judul }}</p>
    @if($keterangan)
        <p class="text-sm text-steel mt-1">{{ $keterangan }}</p>
    @endif
</div>
