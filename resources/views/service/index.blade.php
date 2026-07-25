@php
    $service = [
        ['no' => 'SV2026000045', 'tanggal' => '22 Jul 2026', 'customer' => 'Andi', 'motor' => 'Honda Beat', 'status' => 'proses', 'label' => 'Selesai'],
        ['no' => 'SV2026000044', 'tanggal' => '21 Jul 2026', 'customer' => 'Sinta', 'motor' => 'Yamaha NMax', 'status' => 'proses', 'label' => 'Dikerjakan'],
        ['no' => 'SV2026000043', 'tanggal' => '20 Jul 2026', 'customer' => 'Rudi', 'motor' => 'Honda Vario', 'status' => 'lunas', 'label' => 'Lunas'],
    ];
@endphp
<x-app-layout title="Service / Bengkel">
    <x-filter-bar>
        <x-input type="date" label="Dari Tanggal" value="2026-07-01" />
        <x-input type="date" label="Sampai Tanggal" value="2026-07-22" />
        <x-select label="Status">
            <option>Semua Status</option>
            <option>Masuk</option>
            <option>Dikerjakan</option>
            <option>Dikirim</option>
            <option>Selesai</option>
            <option>Diambil</option>
            <option>Lunas</option>
        </x-select>
        <div class="ml-auto">
            <x-button as="a" href="{{ route('service.create') }}" variant="primary"><x-icon name="plus" class="w-4 h-4" /> Terima Service Baru</x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">No Service</th>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal Masuk</th>
                    <th class="text-left font-semibold px-4 py-2.5">Customer</th>
                    <th class="text-left font-semibold px-4 py-2.5">Motor</th>
                    <th class="text-left font-semibold px-4 py-2.5">Status</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($service as $s)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $s['no'] }}</td>
                        <td class="px-4 py-2.5">{{ $s['tanggal'] }}</td>
                        <td class="px-4 py-2.5">{{ $s['customer'] }}</td>
                        <td class="px-4 py-2.5">{{ $s['motor'] }}</td>
                        <td class="px-4 py-2.5"><x-badge :status="$s['status']">{{ $s['label'] }}</x-badge></td>
                        <td class="px-4 py-2.5 text-right">
                            <a href="{{ route('service.show', $s['no']) }}" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas inline-block" title="Lihat Detail Service" data-tooltip="Lihat Detail Service"><x-icon name="eye" class="w-4 h-4" /></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</x-app-layout>
