<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<x-app-layout title="Kasir">

<div
    x-data="kasirApp(@js($daftarBarangJson), @js($daftarCustomerJson), @js($batasDiskonPersen), @js($izinkanStokMinus), @js($bolehJualDibawahHpp), @js($printerStrukAktif), @js($printerFakturAktif))"
    x-init="initApp(); $nextTick(() => $refs.barcode.focus())"
    x-on:keydown.window="tanganiShortcut($event)"
    x-on:buka-modal.window="modalTerbuka++"
    x-on:tutup-modal.window="modalTerbuka = Math.max(0, modalTerbuka - 1)"
    class="flex flex-col gap-3 -m-5 p-5 h-[calc(100vh-3.5rem)]"
>
    {{-- ATAS --}}
    <div class="flex items-center gap-3 flex-wrap">
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
                    placeholder="Scan atau ketik nama barang / kode / barcode..."
                    class="w-full rounded-md border border-line bg-white pl-9 pr-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-rajawali focus:border-rajawali"
                >

                <!-- Live Search Floating Dropdown Popup -->
                <div
                    x-show="barcode.trim().length >= 1 && hasilLive.length > 0"
                    x-cloak
                    class="absolute left-0 right-0 top-full mt-1 bg-white border-2 border-rajawali rounded-lg shadow-2xl z-50 max-h-80 overflow-y-auto divide-y divide-line"
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
                                </div>
                                <p class="text-[11px] text-steel mt-0.5">Barcode: <span class="font-mono" x-text="b.barcode || '-'"></span> | Rak: <span x-text="b.lokasi_rak || '-'"></span></p>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-sm text-ink block" x-text="formatRp(b.harga)"></span>
                                <span class="text-[11px] font-mono text-steel">Stok: <strong :class="b.stok <= 0 ? 'text-rajawali' : 'text-lunas'" x-text="b.stok"></strong></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <button type="button" x-on:click="bukaScannerKamera()" class="px-3 py-2.5 bg-rajawali/10 text-rajawali rounded-md hover:bg-rajawali/20 font-bold text-xs flex items-center gap-1.5 shrink-0 transition" data-tooltip="Scan Barcode via Kamera HP / Laptop">
                <x-icon name="camera" class="w-4 h-4" />
                <span class="hidden sm:inline">Kamera</span>
            </button>
        </div>

        {{-- CUSTOMER SELECT SEARCHABLE (SELECT2 STYLE) --}}
        <div class="relative min-w-56 max-w-xs" x-data="{ terbuka: false, cari: '' }" x-on:click.outside="terbuka = false">
            {{-- Trigger Button --}}
            <button
                type="button"
                x-on:click="terbuka = !terbuka; if(terbuka) $nextTick(() => $refs.inputCariCust?.focus())"
                class="w-full flex items-center justify-between rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-bold shadow-sm focus:outline-none focus:ring-2 focus:ring-rajawali hover:bg-slate-50 transition"
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

            {{-- Dropdown Popup --}}
            <div
                x-show="terbuka"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-cloak
                class="absolute left-0 top-full mt-1.5 w-72 sm:w-80 rounded-xl border border-slate-200 bg-white shadow-2xl z-50 overflow-hidden"
            >
                {{-- Search Input --}}
                <div class="p-2 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                    <x-icon name="search" class="w-4 h-4 text-steel shrink-0 ml-1" />
                    <input
                        x-ref="inputCariCust"
                        type="text"
                        x-model="cari"
                        placeholder="Cari Nama / Plat / Motor / WA..."
                        class="w-full bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-rajawali font-medium"
                    >
                    <button x-show="cari" type="button" x-on:click="cari = ''" class="text-steel hover:text-ink text-xs font-bold px-1">✕</button>
                </div>

                {{-- Customer List --}}
                <div class="max-h-60 overflow-y-auto divide-y divide-slate-100">
                    <template x-for="c in filterCustomer(cari)" :key="c.id">
                        <div
                            x-on:click="customerId = c.id; terbuka = false; cari = ''"
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

        <div class="flex items-center rounded-md border border-line overflow-hidden text-sm font-medium">
            <button type="button" x-on:click="jenisBayar = 'tunai'" :class="jenisBayar === 'tunai' ? 'bg-rajawali text-white' : 'bg-white text-steel'" class="px-3.5 py-2.5">Tunai</button>
            <button type="button" x-on:click="jenisBayar = 'tempo'" :class="jenisBayar === 'tempo' ? 'bg-rajawali text-white' : 'bg-white text-steel'" class="px-3.5 py-2.5">Tempo</button>
        </div>

        <div class="text-sm text-steel font-mono ml-auto" x-data="{ tgl: '' }" x-init="tgl = new Intl.DateTimeFormat('id-ID', {dateStyle:'long', timeZone:'Asia/Jakarta'}).format(new Date())">
            <span x-text="tgl"></span>
        </div>
    </div>

    <p x-show="jenisBayar === 'tempo' && customerId == customerList[0].id" x-cloak class="text-xs text-marka bg-marka/10 border border-marka/30 rounded-md px-3 py-2 font-bold">
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
                        <th class="text-right font-semibold px-3 py-2 w-20">Qty</th>
                        <th class="text-right font-semibold px-3 py-2 w-28">Harga</th>
                        <th class="text-right font-semibold px-3 py-2 w-20">Disc (%)</th>
                        <th class="text-right font-semibold px-3 py-2 w-24">Disc (Rp)</th>
                        <th class="text-right font-semibold px-3 py-2 w-32">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="keranjang.length === 0">
                        <tr>
                            <td colspan="8" class="text-center text-steel py-12">
                                <x-icon name="shopping-bag" class="w-8 h-8 mx-auto text-steel/40 mb-2" />
                                <p class="font-bold text-ink text-sm">Keranjang Transaksi Masih Kosong</p>
                                <p class="text-xs text-steel mt-0.5">Ketik nama barang / scan barcode di atas, atau tekan <kbd class="bg-canvas border border-line px-1.5 py-0.5 rounded text-[10px] font-mono text-ink font-bold">F2</kbd> (Cari Barang).</p>
                            </td>
                        </tr>
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
                            <td class="px-3 py-2 font-mono text-xs text-steel font-bold" x-text="item.kode"></td>
                            <td class="px-3 py-2 font-bold text-ink">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span x-text="item.nama"></span>
                                    <template x-if="item.tierLabel">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-100 text-amber-900 border border-amber-300" x-text="item.tierLabel"></span>
                                    </template>
                                </div>
                                <template x-if="item.hargaOriginal && item.harga < item.hargaOriginal">
                                    <div class="text-[11px] text-emerald-700 font-mono font-bold mt-0.5 flex items-center gap-1">
                                        <span>🎉 Diskon Grosir: Hemat Rp <span x-text="formatRp(item.hargaOriginal - item.harga)"></span>/pcs</span>
                                        <span>(Total hemat Rp <span x-text="formatRp((item.hargaOriginal - item.harga) * item.qty)"></span>)</span>
                                    </div>
                                </template>
                            </td>
                            <td class="px-3 py-2 text-right font-mono">
                                <input
                                    type="number" min="1" step="1"
                                    x-model.number="item.qty"
                                    x-on:click.stop
                                    x-on:input="recalculateDiskonRow(idx)"
                                    x-on:focus="barisAktif = idx"
                                    class="w-14 text-right font-mono bg-transparent focus:outline-none focus:bg-white rounded px-1"
                                >
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-bold" x-text="formatRp(item.harga)"></td>
                            
                            {{-- DISC % --}}
                            <td class="px-3 py-2 text-right font-mono">
                                <input
                                    type="number" min="0" max="100" step="0.1"
                                    x-model.number="item.diskonPersen"
                                    x-on:click.stop
                                    x-on:focus="barisAktif = idx"
                                    x-on:input="hitungDiskonDariPersen(idx)"
                                    class="w-12 text-right font-mono bg-transparent focus:outline-none focus:bg-white rounded px-1"
                                >%
                            </td>
                            
                            {{-- DISC RP --}}
                            <td class="px-3 py-2 text-right font-mono">
                                <input
                                    type="number" min="0" step="1"
                                    x-model.number="item.diskon"
                                    x-on:click.stop
                                    x-on:focus="barisAktif = idx"
                                    x-on:input="hitungPersenDariDiskon(idx)"
                                    class="w-18 text-right font-mono bg-transparent focus:outline-none focus:bg-white rounded px-1"
                                >
                            </td>
                            <td class="px-3 py-2 text-right font-mono font-bold text-ink" x-text="formatRp((item.harga * item.qty) - item.diskon)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- BAWAH --}}
    <x-card class="shrink-0">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            {{-- TOMBOL & AKSI --}}
            <div class="flex flex-wrap gap-2 content-start">
                <x-button variant="secondary" x-on:click="$dispatch('buka-modal', { name: 'cari-barang' })"><kbd class="text-[10px] bg-canvas px-1 rounded">F2</kbd> Cari Barang</x-button>
                <x-button variant="secondary" x-on:click="$dispatch('buka-modal', { name: 'diskon-nota' })"><kbd class="text-[10px] bg-canvas px-1 rounded">F6</kbd> Diskon Nota</x-button>
                <x-button variant="secondary" x-on:click="kosongkanKeranjang()"><kbd class="text-[10px] bg-canvas px-1 rounded">F8</kbd> Kosongkan</x-button>
                <p class="w-full text-xs text-steel mt-1">Total Qty: <span class="font-mono font-bold text-ink" x-text="totalQty"></span> barang</p>
            </div>

            {{-- PANEL HISTORI HARGA TERAKHIR CUSTOMER --}}
            <div class="bg-canvas border border-line rounded-lg p-3 text-xs flex flex-col justify-center font-bold">
                <h4 class="font-bold text-steel uppercase tracking-wider mb-2 flex items-center gap-1">
                    <x-icon name="history" class="w-3.5 h-3.5" /> Harga Terakhir Customer
                </h4>
                <template x-if="hargaTerakhirInfo">
                    <div class="space-y-1">
                        <p class="text-sm font-black text-rajawali" x-text="formatRp(hargaTerakhirInfo.harga)"></p>
                        <p class="text-[10px] text-steel">Nomor Nota: <span class="font-mono font-bold text-ink" x-text="hargaTerakhirInfo.nota"></span></p>
                        <p class="text-[10px] text-steel">Tanggal: <span class="text-ink" x-text="hargaTerakhirInfo.tanggal"></span></p>
                    </div>
                </template>
                <template x-if="!hargaTerakhirInfo">
                    <p class="text-steel italic font-medium">Pilih baris barang untuk melihat riwayat harga customer ini.</p>
                </template>
            </div>

            {{-- PEMBAYARAN & TOTAL --}}
            <div class="space-y-2 text-sm font-bold">
                <div class="flex justify-between"><span class="text-steel">Subtotal</span><span class="font-mono" x-text="formatRp(subtotal)"></span></div>
                <template x-if="totalDiskonGrosir > 0">
                    <div class="flex justify-between items-center bg-emerald-50 border border-emerald-200 px-2.5 py-1.5 rounded-lg text-emerald-800 text-xs font-bold">
                        <span>🎉 Total Hemat Diskon Grosir:</span>
                        <span class="font-mono text-xs font-black text-emerald-700">Rp <span x-text="formatRp(totalDiskonGrosir)"></span></span>
                    </div>
                </template>
                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <span class="text-steel">
                            Diskon Nota
                            <template x-if="batasDiskonPersen > 0">
                                <span class="text-[11px] text-steel/70">(maks <span x-text="batasDiskonPersen"></span>%)</span>
                            </template>
                        </span>
                        <div class="flex items-center gap-1.5">
                            {{-- Toggle Button Segmented --}}
                            <div class="inline-flex rounded-lg border border-slate-300 bg-slate-100 p-0.5 text-xs font-bold shadow-inner">
                                <button
                                    type="button"
                                    x-on:click="diskonNotaMode = 'rp'; validasiDiskonNota()"
                                    :class="diskonNotaMode === 'rp' ? 'bg-rajawali text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60'"
                                    class="px-2.5 py-1 rounded-md transition font-mono"
                                    title="Diskon Mode Nominal (Rp)"
                                >Rp</button>
                                <button
                                    type="button"
                                    x-on:click="diskonNotaMode = 'persen'; validasiDiskonNota()"
                                    :class="diskonNotaMode === 'persen' ? 'bg-rajawali text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-200/60'"
                                    class="px-2.5 py-1 rounded-md transition font-mono"
                                    title="Diskon Mode Persentase (%)"
                                >%</button>
                            </div>

                            {{-- Input dengan Imbuhan Dinamis Rp / % --}}
                            <div class="relative flex items-center">
                                <span
                                    x-show="diskonNotaMode === 'rp'"
                                    class="absolute left-2 text-xs font-mono font-bold text-slate-500 pointer-events-none"
                                >Rp</span>
                                <input
                                    type="number"
                                    min="0"
                                    :max="diskonNotaMode === 'persen' ? 100 : null"
                                    x-model="diskonNotaValue"
                                    x-on:focus="$event.target.select()"
                                    x-on:click="$event.target.select()"
                                    x-on:input="validasiDiskonNota()"
                                    placeholder="0"
                                    :class="diskonNotaMode === 'rp' ? 'pl-7 pr-2' : 'pl-2 pr-6'"
                                    class="w-28 text-right font-mono font-bold border border-slate-300 bg-white rounded-lg py-1 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali focus:border-rajawali"
                                >
                                <span
                                    x-show="diskonNotaMode === 'persen'"
                                    class="absolute right-2 text-xs font-mono font-bold text-slate-500 pointer-events-none"
                                >%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dynamic Nominal Preview --}}
                    <template x-if="diskonNotaMode === 'persen' && diskonNotaNominal > 0">
                        <div class="text-right text-xs text-emerald-700 font-mono font-bold">
                            = Potongan <span x-text="formatRp(diskonNotaNominal)"></span>
                        </div>
                    </template>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-line">
                    <span class="font-display font-bold text-ink">GRAND TOTAL</span>
                    <span class="font-mono font-black text-2xl text-rajawali" x-text="formatRp(grandTotal)"></span>
                </div>

                {{-- TEMPO / KREDIT --}}
                <template x-if="jenisBayar === 'tempo'">
                    <div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-steel">Uang Muka (DP)</span>
                            <input type="number" min="0" x-model.number="uangMuka" x-on:focus="$event.target.select()" x-on:click="$event.target.select()" class="w-32 text-right font-mono border border-line bg-white rounded-md px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali">
                        </div>
                        <div class="flex justify-between items-baseline">
                            <span class="font-display font-semibold text-ink">Sisa Piutang</span>
                            <span class="font-mono font-bold text-xl text-rajawali" x-text="formatRp(grandTotal - (uangMuka || 0))"></span>
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
    <div class="space-y-3" x-init="$nextTick(() => $refs.modalCariInput?.focus())">
        <div class="relative">
            <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-steel" />
            <input
                x-ref="modalCariInput"
                x-model="cariQuery"
                x-on:keydown.enter.prevent="pilihTopBarangModal()"
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
                    <div class="text-right font-bold">
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

