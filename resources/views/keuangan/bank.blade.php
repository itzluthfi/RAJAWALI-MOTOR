<x-app-layout title="Bank Cash Flow">
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
                <x-select class="mb-2 w-56 font-bold">
                    <option>BCA — 123-456-7890</option>
                    <option>Mandiri — 998-877-6655</option>
                </x-select>
                <p class="text-xs font-bold text-steel uppercase tracking-wide">Saldo Berjalan Bank</p>
                <p class="font-mono font-black text-2xl mt-1 text-ink">Rp {{ number_format($saldoBank, 0, ',', '.') }}</p>
            </div>
            <x-button variant="primary" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'form-bank'}}))"><x-icon name="plus" class="w-4 h-4" /> Transaksi Bank</x-button>
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
                        <td class="px-4 py-2.5 text-ink">{{ ucfirst($m->kategori) }}</td>
                        <td class="px-4 py-2.5 text-steel font-medium">{{ $m->keterangan ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono {{ $m->tipe === 'masuk' ? 'text-lunas' : 'text-rajawali' }}">
                            {{ $m->tipe === 'masuk' ? '+' : '-' }}Rp {{ number_format($m->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-steel font-medium">Belum ada transaksi bank hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <x-modal name="form-bank" title="Transaksi Bank Baru">
        <div class="flex items-center rounded-md border border-line overflow-hidden text-sm font-semibold mb-4 w-fit">
            <button type="button" x-on:click="jenis = 'masuk'" :class="jenis === 'masuk' ? 'bg-lunas text-white' : 'bg-white text-steel'" class="px-4 py-2">Transfer Masuk</button>
            <button type="button" x-on:click="jenis = 'keluar'" :class="jenis === 'keluar' ? 'bg-rajawali text-white' : 'bg-white text-steel'" class="px-4 py-2">Transfer Keluar</button>
        </div>
        <form method="POST" action="{{ route('keuangan.transaksi.store') }}" class="space-y-4 font-bold">
            @csrf
            <input type="hidden" name="tipe" :value="jenis">
            <input type="hidden" name="sumber" value="bank">
            
            <x-input type="date" name="tanggal" label="Tanggal Transaksi" value="{{ date('Y-m-d') }}" required />
            
            <x-select name="kategori" label="Kategori Perkiraan" required>
                <option value="transfer">Transfer Piutang Pelanggan</option>
                <option value="pembayaran">Pembayaran Hutang Supplier</option>
                <option value="bunga">Pendapatan Bunga Bank</option>
                <option value="admin">Biaya Administrasi Bank</option>
                <option value="lainnya">Lainnya</option>
            </x-select>
            
            <x-input label="Jumlah (Nominal Rp)" name="nominal" type="number" min="0.01" step="0.01" required mono />
            
            <x-input label="Keterangan Tambahan" name="keterangan" placeholder="cth. Transfer piutang Bengkel Sumber Rejeki" />
            
            <div class="flex justify-end gap-2 pt-4 border-t">
                <x-button type="button" variant="secondary" onclick="window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'form-bank'}}))">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Transaksi</x-button>
            </div>
        </form>
    </x-modal>
</div>
</x-app-layout>
