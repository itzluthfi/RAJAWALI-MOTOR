<x-app-layout title="Kasir">

<div
    x-data="kasirApp(@js($daftarBarangJson), @js($daftarCustomerJson), @js($batasDiskonPersen), @js($izinkanStokMinus), @js($bolehJualDibawahHpp))"
    x-init="$nextTick(() => $refs.barcode.focus())"
    x-on:keydown.window="tanganiShortcut($event)"
    x-on:buka-modal.window="modalTerbuka++"
    x-on:tutup-modal.window="modalTerbuka = Math.max(0, modalTerbuka - 1)"
    class="flex flex-col gap-3 -m-5 p-5 h-[calc(100vh-3.5rem)]"
>
    {{-- ATAS --}}
    <div class="flex items-center gap-3 flex-wrap">
        <div class="relative flex-1 min-w-64">
            <x-icon name="scan-barcode" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-steel" />
            <input
                x-ref="barcode"
                x-model="barcode"
                x-on:keydown.enter.prevent="tambahDariBarcode()"
                type="text"
                autocomplete="off"
                placeholder="Scan atau ketik kode barang lalu Enter..."
                class="w-full rounded-md border border-line bg-white pl-9 pr-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-rajawali focus:border-rajawali"
            >
        </div>

        <select x-model="customerId" class="rounded-md border border-line bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali">
            <template x-for="c in customerList" :key="c.id">
                <option :value="c.id" x-text="c.nama"></option>
            </template>
        </select>

        <div class="flex items-center rounded-md border border-line overflow-hidden text-sm font-medium">
            <button type="button" x-on:click="jenisBayar = 'tunai'" :class="jenisBayar === 'tunai' ? 'bg-rajawali text-white' : 'bg-white text-steel'" class="px-3.5 py-2.5">Tunai</button>
            <button type="button" x-on:click="jenisBayar = 'tempo'" :class="jenisBayar === 'tempo' ? 'bg-rajawali text-white' : 'bg-white text-steel'" class="px-3.5 py-2.5">Tempo</button>
        </div>

        <div class="text-sm text-steel font-mono ml-auto" x-data="{ tgl: '' }" x-init="tgl = new Intl.DateTimeFormat('id-ID', {dateStyle:'long', timeZone:'Asia/Jakarta'}).format(new Date())">
            <span x-text="tgl"></span>
        </div>
    </div>

    <p x-show="jenisBayar === 'tempo' && customerId == customerList[0].id" x-cloak class="text-xs text-marka bg-marka/10 border border-marka/30 rounded-md px-3 py-2">
        Transaksi tempo wajib memilih customer terdaftar, bukan Umum.
    </p>

    {{-- TENGAH --}}
    <x-card :padded="false" class="flex-1 overflow-hidden flex flex-col">
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                    <tr>
                        <th class="text-left font-semibold px-3 py-2 w-10">No</th>
                        <th class="text-left font-semibold px-3 py-2">Kode</th>
                        <th class="text-left font-semibold px-3 py-2">Nama Barang</th>
                        <th class="text-right font-semibold px-3 py-2 w-24">Qty</th>
                        <th class="text-right font-semibold px-3 py-2 w-32">Harga</th>
                        <th class="text-right font-semibold px-3 py-2 w-24">Disc</th>
                        <th class="text-right font-semibold px-3 py-2 w-36">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="keranjang.length === 0">
                        <tr><td colspan="7" class="text-center text-steel py-12">Belum ada barang. Scan barcode untuk mulai.</td></tr>
                    </template>
                    <template x-for="(item, idx) in keranjang" :key="item.uid">
                        <tr
                            x-on:click="barisAktif = idx"
                            :class="barisAktif === idx ? 'bg-rajawali/5' : 'hover:bg-canvas'"
                            class="border-b border-line cursor-pointer transition duration-150"
                            x-transition:enter="ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-x-1"
                            x-transition:enter-end="opacity-100 translate-x-0"
                        >
                            <td class="px-3 py-2 text-steel" x-text="idx + 1"></td>
                            <td class="px-3 py-2 font-mono text-xs text-steel" x-text="item.kode"></td>
                            <td class="px-3 py-2 font-medium" x-text="item.nama"></td>
                            <td class="px-3 py-2 text-right font-mono">
                                <input
                                    type="number" min="1" step="1"
                                    x-model.number="item.qty"
                                    x-on:click.stop
                                    x-on:focus="barisAktif = idx"
                                    class="w-16 text-right font-mono bg-transparent focus:outline-none focus:bg-white rounded px-1"
                                >
                            </td>
                            <td class="px-3 py-2 text-right font-mono" x-text="formatRp(item.harga)"></td>
                            <td class="px-3 py-2 text-right font-mono">
                                <input
                                    type="number" min="0" step="1"
                                    x-model.number="item.diskon"
                                    x-on:click.stop
                                    x-on:change="validasiDiskonBaris(idx)"
                                    class="w-16 text-right font-mono bg-transparent focus:outline-none focus:bg-white rounded px-1"
                                >
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-medium" x-text="formatRp((item.harga * item.qty) - item.diskon)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- BAWAH --}}
    <x-card class="shrink-0">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-wrap gap-2 content-start">
                <x-button variant="secondary" x-on:click="$dispatch('buka-modal', { name: 'cari-barang' })"><kbd class="text-[10px] bg-canvas px-1 rounded">F2</kbd> Cari Barang</x-button>
                <x-button variant="secondary" x-on:click="$dispatch('buka-modal', { name: 'diskon-nota' })"><kbd class="text-[10px] bg-canvas px-1 rounded">F6</kbd> Diskon Nota</x-button>
                <x-button variant="secondary" x-on:click="kosongkanKeranjang()"><kbd class="text-[10px] bg-canvas px-1 rounded">F8</kbd> Kosongkan</x-button>
                <p class="w-full text-xs text-steel mt-1">Total Qty: <span class="font-mono font-medium text-ink" x-text="totalQty"></span> barang</p>
            </div>

            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between"><span class="text-steel">Subtotal</span><span class="font-mono" x-text="formatRp(subtotal)"></span></div>
                <div class="flex justify-between items-center">
                    <span class="text-steel">
                        Diskon Nota
                        <template x-if="batasDiskonPersen > 0">
                            <span class="text-[11px] text-steel/70">(maks <span x-text="batasDiskonPersen"></span>%)</span>
                        </template>
                    </span>
                    <input type="number" min="0" x-model.number="diskonNota" x-on:change="validasiDiskonNota()" class="w-28 text-right font-mono border border-line rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali">
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-line">
                    <span class="font-display font-semibold text-ink">GRAND TOTAL</span>
                    <span class="font-mono font-bold text-2xl text-rajawali" x-text="formatRp(grandTotal)"></span>
                </div>

                <template x-if="jenisBayar === 'tunai'">
                    <div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-steel">Bayar <span class="text-xs">(F9)</span></span>
                            <input x-ref="bayar" type="number" min="0" x-model.number="bayar" class="w-32 text-right font-mono border border-line rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali">
                        </div>
                        <div class="flex justify-between items-baseline">
                            <span class="font-display font-semibold text-ink">Kembali</span>
                            <span class="font-mono font-bold text-xl" :class="kembalian < 0 ? 'text-rajawali' : 'text-lunas'" x-text="formatRp(kembalian)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-end mt-4 pt-3 border-t border-line">
            <x-button variant="primary" class="px-8 py-3 text-base" x-on:click="simpanNota()" x-bind:disabled="sedangMenyimpan">
                <x-icon name="save" class="w-4 h-4" /> Simpan <kbd class="text-[10px] bg-white/20 px-1.5 rounded ml-1">F12</kbd>
            </x-button>
        </div>
    </x-card>