<x-modal name="diskon-nota" title="Diskon Nota (F6)">
    <div class="space-y-4" x-init="$nextTick(() => $refs.modalDiskonInput?.focus())">
        <div class="flex items-center justify-between">
            <label class="text-sm font-bold text-steel">Mode Diskon</label>
            <div class="inline-flex rounded-md border border-line bg-canvas p-1 text-xs font-mono">
                <button
                    type="button"
                    x-on:click="diskonNotaMode = 'rp'; validasiDiskonNota()"
                    :class="diskonNotaMode === 'rp' ? 'bg-rajawali text-white font-bold' : 'text-steel hover:text-ink'"
                    class="px-3 py-1.5 rounded transition"
                >Nominal (Rp)</button>
                <button
                    type="button"
                    x-on:click="diskonNotaMode = 'persen'; validasiDiskonNota()"
                    :class="diskonNotaMode === 'persen' ? 'bg-rajawali text-white font-bold' : 'text-steel hover:text-ink'"
                    class="px-3 py-1.5 rounded transition"
                >Persentase (%)</button>
            </div>
        </div>

        <div>
            <label class="text-sm font-bold text-steel block mb-1" x-text="diskonNotaMode === 'persen' ? 'Diskon Persentase (%)' : 'Diskon Nominal (Rp)'"></label>
            <input
                x-ref="modalDiskonInput"
                type="number"
                min="0"
                :max="diskonNotaMode === 'persen' ? 100 : null"
                x-model="diskonNotaValue"
                x-on:input="validasiDiskonNota()"
                x-on:focus="$event.target.select()"
                placeholder="Kosongkan jika tidak ada diskon"
                class="w-full rounded-md border border-line bg-white px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-rajawali"
            >
        </div>

        <div class="text-xs text-steel font-mono bg-canvas p-3 rounded-lg flex justify-between items-center">
            <span>Potongan Diskon:</span>
            <strong class="text-rajawali font-mono text-base font-black" x-text="formatRp(diskonNotaNominal)"></strong>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t border-line">
            <x-button variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'diskon-nota' })">Batal</x-button>
            <x-button variant="primary" x-on:click="validasiDiskonNota(); $dispatch('tutup-modal', { name: 'diskon-nota' })">Terapkan</x-button>
        </div>
    </div>
