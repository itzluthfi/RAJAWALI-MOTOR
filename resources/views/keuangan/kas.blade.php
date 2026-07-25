@php
    $mutasi = [
        ['tanggal' => '22 Jul 2026', 'kategori' => 'Penjualan Tunai', 'jenis' => 'masuk', 'jumlah' => 4850000, 'ket' => 'Setoran kasir harian'],
        ['tanggal' => '22 Jul 2026', 'kategori' => 'Beban Listrik', 'jenis' => 'keluar', 'jumlah' => 350000, 'ket' => 'Bayar PLN Juli'],
    ];
@endphp
<x-app-layout title="Kas">
<div x-data="{ jenis: 'masuk' }">
    <x-card class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-steel uppercase tracking-wide">Saldo Kas Berjalan</p>
                <p class="font-mono font-bold text-2xl mt-1">Rp 12.480.000</p>
            </div>
            <x-button variant="primary" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'form-kas'}}))"><x-icon name="plus" class="w-4 h-4" /> Transaksi Kas</x-button>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Tanggal</th>
                    <th class="text-left font-semibold px-4 py-2.5">Kategori</th>
                    <th class="text-left font-semibold px-4 py-2.5">Keterangan</th>
                    <th class="text-right font-semibold px-4 py-2.5">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasi as $m)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5">{{ $m['tanggal'] }}</td>
                        <td class="px-4 py-2.5">{{ $m['kategori'] }}</td>
                        <td class="px-4 py-2.5 text-steel">{{ $m['ket'] }}</td>
                        <td class="px-4 py-2.5 text-right font-mono {{ $m['jenis'] === 'masuk' ? 'text-lunas' : 'text-rajawali' }}">
                            {{ $m['jenis'] === 'masuk' ? '+' : '-' }}{{ number_format($m['jumlah'], 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <x-modal name="form-kas" title="Transaksi Kas">
        <div class="flex items-center rounded-md border border-line overflow-hidden text-sm font-medium mb-4 w-fit">
            <button type="button" x-on:click="jenis = 'masuk'" :class="jenis === 'masuk' ? 'bg-lunas text-white' : 'bg-white text-steel'" class="px-4 py-2">Kas Masuk</button>
            <button type="button" x-on:click="jenis = 'keluar'" :class="jenis === 'keluar' ? 'bg-rajawali text-white' : 'bg-white text-steel'" class="px-4 py-2">Kas Keluar</button>
        </div>
        <form class="space-y-4">
            <x-input type="date" label="Tanggal" value="2026-07-22" />
            <x-select label="Kategori Perkiraan">
                <option>Beban Listrik</option>
                <option>Gaji</option>
                <option>Pendapatan Lain</option>
            </x-select>
            <x-input label="Jumlah" type="number" mono />
            <x-input label="Keterangan" />
            <div class="flex justify-end gap-2">
                <x-button variant="secondary" onclick="window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'form-kas'}}))">Batal</x-button>
                <x-button variant="primary" onclick="window.toastSukses('Transaksi kas berhasil disimpan.'); window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'form-kas'}}))">Simpan</x-button>
            </div>
        </form>
    </x-modal>
</div>
</x-app-layout>