</div>

<!-- Modal Cari Barang (F2) -->
<x-modal name="cari-barang" title="Cari Barang & Sparepart (F2)">
    <div class="space-y-3" x-data x-init="$nextTick(() => $refs.modalCariInput?.focus())">
        <div class="relative">
            <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-steel" />
            <input
                x-ref="modalCariInput"
                x-model="cariQuery"
                type="text"
                placeholder="Ketik nama barang, kode, atau barcode..."
                class="w-full rounded-md border border-line bg-white pl-9 pr-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rajawali"
            >
        </div>

        <div class="max-h-72 overflow-y-auto border border-line rounded-lg divide-y divide-line">
            <template x-for="b in daftarBarangFiltered" :key="b.id">
                <div
                    x-on:click="pilihBarangDariModal(b)"
                    class="p-3 hover:bg-canvas cursor-pointer flex justify-between items-center transition duration-150 group"
                >
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-rajawali" x-text="b.kode"></span>
                            <span class="font-bold text-sm text-ink group-hover:text-rajawali" x-text="b.nama"></span>
                        </div>
                        <p class="text-xs text-steel mt-0.5">Barcode: <span class="font-mono" x-text="b.barcode"></span></p>
                    </div>
                    <div class="text-right">
                        <span class="font-mono font-bold text-sm text-ink block" x-text="formatRp(b.harga)"></span>
                        <span class="text-xs font-mono text-steel">Stok: <strong :class="b.stok <= 0 ? 'text-rajawali' : 'text-lunas'" x-text="b.stok"></strong></span>
                    </div>
                </div>
            </template>
            <template x-if="daftarBarangFiltered.length === 0">
                <div class="p-6 text-center text-steel text-sm">Barang tidak ditemukan.</div>
            </template>
        </div>
    </div>
