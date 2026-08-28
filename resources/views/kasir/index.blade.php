<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<x-app-layout title="Kasir POS Terpadu">

<div
    x-data="kasirApp(
        @js($daftarBarangJson),
        @js($daftarCustomerJson),
        @js($montirsJson),
        @js($antreanServiceJson),
        @js($batasDiskonPersen),
        @js($izinkanStokMinus),
        @js($bolehJualDibawahHpp),
        @js($printerStrukAktif),
        @js($printerFakturAktif)
    )"
    x-init="initApp(); $nextTick(() => $refs.barcode.focus())"
    x-on:keydown.window="tanganiShortcut($event)"
    x-on:buka-modal.window="modalTerbuka++"
    x-on:tutup-modal.window="modalTerbuka = Math.max(0, modalTerbuka - 1)"
    class="flex flex-col gap-3 -m-5 p-5 min-h-[calc(100vh-3.5rem)]"
>
    {{-- BARIS 1: MODE TRANSAKSI & STATUS CEPAT --}}
    <div class="flex items-center justify-between gap-3 bg-surface p-2.5 rounded-xl border border-line flex-wrap">
        {{-- TOGGLE MODE TRANSAKSI --}}
        <div class="inline-flex rounded-lg border border-line bg-canvas p-1 text-xs font-bold shadow-xs">
            <button
                type="button"
                x-on:click="gantiMode('penjualan')"
                :class="modeTransaksi === 'penjualan' ? 'bg-rajawali text-white shadow-sm' : 'text-steel hover:text-ink'"
                class="px-4 py-2 rounded-md transition flex items-center gap-1.5 cursor-pointer"
            >
                <x-icon name="shopping-cart" class="w-4 h-4" />
                <span>Penjualan Suku Cadang</span>
            </button>
            <button
                type="button"
                x-on:click="gantiMode('service')"
                :class="modeTransaksi === 'service' ? 'bg-blue-600 text-white shadow-sm' : 'text-steel hover:text-ink'"
                class="px-4 py-2 rounded-md transition flex items-center gap-1.5 cursor-pointer"
            >
                <x-icon name="wrench" class="w-4 h-4" />
                <span>Servis Bengkel Motor</span>
            </button>
        </div>

        {{-- TOMBOL PANGGIL ANTREAN SERVIS --}}
        <div class="flex items-center gap-2 ml-auto">
            <button
                type="button"
                x-on:click="bukaModalAntreanService()"
                class="px-3.5 py-2 rounded-lg bg-white border border-slate-300 hover:border-blue-600 text-slate-800 text-xs font-bold flex items-center gap-2 shadow-xs transition hover:bg-blue-50"
            >
                <x-icon name="clipboard-list" class="w-4 h-4 text-blue-600" />
                <span>Antrean Servis</span>
                <span
                    class="px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-blue-100 text-blue-800 border border-blue-200"
                    x-text="antreanServiceList.length + ' Motor'"
                ></span>
            </button>

            <div class="text-xs text-steel font-mono font-bold px-2.5 py-1.5 bg-canvas rounded-lg border border-line" x-data="{ tgl: '' }" x-init="tgl = new Intl.DateTimeFormat('id-ID', {dateStyle:'medium', timeStyle:'short', timeZone:'Asia/Jakarta'}).format(new Date())">
                <span x-text="tgl"></span> WIB
            </div>
        </div>
    </div>

    {{-- BARIS 2: DATA KENDARAAN (KHUSUS MODE SERVIS MOTOR) --}}
    <div
        x-show="modeTransaksi === 'service'"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-cloak
        class="p-3.5 bg-blue-50/60 border border-blue-200 rounded-xl space-y-3"
    >
        <div class="flex justify-between items-center pb-2 border-b border-blue-200/60">
            <h4 class="font-bold text-xs text-blue-900 flex items-center gap-1.5 uppercase tracking-wide">
                <x-icon name="bike" class="w-4 h-4 text-blue-700" />
                <span>Informasi Kendaraan &amp; Montir Teknisi</span>
            </h4>
            <template x-if="serviceIdAktif">
                <span class="text-xs font-mono font-bold bg-blue-600 text-white px-2.5 py-0.5 rounded-full flex items-center gap-1">
                    <span>Pelunasan SPK:</span>
                    <span x-text="nomorSpkAktif"></span>
                </span>
            </template>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-blue-900 uppercase tracking-wider mb-1">Nomor Plat Motor *</label>
                <input
                    type="text"
                    x-model="platNomor"
                    x-on:input="sinkronPlatKeCustomer()"
                    placeholder="Contoh: L 1234 ABC"
                    class="w-full text-xs font-mono font-bold bg-white border border-blue-300 rounded-lg px-2.5 py-2 uppercase focus:ring-2 focus:ring-blue-600 focus:outline-none"
                >
            </div>
            <div>
                <label class="block text-[11px] font-bold text-blue-900 uppercase tracking-wider mb-1">Merk / Tipe Motor</label>
                <input
                    type="text"
                    x-model="merkType"
                    placeholder="Contoh: Honda Vario 125"
                    class="w-full text-xs font-bold bg-white border border-blue-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-blue-600 focus:outline-none"
                >
            </div>
            <div>
                <label class="block text-[11px] font-bold text-blue-900 uppercase tracking-wider mb-1">Montir / Teknisi *</label>
                <select
                    x-model="montirId"
                    class="w-full text-xs font-bold bg-white border border-blue-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-blue-600 focus:outline-none"
                >
                    <option value="">-- Pilih Montir --</option>
                    <template x-for="m in montirs" :key="m.id">
                        <option :value="m.id" x-text="m.nama + ' (' + m.peran + ')'"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-blue-900 uppercase tracking-wider mb-1">Keluhan / Diagnosa</label>
                <input
                    type="text"
                    x-model="keluhan"
                    placeholder="Contoh: Ganti oli mesin &amp; kampas rem"
                    class="w-full text-xs font-medium bg-white border border-blue-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-blue-600 focus:outline-none"
                >
            </div>
        </div>
    </div>

    {{-- BARIS 3: SEARCH BARCODE, SELECT CUSTOMER, & JENIS BAYAR --}}
    <div class="flex items-center gap-3 flex-wrap">
        {{-- INPUT BARCODE & LIVE SEARCH --}}
        <div class="relative flex-1 min-w-64 flex items-center gap-2">
            <div class="relative flex-1" x-on:click.outside="fokusMainInput = false">
                <x-icon name="scan-barcode" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-steel" />
                <input
                    x-ref="barcode"
                    x-model="barcode"
                    x-on:focus="fokusMainInput = true"
                    x-on:keydown.down.prevent="indeksLive = Math.min(indeksLive + 1, hasilLive.length - 1)"
                    x-on:keydown.up.prevent="indeksLive = Math.max(indeksLive - 1, 0)"
                    x-on:keydown.enter.prevent="pilihLiveAtauBarcode()"
                    type="text"
                    autocomplete="off"
                    placeholder="Scan atau ketik nama barang / kode / barcode / jasa..."
                    class="w-full rounded-lg border border-line bg-white pl-9 pr-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-rajawali focus:border-rajawali shadow-xs"
                >

                <!-- Live Search Floating Dropdown Popup -->
                <div
                    x-show="barcode.trim().length >= 1 && hasilLive.length > 0"
                    x-cloak
                    class="absolute left-0 right-0 top-full mt-1 bg-white border-2 border-rajawali rounded-xl shadow-2xl z-50 max-h-80 overflow-y-auto divide-y divide-line"
                >
                    <template x-for="(b, idx) in hasilLive" :key="b.id">
                        <div
                            x-on:click="pilihBarangLive(b)"
                            x-on:mouseenter="indeksLive = idx"
                            :class="indeksLive === idx ? 'bg-rajawali/10 font-bold border-l-4 border-rajawali' : 'hover:bg-canvas'"
                            class="p-2.5 cursor-pointer flex justify-between items-center transition text-xs"
                        >
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-rajawali" x-text="b.kode"></span>
                                    <span class="font-bold text-sm text-ink" x-text="b.nama"></span>
                                    <template x-if="b.is_jasa">
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-blue-100 text-blue-800 border border-blue-300 font-bold">JASA</span>
                                    </template>
                                </div>
                                <p class="text-[11px] text-steel mt-0.5">Barcode: <span class="font-mono" x-text="b.barcode || '-'"></span></p>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-sm text-ink block" x-text="formatRp(b.harga)"></span>
                                <template x-if="!b.is_jasa">
                                    <span class="text-[11px] font-mono text-steel">Stok: <strong :class="b.stok <= 0 ? 'text-rajawali' : 'text-lunas'" x-text="b.stok"></strong></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <button type="button" x-on:click="bukaScannerKamera()" class="px-3 py-2.5 bg-rajawali/10 text-rajawali rounded-lg hover:bg-rajawali/20 font-bold text-xs flex items-center gap-1.5 shrink-0 transition" data-tooltip="Scan Barcode via Kamera">
                <x-icon name="camera" class="w-4 h-4" />
                <span class="hidden sm:inline">Kamera</span>
            </button>
        </div>

        {{-- CUSTOMER SELECT SEARCHABLE (DENGAN TOMBOL TAMBAH CEPAT) --}}
        <div class="relative min-w-56 max-w-xs" x-data="{ terbuka: false, cari: '' }" x-on:click.outside="terbuka = false">
            <button
                type="button"
                x-on:click="terbuka = !terbuka; if(terbuka) $nextTick(() => $refs.inputCariCust?.focus())"
                class="w-full flex items-center justify-between rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-bold shadow-xs focus:outline-none focus:ring-2 focus:ring-rajawali hover:bg-slate-50 transition"
            >
                <div class="flex items-center gap-2 truncate">
                    <x-icon name="user" class="w-4 h-4 text-steel shrink-0" />
                    <span class="truncate" x-text="customerTerpilih ? customerTerpilih.nama : 'Pilih Customer'"></span>
                    <template x-if="customerTerpilih && customerTerpilih.kategori && customerTerpilih.kategori !== 'umum'">
                        <span
                            :class="customerTerpilih.kategori === 'grosir' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'"
                            class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase shrink-0"
                            x-text="customerTerpilih.kategori"
                        ></span>
                    </template>
                </div>
                <x-icon name="chevron-down" class="w-4 h-4 text-steel shrink-0 ml-1" />
            </button>

            {{-- Dropdown Customer Popup --}}
            <div
                x-show="terbuka"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak
                class="absolute left-0 top-full mt-1.5 w-80 rounded-xl border border-slate-200 bg-white shadow-2xl z-50 overflow-hidden"
            >
                <div class="p-2 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                    <x-icon name="search" class="w-4 h-4 text-steel shrink-0 ml-1" />
                    <input
                        x-ref="inputCariCust"
                        type="text"
                        x-model="cari"
                        placeholder="Cari Nama / Plat / Motor / WA..."
                        class="w-full bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-rajawali font-medium"
                    >
                    <button x-show="cari" type="button" x-on:click="cari = ''" class="text-steel hover:text-ink text-xs font-bold px-1">
                        <x-icon name="x" class="w-3.5 h-3.5" />
                    </button>
                </div>

                <div class="p-2 bg-slate-100/70 border-b border-slate-200/80 flex justify-between items-center">
                    <span class="text-[11px] text-steel font-bold">Pelanggan Toko</span>
                    <button
                        type="button"
                        x-on:click="terbuka = false; bukaModalCustomerCepat()"
                        class="text-[11px] font-bold text-rajawali hover:underline flex items-center gap-1"
                    >
                        <x-icon name="plus" class="w-3 h-3" />
                        <span>Customer Baru</span>
                    </button>
                </div>

                {{-- Customer List --}}
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-100">
                    <template x-for="c in filterCustomer(cari)" :key="c.id">
                        <div
                            x-on:click="pilihCustomer(c); terbuka = false; cari = ''"
                            :class="customerId == c.id ? 'bg-rajawali/10 font-bold text-rajawali' : 'hover:bg-slate-100 text-slate-800'"
                            class="p-2.5 cursor-pointer text-xs transition flex justify-between items-center"
                        >
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-1.5 font-bold">
                                    <span x-text="c.nama"></span>
                                    <template x-if="c.kategori && c.kategori !== 'umum'">
                                        <span
                                            :class="c.kategori === 'grosir' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-blue-100 text-blue-800 border-blue-300'"
                                            class="px-1.5 py-0.2 rounded text-[9px] font-mono border uppercase font-bold"
                                            x-text="c.kategori"
                                        ></span>
                                    </template>
                                </div>
                                <template x-if="c.plat || c.motor">
                                    <div class="text-[11px] text-steel font-mono">
                                        <span x-text="c.plat || ''" class="text-rajawali font-bold"></span>
                                        <span x-text="c.motor ? ' (' + c.motor + ')' : ''"></span>
                                    </div>
                                </template>
                            </div>
                            <x-icon x-show="customerId == c.id" name="check" class="w-4 h-4 text-rajawali shrink-0" />
                        </div>
                    </template>
                    <template x-if="filterCustomer(cari).length === 0">
                        <div class="p-4 text-center text-xs text-steel">Customer tidak ditemukan.</div>
                    </template>
                </div>
            </div>
        </div>

        {{-- TOGGLE JENIS BAYAR (TUNAI / TEMPO) --}}
        <div class="flex items-center rounded-lg border border-line overflow-hidden text-sm font-medium shadow-xs">
            <button type="button" x-on:click="jenisBayar = 'tunai'" :class="jenisBayar === 'tunai' ? 'bg-rajawali text-white font-bold' : 'bg-white text-steel hover:bg-slate-50'" class="px-4 py-2 transition">Tunai</button>
            <button type="button" x-on:click="jenisBayar = 'tempo'" :class="jenisBayar === 'tempo' ? 'bg-rajawali text-white font-bold' : 'bg-white text-steel hover:bg-slate-50'" class="px-4 py-2 transition">Tempo</button>
        </div>
    </div>

    {{-- BARIS 4: TABEL KERANJANG TRANSAKSI POS --}}
    <x-card :padded="false" class="flex-1 overflow-hidden flex flex-col shadow-lg border border-slate-200/80">
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line font-bold">
                    <tr>
                        <th class="text-left px-3 py-2.5 w-10">No</th>
                        <th class="text-left px-3 py-2.5">Kode</th>
                        <th class="text-left px-3 py-2.5">Nama Barang / Jasa</th>
                        <th class="text-right px-3 py-2.5 w-24">Qty</th>
                        <th class="text-right px-3 py-2.5 w-32">Harga Satuan</th>
                        <th class="text-right px-3 py-2.5 w-24">Disc (%)</th>
                        <th class="text-right px-3 py-2.5 w-28">Disc (Rp)</th>
                        <th class="text-right px-3 py-2.5 w-36">Total (Rp)</th>
                        <th class="text-center px-2 py-2.5 w-12">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <template x-for="(item, idx) in keranjang" :key="item.uid">
                        <tr
                            x-on:click="barisAktif = idx"
                            :class="barisAktif === idx ? 'bg-rajawali/5 font-medium' : 'hover:bg-canvas/60'"
                            class="transition duration-100"
                        >
                            <td class="px-3 py-2 text-steel text-xs font-mono text-center" x-text="idx + 1"></td>
                            <td class="px-3 py-2 font-mono text-xs font-bold text-rajawali" x-text="item.kode"></td>
                            <td class="px-3 py-2">
                                <div class="font-bold text-ink" x-text="item.nama"></div>
                                <template x-if="item.labelTier">
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-mono bg-emerald-100 text-emerald-800 font-bold border border-emerald-300">
                                        <x-icon name="sparkles" class="w-3 h-3 text-emerald-600" />
                                        <span x-text="item.labelTier"></span>
                                    </span>
                                </template>
                            </td>
                            <td class="px-3 py-2 text-right">
                                <input
                                    type="number"
                                    x-model.number="item.qty"
                                    x-on:input="ubahQty(idx, item.qty)"
                                    min="0.001"
                                    step="any"
                                    class="w-16 text-right font-mono font-bold text-xs bg-white border border-line rounded px-1.5 py-1 focus:ring-2 focus:ring-rajawali focus:outline-none"
                                >
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-bold text-ink">
                                <input
                                    type="number"
                                    x-model.number="item.harga"
                                    x-on:input="ubahHargaManual(idx, item.harga)"
                                    class="w-24 text-right font-mono font-bold text-xs bg-white border border-line rounded px-1.5 py-1 focus:ring-2 focus:ring-rajawali focus:outline-none"
                                >
                            </td>
                            <td class="px-3 py-2 text-right">
                                <input
                                    type="number"
                                    x-model.number="item.diskonPersen"
                                    x-on:input="ubahDiskonPersen(idx, item.diskonPersen)"
                                    min="0"
                                    max="100"
                                    class="w-14 text-right font-mono text-xs bg-white border border-line rounded px-1.5 py-1 focus:ring-2 focus:ring-rajawali focus:outline-none"
                                >
                            </td>
                            <td class="px-3 py-2 text-right font-mono text-xs">
                                <input
                                    type="number"
                                    x-model.number="item.diskon"
                                    x-on:input="ubahDiskonNominal(idx, item.diskon)"
                                    min="0"
                                    class="w-20 text-right font-mono text-xs bg-white border border-line rounded px-1.5 py-1 focus:ring-2 focus:ring-rajawali focus:outline-none"
                                >
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-bold text-ink text-sm" x-text="formatRp(hitungTotalItem(item))"></td>
                            <td class="px-2 py-2 text-center">
                                <button
                                    type="button"
                                    x-on:click="hapusBaris(idx)"
                                    class="p-1 rounded text-steel hover:text-rajawali hover:bg-red-50 transition"
                                    data-tooltip="Hapus Baris"
                                >
                                    <x-icon name="trash-2" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="keranjang.length === 0">
                        <tr>
                            <td colspan="9" class="py-16 text-center text-steel">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <x-icon name="shopping-bag" class="w-12 h-12 mx-auto text-steel/40" />
                                    <p class="font-bold text-sm text-ink">Keranjang Transaksi Masih Kosong</p>
                                    <p class="text-xs text-steel">Ketik nama barang / scan barcode di atas, atau tekan <kbd class="px-1.5 py-0.5 bg-canvas border border-line rounded font-mono text-[10px]">F2</kbd> (Cari Barang).</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- FOOTER KASIR: TOTAL & TOMBOL AKSI --}}
        <div class="border-t border-line bg-surface p-4 grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
            <div class="flex items-center gap-2 flex-wrap">
                <x-button type="button" variant="secondary" size="sm" x-on:click="bukaModalCari()">
                    <kbd class="text-[10px] bg-canvas border px-1 rounded">F2</kbd> Cari Barang
                </x-button>
                <x-button type="button" variant="secondary" size="sm" x-on:click="$dispatch('buka-modal', { name: 'diskon-nota' })">
                    <kbd class="text-[10px] bg-canvas border px-1 rounded">F6</kbd> Diskon Nota
                </x-button>
                <x-button type="button" variant="secondary" size="sm" x-on:click="kosongkanKeranjang()">
                    <x-icon name="trash" class="w-3.5 h-3.5 text-steel" /> Kosongkan
                </x-button>
            </div>

            <div class="p-3 bg-canvas rounded-xl border border-line text-xs space-y-1">
                <div class="flex justify-between font-medium"><span class="text-steel">Subtotal Belanja:</span> <strong class="font-mono text-ink" x-text="formatRp(subtotal)"></strong></div>
                <template x-if="diskonNotaNominal > 0">
                    <div class="flex justify-between font-medium text-rajawali"><span>Diskon Nota:</span> <strong class="font-mono" x-text="'- ' + formatRp(diskonNotaNominal)"></strong></div>
                </template>
                <div class="flex justify-between items-center pt-1 border-t border-line">
                    <span class="text-xs font-bold text-steel uppercase tracking-wider">GRAND TOTAL:</span>
                    <strong class="font-mono font-black text-xl text-rajawali" x-text="formatRp(grandTotal)"></strong>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 flex-wrap">
                {{-- TOMBOL AKSI MODE SERVIS --}}
                <template x-if="modeTransaksi === 'service'">
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            x-on:click="simpanSpkService()"
                            x-bind:disabled="sedangMenyimpan"
                            class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md transition active:scale-98 cursor-pointer"
                            data-tooltip="Simpan order antrean servis & terbitkan tanda terima tanpa pembayaran langsung"
                        >
                            <x-icon name="clipboard-check" class="w-4 h-4" />
                            <span>Terima Motor Servis (Tanda Terima)</span>
                            <kbd class="text-[10px] bg-white/20 px-1.5 rounded">F10</kbd>
                        </button>

                        <button
                            type="button"
                            x-on:click="simpanNota()"
                            x-bind:disabled="sedangMenyimpan"
                            class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md transition active:scale-98 cursor-pointer"
                            data-tooltip="Servis selesai & langsung bayar lunas sekarang"
                        >
                            <x-icon name="check-circle" class="w-4 h-4" />
                            <span>Bayar Lunas (Cetak Nota)</span>
                            <kbd class="text-[10px] bg-white/20 px-1.5 rounded">F12</kbd>
                        </button>
                    </div>
                </template>

                {{-- TOMBOL AKSI MODE PENJUALAN --}}
                <template x-if="modeTransaksi === 'penjualan'">
                    <button
                        type="button"
                        x-on:click="simpanNota()"
                        x-bind:disabled="sedangMenyimpan"
                        class="px-8 py-3 rounded-xl bg-[#B0181C] hover:bg-[#8f1013] text-white font-bold text-sm flex items-center gap-2 shadow-xl shadow-red-900/20 transition active:scale-98 cursor-pointer"
                    >
                        <x-icon name="save" class="w-4 h-4" />
                        <span>Simpan &amp; Cetak Struk</span>
                        <kbd class="text-[10px] bg-white/20 px-1.5 rounded ml-1">F12</kbd>
                    </button>
                </template>
            </div>
        </div>
    </x-card>

    {{-- MODAL SCANNER KAMERA --}}
    <x-modal name="scan-kamera" title="Scanner Kamera Barcode">
        <div class="space-y-4 text-center">
            <div id="html5-qr-code-reader" class="w-full max-w-sm mx-auto overflow-hidden rounded-xl border border-line bg-black"></div>
            <p class="text-xs text-steel">Arahkan kamera ke barcode produk.</p>
            <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'scan-kamera' }); stopKamera()">Tutup Kamera</x-button>
        </div>
    </x-modal>

    {{-- MODAL CARI BARANG (F2) --}}
    <x-modal name="cari-barang" title="Cari Barang &amp; Sparepart (F2)">
        <div class="space-y-3" x-init="$nextTick(() => $refs.modalCariInput?.focus())">
            <div class="relative">
                <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-steel" />
                <input
                    x-ref="modalCariInput"
                    x-model="cariQuery"
                    x-on:keydown.enter.prevent="pilihTopBarangModal()"
                    type="text"
                    placeholder="Ketik nama barang, kode, barcode, atau jasa..."
                    class="w-full rounded-lg border border-line bg-white pl-9 pr-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rajawali"
                >
            </div>

            <div class="max-h-72 overflow-y-auto border border-line rounded-xl divide-y divide-line">
                <template x-for="b in daftarBarangFiltered" :key="b.id">
                    <div
                        x-on:click="pilihBarangDariModal(b)"
                        class="p-3 hover:bg-canvas cursor-pointer flex justify-between items-center transition duration-150 group"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-rajawali" x-text="b.kode"></span>
                                <span class="font-bold text-sm text-ink group-hover:text-rajawali" x-text="b.nama"></span>
                                <template x-if="b.is_jasa">
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-blue-100 text-blue-800 border border-blue-300 font-bold">JASA</span>
                                </template>
                            </div>
                            <p class="text-xs text-steel mt-0.5">Barcode: <span class="font-mono" x-text="b.barcode"></span></p>
                        </div>
                        <div class="text-right font-bold">
                            <span class="font-mono font-bold text-sm text-ink block" x-text="formatRp(b.harga)"></span>
                            <template x-if="!b.is_jasa">
                                <span class="text-xs font-mono text-steel">Stok: <strong :class="b.stok <= 0 ? 'text-rajawali' : 'text-lunas'" x-text="b.stok"></strong></span>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="daftarBarangFiltered.length === 0">
                    <div class="p-6 text-center text-steel text-sm">Barang tidak ditemukan.</div>
                </template>
            </div>
        </div>
    </x-modal>

    {{-- MODAL DISKON NOTA (F6) --}}
    <x-modal name="diskon-nota" title="Diskon Nota (F6)">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-bold text-steel">Mode Diskon</label>
                <div class="inline-flex rounded-lg border border-line bg-canvas p-1 text-xs font-mono">
                    <button
                        type="button"
                        x-on:click="diskonNotaMode = 'rp'; validasiDiskonNota()"
                        :class="diskonNotaMode === 'rp' ? 'bg-rajawali text-white font-bold' : 'text-steel hover:text-ink'"
                        class="px-3 py-1.5 rounded transition cursor-pointer"
                    >Nominal (Rp)</button>
                    <button
                        type="button"
                        x-on:click="diskonNotaMode = 'persen'; validasiDiskonNota()"
                        :class="diskonNotaMode === 'persen' ? 'bg-rajawali text-white font-bold' : 'text-steel hover:text-ink'"
                        class="px-3 py-1.5 rounded transition cursor-pointer"
                    >Persentase (%)</button>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-steel block mb-1" x-text="diskonNotaMode === 'persen' ? 'Diskon Persentase (%)' : 'Diskon Nominal (Rp)'"></label>
                <input
                    type="number"
                    x-model.number="diskonNotaValue"
                    x-on:input="validasiDiskonNota()"
                    min="0"
                    placeholder="Masukkan nilai diskon..."
                    class="w-full rounded-lg border border-line bg-white px-3 py-2 text-sm font-mono font-bold focus:ring-2 focus:ring-rajawali focus:outline-none"
                >
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-line">
                <x-button type="button" variant="primary" x-on:click="$dispatch('tutup-modal', { name: 'diskon-nota' })">
                    Terapkan Diskon
                </x-button>
            </div>
        </div>
    </x-modal>

    {{-- MODAL CUSTOMER BARU CEPAT (QUICK ADD) --}}
    <x-modal name="tambah-customer-cepat" title="Pendaftaran Customer Baru Cepat">
        <form x-on:submit.prevent="simpanCustomerCepat()" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-steel uppercase mb-1">Nama Customer *</label>
                <input type="text" x-model="formCustomer.nama" required placeholder="Nama lengkap pelanggan..." class="w-full text-xs font-bold rounded-lg border border-line px-3 py-2 focus:ring-2 focus:ring-rajawali focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-steel uppercase mb-1">No HP / WhatsApp</label>
                    <input type="text" x-model="formCustomer.telepon" placeholder="08xxxxxxxxxx" class="w-full text-xs font-mono font-bold rounded-lg border border-line px-3 py-2 focus:ring-2 focus:ring-rajawali focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-steel uppercase mb-1">Kategori</label>
                    <select x-model="formCustomer.kategori" class="w-full text-xs font-bold rounded-lg border border-line px-3 py-2 focus:ring-2 focus:ring-rajawali focus:outline-none">
                        <option value="umum">Umum (Eceran)</option>
                        <option value="grosir">Grosir / Bengkel</option>
                        <option value="langganan">Langganan Tetap</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-steel uppercase mb-1">Plat Nomor Motor</label>
                    <input type="text" x-model="formCustomer.plat_nomor" placeholder="L 1234 ABC" class="w-full text-xs font-mono font-bold uppercase rounded-lg border border-line px-3 py-2 focus:ring-2 focus:ring-rajawali focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-steel uppercase mb-1">Tipe Motor</label>
                    <input type="text" x-model="formCustomer.jenis_kendaraan" placeholder="Honda Vario 125" class="w-full text-xs font-bold rounded-lg border border-line px-3 py-2 focus:ring-2 focus:ring-rajawali focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-line">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'tambah-customer-cepat' })">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan Customer</x-button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL DAFTAR ANTREAN SERVIS --}}
    <x-modal name="antrean-service" title="Daftar Antrean &amp; Servis Bengkel Siap Ambil">
        <div class="space-y-3">
            <div class="overflow-x-auto max-h-80 border border-line rounded-xl">
                <table class="w-full text-xs">
                    <thead class="bg-canvas text-steel uppercase font-bold border-b border-line">
                        <tr>
                            <th class="px-3 py-2.5 text-left">No Dokumen</th>
                            <th class="px-3 py-2.5 text-left">Customer &amp; Motor</th>
                            <th class="px-3 py-2.5 text-left">Montir</th>
                            <th class="px-3 py-2.5 text-right">Estimasi Biaya</th>
                            <th class="px-3 py-2.5 text-center">Status</th>
                            <th class="px-3 py-2.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <template x-for="s in antreanServiceList" :key="s.id">
                            <tr class="hover:bg-canvas transition">
                                <td class="px-3 py-2.5 font-mono font-bold text-rajawali" x-text="s.nomor_dokumen"></td>
                                <td class="px-3 py-2.5">
                                    <div class="font-bold text-ink" x-text="s.customer_nama"></div>
                                    <div class="text-[11px] text-steel font-mono">
                                        <span class="font-bold text-blue-700" x-text="s.plat_nomor"></span>
                                        <span x-text="' (' + s.merk_type + ')'"></span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-steel" x-text="s.montir_nama"></td>
                                <td class="px-3 py-2.5 text-right font-mono font-bold text-ink" x-text="formatRp(s.total_biaya)"></td>
                                <td class="px-3 py-2.5 text-center">
                                    <span
                                        :class="{
                                            'bg-amber-100 text-amber-800 border-amber-300': s.status === 'masuk',
                                            'bg-blue-100 text-blue-800 border-blue-300': s.status === 'dikerjakan',
                                            'bg-emerald-100 text-emerald-800 border-emerald-300': s.status === 'selesai'
                                        }"
                                        class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase border"
                                        x-text="s.status"
                                    ></span>
                                </td>
                                <td class="px-3 py-2.5 text-right">
                                    <button
                                        type="button"
                                        x-on:click="muatServiceKeKasir(s.id)"
                                        class="px-2.5 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition shadow-xs"
                                    >
                                        Muat ke Kasir →
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="antreanServiceList.length === 0">
                            <tr>
                                <td colspan="6" class="py-8 text-center text-steel italic">Tidak ada antrean servis motor yang aktif saat ini.</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </x-modal>
