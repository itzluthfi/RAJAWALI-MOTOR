<x-app-layout title="Opname Stok">
<div class="max-w-3xl mx-auto" x-data="opnameApp(@js($barangListJson))">
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

    <x-card>
        <div class="border-b border-line pb-3 mb-4">
            <h2 class="font-display font-bold text-lg text-ink">Penyesuaian Stok Opname Fisik</h2>
            <p class="text-xs text-steel">Sesuaikan jumlah stok tercatat di sistem dengan hasil perhitungan fisik di toko/gudang.</p>
        </div>

        <form action="{{ route('stok.opname.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-label>Pilih Barang / Sparepart</x-label>
                    <select
                        name="barang_id"
                        x-model="barangId"
                        x-on:change="pilihBarang()"
                        class="w-full text-xs font-bold rounded-lg border border-line bg-white px-3 py-2.5 focus:ring-2 focus:ring-rajawali"
                        required
                    >
                        <option value="">-- Pilih Barang --</option>
                        @foreach($daftarBarang as $b)
                            <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-label>Alasan Penyesuaian (Wajib Diisi)</x-label>
                    <x-input name="alasan" placeholder="Contoh: Barang rusak, hilang, salah hitung" required />
                </div>
            </div>

            <div class="p-4 bg-canvas border border-line rounded-xl grid grid-cols-3 gap-4 text-center">
                <div>
                    <span class="text-xs text-steel font-bold uppercase">Stok Sistem Saat Ini</span>
                    <p class="font-mono font-black text-xl text-ink mt-1" x-text="stokSistem"></p>
                </div>
                <div>
                    <span class="text-xs text-steel font-bold uppercase">Stok Fisik Sebenarnya</span>
                    <input
                        type="number"
                        name="stok_fisik"
                        x-model.number="stokFisik"
                        min="0"
                        class="w-full mt-1 text-center font-mono font-black text-xl rounded-lg border border-line bg-white p-1.5 focus:ring-2 focus:ring-rajawali"
                        required
                    >
                </div>
                <div>
                    <span class="text-xs text-steel font-bold uppercase">Selisih Penyesuaian</span>
                    <p
                        class="font-mono font-black text-xl mt-1"
                        :class="selisih < 0 ? 'text-rajawali' : (selisih > 0 ? 'text-emerald-600' : 'text-steel')"
                        x-text="(selisih > 0 ? '+' : '') + selisih"
                    ></p>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-line">
                <x-button type="submit" variant="primary">
                    <x-icon name="save" class="w-4 h-4" /> Simpan Penyesuaian Stok
                </x-button>
            </div>
        </form>
    </x-card>
</div>

<script>
function opnameApp(daftarBarang) {
    return {
        daftarBarang: daftarBarang,
        barangId: '',
        stokSistem: 0,
        stokFisik: 0,

        pilihBarang() {
            const b = this.daftarBarang.find(item => item.id == this.barangId);
            if (b) {
                this.stokSistem = Number(b.stok);
                this.stokFisik = Number(b.stok);
            } else {
                this.stokSistem = 0;
                this.stokFisik = 0;
            }
        },

        get selisih() {
            return this.stokFisik - this.stokSistem;
        }
    };
}
</script>
</x-app-layout>
