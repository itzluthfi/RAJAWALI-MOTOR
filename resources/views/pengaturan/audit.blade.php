@php
    $audit = [
        ['waktu' => '22 Jul 2026 14:12', 'user' => 'Budi Santoso', 'aksi' => 'Batalkan Nota', 'objek' => 'PJ2026000098', 'perubahan' => 'status: lunas → batal'],
        ['waktu' => '22 Jul 2026 10:03', 'user' => 'Sari Wulandari', 'aksi' => 'Simpan Nota', 'objek' => 'PJ2026000123', 'perubahan' => 'total: Rp 40.000'],
    ];
@endphp
<x-app-layout title="Audit Log">
    <x-filter-bar>
        <x-select label="User">
            <option>Semua User</option>
            <option>Budi Santoso</option>
            <option>Sari Wulandari</option>
        </x-select>
        <x-input type="date" label="Tanggal" value="2026-07-22" />
        <x-select label="Aksi">
            <option>Semua Aksi</option>
            <option>Simpan Nota</option>
            <option>Batalkan Nota</option>
        </x-select>
        <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Tampilkan</x-button>
    </x-filter-bar>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Waktu</th>
                    <th class="text-left font-semibold px-4 py-2.5">User</th>
                    <th class="text-left font-semibold px-4 py-2.5">Aksi</th>
                    <th class="text-left font-semibold px-4 py-2.5">Objek</th>
                    <th class="text-left font-semibold px-4 py-2.5">Perubahan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audit as $a)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $a['waktu'] }}</td>
                        <td class="px-4 py-2.5">{{ $a['user'] }}</td>
                        <td class="px-4 py-2.5">{{ $a['aksi'] }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $a['objek'] }}</td>
                        <td class="px-4 py-2.5 text-steel">{{ $a['perubahan'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