</div>

<script>
function kasirApp(dataBarang, dataCustomer, dataMontirs, dataAntrean, batasDiskonPersen, izinkanStokMinus, bolehJualDibawahHpp, printerStrukAktif, printerFakturAktif) {
    return {
        daftarBarang: dataBarang,
        customerList: dataCustomer,
        montirs: dataMontirs || [],
        antreanServiceList: dataAntrean || [],
        batasDiskonPersen: batasDiskonPersen,
        izinkanStokMinus: izinkanStokMinus,
        bolehJualDibawahHpp: bolehJualDibawahHpp,
        
        // Mode Transaksi
        modeTransaksi: 'penjualan', // 'penjualan' | 'service'
        serviceIdAktif: null,
        nomorSpkAktif: '',
        platNomor: '',
        merkType: '',
        montirId: '',
        keluhan: '',

        // POS States
        customerId: dataCustomer[0].id,
        jenisBayar: 'tunai',
        barcode: '',
        cariQuery: '',
        diskonNotaMode: 'rp',
        diskonNotaValue: '',
        indeksLive: 0,
        fokusMainInput: false,
        keranjang: [],
        barisAktif: null,
        bayar: 0,
        uangMuka: 0,
        uidCounter: 1,
        modalTerbuka: 0,
        sedangMenyimpan: false,
        hargaTerakhirInfo: null,
        html5QrCode: null,
        kameraAktif: false,

        formCustomer: {
            nama: '',
            telepon: '',
            plat_nomor: '',
            jenis_kendaraan: '',
            kategori: 'umum',
        },

        initApp() {
            this.$watch('customerId', () => this.dapatkanHargaTerakhir());
            this.$watch('barisAktif', () => this.dapatkanHargaTerakhir());
        },

        gantiMode(mode) {
            this.modeTransaksi = mode;
            if (mode === 'penjualan') {
                this.serviceIdAktif = null;
                this.nomorSpkAktif = '';
            }
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        bukaModalCustomerCepat() {
            this.formCustomer = { nama: '', telepon: '', plat_nomor: '', jenis_kendaraan: '', kategori: 'umum' };
            this.$dispatch('buka-modal', { name: 'tambah-customer-cepat' });
        },

        async simpanCustomerCepat() {
            if (!this.formCustomer.nama) return;
            try {
                const res = await fetch('/admin/kasir/customer-cepat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formCustomer)
                });
                const data = await res.json();
                if (data.sukses && data.customer) {
                    this.customerList.push(data.customer);
                    this.customerId = data.customer.id;
                    if (data.customer.plat) this.platNomor = data.customer.plat;
                    if (data.customer.motor) this.merkType = data.customer.motor;
                    this.$dispatch('tutup-modal', { name: 'tambah-customer-cepat' });
                    if (window.toastSukses) window.toastSukses(data.pesan);
                } else {
                    if (window.toastGagal) window.toastGagal(data.pesan || 'Gagal menyimpan customer');
                }
            } catch (err) {
                if (window.toastGagal) window.toastGagal('Error: ' + err.message);
            }
        },

        bukaModalAntreanService() {
            this.$dispatch('buka-modal', { name: 'antrean-service' });
        },

        async muatServiceKeKasir(serviceId) {
            try {
                const res = await fetch(`/admin/kasir/antrean-service/${serviceId}`);
                const data = await res.json();
                if (data.sukses && data.service) {
                    const s = data.service;
                    this.modeTransaksi = 'service';
                    this.serviceIdAktif = s.id;
                    this.nomorSpkAktif = s.nomor_dokumen;
                    if (s.customer_id) this.customerId = s.customer_id;
                    this.platNomor = s.plat_nomor;
                    this.merkType = s.merk_type;
                    this.montirId = s.montir_id;
                    this.keluhan = s.keluhan;

                    // Muat item ke keranjang
                    this.keranjang = [];
                    s.items.forEach(it => {
                        this.keranjang.push({
                            uid: this.uidCounter++,
                            kode: it.kode,
                            nama: it.nama,
                            is_jasa: it.is_jasa,
                            qty: it.qty,
                            harga: it.harga,
                            hargaOriginal: it.harga,
                            diskon: 0,
                            diskonPersen: 0,
                            hpp: 0,
                            stok: 999,
                            labelTier: null
                        });
                    });

                    this.$dispatch('tutup-modal', { name: 'antrean-service' });
                    if (window.toastSukses) window.toastSukses(`SPK ${s.nomor_dokumen} berhasil dimuat ke kasir!`);
                }
            } catch (err) {
                if (window.toastGagal) window.toastGagal('Gagal memuat antrean servis: ' + err.message);
            }
        },

        pilihCustomer(c) {
            this.customerId = c.id;
            if (c.plat && !this.platNomor) this.platNomor = c.plat;
            if (c.motor && !this.merkType) this.merkType = c.motor;
        },

        sinkronPlatKeCustomer() {
            if (!this.platNomor) return;
            const match = this.customerList.find(c => c.plat && c.plat.toUpperCase() === this.platNomor.trim().toUpperCase());
            if (match) {
                this.customerId = match.id;
                if (match.motor && !this.merkType) this.merkType = match.motor;
            }
        },

        get hasilLive() {
            const q = (this.barcode || '').trim().toLowerCase();
            if (!q) return [];
            return this.daftarBarang.filter(b => 
                b.nama.toLowerCase().includes(q) || 
                b.kode.toLowerCase().includes(q) || 
                (b.barcode && b.barcode.toLowerCase().includes(q))
            ).slice(0, 8);
        },

        pilihLiveAtauBarcode() {
            const q = this.barcode.trim();
            if (!q) return;

            if (this.hasilLive.length > 0 && this.indeksLive >= 0 && this.indeksLive < this.hasilLive.length) {
                this.tambahBarangKeKeranjang(this.hasilLive[this.indeksLive]);
                this.barcode = '';
                this.indeksLive = 0;
                this.fokusMainInput = false;
                return;
            }

            this.tambahDariBarcode();
        },

        pilihBarangLive(b) {
            this.tambahBarangKeKeranjang(b);
            this.barcode = '';
            this.indeksLive = 0;
            this.fokusMainInput = false;
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        bukaModalCari() {
            this.cariQuery = this.barcode;
            this.$dispatch('buka-modal', { name: 'cari-barang' });
            this.$nextTick(() => {
                setTimeout(() => {
                    if (this.$refs.modalCariInput) {
                        this.$refs.modalCariInput.focus();
                        this.$refs.modalCariInput.select();
                    }
                }, 100);
            });
        },

        pilihTopBarangModal() {
            if (this.daftarBarangFiltered.length > 0) {
                this.pilihBarangDariModal(this.daftarBarangFiltered[0]);
            }
        },

        pilihBarangDariModal(b) {
            this.tambahBarangKeKeranjang(b);
            this.$dispatch('tutup-modal', { name: 'cari-barang' });
            this.barcode = '';
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        get daftarBarangFiltered() {
            const q = (this.cariQuery || '').trim().toLowerCase();
            if (!q) return this.daftarBarang;
            return this.daftarBarang.filter(b => 
                b.nama.toLowerCase().includes(q) || 
                b.kode.toLowerCase().includes(q) || 
                (b.barcode && b.barcode.toLowerCase().includes(q))
            );
        },

        get customerTerpilih() {
            return this.customerList.find(c => c.id == this.customerId) || this.customerList[0];
        },

        filterCustomer(query) {
            const q = (query || '').trim().toLowerCase();
            if (!q) return this.customerList;
            return this.customerList.filter(c => 
                (c.nama && c.nama.toLowerCase().includes(q)) ||
                (c.plat && c.plat.toLowerCase().includes(q)) ||
                (c.motor && c.motor.toLowerCase().includes(q)) ||
                (c.kategori && c.kategori.toLowerCase().includes(q))
            );
        },

        get subtotal() {
            return this.keranjang.reduce((acc, item) => acc + this.hitungTotalItem(item), 0);
        },

        get diskonNotaNominal() {
            if (this.diskonNotaValue === '' || this.diskonNotaValue === null || isNaN(this.diskonNotaValue)) {
                return 0;
            }
            const val = Number(this.diskonNotaValue);
            if (val <= 0) return 0;

            if (this.diskonNotaMode === 'persen') {
                return Math.round((this.subtotal * val) / 100);
            }
            return Math.round(val);
        },

        get grandTotal() {
            return Math.max(0, this.subtotal - this.diskonNotaNominal);
        },

        formatRp(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },

        hitungTotalItem(item) {
            const hargaBersih = Math.max(0, (item.qty * item.harga) - (item.diskon || 0));
            return hargaBersih;
        },

        tambahBarangKeKeranjang(b) {
            const idx = this.keranjang.findIndex(i => i.kode === b.kode);
            if (idx >= 0) {
                this.ubahQty(idx, this.keranjang[idx].qty + 1);
                this.barisAktif = idx;
            } else {
                const tier = this.hitungHargaTier(b, 1);
                this.keranjang.push({
                    uid: this.uidCounter++,
                    id: b.id,
                    kode: b.kode,
                    nama: b.nama,
                    is_jasa: b.is_jasa || false,
                    qty: 1,
                    harga: tier.harga,
                    hargaOriginal: b.harga,
                    diskon: 0,
                    diskonPersen: 0,
                    hpp: b.hpp,
                    stok: b.stok,
                    labelTier: tier.label
                });
                this.barisAktif = this.keranjang.length - 1;
            }
        },

        tambahDariBarcode() {
            const query = this.barcode.trim().toUpperCase();
            if (!query) return;

            let barang = this.daftarBarang.find(b => b.kode.toUpperCase() === query || (b.barcode && b.barcode.toUpperCase() === query));

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
                if (window.toastGagal) window.toastGagal(`Barang "${this.barcode}" tidak ditemukan.`);
                this.barcode = '';
                return;
            }

            this.tambahBarangKeKeranjang(barang);
            this.barcode = '';
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        hitungHargaTier(barang, qty) {
            const cust = this.customerList.find(c => c.id == this.customerId);
            const isMitraGrosir = cust && (cust.kategori === 'mitra' || cust.kategori === 'grosir');

            const minQty2 = Number(barang.min_qty_grosir_2 || 24);
            const minQty1 = Number(barang.min_qty_grosir_1 || 3);
            const hg2 = Number(barang.harga_grosir_2 || 0);
            const hg1 = Number(barang.harga_grosir_1 || barang.harga_grosir || 0);
            const hEceran = Number(barang.harga || 0);

            if (hg2 > 0 && qty >= minQty2) {
                return { harga: hg2, label: `Grosir (≥${minQty2})` };
            }
            if (hg1 > 0 && qty >= minQty1) {
                return { harga: hg1, label: `Semi-Grosir (≥${minQty1})` };
            }
            if (isMitraGrosir && hg1 > 0) {
                return { harga: hg1, label: `Mitra/Grosir` };
            }
            return { harga: hEceran, label: null };
        },

        ubahQty(idx, qtyBaru) {
            const item = this.keranjang[idx];
            if (!item) return;

            const qty = Math.max(0.001, Number(qtyBaru) || 1);
            item.qty = qty;

            const barang = this.daftarBarang.find(b => b.kode === item.kode);
            if (barang) {
                const tier = this.hitungHargaTier(barang, qty);
                item.harga = tier.harga;
                item.labelTier = tier.label;
            }
        },

        ubahHargaManual(idx, hargaBaru) {
            const item = this.keranjang[idx];
            if (!item) return;
            item.harga = Math.max(0, Number(hargaBaru) || 0);
        },

        ubahDiskonPersen(idx, persen) {
            const item = this.keranjang[idx];
            if (!item) return;
            const p = Math.min(100, Math.max(0, Number(persen) || 0));
            item.diskonPersen = p;
            item.diskon = Math.round(((item.qty * item.harga) * p) / 100);
        },

        ubahDiskonNominal(idx, nominal) {
            const item = this.keranjang[idx];
            if (!item) return;
            const sub = item.qty * item.harga;
            const disc = Math.min(sub, Math.max(0, Number(nominal) || 0));
            item.diskon = disc;
            item.diskonPersen = sub > 0 ? Math.round((disc / sub) * 100) : 0;
        },

        hapusBaris(idx) {
            this.keranjang.splice(idx, 1);
            if (this.barisAktif === idx) this.barisAktif = null;
        },

        kosongkanKeranjang() {
            if (this.keranjang.length === 0) return;
            if (confirm('Kosongkan semua barang di keranjang kasir?')) {
                this.keranjang = [];
                this.diskonNotaValue = '';
                this.serviceIdAktif = null;
                this.nomorSpkAktif = '';
                this.$refs.barcode?.focus();
            }
        },

        validasiDiskonNota() {
            const val = Number(this.diskonNotaValue);
            if (val <= 0) return;
            if (this.diskonNotaMode === 'persen' && this.batasDiskonPersen > 0 && val > this.batasDiskonPersen) {
                if (window.toastGagal) window.toastGagal(`Batas diskon kasir adalah ${this.batasDiskonPersen}%.`);
                this.diskonNotaValue = this.batasDiskonPersen;
            }
        },

        async simpanSpkService() {
            if (this.sedangMenyimpan) return;
            if (this.keranjang.length === 0) {
                if (window.toastGagal) window.toastGagal('Belum ada jasa atau sparepart yang diinput.');
                return;
            }

            this.sedangMenyimpan = true;

            try {
                const payload = {
                    tipe_transaksi: 'service_spk',
                    customer_id: this.customerId,
                    plat_nomor: this.platNomor,
                    merk_type: this.merkType,
                    montir_id: this.montirId || null,
                    keluhan: this.keluhan,
                    items: this.keranjang.map(i => ({
                        kode: i.kode,
                        nama: i.nama,
                        qty: i.qty,
                        harga: i.harga,
                        diskon: i.diskon || 0
                    })),
                    metode_pembayaran: 'tunai',
                };

                const res = await fetch('/admin/kasir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                this.sedangMenyimpan = false;

                if (data.sukses) {
                    if (window.toastSukses) window.toastSukses(data.pesan);
                    window.open(data.cetak_url, '_blank');
                    this.keranjang = [];
                    this.platNomor = '';
                    this.merkType = '';
                    this.keluhan = '';
                    this.montirId = '';
                    this.serviceIdAktif = null;
                } else {
                    if (window.toastGagal) window.toastGagal(data.pesan || 'Gagal membuat SPK servis.');
                }
            } catch (err) {
                this.sedangMenyimpan = false;
                if (window.toastGagal) window.toastGagal('Error: ' + err.message);
            }
        },

        async simpanNota() {
            if (this.sedangMenyimpan) return;
            if (this.keranjang.length === 0) {
                if (window.toastGagal) window.toastGagal('Belum ada barang di keranjang.');
                return;
            }

            this.sedangMenyimpan = true;

            const bayarFinal = this.jenisBayar === 'tunai' ? this.grandTotal : (this.uangMuka || 0);

            const tipe = this.serviceIdAktif ? 'service_pelunasan' : (this.modeTransaksi === 'service' ? 'service_langsung' : 'penjualan');

            const payload = {
                tipe_transaksi: tipe,
                service_id: this.serviceIdAktif,
                customer_id: this.customerId,
                plat_nomor: this.platNomor,
                merk_type: this.merkType,
                montir_id: this.montirId || null,
                keluhan: this.keluhan,
                items: this.keranjang.map(i => ({
                    kode: i.kode,
                    nama: i.nama,
                    qty: i.qty,
                    harga: i.harga,
                    diskon: i.diskon || 0
                })),
                diskon: this.diskonNotaNominal,
                bayar: bayarFinal,
                uang_muka: this.uangMuka,
                metode_pembayaran: this.jenisBayar,
            };

            try {
                const res = await fetch('/admin/kasir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                this.sedangMenyimpan = false;

                if (data.sukses) {
                    if (window.toastSukses) window.toastSukses(data.pesan);
                    window.open(data.cetak_url, '_blank');
                    this.keranjang = [];
                    this.diskonNotaValue = '';
                    this.serviceIdAktif = null;
                    this.nomorSpkAktif = '';
                    this.platNomor = '';
                    this.merkType = '';
                    this.keluhan = '';
                    this.montirId = '';
                } else {
                    if (window.toastGagal) window.toastGagal(data.pesan || 'Gagal memproses transaksi.');
                }
            } catch (err) {
                this.sedangMenyimpan = false;
                if (window.toastGagal) window.toastGagal('Error: ' + err.message);
            }
        },

        bukaScannerKamera() {
            this.$dispatch('buka-modal', { name: 'scan-kamera' });
            this.$nextTick(() => this.mulaiKamera());
        },

        mulaiKamera() {
            if (typeof Html5Qrcode === 'undefined') return;
            if (this.html5QrCode) this.stopKamera();
            this.html5QrCode = new Html5Qrcode("html5-qr-code-reader");
            const config = { fps: 15, qrbox: { width: 250, height: 180 } };
            this.html5QrCode.start(
                { facingMode: "environment" },
                config,
                (decodedText) => {
                    this.barcode = decodedText;
                    this.tambahDariBarcode();
                    this.stopKamera();
                    this.$dispatch('tutup-modal', { name: 'scan-kamera' });
                },
                () => {}
            ).then(() => { this.kameraAktif = true; }).catch(() => {});
        },

        stopKamera() {
            if (this.html5QrCode && this.kameraAktif) {
                this.html5QrCode.stop().then(() => {
                    this.html5QrCode.clear();
                    this.kameraAktif = false;
                }).catch(() => { this.kameraAktif = false; });
            }
        },

        tanganiShortcut(e) {
            if (this.modalTerbuka > 0) return;
            if (e.key === 'F2') {
                e.preventDefault();
                this.bukaModalCari();
            } else if (e.key === 'F6') {
                e.preventDefault();
                this.$dispatch('buka-modal', { name: 'diskon-nota' });
            } else if (e.key === 'F10' && this.modeTransaksi === 'service') {
                e.preventDefault();
                this.simpanSpkService();
            } else if (e.key === 'F12') {
                e.preventDefault();
                this.simpanNota();
            }
        },

        async dapatkanHargaTerakhir() {
            if (this.barisAktif === null || !this.customerId) {
                this.hargaTerakhirInfo = null;
                return;
            }
            const item = this.keranjang[this.barisAktif];
            if (!item) {
                this.hargaTerakhirInfo = null;
                return;
            }
            const barang = this.daftarBarang.find(b => b.kode === item.kode);
            if (!barang) {
                this.hargaTerakhirInfo = null;
                return;
            }
            try {
                const res = await fetch(`/admin/kasir/harga-terakhir?customer_id=${this.customerId}&barang_id=${barang.id}`);
                const data = await res.json();
                this.hargaTerakhirInfo = data.harga ? data : null;
            } catch {
                this.hargaTerakhirInfo = null;
            }
        }
    };
}
</script>
</x-app-layout>
