<x-app-layout title="Harian Kas">
<div x-data="{ jenis: 'masuk' }" class="-m-3 p-3">
    @if(session('sukses'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm font-bold">
            {{ session('sukses') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <x-card class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-steel uppercase tracking-wide">Saldo Kas Berjalan</p>
                <p class="font-mono font-black text-2xl mt-1 text-ink">Rp {{ number_format($saldoKas, 0, ',', '.') }}</p>
            </div>
            <x-button variant="primary" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'form-kas'}}))"><x-icon name="plus" class="w-4 h-4" /> Transaksi Kas</x-button>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-bold px-4 py-2.5">Tanggal</th>
                    <th class="text-left font-bold px-4 py-2.5">Kategori</th>
                    <th class="text-left font-bold px-4 py-2.5">Keterangan</th>
                    <th class="text-right font-bold px-4 py-2.5">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasi as $m)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 font-bold">
                        <td class="px-4 py-2.5 text-steel font-medium">{{ $m->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-2.5 font-bold text-ink">{{ ucfirst($m->kategori) }}</td>
                        <td class="px-4 py-2.5 text-steel font-medium">{{ $m->keterangan ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono {{ $m->tipe === 'masuk' ? 'text-lunas' : 'text-rajawali' }}">
                            {{ $m->tipe === 'masuk' ? '+' : '-' }}Rp {{ number_format($m->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-steel font-medium">Belum ada transaksi kas hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <x-modal name="form-kas" title="Transaksi Kas Baru">
        <div class="flex items-center rounded-md border border-line overflow-hidden text-sm font-semibold mb-4 w-fit">
            <button type="button" x-on:click="jenis = 'masuk'" :class="jenis === 'masuk' ? 'bg-lunas text-white' : 'bg-white text-steel'" class="px-4 py-2">Kas Masuk</button>
            <button type="button" x-on:click="jenis = 'keluar'" :class="jenis === 'keluar' ? 'bg-rajawali text-white' : 'bg-white text-steel'" class="px-4 py-2">Kas Keluar</button>
        </div>
        <form method="POST" action="{{ route('keuangan.transaksi.store') }}" class="space-y-4 font-bold">
            @csrf
            <input type="hidden" name="tipe" :value="jenis">
            <input type="hidden" name="sumber" value="kas">
            
            <x-input type="date" name="tanggal" label="Tanggal Transaksi" value="{{ date('Y-m-d') }}" required />
            
            <x-select name="kategori" label="Kategori Perkiraan" required>
                <option value="penjualan">Pendapatan Penjualan / Jasa</option>
                <option value="operasional">Beban Operasional Toko</option>
                <option value="gaji">Beban Gaji Karyawan</option>
                <option value="listrik">Beban Listrik & Air</option>
                <option value="piutang">Pelunasan Piutang</option>
                <option value="hutang">Pelunasan Hutang</option>
                <option value="lainnya">Pendapatan/Pengeluaran Lainnya</option>
            </x-select>
            
            <x-input label="Jumlah (Nominal Rp)" name="nominal" type="number" min="0.01" step="0.01" required mono />
            
            <x-input label="Keterangan Tambahan" name="keterangan" placeholder="cth. Bayar listrik bulan Juli 2026" />
            
            <div class="flex justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" onclick="window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'form-kas'}}))">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Transaksi</x-button>
            </div>
        </form>
    </x-modal>
</div>
</x-app-layout>
