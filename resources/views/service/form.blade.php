<x-app-layout title="Terima Service Baru">
<div x-data="serviceForm(@js($daftarBarangJson), @js($daftarCustomerJson))" class="-m-3 p-3 space-y-4">
    @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('service.store') }}">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            
            {{-- DATA CUSTOMER & KENDARAAN --}}
            <x-card class="lg:col-span-2 shadow-lg border border-slate-200/80">
                <div class="border-b border-line pb-3 mb-4 flex justify-between items-center">
                    <div>
                        <h3 class="font-display font-bold text-sm text-ink flex items-center gap-2">
                            <x-icon name="bike" class="w-4 h-4 text-rajawali" /> Data Kendaraan &amp; Customer
                        </h3>
                        <p class="text-xs text-steel">Catat informasi motor dan keluhan pelanggan saat masuk bengkel.</p>
                    </div>
                    <span class="font-mono text-xs font-bold text-rajawali bg-rajawali/10 px-2.5 py-1 rounded-full border border-rajawali/20">
                        {{ $nomorDokumenBaru }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-input type="date" name="tanggal_masuk" label="Tanggal Masuk *" value="{{ date('Y-m-d') }}" required />
                    
                    <div>
                        <label class="block text-xs font-bold text-steel uppercase mb-1">Pilih Customer *</label>
                        <select
                            name="customer_id"
                            x-model="customerId"
                            x-on:change="pilihCustomer()"
                            class="w-full text-xs font-bold rounded-lg border border-line px-3 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white"
                            required
                        >
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->plat_nomor ?: 'Tanpa Plat' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-steel uppercase mb-1">No WhatsApp / Telepon *</label>
                        <input
                            type="text"
                            name="telepon"
                            x-model="telepon"
                            placeholder="08xxxxxxxxxx"
                            class="w-full text-xs font-mono font-bold rounded-lg border border-line px-3 py-2 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white"
                            required
                        >
                        <p class="text-[10px] text-steel mt-0.5">Nota tanda terima servis akan dikirimkan ke nomor WA ini.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-steel uppercase mb-1">Montir / Teknisi *</label>
                        <select name="montir_id" class="w-full text-xs font-bold rounded-lg border border-line px-3 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white" required>
                            <option value="">-- Pilih Montir Penanggung Jawab --</option>
                            @foreach($montirs as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->peran }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-steel uppercase mb-1">Sales Penerima (Opsional)</label>
                        <select name="sales_id" class="w-full text-xs font-medium rounded-lg border border-line px-3 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white">
                            <option value="">-- Tanpa Sales Khusus --</option>
                            @foreach($sales as $sl)
                                <option value="{{ $sl->id }}">{{ $sl->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-steel uppercase mb-1">Merk / Tipe Motor</label>
                        <input type="text" name="merk_type" x-model="merkType" placeholder="cth. Honda Vario 125" class="w-full text-xs font-bold rounded-lg border border-line px-3 py-2 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white">
                    </div>

                    <x-input name="no_rangka" label="No Rangka (Opsional)" placeholder="Nomor rangka rangka motor..." />
                    <x-input name="no_mesin" label="No Mesin (Opsional)" placeholder="Nomor mesin motor..." />
                    <x-input name="kelengkapan" label="Kelengkapan Barang" placeholder="cth. STNK, Kunci Kontak, Helm" class="md:col-span-2" />
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-steel uppercase mb-1">Keluhan / Diagnosa Pelanggan</label>
                    <textarea name="keluhan" rows="2" class="w-full rounded-lg border border-line bg-white px-3 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-rajawali" placeholder="cth. CVT berbunyi kasar saat akselerasi, tarikan gas berat"></textarea>
                </div>
                <div class="mt-3">
                    <label class="block text-xs font-bold text-steel uppercase mb-1">Catatan Tambahan Bengkel</label>
                    <textarea name="catatan" rows="2" class="w-full rounded-lg border border-line bg-white px-3 py-2 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-rajawali" placeholder="cth. Ban belakang sudah aus tipis, ingatkan customer"></textarea>
                </div>
            </x-card>

            {{-- DETAIL REPAIR & PENGERJAAN --}}
            <x-card class="flex flex-col justify-between shadow-lg border border-slate-200/80">
                <div>
                    <h3 class="font-display font-bold text-sm text-ink mb-3 flex items-center gap-2">
                        <x-icon name="wrench" class="w-4 h-4 text-blue-600" /> Metode Pengerjaan
                    </h3>

                    <div class="grid grid-cols-2 gap-2 mb-4 p-1 bg-canvas rounded-xl border border-line text-xs font-bold">
                        <button
                            type="button"
                            x-on:click="repairedBy = 'intern'"
                            :class="repairedBy === 'intern' ? 'bg-blue-600 text-white shadow-xs' : 'text-steel hover:text-ink'"
                            class="py-2.5 rounded-lg text-center transition cursor-pointer"
                        >
                            Bengkel Sendiri (Intern)
                        </button>
                        <button
                            type="button"
                            x-on:click="repairedBy = 'extern'"
                            :class="repairedBy === 'extern' ? 'bg-blue-600 text-white shadow-xs' : 'text-steel hover:text-ink'"
                            class="py-2.5 rounded-lg text-center transition cursor-pointer"
                        >
                            Bengkel Luar (Outsource)
                        </button>
                        <input type="hidden" name="repaired_by" :value="repairedBy">
                    </div>

                    <div x-show="repairedBy === 'extern'" x-cloak class="space-y-3 border-t border-line pt-3">
                        <div>
                            <label class="block text-xs font-bold text-blue-900 uppercase mb-1">Pilih Bengkel Rekanan Luar *</label>
                            <select name="supplier_id" class="w-full text-xs font-bold rounded-lg border border-blue-300 px-3 py-2 bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                                <option value="">-- Pilih Bengkel Rekanan --</option>
                                @foreach($suppliers as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-input type="date" name="tanggal_kirim" label="Tanggal Kirim ke Bengkel Luar" />
                        <x-input type="date" name="tanggal_kembali" label="Estimasi Tanggal Jadi" />
                    </div>
                </div>

                <div class="border-t border-line pt-4 mt-6">
                    <div class="space-y-2 text-xs">
                        <template x-if="repairedBy === 'extern'">
                            <div class="flex justify-between text-steel">
                                <span>Biaya Bengkel Luar:</span>
                                <strong class="font-mono text-ink" x-text="formatRp(totalSupplier)"></strong>
                            </div>
                        </template>
                        <div class="flex justify-between items-baseline pt-2 border-t border-line">
                            <span class="font-display font-bold text-xs text-steel uppercase tracking-wider">TOTAL ESTIMASI BIAYA:</span>
                            <span class="font-mono font-black text-xl text-rajawali" x-text="formatRp(totalNett)"></span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- JASA & SPAREPARTS SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            
            {{-- DATA JASA --}}
            <x-card :padded="false" class="shadow-lg border border-slate-200/80 overflow-hidden flex flex-col">
                <div class="p-3.5 border-b border-line flex justify-between items-center bg-surface">
                    <div>
                        <h3 class="font-display font-bold text-sm text-ink flex items-center gap-2">
                            <x-icon name="sparkles" class="w-4 h-4 text-amber-500" /> Daftar Layanan Jasa
                        </h3>
                    </div>
                    <x-button type="button" variant="secondary" size="xs" x-on:click="tambahJasa()">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> Tambah Jasa
                    </x-button>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-xs">
                        <thead class="bg-canvas text-steel uppercase font-bold border-b border-line">
                            <tr>
                                <th class="text-left px-3 py-2.5">Nama Layanan Jasa</th>
                                <template x-if="repairedBy === 'extern'">
                                    <th class="text-right px-3 py-2.5 w-28">Biaya Luar</th>
                                </template>
                                <th class="text-right px-3 py-2.5 w-32" x-text="repairedBy === 'intern' ? 'Tarif Jasa (Rp)' : 'Tarif Pelanggan'"></th>
                                <th class="px-2 py-2.5 w-10 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <template x-if="jasas.length === 0">
                                <tr>
                                    <td :colspan="repairedBy === 'extern' ? 4 : 3" class="text-center text-steel py-8 italic">
                                        Belum ada jasa ditambahkan. Klik tombol <strong>+ Tambah Jasa</strong> di atas.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(jasa, index) in jasas" :key="index">
                                <tr class="hover:bg-canvas/50 transition">
                                    <td class="px-3 py-2">
                                        <input type="text" :name="`jasas[${index}][nama_jasa]`" x-model="jasa.nama_jasa" placeholder="cth. Servis Karburator / Tune-Up" class="w-full bg-white border border-line px-2.5 py-1.5 rounded-md text-xs font-bold focus:ring-2 focus:ring-rajawali focus:outline-none" required>
                                    </td>
                                    <template x-if="repairedBy === 'extern'">
                                        <td class="px-3 py-2 text-right">
                                            <input type="number" min="0" :name="`jasas[${index}][harga_supplier]`" x-model.number="jasa.harga_supplier" class="w-24 text-right font-mono font-bold bg-white border border-line px-2 py-1.5 rounded-md text-xs focus:ring-2 focus:ring-rajawali focus:outline-none">
                                        </td>
                                    </template>
                                    <td class="px-3 py-2 text-right">
                                        <input type="number" min="0" :name="`jasas[${index}][harga_nett]`" x-model.number="jasa.harga_nett" class="w-28 text-right font-mono font-bold bg-white border border-line px-2 py-1.5 rounded-md text-xs focus:ring-2 focus:ring-rajawali focus:outline-none" required>
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" x-on:click="hapusJasa(index)" class="p-1 rounded text-steel hover:text-rajawali hover:bg-red-50 transition">
                                            <x-icon name="trash-2" class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-card>

            {{-- DATA SPAREPART --}}
            <x-card :padded="false" class="shadow-lg border border-slate-200/80 overflow-hidden flex flex-col">
                <div class="p-3.5 border-b border-line flex justify-between items-center bg-surface">
                    <div>
                        <h3 class="font-display font-bold text-sm text-ink flex items-center gap-2">
                            <x-icon name="package" class="w-4 h-4 text-rajawali" /> Penggunaan Spareparts
                        </h3>
                    </div>
                    <x-button type="button" variant="secondary" size="xs" x-on:click="$dispatch('buka-modal', { name: 'cari-barang-service' })">
                        <x-icon name="search" class="w-3.5 h-3.5" /> Pilih Sparepart
                    </x-button>
                </div>
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-xs">
                        <thead class="bg-canvas text-steel uppercase font-bold border-b border-line">
                            <tr>
                                <th class="text-left px-3 py-2.5">Nama Sparepart</th>
                                <th class="text-right px-3 py-2.5 w-16">Qty</th>
                                <th class="text-right px-3 py-2.5 w-24">Harga (Rp)</th>
                                <th class="text-right px-3 py-2.5 w-28">Total</th>
                                <th class="px-2 py-2.5 w-10 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <template x-if="items.length === 0">
                                <tr>
                                    <td colspan="5" class="text-center text-steel py-8 italic">
                                        Belum ada sparepart digunakan. Klik <strong>Pilih Sparepart</strong>.
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(item, index) in items" :key="item.id">
                                <tr class="hover:bg-canvas/50 transition">
                                    <td class="px-3 py-2">
                                        <input type="hidden" :name="`items[${index}][barang_id]`" :value="item.id">
                                        <span class="font-bold text-ink" x-text="item.nama"></span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <input type="number" min="1" step="1" :name="`items[${index}][qty]`" x-model.number="item.qty" class="w-14 text-right font-mono font-bold bg-white border border-line px-1.5 py-1 rounded-md text-xs focus:ring-2 focus:ring-rajawali focus:outline-none" required>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <input type="number" min="0" :name="`items[${index}][harga]`" x-model.number="item.harga" class="w-24 text-right font-mono font-bold bg-white border border-line px-1.5 py-1 rounded-md text-xs focus:ring-2 focus:ring-rajawali focus:outline-none" required>
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono font-bold text-ink" x-text="formatRp(item.qty * item.harga)"></td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" x-on:click="hapusSparepart(index)" class="p-1 rounded text-steel hover:text-rajawali hover:bg-red-50 transition">
                                            <x-icon name="trash-2" class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <div class="flex justify-end gap-3 mt-6 p-4 bg-surface rounded-xl border border-line shadow-md">
            <x-button as="a" href="{{ route('service.index') }}" variant="secondary">Batal</x-button>
            <x-button type="submit" variant="primary">
                <x-icon name="save" class="w-4 h-4" /> Terbitkan SPK &amp; Tanda Terima Servis
            </x-button>
        </div>
    </form>

    {{-- MODAL CARI BARANG SPAREPART --}}
    <x-modal name="cari-barang-service" title="Pilih Sparepart &amp; Komponen">
        <div class="space-y-3" x-data x-init="$nextTick(() => $refs.modalSearchInput?.focus())">
            <div class="relative">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-steel" />
                <input
                    x-ref="modalSearchInput"
                    x-model="cariQuery"
                    type="text"
                    placeholder="Ketik nama sparepart atau kode barang..."
                    class="w-full rounded-lg border border-line bg-white pl-9 pr-3 py-2 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-rajawali"
                >
            </div>
            <div class="max-h-72 overflow-y-auto border border-line rounded-xl divide-y divide-line">
                <template x-for="b in daftarBarangFiltered" :key="b.id">
                    <div
                        x-on:click="pilihSparepart(b)"
                        class="p-3 hover:bg-canvas cursor-pointer flex justify-between items-center transition group"
                    >
                        <div>
                            <span class="font-mono text-xs font-bold text-rajawali" x-text="b.kode"></span>
                            <span class="font-bold text-sm text-ink block group-hover:text-rajawali" x-text="b.nama"></span>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-sm text-ink block" x-text="formatRp(b.harga)"></span>
                            <span class="text-xs font-mono text-steel">Stok: <strong :class="b.stok <= 0 ? 'text-rajawali' : 'text-lunas'" x-text="b.stok"></strong></span>
                        </div>
                    </div>
                </template>
                <template x-if="daftarBarangFiltered.length === 0">
                    <div class="p-6 text-center text-steel text-xs">Barang tidak ditemukan.</div>
                </template>
            </div>
        </div>
    </x-modal>
</div>

<script>
function serviceForm(daftarBarang, daftarCustomer) {
    return {
        barangList: daftarBarang,
        customerList: daftarCustomer || [],
        repairedBy: 'intern',
        customerId: (daftarCustomer && daftarCustomer.length > 0) ? daftarCustomer[0].id : '',
        telepon: (daftarCustomer && daftarCustomer.length > 0) ? (daftarCustomer[0].telepon || '') : '',
        merkType: (daftarCustomer && daftarCustomer.length > 0) ? (daftarCustomer[0].motor || '') : '',
        jasas: [],
        items: [],
        cariQuery: '',

        pilihCustomer() {
            const cust = this.customerList.find(c => c.id == this.customerId);
            if (cust) {
                this.telepon = cust.telepon || '';
                if (cust.motor && !this.merkType) this.merkType = cust.motor;
            }
        },

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
            if (this.repairedBy !== 'extern') return 0;
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
