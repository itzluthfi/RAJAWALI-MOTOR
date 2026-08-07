<x-app-layout title="Terima Service Baru">
<div x-data="serviceForm(@js($daftarBarangJson))" class="-m-3 p-3">
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('service.store') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            {{-- DATA CUSTOMER & KENDARAAN --}}
            <x-card class="lg:col-span-2">
                <h3 class="font-display font-semibold text-sm mb-4">Data Kendaraan &amp; Customer</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input label="No Dokumen" value="{{ $nomorDokumenBaru }}" readonly />
                    <x-input type="date" name="tanggal_masuk" label="Tanggal Masuk" value="{{ date('Y-m-d') }}" required />
                    
                    <x-select name="customer_id" label="Customer" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->nama }}</option>
                        @endforeach
                    </x-select>

                    <x-select name="montir_id" label="Montir / Teknisi" required>
                        <option value="">-- Pilih Montir --</option>
                        @foreach($montirs as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </x-select>

                    <x-select name="sales_id" label="Sales Penerima">
                        <option value="">-- Pilih Sales --</option>
                        @foreach($sales as $sl)
                            <option value="{{ $sl->id }}">{{ $sl->nama }}</option>
                        @endforeach
                    </x-select>

                    <x-input name="merk_type" label="Merk / Type Motor" placeholder="cth. Honda Vario 125" />
                    <x-input name="no_rangka" label="No Rangka" />
                    <x-input name="no_mesin" label="No Mesin" />
                    <x-input name="kelengkapan" label="Kelengkapan" placeholder="cth. STNK, Kunci Kontak" class="md:col-span-2" />
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-steel mb-1">Keluhan / Diagnosa</label>
                    <textarea name="keluhan" rows="3" class="w-full rounded-md border border-line bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali" placeholder="cth. CVT berbunyi kasar saat akselerasi"></textarea>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-semibold text-steel mb-1">Catatan Tambahan</label>
                    <textarea name="catatan" rows="2" class="w-full rounded-md border border-line bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali" placeholder="cth. Ban belakang botak, ingatkan customer"></textarea>
                </div>
            </x-card>

            {{-- DETAIL REPAIR & PENGERJAAN --}}
            <x-card class="flex flex-col justify-between">
                <div>
                    <h3 class="font-display font-semibold text-sm mb-4">Metode Pengerjaan</h3>
                    <div class="flex items-center gap-4 mb-4">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="repaired_by" x-model="repairedBy" value="intern"> Intern
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="radio" name="repaired_by" x-model="repairedBy" value="extern"> Extern (Outsource)
                        </label>
                    </div>

                    <div x-show="repairedBy === 'extern'" x-cloak class="space-y-4 border-t border-line pt-4">
                        <x-select name="supplier_id" label="Supplier / Bengkel Rekanan" ::required="repairedBy === 'extern'">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $sp)
                                <option value="{{ $sp->id }}">{{ $sp->nama }}</option>
                            @endforeach
                        </x-select>
                        <x-input type="date" name="tanggal_kirim" label="Tanggal Kirim ke Supplier" />
                        <x-input type="date" name="tanggal_kembali" label="Estimasi Tanggal Kembali" />
                    </div>
                </div>

                <div class="border-t border-line pt-4 mt-6">
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between"><span class="text-steel">Total Harga Supplier</span><span class="font-mono font-semibold" x-text="formatRp(totalSupplier)"></span></div>
                        <div class="flex justify-between items-baseline pt-2 border-t border-line">
                            <span class="font-display font-bold text-ink">TOTAL NETT</span>
                            <span class="font-mono font-black text-xl text-rajawali" x-text="formatRp(totalNett)"></span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- JASA & SPAREPARTS SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            
            {{-- DATA JASA --}}
            <x-card :padded="false">
                <div class="p-4 border-b border-line flex justify-between items-center bg-surface">
                    <h3 class="font-display font-semibold text-sm">Daftar Jasa Service</h3>
                    <x-button type="button" variant="secondary" size="sm" x-on:click="tambahJasa()"><x-icon name="plus" class="w-3.5 h-3.5" /> Tambah Jasa</x-button>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                        <tr>
                            <th class="text-left font-semibold px-3 py-2">Nama Jasa</th>
                            <th class="text-right font-semibold px-3 py-2 w-28">Hrg Supp</th>
                            <th class="text-right font-semibold px-3 py-2 w-28">Hrg Nett</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="jasas.length === 0">
                            <tr><td colspan="4" class="text-center text-steel py-8">Belum ada jasa ditambahkan. Klik Tambah Jasa.</td></tr>
                        </template>
                        <template x-for="(jasa, index) in jasas" :key="index">
                            <tr class="border-b border-line last:border-0">
                                <td class="px-3 py-2">
                                    <input type="text" :name="`jasas[${index}][nama_jasa]`" x-model="jasa.nama_jasa" placeholder="cth. Servis CVT" class="w-full bg-transparent border-none p-1 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rajawali rounded text-sm" required>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <input type="number" min="0" :name="`jasas[${index}][harga_supplier]`" x-model.number="jasa.harga_supplier" class="w-24 text-right font-mono bg-transparent border-none p-1 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rajawali rounded text-sm" required>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <input type="number" min="0" :name="`jasas[${index}][harga_nett]`" x-model.number="jasa.harga_nett" class="w-24 text-right font-mono bg-transparent border-none p-1 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rajawali rounded text-sm" required>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" x-on:click="hapusJasa(index)" class="text-steel hover:text-rajawali"><x-icon name="trash" class="w-4 h-4" /></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-card>

            {{-- DATA SPAREPART --}}
            <x-card :padded="false">
                <div class="p-4 border-b border-line flex justify-between items-center bg-surface">
                    <h3 class="font-display font-semibold text-sm">Penggunaan Spareparts</h3>
                    <x-button type="button" variant="secondary" size="sm" x-on:click="$dispatch('buka-modal', { name: 'cari-barang-service' })"><x-icon name="search" class="w-3.5 h-3.5" /> Pilih Sparepart</x-button>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                        <tr>
                            <th class="text-left font-semibold px-3 py-2">Nama Sparepart</th>
                            <th class="text-right font-semibold px-3 py-2 w-20">Qty</th>
                            <th class="text-right font-semibold px-3 py-2 w-28">Harga</th>
                            <th class="text-right font-semibold px-3 py-2 w-28">Total</th>
                            <th class="px-3 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="items.length === 0">
                            <tr><td colspan="5" class="text-center text-steel py-8">Belum ada sparepart digunakan. Klik Pilih Sparepart.</td></tr>
                        </template>
                        <template x-for="(item, index) in items" :key="item.id">
                            <tr class="border-b border-line last:border-0">
                                <td class="px-3 py-2">
                                    <input type="hidden" :name="`items[${index}][barang_id]`" :value="item.id">
                                    <span class="font-medium text-ink" x-text="item.nama"></span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <input type="number" min="1" step="1" :name="`items[${index}][qty]`" x-model.number="item.qty" class="w-16 text-right font-mono bg-transparent border-none p-1 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rajawali rounded text-sm" required>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <input type="number" min="0" :name="`items[${index}][harga]`" x-model.number="item.harga" class="w-24 text-right font-mono bg-transparent border-none p-1 focus:bg-white focus:outline-none focus:ring-1 focus:ring-rajawali rounded text-sm" required>
                                </td>
                                <td class="px-3 py-2 text-right font-mono font-medium" x-text="formatRp(item.qty * item.harga)"></td>
                                <td class="px-3 py-2 text-center">
                                    <button type="button" x-on:click="hapusSparepart(index)" class="text-steel hover:text-rajawali"><x-icon name="trash" class="w-4 h-4" /></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </x-card>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <x-button as="a" href="{{ route('service.index') }}" variant="secondary">Batal</x-button>
            <x-button type="submit" variant="primary"><x-icon name="save" class="w-4 h-4" /> Simpan Transaksi</x-button>
        </div>
    </form>

    {{-- MODAL CARI BARANG SPAREPART --}}
    <x-modal name="cari-barang-service" title="Pilih Sparepart">
        <div class="space-y-3" x-data x-init="$nextTick(() => $refs.modalSearchInput?.focus())">
            <div class="relative">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-steel" />
                <input
                    x-ref="modalSearchInput"
                    x-model="cariQuery"
                    type="text"
                    placeholder="Ketik nama barang atau kode..."
                    class="w-full rounded-md border border-line bg-white pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali"
                >
            </div>
            <div class="max-h-72 overflow-y-auto border border-line rounded-lg divide-y divide-line">
                <template x-for="b in daftarBarangFiltered" :key="b.id">
                    <div
                        x-on:click="pilihSparepart(b)"
                        class="p-3 hover:bg-canvas cursor-pointer flex justify-between items-center transition"
                    >
                        <div>
                            <span class="font-mono text-xs font-bold text-rajawali" x-text="b.kode"></span>
                            <span class="font-bold text-sm text-ink block" x-text="b.nama"></span>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-sm text-ink block" x-text="formatRp(b.harga)"></span>
                            <span class="text-xs font-mono text-steel">Stok: <strong x-text="b.stok"></strong></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </x-modal>
