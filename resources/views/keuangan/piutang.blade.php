<x-app-layout title="Piutang Dagang Customer">
<div class="-m-3 p-3" x-data="piutangApp()">
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
                <p class="text-xs font-bold text-steel uppercase tracking-wide">Total Outstanding Piutang Customer</p>
                <p class="font-mono font-black text-2xl mt-1 text-rajawali">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
            </div>
            <form method="GET" action="{{ route('keuangan.piutang') }}" class="flex items-center gap-2">
                <x-input name="cari" value="{{ $filter['cari'] }}" placeholder="No Nota / Customer" class="w-64" />
                <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
            </form>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-bold px-4 py-2.5">No Nota</th>
                    <th class="text-left font-bold px-4 py-2.5">Customer & Kendaraan</th>
                    <th class="text-left font-bold px-4 py-2.5">Tanggal & Jatuh Tempo</th>
                    <th class="text-right font-bold px-4 py-2.5">Grand Total</th>
                    <th class="text-right font-bold px-4 py-2.5">Telah Dibayar / DP</th>
                    <th class="text-right font-bold px-4 py-2.5">Sisa Piutang</th>
                    <th class="text-right font-bold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($piutangs as $p)
                    @php
                        $sisa = max(0, $p->total_akhir - $p->uang_muka);
                        $jatuhTempo = $p->created_at->addDays($p->customer->termin_hari ?? 30);
                    @endphp
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150 font-bold">
                        <td class="px-4 py-2.5 font-mono text-xs text-rajawali">{{ $p->nomor_nota }}</td>
                        <td class="px-4 py-2.5 text-ink font-bold">
                            <div>{{ $p->customer->nama }}</div>
                            @if($p->customer->plat_nomor)
                                <div class="text-xs text-steel font-mono">{{ $p->customer->plat_nomor }} ({{ $p->customer->jenis_kendaraan ?? '-' }})</div>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-steel font-medium text-xs">
                            <div>Trans: {{ $p->created_at->format('d M Y') }}</div>
                            <div class="text-rajawali font-bold">Jatuh Tempo: {{ $jatuhTempo->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-2.5 text-right font-mono text-ink">Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-lunas">Rp {{ number_format($p->uang_muka, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rajawali">Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex justify-end gap-1">
                                <x-button type="button" variant="secondary" class="text-xs px-2.5 py-1"
                                    x-on:click="bukaModalCicilan({{ $p->id }}, @js($p->nomor_nota), @js($p->customer->nama), {{ $sisa }})">
                                    💵 Nyicil
                                </x-button>

                                <form method="POST" action="{{ route('keuangan.piutang.bayar', $p) }}" onsubmit="return confirm('Pelunasan penuh untuk nota {{ $p->nomor_nota }}?')">
                                    @csrf
                                    <input type="hidden" name="nominal_bayar" value="{{ $sisa }}">
                                    <x-button type="submit" variant="primary" class="text-xs px-2.5 py-1">Bayar Lunas</x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-steel font-medium">Tidak ada outstanding piutang saat ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    <x-modal name="modal-cicilan" title="Pembayaran Cicilan Piutang">
        <form method="POST" x-bind:action="urlCicilan">
            @csrf
            <div class="space-y-4">
                <div class="text-xs text-steel bg-canvas p-3 rounded-lg space-y-1">
                    <p>Customer: <strong class="text-ink font-bold" x-text="namaCustomer"></strong></p>
                    <p>No. Nota: <strong class="text-rajawali font-mono" x-text="nomorNota"></strong></p>
                    <p>Sisa Piutang: <strong class="text-rajawali font-mono" x-text="'Rp ' + Math.round(sisaPiutang).toLocaleString('id-ID')"></strong></p>
                </div>

                <div>
                    <label class="text-sm font-bold text-steel block mb-1">Nominal Pembayaran / Cicilan (Rp)</label>
                    <input
                        type="number"
                        name="nominal_bayar"
                        x-model="nominalBayar"
                        x-bind:max="sisaPiutang"
                        min="1"
                        required
                        class="w-full rounded-md border border-line bg-white px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-rajawali"
                    >
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 pt-3 border-t border-line">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'modal-cicilan' })">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Cicilan</x-button>
            </div>
        </form>
    </x-modal>
</div>
</x-app-layout>

<script>
function piutangApp() {
    return {
        urlCicilan: '',
        nomorNota: '',
        namaCustomer: '',
        sisaPiutang: 0,
        nominalBayar: 0,

        bukaModalCicilan(id, nota, nama, sisa) {
            this.urlCicilan = `{{ url('/admin/keuangan/piutang') }}/${id}/pelunasan`;
            this.nomorNota = nota;
            this.namaCustomer = nama;
            this.sisaPiutang = sisa;
            this.nominalBayar = sisa;
            this.$dispatch('buka-modal', { name: 'modal-cicilan' });
        }
    }
}
</script>