</x-modal>

<x-modal name="diskon-nota" title="Diskon Nota">
    <div x-data x-init="$nextTick(() => $el.querySelector('input')?.focus())">
        <x-input label="Jumlah diskon (Rp)" type="number" x-model.number="diskonNota" />
        <div class="flex justify-end gap-2 mt-4">
            <x-button variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'diskon-nota' })">Batal</x-button>
            <x-button variant="primary" x-on:click="validasiDiskonNota(); $dispatch('tutup-modal', { name: 'diskon-nota' })">Terapkan</x-button>
        </div>
    </div>
</x-modal>

</x-app-layout>

<script>
function kasirApp(dataBarang, dataCustomer, batasDiskonPersen, izinkanStokMinus, bolehJualDibawahHpp) {
    return {
        daftarBarang: dataBarang,
        customerList: dataCustomer,
        customerId: dataCustomer[0].id,
        batasDiskonPersen: batasDiskonPersen,
        izinkanStokMinus: izinkanStokMinus,
        bolehJualDibawahHpp: bolehJualDibawahHpp,
        jenisBayar: 'tunai',
        barcode: '',
        cariQuery: '',
        keranjang: [],
        barisAktif: null,
        diskonNota: 0,
        bayar: 0,
        uidCounter: 1,
        modalTerbuka: 0,
        sedangMenyimpan: false,

        get daftarBarangFiltered() {
            const q = (this.cariQuery || '').trim().toLowerCase();
            if (!q) return this.daftarBarang;
            return this.daftarBarang.filter(b => 
                b.nama.toLowerCase().includes(q) || 
                b.kode.toLowerCase().includes(q) || 
                (b.barcode && b.barcode.toLowerCase().includes(q))
            );
        },

        tambahDariBarcode() {
            const query = this.barcode.trim().toUpperCase();
            if (!query) return;

            // 1. Match persis kode atau barcode
            let barang = this.daftarBarang.find(b => b.kode.toUpperCase() === query || (b.barcode && b.barcode.toUpperCase() === query));

            // 2. Jika tidak ada kode/barcode persis, cari pencocokan berdasarkan NAMA BARANG atau partial kode
            if (!barang) {
                const matches = this.daftarBarang.filter(b => 
                    b.nama.toUpperCase().includes(query) || 
                    b.kode.toUpperCase().includes(query) ||
                    (b.barcode && b.barcode.toUpperCase().includes(query))
                );

                if (matches.length === 1) {
                    barang = matches[0];
                } else if (matches.length > 1) {
                    this.cariQuery = this.barcode;
                    this.$dispatch('buka-modal', { name: 'cari-barang' });
                    this.barcode = '';
                    return;
                }
            }

            if (!barang) {
                window.toastGagal(`Barang dengan nama/kode "${this.barcode}" tidak ditemukan.`);
                this.barcode = '';
                return;
            }

            this.tambahBarangKeKeranjang(barang);
            this.barcode = '';
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        tambahBarangKeKeranjang(barang) {
            const ada = this.keranjang.find(i => i.kode === barang.kode);
            const qtyDiminta = ada ? ada.qty + 1 : 1;

            if (qtyDiminta > barang.stok && !this.izinkanStokMinus) {
                window.toastGagal(`Stok ${barang.nama} tersedia ${barang.stok}, diminta ${qtyDiminta}.`);
                return;
            }

            if (qtyDiminta > barang.stok && this.izinkanStokMinus) {
                window.toastGagal(`Stok ${barang.nama} akan minus (tersedia ${barang.stok}, diminta ${qtyDiminta}). Tetap ditambahkan.`);
            }

            if (ada) {
                ada.qty++;
            } else {
                this.keranjang.push({
                    uid: this.uidCounter++,
                    kode: barang.kode,
                    nama: barang.nama,
                    harga: barang.harga,
                    hpp: barang.hpp,
                    stok: barang.stok,
                    qty: 1,
                    diskon: 0,
                });
            }
        },

        pilihBarangDariModal(barang) {
            this.tambahBarangKeKeranjang(barang);
            this.cariQuery = '';
            this.$dispatch('tutup-modal', { name: 'cari-barang' });
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        validasiDiskonBaris(idx) {
            const item = this.keranjang[idx];
            if (!item) return;

            const hargaEfektif = (item.harga * item.qty - item.diskon) / item.qty;
            if (hargaEfektif < item.hpp) {
                if (this.bolehJualDibawahHpp) {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'Harga di bawah HPP',
                        text: `${item.nama} dijual Rp ${Math.round(hargaEfektif).toLocaleString('id-ID')}, di bawah HPP Rp ${item.hpp.toLocaleString('id-ID')}.`,
                        confirmButtonText: 'Tetap Lanjutkan',
                        confirmButtonColor: '#B0181C',
                    });
                } else {
                    window.toastGagal(`Diskon membuat harga ${item.nama} di bawah HPP. Hanya owner/admin yang bisa menjual di bawah HPP.`);
                    item.diskon = 0;
                }
            }
        },

        validasiDiskonNota() {
            if (this.batasDiskonPersen <= 0) return;

            const batasRp = this.subtotal * (this.batasDiskonPersen / 100);
            if (this.diskonNota > batasRp) {
                window.toastGagal(`Diskon nota melebihi batas ${this.batasDiskonPersen}% (maks Rp ${Math.round(batasRp).toLocaleString('id-ID')}).`);
                this.diskonNota = Math.floor(batasRp);
            }
        },

        kosongkanKeranjang() {
            if (this.keranjang.length === 0) return;
            window.Swal.fire({
                icon: 'warning',
                title: 'Kosongkan keranjang?',
                text: `${this.keranjang.length} barang pada nota ini akan dihapus.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, kosongkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B0181C',
                reverseButtons: true,
            }).then(hasil => {
                if (hasil.isConfirmed) {
                    this.keranjang = [];
                    this.diskonNota = 0;
                    this.bayar = 0;
                    this.$refs.barcode.focus();
                }
            });
        },

        async simpanNota() {
            if (this.sedangMenyimpan) return;

            if (this.keranjang.length === 0) {
                window.toastGagal('Belum ada barang pada nota ini.');
                return;
            }
            if (this.jenisBayar === 'tempo' && this.customerId == this.customerList[0].id) {
                window.toastGagal('Transaksi tempo wajib memilih customer terdaftar.');
                return;
            }
            if (this.jenisBayar === 'tunai' && this.bayar < this.grandTotal) {
                window.toastGagal(`Pembayaran kurang Rp ${(this.grandTotal - this.bayar).toLocaleString('id-ID')}.`);
                return;
            }

            this.sedangMenyimpan = true;

            try {
                const respon = await fetch('{{ route('kasir.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        customer_id: this.customerId,
                        items: this.keranjang.map(i => ({ kode: i.kode, qty: i.qty, harga: i.harga })),
                        diskon: this.diskonNota,
                        bayar: this.bayar,
                        metode_pembayaran: this.jenisBayar,
                    })
                });

                const hasil = await respon.json();

                if (hasil.sukses) {
                    window.toastSukses(hasil.pesan);

                    window.Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        html: `<p class="text-sm text-slate-600 mb-2">Nomor Nota: <strong>${hasil.nomor_nota}</strong></p><p class="text-xs text-slate-500">Apakah Anda ingin mencetak struk thermal kasir?</p>`,
                        showCancelButton: true,
                        confirmButtonText: '🖨️ Cetak Struk (58/80mm)',
                        cancelButtonText: 'Transaksi Baru',
                        confirmButtonColor: '#B0181C',
                        cancelButtonColor: '#64748b',
                        reverseButtons: true,
                    }).then((pilihan) => {
                        if (pilihan.isConfirmed && hasil.cetak_url) {
                            window.open(hasil.cetak_url, '_blank');
                        }
                    });

                    this.keranjang = [];
                    this.diskonNota = 0;
                    this.bayar = 0;
                    this.$nextTick(() => this.$refs.barcode?.focus());
                } else {
                    window.toastGagal(hasil.pesan || 'Gagal menyimpan transaksi.');
                }
            } catch (err) {
                window.toastGagal('Terjadi kesalahan jaringan atau server saat menyimpan nota.');
            } finally {
                this.sedangMenyimpan = false;
            }
        },

        tanganiShortcut(e) {
            // Jangan proses shortcut kasir kalau ada modal terbuka (biar tidak
            // "tembus" ke keranjang di belakangnya), kecuali Escape.
            if (this.modalTerbuka > 0 && e.key !== 'Escape') return;

            if (e.key === 'F2') { e.preventDefault(); this.$dispatch('buka-modal', { name: 'cari-barang' }); }
            if (e.key === 'F6') { e.preventDefault(); this.$dispatch('buka-modal', { name: 'diskon-nota' }); }
            if (e.key === 'F8') { e.preventDefault(); this.kosongkanKeranjang(); }
            if (e.key === 'F9') { e.preventDefault(); this.jenisBayar === 'tunai' && this.$refs.bayar?.focus(); }
            // F12 sering direbut browser (buka DevTools) sehingga tidak selalu
            // bisa dicegat lewat JS -- Ctrl+Enter disediakan sebagai cara pasti.
            if (e.key === 'F12' || (e.ctrlKey && e.key === 'Enter')) { e.preventDefault(); this.simpanNota(); }
            if (e.key === 'Delete' && this.barisAktif !== null) {
                this.keranjang.splice(this.barisAktif, 1);
                this.barisAktif = null;
            }
            if (e.key === 'ArrowDown' && document.activeElement === this.$refs.barcode) {
                this.barisAktif = this.barisAktif === null ? 0 : Math.min(this.barisAktif + 1, this.keranjang.length - 1);
            }
            if (e.key === 'ArrowUp' && document.activeElement === this.$refs.barcode) {
                this.barisAktif = this.barisAktif === null ? 0 : Math.max(this.barisAktif - 1, 0);
            }
        },

        formatRp(angka) {
            return 'Rp ' + Math.round(angka || 0).toLocaleString('id-ID');
        },

        get totalQty() {
            return this.keranjang.reduce((t, i) => t + Number(i.qty || 0), 0);
        },
        get subtotal() {
            return this.keranjang.reduce((t, i) => t + (i.harga * i.qty - i.diskon), 0);
        },
        get grandTotal() {
            return Math.max(this.subtotal - (this.diskonNota || 0), 0);
        },
        get kembalian() {
            return (this.bayar || 0) - this.grandTotal;
        },
    };
}
</script>