</x-modal>

<!-- Modal Scan Barcode via Kamera Device -->
<x-modal name="scan-kamera" title="Scan Barcode via Kamera HP / Laptop">
    <div class="space-y-4">
        <div id="html5-qr-code-reader" class="w-full h-64 sm:h-72 rounded-2xl overflow-hidden bg-slate-900 flex items-center justify-center border-2 border-dashed border-slate-700"></div>
        <p class="text-xs text-steel text-center italic">Arahkan stiker barcode barang ke depan kamera. Sistem akan membaca barcode secara otomatis.</p>
        <div class="flex justify-end gap-2">
            <x-button type="button" variant="secondary" x-on:click="stopKamera(); $dispatch('tutup-modal', { name: 'scan-kamera' })">Tutup Kamera</x-button>
        </div>
    </div>
</x-modal>

</x-app-layout>

<script>
function kasirApp(dataBarang, dataCustomer, batasDiskonPersen, izinkanStokMinus, bolehJualDibawahHpp, printerStrukAktif, printerFakturAktif) {
    return {
        daftarBarang: dataBarang,
        customerList: dataCustomer,
        printerStrukAktif: printerStrukAktif,
        printerFakturAktif: printerFakturAktif,
        html5QrCode: null,
        kameraAktif: false,

        bukaScannerKamera() {
            this.$dispatch('buka-modal', { name: 'scan-kamera' });
            this.$nextTick(() => {
                this.mulaiKamera();
            });
        },

        mulaiKamera() {
            if (typeof Html5Qrcode === 'undefined') {
                if (window.toastGagal) window.toastGagal('Library kamera scanner belum siap.');
                return;
            }
            if (this.html5QrCode) {
                this.stopKamera();
            }
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
                    if (window.toastSukses) window.toastSukses('Barcode terdeteksi: ' + decodedText);
                },
                () => {}
            ).then(() => {
                this.kameraAktif = true;
            }).catch(err => {
                if (window.toastGagal) window.toastGagal('Gagal membuka kamera: ' + (err.message || 'Izin kamera ditolak.'));
            });
        },

        stopKamera() {
            if (this.html5QrCode && this.kameraAktif) {
                this.html5QrCode.stop().then(() => {
                    this.html5QrCode.clear();
                    this.kameraAktif = false;
                }).catch(() => {
                    this.kameraAktif = false;
                });
            }
        },
        customerId: dataCustomer[0].id,
        batasDiskonPersen: batasDiskonPersen,
        izinkanStokMinus: izinkanStokMinus,
        bolehJualDibawahHpp: bolehJualDibawahHpp,
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

        initApp() {
            this.$watch('customerId', () => this.dapatkanHargaTerakhir());
            this.$watch('barisAktif', () => this.dapatkanHargaTerakhir());
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

        get totalDiskonGrosir() {
            return this.keranjang.reduce((acc, item) => {
                const hemat = (item.hargaOriginal && item.harga < item.hargaOriginal) ? (item.hargaOriginal - item.harga) : 0;
                return acc + (hemat * item.qty);
            }, 0);
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
                const url = `/admin/kasir/harga-terakhir?customer_id=${this.customerId}&barang_id=${barang.id}`;
                const res = await fetch(url);
                const data = await res.json();
                if (data.harga) {
                    this.hargaTerakhirInfo = data;
                } else {
                    this.hargaTerakhirInfo = null;
                }
            } catch (err) {
                this.hargaTerakhirInfo = null;
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
                window.toastGagal(`Barang dengan nama/kode "${this.barcode}" tidak ditemukan.`);
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
                return { harga: hg2, label: `Grosir Karton (≥${minQty2})` };
            }
            if (hg1 > 0 && qty >= minQty1) {
                return { harga: hg1, label: `Semi-Grosir (≥${minQty1})` };
            }
            if (isMitraGrosir && hg1 > 0) {
                return { harga: hg1, label: `Mitra/Grosir` };
            }
            return { harga: hEceran, label: null };
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
                this.recalculateDiskonRow(this.keranjang.indexOf(ada));
            } else {
                const tier = this.hitungHargaTier(barang, 1);
                this.keranjang.push({
                    uid: this.uidCounter++,
                    kode: barang.kode,
                    nama: barang.nama,
                    hargaOriginal: barang.harga,
                    hargaGrosir1: barang.harga_grosir_1 || barang.harga_grosir,
                    minQty1: barang.min_qty_grosir_1 || 3,
                    hargaGrosir2: barang.harga_grosir_2,
                    minQty2: barang.min_qty_grosir_2 || 24,
                    harga: tier.harga,
                    tierLabel: tier.label,
                    hpp: barang.hpp,
                    stok: barang.stok,
                    qty: 1,
                    diskonPersen: 0,
                    diskon: 0,
                });
                this.barisAktif = this.keranjang.length - 1;
            }
        },

        updateHargaTierItem(idx) {
            const item = this.keranjang[idx];
            if (!item) return;

            const barangRef = {
                harga: item.hargaOriginal,
                harga_grosir_1: item.hargaGrosir1,
                min_qty_grosir_1: item.minQty1,
                harga_grosir_2: item.hargaGrosir2,
                min_qty_grosir_2: item.minQty2,
            };

            const tier = this.hitungHargaTier(barangRef, item.qty);
            item.harga = tier.harga;
            item.tierLabel = tier.label;
        },

        pilihBarangDariModal(barang) {
            this.tambahBarangKeKeranjang(barang);
            this.cariQuery = '';
            this.$dispatch('tutup-modal', { name: 'cari-barang' });
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        hitungDiskonDariPersen(idx) {
            const item = this.keranjang[idx];
            if (!item) return;
            item.diskon = Math.round((item.harga * item.qty) * ((item.diskonPersen || 0) / 100));
            this.validasiDiskonBaris(idx);
        },

        hitungPersenDariDiskon(idx) {
            const item = this.keranjang[idx];
            if (!item) return;
            const totalHarga = item.harga * item.qty;
            item.diskonPersen = totalHarga > 0 ? Math.round((item.diskon / totalHarga) * 1000) / 10 : 0;
            this.validasiDiskonBaris(idx);
        },

        recalculateDiskonRow(idx) {
            this.updateHargaTierItem(idx);
            const item = this.keranjang[idx];
            if (!item) return;
            if (item.diskonPersen > 0) {
                this.hitungDiskonDariPersen(idx);
            } else if (item.diskon > 0) {
                this.hitungPersenDariDiskon(idx);
            }
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
                    item.diskonPersen = 0;
                }
            }
        },

        validasiDiskonNota() {
            if (this.diskonNotaValue === '' || this.diskonNotaValue === null || isNaN(this.diskonNotaValue)) {
                return;
            }

            const val = Number(this.diskonNotaValue);
            if (val <= 0) return;

            if (this.diskonNotaMode === 'persen') {
                if (this.batasDiskonPersen > 0 && val > this.batasDiskonPersen) {
                    window.toastGagal(`Batas maksimal diskon adalah ${this.batasDiskonPersen}%.`);
                    this.diskonNotaValue = this.batasDiskonPersen;
                }
            } else {
                if (this.subtotal > 0 && this.batasDiskonPersen > 0) {
                    const persen = (val / this.subtotal) * 100;
                    if (persen > this.batasDiskonPersen) {
                        const maxRp = Math.floor((this.subtotal * this.batasDiskonPersen) / 100);
                        window.toastGagal(`Diskon nota melebihi batas ${this.batasDiskonPersen}% (maks Rp ${maxRp.toLocaleString('id-ID')}).`);
                        this.diskonNotaValue = maxRp;
                    }
                }
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
                    this.diskonNotaValue = '';
                    this.bayar = 0;
                    this.uangMuka = 0;
                    this.barisAktif = null;
                    this.hargaTerakhirInfo = null;
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

            const bayarFinal = this.jenisBayar === 'tunai' ? this.grandTotal : (this.uangMuka || 0);

            this.sedangMenyimpan = true;

            try {
                const respon = await fetch('/admin/kasir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        customer_id: this.customerId,
                        items: this.keranjang.map(i => ({ 
                            kode: i.kode, 
                            qty: i.qty, 
                            harga: i.harga,
                            diskon: i.diskon
                        })),
                        diskon: this.diskonNotaNominal,
                        bayar: bayarFinal,
                        uang_muka: this.uangMuka,
                        metode_pembayaran: this.jenisBayar,
                    })
                });

                const hasil = await respon.json();

                if (hasil.sukses) {
                    window.toastSukses(hasil.pesan);

                    if (this.printerStrukAktif && hasil.cetak_url) {
                        window.open(hasil.cetak_url, '_blank');
                    }
                    if (this.printerFakturAktif && hasil.nomor_nota) {
                        window.open(`{{ url('/admin/cetak/faktur') }}/${hasil.nomor_nota}`, '_blank');
                    }

                    window.Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        text: `Nota ${hasil.nomor_nota} telah disimpan.`,
                        html: `
                            <div class="space-y-3 text-center my-3">
                                <p class="text-sm text-steel">Nomor Nota: <strong class="text-ink font-mono">${hasil.nomor_nota}</strong></p>
                                <div class="flex justify-center gap-2 pt-2">
                                    <a href="${hasil.cetak_url}" target="_blank" class="px-3.5 py-2 bg-rajawali text-white font-bold rounded-lg hover:bg-rajawali-dark text-xs transition">🖨️ Cetak Struk (80mm)</a>
                                    <a href="{{ url('/admin/cetak/faktur') }}/${hasil.nomor_nota}" target="_blank" class="px-3.5 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 text-xs transition">📑 Cetak Faktur (A5 NCR)</a>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Transaksi Baru',
                        cancelButtonColor: '#64748b',
                    });

                    this.keranjang = [];
                    this.diskonNotaValue = '';
                    this.bayar = 0;
                    this.uangMuka = 0;
                    this.barisAktif = null;
                    this.hargaTerakhirInfo = null;
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
            const key = e.key;

            if (key === 'F2') {
                e.preventDefault();
                e.stopPropagation();
                this.bukaModalCari();
                return;
            }
            if (key === 'F6') {
                e.preventDefault();
                e.stopPropagation();
                this.$dispatch('buka-modal', { name: 'diskon-nota' });
                return;
            }
            if (key === 'F8') {
                e.preventDefault();
                e.stopPropagation();
                this.kosongkanKeranjang();
                return;
            }
            if (key === 'F12' || (e.ctrlKey && key === 'Enter')) {
                e.preventDefault();
                e.stopPropagation();
                this.simpanNota();
                return;
            }

            if (this.modalTerbuka > 0 && key !== 'Escape') return;

            if (key === 'Delete' && this.barisAktif !== null) {
                this.keranjang.splice(this.barisAktif, 1);
                this.barisAktif = null;
                this.hargaTerakhirInfo = null;
            }
            if (key === 'ArrowDown' && document.activeElement === this.$refs.barcode) {
                this.barisAktif = this.barisAktif === null ? 0 : Math.min(this.barisAktif + 1, this.keranjang.length - 1);
            }
            if (key === 'ArrowUp' && document.activeElement === this.$refs.barcode) {
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
            return Math.max(this.subtotal - this.diskonNotaNominal, 0);
        },
        get kembalian() {
            return 0;
        },
    };
}
</script>