</div>

<script>
function serviceForm(daftarBarang) {
    return {
        barangList: daftarBarang,
        repairedBy: 'intern',
        jasas: [],
        items: [],
        cariQuery: '',

        get daftarBarangFiltered() {
            const q = (this.cariQuery || '').trim().toLowerCase();
            if (!q) return this.barangList;
            return this.barangList.filter(b => 
                b.nama.toLowerCase().includes(q) || b.kode.toLowerCase().includes(q)
            );
        },

        tambahJasa() {
            this.jasas.push({
                nama_jasa: '',
                harga_supplier: 0,
                harga_nett: 0
            });
        },

        hapusJasa(index) {
            this.jasas.splice(index, 1);
        },

        pilihSparepart(barang) {
            const ada = this.items.find(i => i.id === barang.id);
            if (ada) {
                ada.qty++;
            } else {
                this.items.push({
                    id: barang.id,
                    nama: barang.nama,
                    qty: 1,
                    harga: barang.harga
                });
            }
            this.cariQuery = '';
            this.$dispatch('tutup-modal', { name: 'cari-barang-service' });
        },

        hapusSparepart(index) {
            this.items.splice(index, 1);
        },

        get totalSupplier() {
            return this.jasas.reduce((sum, j) => sum + Number(j.harga_supplier || 0), 0);
        },

        get totalNett() {
            const totalJasa = this.jasas.reduce((sum, j) => sum + Number(j.harga_nett || 0), 0);
            const totalSparepart = this.items.reduce((sum, i) => sum + (Number(i.qty || 0) * Number(i.harga || 0)), 0);
            return totalJasa + totalSparepart;
        },

        formatRp(angka) {
            return 'Rp ' + Math.round(angka || 0).toLocaleString('id-ID');
        }
    };
}
</script>
</x-app-layout>
