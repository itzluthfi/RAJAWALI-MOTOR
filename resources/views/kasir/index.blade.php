<x-app-layout title="Kasir POS Terpadu">
<div
    x-data="kasirPosApp(@js($daftarBarangJson), @js($daftarCustomerJson), @js($montirsJson))"
    x-init="initApp()"
    x-on:keydown.window="tanganiShortcut($event)"
    class="flex flex-col gap-3 -m-3 p-3 min-h-[calc(100vh-4.5rem)]"
>
    {{-- BARIS 1: TOP BAR (KASIR STATUS & TRANSAKSI BARU) --}}
    <div class="flex items-center justify-between flex-wrap gap-2.5 bg-surface p-3 rounded-2xl border border-line shadow-xs">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-rajawali text-white flex items-center justify-center font-black shadow-xs">
                <x-icon name="shopping-bag" class="w-5 h-5" />
            </div>
            <div>
                <h1 class="font-black text-slate-900 text-base leading-tight">Kasir POS &amp; Bengkel Terpadu</h1>
                <p class="text-xs text-steel">Penjualan sparepart langsung &amp; servis motor dalam satu kasir.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                x-on:click="bukaModalCustomerCepat()"
                class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-1.5 transition cursor-pointer shadow-xs"
                data-tooltip="Daftar customer baru secara cepat"
            >
                <x-icon name="user-plus" class="w-3.5 h-3.5" />
                <span>+ Customer Baru</span>
            </button>

            <button
                type="button"
                x-on:click="resetTransaksi()"
                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center gap-1.5 transition cursor-pointer border border-slate-300"
                data-tooltip="Reset kasir & buka transaksi baru"
            >
                <x-icon name="rotate-ccw" class="w-3.5 h-3.5" />
                <span>+ Transaksi Baru</span>
            </button>

            <div class="text-xs text-steel font-mono font-bold px-3 py-2 bg-canvas rounded-xl border border-line hidden sm:block" x-data="{ tgl: '' }" x-init="tgl = new Intl.DateTimeFormat('id-ID', {dateStyle:'medium', timeStyle:'short', timeZone:'Asia/Jakarta'}).format(new Date())">
                <span x-text="tgl"></span> WIB
            </div>
        </div>
    </div>

    {{-- BARIS 2: PANEL TERPADU PELANGGAN & KENDARAAN (SEMUA FIELD OPSIONAL) --}}
    <div class="bg-white p-3 rounded-2xl border-2 border-slate-200 shadow-xs space-y-2">
        <div class="flex flex-wrap lg:flex-nowrap gap-2.5 items-end">
            {{-- PILIH CUSTOMER (SEARCHABLE) --}}
            <div class="w-full sm:w-[calc(50%-5px)] lg:flex-1 min-w-0">
                <label class="block text-[11px] font-black text-slate-600 uppercase mb-1 truncate">Pelanggan / Customer (Opsional)</label>
                <div class="relative" x-data="{ terbuka: false, cari: '' }" x-on:click.outside="terbuka = false">
                    <button
                        type="button"
                        x-on:click="terbuka = !terbuka; if(terbuka) $nextTick(() => $refs.inputCariCust?.focus())"
                        class="w-full flex items-center justify-between rounded-xl border-2 border-slate-300 bg-slate-50 px-3 py-2 text-xs font-bold shadow-xs focus:outline-none focus:ring-2 focus:ring-rajawali hover:bg-white transition"
                    >
                        <div class="flex items-center gap-1.5 truncate">
                            <x-icon name="user" class="w-3.5 h-3.5 text-steel shrink-0" />
                            <span class="truncate text-ink font-black" x-text="customerTerpilih ? customerTerpilih.nama : 'Umum (Tunai)'"></span>
                            <template x-if="customerTerpilih && customerTerpilih.kategori && customerTerpilih.kategori !== 'umum'">
                                <span
                                    :class="customerTerpilih.kategori === 'grosir' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-blue-100 text-blue-800 border-blue-300'"
                                    class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold uppercase shrink-0 border"
                                    x-text="customerTerpilih.kategori"
                                ></span>
                            </template>
                        </div>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 text-steel shrink-0 ml-1" />
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
                        class="absolute left-0 top-full mt-1.5 w-84 rounded-2xl border-2 border-slate-300 bg-white shadow-2xl z-50 overflow-hidden"
                    >
                        <div class="p-2.5 border-b border-slate-100 bg-slate-50 flex items-center gap-2">
                            <x-icon name="search" class="w-4 h-4 text-steel shrink-0 ml-1" />
                            <input
                                x-ref="inputCariCust"
                                type="text"
                                x-model="cari"
                                placeholder="Cari Nama / Plat / Motor / WA..."
                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-rajawali font-bold"
                            >
                            <button x-show="cari" type="button" x-on:click="cari = ''" class="text-steel hover:text-ink text-xs font-bold px-1">
                                <x-icon name="x" class="w-3.5 h-3.5" />
                            </button>
                        </div>

                        <div class="p-2 bg-slate-100/70 border-b border-slate-200 flex justify-between items-center">
                            <span class="text-[11px] text-steel font-bold">Pilih Pelanggan Toko</span>
                            <button
                                type="button"
                                x-on:click="terbuka = false; bukaModalCustomerCepat()"
                                class="text-[11px] font-black text-rajawali hover:underline flex items-center gap-1 cursor-pointer"
                            >
                                <x-icon name="plus" class="w-3 h-3" />
                                <span>+ Customer Baru</span>
                            </button>
                        </div>

                        <div class="max-h-56 overflow-y-auto divide-y divide-slate-100">
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
                                        <template x-if="c.plat || c.motor || c.telepon">
                                            <div class="text-[11px] text-steel font-mono">
                                                <span x-text="c.plat || ''" class="text-rajawali font-bold"></span>
                                                <span x-text="c.motor ? ' (' + c.motor + ')' : ''"></span>
                                                <span x-text="c.telepon ? ' · ' + c.telepon : ''" class="text-slate-500"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <x-icon x-show="customerId == c.id" name="check" class="w-4 h-4 text-rajawali shrink-0" />
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- NO WA / HP (OPSIONAL) --}}
            <div class="w-full sm:w-[calc(50%-5px)] lg:w-44 shrink-0">
                <label class="block text-[11px] font-black text-slate-600 uppercase mb-1 truncate">No. WA / HP (Opsional)</label>
                <input
                    type="text"
                    x-model="teleponCustomer"
                    placeholder="cth. 08123456789"
                    class="w-full text-xs font-mono font-bold bg-slate-50 border-2 border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-rajawali focus:bg-white focus:outline-none"
                >
            </div>

            {{-- PLAT NOMOR (OPSIONAL) --}}
            <div class="w-full sm:w-[calc(50%-5px)] lg:w-36 shrink-0">
                <label class="block text-[11px] font-black text-slate-600 uppercase mb-1 truncate">Plat No (Opsional)</label>
                <input
                    type="text"
                    x-model="platNomor"
                    x-on:input="sinkronPlatKeCustomer()"
                    placeholder="L 1234 ABC"
                    class="w-full text-xs font-mono font-black bg-slate-50 border-2 border-slate-300 rounded-xl px-3 py-2 uppercase focus:ring-2 focus:ring-rajawali focus:bg-white focus:outline-none"
                >
            </div>

            {{-- TIPE MOTOR (OPSIONAL) --}}
            <div class="w-full sm:w-[calc(50%-5px)] lg:w-36 shrink-0">
                <label class="block text-[11px] font-black text-slate-600 uppercase mb-1 truncate">Tipe Motor (Opsional)</label>
                <input
                    type="text"
                    x-model="merkType"
                    placeholder="cth. Vario 125"
                    class="w-full text-xs font-bold bg-slate-50 border-2 border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-rajawali focus:bg-white focus:outline-none"
                >
            </div>

            {{-- MEKANIK / MONTIR (OPSIONAL) --}}
            <div class="w-full sm:w-[calc(50%-5px)] lg:w-44 shrink-0">
                <label class="block text-[11px] font-black text-slate-600 uppercase mb-1 truncate">Mekanik / Montir (Opsional)</label>
                <select
                    x-model="montirId"
                    class="w-full text-xs font-bold bg-slate-50 border-2 border-slate-300 rounded-xl px-2.5 py-2 focus:ring-2 focus:ring-rajawali focus:bg-white focus:outline-none"
                >
                    <option value="">-- Tanpa Montir --</option>
                    <template x-for="m in montirs" :key="m.id">
                        <option :value="m.id" x-text="m.nama + ' (' + m.peran + ')'"></option>
                    </template>
                </select>
            </div>

            {{-- JENIS PEMBAYARAN (TUNAI / TEMPO) --}}
            <div class="w-full sm:w-[calc(50%-5px)] lg:w-36 shrink-0">
                <label class="block text-[11px] font-black text-slate-600 uppercase mb-1 truncate">Jenis Bayar</label>
                <div class="flex items-center rounded-xl border-2 border-slate-300 overflow-hidden text-xs font-black shadow-xs bg-white">
                    <button type="button" x-on:click="jenisBayar = 'tunai'" :class="jenisBayar === 'tunai' ? 'bg-rajawali text-white shadow-xs' : 'bg-white text-steel hover:bg-slate-50'" class="flex-1 py-2 text-center transition cursor-pointer">Tunai</button>
                    <button type="button" x-on:click="jenisBayar = 'tempo'" :class="jenisBayar === 'tempo' ? 'bg-rajawali text-white shadow-xs' : 'bg-white text-steel hover:bg-slate-50'" class="flex-1 py-2 text-center transition cursor-pointer">Tempo</button>
                </div>
            </div>
        </div>

        {{-- TOGGLE KELUHAN / CATATAN KHUSUS SERVIS (EXPANDABLE) --}}
        <div x-data="{ bukaCatatan: false }" class="pt-1 border-t border-slate-100">
            <div class="flex items-center justify-between">
                <button
                    type="button"
                    x-on:click="bukaCatatan = !bukaCatatan"
                    class="text-[11px] font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1 transition cursor-pointer"
                >
                    <x-icon name="file-text" class="w-3.5 h-3.5" />
                    <span x-text="bukaCatatan ? 'Tutup Kolom Catatan / Keluhan' : '+ Tambah Catatan Servis / Keluhan Motor'"></span>
                </button>
                <template x-if="keluhan">
                    <span class="text-[11px] text-blue-700 font-bold font-mono">Ada Catatan Servis</span>
                </template>
            </div>
            <div x-show="bukaCatatan || keluhan" x-cloak class="mt-2">
                <input
                    type="text"
                    x-model="keluhan"
                    placeholder="Tulis keluhan servis, misal: Ganti oli mesin + stel rantai + rem bunyi..."
                    class="w-full text-xs font-medium bg-slate-50 border-2 border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-rajawali focus:bg-white focus:outline-none"
                >
            </div>
        </div>
    </div>

    {{-- BARIS 3: SEARCH BARCODE & LIVE SEARCH (LEBAR & MUDAH DISCAN) --}}
    <div class="flex items-center gap-2">
        <div class="relative flex-1" x-on:click.outside="fokusMainInput = false">
            <x-icon name="scan-barcode" class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-steel" />
            <input
                x-ref="barcode"
                x-model="barcode"
                x-on:focus="fokusMainInput = true"
                x-on:keydown.down.prevent="indeksLive = Math.min(indeksLive + 1, hasilLive.length - 1)"
                x-on:keydown.up.prevent="indeksLive = Math.max(indeksLive - 1, 0)"
                x-on:keydown.enter.prevent="pilihLiveAtauBarcode()"
                type="text"
                autocomplete="off"
                placeholder="Scan barcode fisik / ketik nama barang atau jasa... (Tekan F2 untuk cari)"
                class="w-full rounded-2xl border-2 border-slate-300 bg-white pl-11 pr-4 py-3 text-base font-bold focus:outline-none focus:ring-2 focus:ring-rajawali focus:border-rajawali shadow-xs placeholder:text-sm placeholder:font-medium placeholder:text-steel"
            >

            <!-- Live Search Floating Dropdown Popup -->
            <div
                x-show="barcode.trim().length >= 1 && hasilLive.length > 0"
                x-cloak
                class="absolute left-0 right-0 top-full mt-1.5 bg-white border-2 border-rajawali rounded-2xl shadow-2xl z-50 max-h-80 overflow-y-auto divide-y divide-line"
            >
                <template x-for="(b, idx) in hasilLive" :key="b.id">
                    <div
                        x-on:click="pilihBarangLive(b)"
                        x-on:mouseenter="indeksLive = idx"
                        :class="indeksLive === idx ? 'bg-rajawali/10 font-bold border-l-4 border-rajawali' : 'hover:bg-canvas'"
                        class="p-3 cursor-pointer flex justify-between items-center transition"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-rajawali bg-red-50 px-1.5 py-0.5 rounded border border-red-200" x-text="b.kode"></span>
                                <span class="font-black text-base text-ink" x-text="b.nama"></span>
                                <template x-if="b.is_jasa">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-100 text-blue-800 border border-blue-300 font-bold">JASA</span>
                                </template>
                            </div>
                            <p class="text-xs text-steel mt-0.5">Barcode: <span class="font-mono" x-text="b.barcode || '-'"></span></p>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-black text-base text-ink block" x-text="formatRp(b.harga)"></span>
                            <template x-if="!b.is_jasa">
                                <span class="text-xs font-mono text-steel">Stok: <strong :class="b.stok <= 0 ? 'text-rajawali' : 'text-lunas'" class="text-sm" x-text="b.stok"></strong></span>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <button
            type="button"
            x-on:click="bukaScannerKamera()"
            class="px-4 py-3 bg-rajawali hover:bg-rajawali-dark text-white rounded-2xl font-bold text-xs flex items-center gap-2 shrink-0 transition shadow-xs cursor-pointer"
            data-tooltip="Scan Barcode via Kamera"
        >
            <x-icon name="camera" class="w-4 h-4" />
            <span class="hidden sm:inline">Kamera</span>
        </button>

        <button
            type="button"
            x-on:click="bukaModalCari()"
            class="px-4 py-3 bg-white hover:bg-slate-50 border-2 border-slate-300 text-slate-800 rounded-2xl font-bold text-xs flex items-center gap-1.5 shrink-0 transition shadow-xs cursor-pointer"
            data-tooltip="Buka Katalog Lengkap (F2)"
        >
            <x-icon name="search" class="w-4 h-4 text-steel" />
            <span class="hidden sm:inline">Cari (F2)</span>
        </button>
    </div>

    {{-- BARIS 4: TABEL KERANJANG TRANSAKSI POS (RAPI, BERSIH & PROPORSIONAL) --}}
    <x-card :padded="false" class="flex-1 overflow-hidden flex flex-col shadow-lg border border-slate-200/80">
        <div class="overflow-y-auto flex-1">
            <table class="w-full text-sm">
                <thead class="sticky top-0 bg-slate-100 text-slate-700 text-xs uppercase tracking-wide border-b-2 border-slate-300 font-black">
                    <tr>
                        <th class="text-left px-3 py-3 w-10">No</th>
                        <th class="text-left px-3 py-3 w-28">Kode</th>
                        <th class="text-left px-3 py-3">Nama Barang / Jasa</th>
                        <th class="text-center px-3 py-3 w-24">Qty</th>
                        <th class="text-right px-3 py-3 w-36">Harga Satuan</th>
                        <th class="text-center px-3 py-3 w-24">Disc (%)</th>
                        <th class="text-right px-3 py-3 w-32">Disc (Rp)</th>
                        <th class="text-right px-3 py-3 w-36">Total (Rp)</th>
                        <th class="text-center px-2 py-3 w-12">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <template x-for="(item, idx) in keranjang" :key="item.uid">
                        <tr
                            x-on:click="barisAktif = idx"
                            :class="barisAktif === idx ? 'bg-rajawali/5 font-medium' : 'hover:bg-canvas/60'"
                            class="transition duration-100"
                        >
                            <td class="px-3 py-2.5 text-steel text-sm font-mono font-bold text-center" x-text="idx + 1"></td>
                            <td class="px-3 py-2.5">
                                <span class="font-mono text-xs font-black text-rajawali bg-red-50 px-2 py-0.5 rounded border border-red-200 inline-block" x-text="item.kode"></span>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="font-black text-sm text-slate-900 leading-tight" x-text="item.nama"></div>
                                {{-- KETERANGAN DISKON / TIER PROMO YANG RAPI & BERSIH --}}
                                <template x-if="item.labelTier || (Number(item.hargaOriginal || 0) > Number(item.harga || 0))">
                                    <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                        <template x-if="item.labelTier">
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[10px] font-mono bg-emerald-100 text-emerald-800 font-bold border border-emerald-300">
                                                <x-icon name="sparkles" class="w-3 h-3 text-emerald-600" />
                                                <span x-text="item.labelTier"></span>
                                            </span>
                                        </template>
                                        <template x-if="Number(item.hargaOriginal || 0) > Number(item.harga || 0)">
                                            <span class="text-[11px] text-slate-500 font-medium">
                                                Normal: <span class="line-through font-mono" x-text="formatRp(item.hargaOriginal)"></span>
                                                <span class="text-emerald-700 font-bold">(Hemat <span x-text="formatRp(item.hargaOriginal - item.harga)"></span>/pcs)</span>
                                            </span>
                                        </template>
                                    </div>
                                </template>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <input
                                    type="number"
                                    x-model.number="item.qty"
                                    x-on:focus="$event.target.select()"
                                    x-on:input="ubahQty(idx, item.qty)"
                                    x-on:blur="validasiQty(idx)"
                                    min="0.001"
                                    step="any"
                                    class="w-20 text-center font-mono font-black text-sm bg-white border-2 border-slate-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-rajawali focus:border-rajawali focus:outline-none shadow-xs"
                                >
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold text-ink">
                                <input
                                    type="number"
                                    x-model.number="item.harga"
                                    x-on:focus="$event.target.select()"
                                    x-on:input="ubahHargaManual(idx, item.harga)"
                                    x-on:blur="validasiHarga(idx)"
                                    class="w-28 text-right font-mono font-black text-sm bg-white border-2 border-slate-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-rajawali focus:border-rajawali focus:outline-none shadow-xs"
                                >
                                <template x-if="Number(item.hargaOriginal || 0) > Number(item.harga || 0)">
                                    <span class="text-[10px] text-slate-400 line-through block text-right mt-0.5" x-text="formatRp(item.hargaOriginal)"></span>
                                </template>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                <input
                                    type="number"
                                    x-model.number="item.diskonPersen"
                                    x-on:focus="$event.target.select()"
                                    x-on:input="ubahDiskonPersen(idx, item.diskonPersen)"
                                    min="0"
                                    max="100"
                                    class="w-16 text-center font-mono font-bold text-sm bg-white border-2 border-slate-300 rounded-lg px-1.5 py-1.5 focus:ring-2 focus:ring-rajawali focus:border-rajawali focus:outline-none"
                                >
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-xs">
                                <input
                                    type="number"
                                    x-model.number="item.diskon"
                                    x-on:focus="$event.target.select()"
                                    x-on:input="ubahDiskonNominal(idx, item.diskon)"
                                    min="0"
                                    class="w-24 text-right font-mono font-bold text-sm bg-white border-2 border-slate-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-rajawali focus:border-rajawali focus:outline-none"
                                >
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono">
                                <div class="font-black text-slate-900 text-sm" x-text="formatRp(hitungTotalItem(item))"></div>
                                <template x-if="(Number(item.hargaOriginal || item.harga || 0) * Number(item.qty || 0)) > hitungTotalItem(item)">
                                    <span class="text-[10px] text-emerald-700 font-bold block">
                                        Hemat <span x-text="formatRp(((item.hargaOriginal || item.harga) * item.qty) - hitungTotalItem(item))"></span>
                                    </span>
                                </template>
                            </td>
                            <td class="px-2 py-2.5 text-center">
                                <button
                                    type="button"
                                    x-on:click="hapusBaris(idx)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-rajawali hover:bg-red-50 transition cursor-pointer"
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
                                <div class="max-w-md mx-auto space-y-2">
                                    <div class="w-16 h-16 rounded-2xl bg-canvas flex items-center justify-center mx-auto text-steel/50">
                                        <x-icon name="shopping-cart" class="w-8 h-8" />
                                    </div>
                                    <p class="font-black text-ink text-base">Keranjang Belanja Masih Kosong</p>
                                    <p class="text-xs text-steel">Scan barcode fisik, ketik nama barang/jasa di atas, atau tekan <kbd class="px-1.5 py-0.5 bg-canvas border rounded text-xs font-mono font-bold">F2</kbd> untuk mencari barang.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- FOOTER KASIR: TOTAL & TOMBOL AKSI UTAMA --}}
        <div class="border-t-2 border-line bg-surface p-4 grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
            <div class="flex items-center gap-2 flex-wrap">
                <x-button type="button" variant="secondary" size="sm" x-on:click="bukaModalCari()">
                    <kbd class="text-xs font-black bg-canvas border px-1.5 py-0.5 rounded">F2</kbd> Cari Barang
                </x-button>
                <x-button type="button" variant="secondary" size="sm" x-on:click="$dispatch('buka-modal', { name: 'diskon-nota' })">
                    <kbd class="text-xs font-black bg-canvas border px-1.5 py-0.5 rounded">F6</kbd> Diskon Nota
                </x-button>
                <x-button type="button" variant="secondary" size="sm" x-on:click="kosongkanKeranjang()">
                    <x-icon name="trash" class="w-4 h-4 text-steel" /> Kosongkan
                </x-button>
            </div>

            <div class="p-3.5 bg-canvas rounded-2xl border-2 border-line text-xs space-y-1.5 shadow-inner">
                <div class="flex justify-between font-medium text-sm"><span class="text-steel font-bold">Subtotal:</span> <strong class="font-mono font-black text-slate-800 text-base" x-text="formatRp(subtotal)"></strong></div>
                <template x-if="diskonNotaNominal > 0">
                    <div class="flex justify-between font-medium text-sm text-rajawali"><span>Diskon Nota:</span> <strong class="font-mono font-black text-base" x-text="'- ' + formatRp(diskonNotaNominal)"></strong></div>
                </template>
                <template x-if="totalHemat > 0">
                    <div class="flex justify-between items-center py-1 px-2 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                        <span class="flex items-center gap-1"><x-icon name="sparkles" class="w-3.5 h-3.5 text-emerald-600" /> Total Anda Hemat:</span>
                        <strong class="font-mono font-black text-emerald-700 text-sm" x-text="formatRp(totalHemat)"></strong>
                    </div>
                </template>
                <div class="flex justify-between items-center pt-1.5 border-t-2 border-line">
                    <span class="text-xs font-black text-slate-700 uppercase tracking-wider">GRAND TOTAL:</span>
                    <strong class="font-mono font-black text-2xl lg:text-3xl text-rajawali tracking-tight" x-text="formatRp(grandTotal)"></strong>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 flex-wrap">
                {{-- TOMBOL UTAMA BAYAR LUNAS (SELESAI TRANSAKSI & CETAK NOTA) --}}
                <button
                    type="button"
                    x-on:click="simpanNota()"
                    x-bind:disabled="sedangMenyimpan"
                    class="w-full lg:w-auto px-8 py-3.5 rounded-xl bg-[#B0181C] hover:bg-[#8f1013] text-white font-black text-base flex items-center justify-center gap-2.5 shadow-xl shadow-red-900/20 transition active:scale-98 cursor-pointer"
                >
                    <x-icon name="save" class="w-5 h-5" />
                    <span>Simpan &amp; Bayar Lunas</span>
                    <kbd class="text-xs bg-white/25 px-2 py-0.5 rounded-lg ml-1 font-mono">F12</kbd>
                </button>
            </div>
        </div>
    </x-card>

    {{-- MODAL SCANNER KAMERA DENGAN RETICLE LASER & SUARA BEEP KASIR --}}
    <x-modal name="scan-kamera" title="Scanner Kamera Barcode &amp; QR Code">
        <div class="space-y-3.5 text-center">
            <div class="relative w-full max-w-sm mx-auto overflow-hidden rounded-2xl border-2 transition-all duration-300 bg-black min-h-60 flex items-center justify-center"
                 :class="statusScanKamera === 'sukses' ? 'border-emerald-500 ring-4 ring-emerald-500/30' : 'border-slate-700'">
                
                <div id="html5-qr-code-reader-kasir" class="w-full"></div>

                <!-- Laser scanning animation overlay -->
                <div x-show="kameraAktif && statusScanKamera === 'scanning'" class="absolute inset-0 pointer-events-none flex flex-col justify-center items-center">
                    <div class="w-64 h-36 border-2 border-red-500/70 rounded-xl relative overflow-hidden shadow-inner">
                        <div class="absolute inset-x-0 h-0.5 bg-red-500 shadow-[0_0_10px_#ef4444] animate-bounce"></div>
                    </div>
                </div>

                <!-- Success Overlay -->
                <div x-show="statusScanKamera === 'sukses'" class="absolute inset-0 bg-emerald-950/90 backdrop-blur-xs flex flex-col items-center justify-center text-white p-4 transition-all">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center text-white mb-2 shadow-lg animate-pulse">
                        <x-icon name="check" class="w-6 h-6 stroke-[3]" />
                    </div>
                    <span class="text-xs font-bold text-emerald-200 uppercase tracking-wide">BERHASIL DITEMUKAN!</span>
                    <p class="font-mono text-xs font-black text-white mt-1 break-all line-clamp-2" x-text="hasilScanTerakhir"></p>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 text-xs font-bold min-h-6" :class="statusScanKamera === 'sukses' ? 'text-emerald-600' : 'text-slate-600'">
                <template x-if="statusScanKamera === 'scanning'">
                    <span class="flex items-center gap-1.5 animate-pulse text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Arahkan garis barcode mendatar ke tengah kotak scanner...
                    </span>
                </template>
                <template x-if="statusScanKamera === 'sukses'">
                    <span class="flex items-center gap-1.5 text-emerald-700">
                        <x-icon name="check" class="w-4 h-4 text-emerald-600" /> Memasukkan ke keranjang kasir...
                    </span>
                </template>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-2.5 text-[11px] text-amber-800 text-left space-y-1">
                <p class="font-bold flex items-center gap-1"><x-icon name="lightbulb" class="w-3.5 h-3.5 text-amber-600 shrink-0" /> Tips Scan Barcode Garis (1D):</p>
                <ul class="list-disc list-inside text-slate-600 space-y-0.5 pl-1">
                    <li>Posisikan garis barcode <strong>mendatar (horizontal)</strong> lurus ke kamera.</li>
                    <li>Hindari pantulan <strong>kilau cahaya/lampu (glare)</strong> pada plastik kemasan.</li>
                    <li>Jaga jarak 15–20 cm agar kamera fokus dan tidak buram.</li>
                </ul>
            </div>

            <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'scan-kamera' }); stopKamera()">Tutup Kamera</x-button>
        </div>
    </x-modal>

    {{-- MODAL CARI BARANG (F2) --}}
    <x-modal name="cari-barang" title="Katalog Barang &amp; Jasa (F2)" wide>
        <div class="space-y-3">
            <x-input
                x-ref="inputCariModal"
                x-model="cariQuery"
                label="Cari Cepat"
                placeholder="Ketik kode, nama sparepart, atau jasa..."
            />
            <div class="max-h-96 overflow-y-auto divide-y divide-line border rounded-xl">
                <template x-for="b in daftarBarangFiltered" :key="b.id">
                    <div
                        x-on:click="pilihBarangDariModal(b)"
                        class="p-3 hover:bg-canvas cursor-pointer flex justify-between items-center transition"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-rajawali bg-red-50 px-1.5 py-0.5 rounded border border-red-200" x-text="b.kode"></span>
                                <span class="font-bold text-ink" x-text="b.nama"></span>
                                <template x-if="b.is_jasa">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-100 text-blue-800 border border-blue-300 font-bold">JASA</span>
                                </template>
                            </div>
                            <p class="text-xs text-steel mt-0.5">Barcode: <span class="font-mono" x-text="b.barcode || '-'"></span></p>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-ink block" x-text="formatRp(b.harga)"></span>
                            <span class="text-xs font-mono text-steel">Stok: <strong :class="b.stok <= 0 ? 'text-rajawali' : 'text-lunas'" class="text-sm font-black" x-text="b.stok"></strong></span>
                        </div>
                    </div>
                </template>
                <template x-if="daftarBarangFiltered.length === 0">
                    <div class="p-8 text-center text-steel text-sm font-bold">Barang tidak ditemukan.</div>
                </template>
            </div>
        </div>
    </x-modal>

    {{-- MODAL DISKON NOTA (F6) --}}
    <x-modal name="diskon-nota" title="Diskon Nota (F6)">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-black text-slate-800">Pilih Mode Diskon</label>
                <div class="inline-flex rounded-xl border-2 border-line bg-canvas p-1 text-xs font-mono">
                    <button
                        type="button"
                        x-on:click="diskonNotaMode = 'rp'; validasiDiskonNota()"
                        :class="diskonNotaMode === 'rp' ? 'bg-rajawali text-white font-black shadow-xs' : 'text-steel hover:text-ink'"
                        class="px-4 py-2 rounded-lg transition cursor-pointer font-bold"
                    >Nominal (Rp)</button>
                    <button
                        type="button"
                        x-on:click="diskonNotaMode = 'persen'; validasiDiskonNota()"
                        :class="diskonNotaMode === 'persen' ? 'bg-rajawali text-white font-black shadow-xs' : 'text-steel hover:text-ink'"
                        class="px-4 py-2 rounded-lg transition cursor-pointer font-bold"
                    >Persentase (%)</button>
                </div>
            </div>

            <div>
                <label class="text-xs font-black text-slate-800 uppercase block mb-1" x-text="diskonNotaMode === 'persen' ? 'Diskon Persentase (%)' : 'Diskon Nominal (Rp)'"></label>
                <input
                    type="number"
                    x-model.number="diskonNotaValue"
                    x-on:input="validasiDiskonNota()"
                    min="0"
                    placeholder="Masukkan nilai diskon..."
                    class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-lg font-mono font-black focus:ring-2 focus:ring-rajawali focus:outline-none"
                >
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-line">
                <x-button type="button" variant="primary" x-on:click="$dispatch('tutup-modal', { name: 'diskon-nota' })">
                    Terapkan Diskon
                </x-button>
            </div>
        </div>
    </x-modal>

    {{-- MODAL TAMBAH CUSTOMER CEPAT --}}
    <x-modal name="tambah-customer-cepat" title="Daftarkan Customer Baru">
        <form x-on:submit.prevent="simpanCustomerCepat()" class="space-y-3">
            <div>
                <label class="block text-xs font-black text-slate-800 uppercase mb-1">Nama Customer *</label>
                <input type="text" x-model="formCustomer.nama" placeholder="Contoh: Budi Santoso" required class="w-full text-sm font-bold rounded-xl border-2 border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black text-slate-800 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="text" x-model="formCustomer.telepon" placeholder="08123456789" class="w-full text-sm font-mono font-bold rounded-xl border-2 border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-800 uppercase mb-1">Kategori Harga</label>
                    <select x-model="formCustomer.kategori" class="w-full text-sm font-bold rounded-xl border-2 border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white">
                        <option value="umum">Umum</option>
                        <option value="mitra">Mitra</option>
                        <option value="grosir">Grosir</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black text-slate-800 uppercase mb-1">Plat Nomor (Opsional)</label>
                    <input type="text" x-model="formCustomer.plat_nomor" placeholder="L 1234 ABC" class="w-full text-sm font-mono font-bold uppercase rounded-xl border-2 border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-800 uppercase mb-1">Tipe Motor (Opsional)</label>
                    <input type="text" x-model="formCustomer.jenis_kendaraan" placeholder="Vario 125" class="w-full text-sm font-bold rounded-xl border-2 border-slate-300 px-3.5 py-2.5 focus:ring-2 focus:ring-rajawali focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-line">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'tambah-customer-cepat' })">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <x-icon name="save" class="w-4 h-4" /> Simpan &amp; Pilih
                </x-button>
            </div>
        </form>
    </x-modal>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function kasirPosApp(daftarBarang, dataCustomer, dataMontir) {
    return {
        // Master Data
        daftarBarang: daftarBarang,
        customerList: dataCustomer,
        montirs: dataMontir,

        // Customer & Vehicle Form States (Semua Opsional)
        platNomor: '',
        merkType: '',
        montirId: '',
        keluhan: '',
        teleponCustomer: '',

        // POS States
        customerId: dataCustomer[0]?.id || null,
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
        statusScanKamera: 'idle',
        hasilScanTerakhir: '',

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

        resetTransaksi() {
            this.keranjang = [];
            this.diskonNotaValue = '';
            this.platNomor = '';
            this.merkType = '';
            this.montirId = '';
            this.keluhan = '';
            this.teleponCustomer = '';
            this.customerId = this.customerList[0]?.id || null;
            this.jenisBayar = 'tunai';
            this.$nextTick(() => this.$refs.barcode?.focus());
            if (window.toastSukses) window.toastSukses('Kasir di-reset. Siap untuk transaksi baru.');
        },

        bukaScannerKamera() {
            this.statusScanKamera = 'idle';
            this.hasilScanTerakhir = '';
            this.$dispatch('buka-modal', { name: 'scan-kamera' });
            setTimeout(() => this.mulaiKamera(), 300);
        },

        mulaiKamera() {
            if (typeof Html5Qrcode === 'undefined') {
                const el = document.getElementById('html5-qr-code-reader-kasir');
                if (el) el.innerHTML = '<div class="p-4 text-xs text-amber-600 bg-amber-50 rounded-xl font-bold">Pustaka scanner kamera sedang dimuat...</div>';
                return;
            }
            if (this.html5QrCode) this.stopKamera();
            try {
                this.statusScanKamera = 'scanning';

                const formatsToSupport = (typeof Html5QrcodeSupportedFormats !== 'undefined') ? [
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.UPC_EAN_EXTENSION,
                    Html5QrcodeSupportedFormats.ITF,
                    Html5QrcodeSupportedFormats.QR_CODE
                ] : undefined;

                this.html5QrCode = new Html5Qrcode("html5-qr-code-reader-kasir", {
                    formatsToSupport: formatsToSupport,
                    verbose: false,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true
                    }
                });

                const qrboxFunction = (viewfinderWidth, viewfinderHeight) => {
                    return {
                        width: Math.floor(viewfinderWidth * 0.9),
                        height: Math.floor(viewfinderHeight * 0.65)
                    };
                };

                const config = {
                    fps: 25,
                    qrbox: qrboxFunction,
                    aspectRatio: 1.333334,
                    videoConstraints: {
                        facingMode: "environment",
                        focusMode: "continuous",
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };

                this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        if (this.statusScanKamera === 'sukses') return;
                        this.statusScanKamera = 'sukses';
                        this.hasilScanTerakhir = decodedText;
                        bunyikanBeepSukses();
                        setTimeout(() => {
                            this.barcode = decodedText.trim();
                            this.tambahDariBarcode();
                            this.stopKamera();
                            this.$dispatch('tutup-modal', { name: 'scan-kamera' });
                            this.statusScanKamera = 'idle';
                        }, 400);
                    },
                    () => {}
                ).then(() => {
                    this.kameraAktif = true;
                }).catch((err) => {
                    this.kameraAktif = false;
                    this.statusScanKamera = 'idle';
                    const el = document.getElementById('html5-qr-code-reader-kasir');
                    if (el) el.innerHTML = '<div class="p-4 text-xs text-red-600 bg-red-50 rounded-xl font-bold">Kamera tidak dapat diakses. Pastikan izin kamera diaktifkan.</div>';
                });
            } catch (e) {
                console.error(e);
            }
        },

        stopKamera() {
            if (this.html5QrCode && this.kameraAktif) {
                this.html5QrCode.stop().then(() => {
                    this.html5QrCode.clear();
                    this.kameraAktif = false;
                    this.statusScanKamera = 'idle';
                }).catch(() => {
                    this.kameraAktif = false;
                    this.statusScanKamera = 'idle';
                });
            }
        },

        pilihCustomer(c) {
            this.customerId = c.id;
            if (c.plat) this.platNomor = c.plat;
            if (c.motor && !this.merkType) this.merkType = c.motor;
            if (c.telepon || c.no_wa) this.teleponCustomer = c.telepon || c.no_wa || '';
            this.keranjang.forEach((item) => {
                const barang = this.daftarBarang.find(b => b.kode === item.kode);
                if (barang) {
                    const tier = this.hitungHargaTier(barang, item.qty);
                    item.harga = tier.harga;
                    item.labelTier = tier.label;
                }
            });
        },

        sinkronPlatKeCustomer() {
            if (!this.platNomor) return;
            const match = this.customerList.find(c => c.plat && c.plat.toUpperCase() === this.platNomor.trim().toUpperCase());
            if (match) {
                this.customerId = match.id;
                if (match.motor && !this.merkType) this.merkType = match.motor;
                if ((match.telepon || match.no_wa) && !this.teleponCustomer) {
                    this.teleponCustomer = match.telepon || match.no_wa || '';
                }
            }
        },

        get hasilLive() {
            const q = (this.barcode || '').trim().toLowerCase();
            if (!q) return [];
            return this.daftarBarang.filter(b => 
                (b.barcode && b.barcode.toLowerCase().includes(q)) || 
                (b.all_barcodes && b.all_barcodes.some(bc => bc.toLowerCase().includes(q))) ||
                (b.qrcode && b.qrcode.toLowerCase().includes(q)) ||
                b.kode.toLowerCase().includes(q) || 
                b.nama.toLowerCase().includes(q)
            ).slice(0, 8);
        },

        pilihLiveAtauBarcode() {
            const q = this.barcode.trim();
            if (!q) return;
            if (this.hasilLive.length > 0) {
                const dipilih = this.hasilLive[this.indeksLive] || this.hasilLive[0];
                this.pilihBarangLive(dipilih);
            } else {
                this.tambahDariBarcode();
            }
        },

        pilihBarangLive(b) {
            this.tambahBarangKeKeranjang(b);
            this.barcode = '';
            this.indeksLive = 0;
            this.$nextTick(() => this.$refs.barcode?.focus());
        },

        bukaModalCari() {
            this.cariQuery = '';
            this.$dispatch('buka-modal', { name: 'cari-barang' });
            setTimeout(() => this.$refs.inputCariModal?.focus(), 200);
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
                (b.barcode && b.barcode.toLowerCase().includes(q)) || 
                (b.all_barcodes && b.all_barcodes.some(bc => bc.toLowerCase().includes(q))) ||
                (b.qrcode && b.qrcode.toLowerCase().includes(q)) ||
                b.kode.toLowerCase().includes(q) || 
                b.nama.toLowerCase().includes(q)
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

        get totalHemat() {
            const hematItem = this.keranjang.reduce((acc, item) => {
                const normalSub = (Number(item.hargaOriginal || item.harga || 0) * Number(item.qty || 0));
                const actualSub = this.hitungTotalItem(item);
                return acc + Math.max(0, normalSub - actualSub);
            }, 0);
            return hematItem + Number(this.diskonNotaNominal || 0);
        },

        formatRp(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },

        hitungTotalItem(item) {
            const qty = Number(item.qty) || 0;
            const harga = Number(item.harga) || 0;
            const diskon = Number(item.diskon) || 0;
            return Math.max(0, (qty * harga) - diskon);
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
            let q = this.barcode.trim();
            if (!q) return;

            // Handle format QR Code JSON: {"kode":"..."} atau {"barcode":"..."} atau {"qrcode":"..."}
            if (q.startsWith('{') && q.endsWith('}')) {
                try {
                    const parsed = JSON.parse(q);
                    q = (parsed.barcode || parsed.qrcode || parsed.kode || parsed.id || q).toString().trim();
                } catch {}
            }

            // Handle format URL pada QR Code: https://.../barang/DISVCBSTK
            if (q.includes('/barang/')) {
                const parts = q.split('/barang/');
                if (parts.length > 1) {
                    q = parts[1].split('?')[0].split('/')[0].trim();
                }
            }

            const qLower = q.toLowerCase();
            const rawLower = this.barcode.trim().toLowerCase();

            // 1. Prioritas PERTAMA: Cek Barcode (1D Garis / all_barcodes)
            let barang = this.daftarBarang.find(b => 
                (b.barcode && (b.barcode.toLowerCase() === qLower || b.barcode.toLowerCase() === rawLower)) || 
                (b.all_barcodes && b.all_barcodes.some(bc => bc.toLowerCase() === qLower || bc.toLowerCase() === rawLower))
            );

            // 2. Prioritas KEDUA: Cek QR Code (2D)
            if (!barang) {
                barang = this.daftarBarang.find(b => 
                    b.qrcode && (b.qrcode.toLowerCase() === qLower || b.qrcode.toLowerCase() === rawLower)
                );
            }

            // 3. Prioritas KETIGA: Cek Kode Barang
            if (!barang) {
                barang = this.daftarBarang.find(b => 
                    b.kode && (b.kode.toLowerCase() === qLower || b.kode.toLowerCase() === rawLower)
                );
            }

            // 4. Prioritas KEEMPAT: Cek Nama Barang (Exact match)
            if (!barang) {
                barang = this.daftarBarang.find(b => 
                    b.nama && (b.nama.toLowerCase() === qLower || b.nama.toLowerCase() === rawLower)
                );
            }

            if (!barang) {
                if (window.toastGagal) window.toastGagal(`Item / Barcode "${this.barcode}" tidak ditemukan.`);
                this.barcode = '';
                return;
            }

            this.tambahBarangKeKeranjang(barang);
            this.barcode = '';
            this.indeksLive = 0;
            this.$nextTick(() => this.$refs.barcode?.focus());
            bunyikanBeepSukses();
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

            if (qtyBaru === '' || qtyBaru === null || isNaN(qtyBaru)) {
                return;
            }

            const qty = Math.max(0.001, Number(qtyBaru));
            const barang = this.daftarBarang.find(b => b.kode === item.kode);
            if (barang) {
                const tier = this.hitungHargaTier(barang, qty);
                item.harga = tier.harga;
                item.labelTier = tier.label;
            }
        },

        validasiQty(idx) {
            const item = this.keranjang[idx];
            if (!item) return;
            if (!item.qty || Number(item.qty) <= 0 || isNaN(item.qty)) {
                item.qty = 1;
                this.ubahQty(idx, 1);
            }
        },

        ubahHargaManual(idx, hargaBaru) {
            const item = this.keranjang[idx];
            if (!item) return;
            if (hargaBaru === '' || hargaBaru === null || isNaN(hargaBaru)) return;
            item.harga = Math.max(0, Number(hargaBaru));
        },

        validasiHarga(idx) {
            const item = this.keranjang[idx];
            if (!item) return;
            if (item.harga === '' || item.harga === null || isNaN(item.harga)) {
                item.harga = 0;
            }
        },

        ubahDiskonPersen(idx, persen) {
            const item = this.keranjang[idx];
            if (!item) return;
            const p = Math.min(100, Math.max(0, Number(persen) || 0));
            item.diskonPersen = p;
            item.diskon = Math.round(((Number(item.qty || 0) * Number(item.harga || 0)) * p) / 100);
        },

        ubahDiskonNominal(idx, nominal) {
            const item = this.keranjang[idx];
            if (!item) return;
            const sub = Number(item.qty || 0) * Number(item.harga || 0);
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

        async simpanNota() {
            if (this.sedangMenyimpan) return;
            if (this.keranjang.length === 0) {
                if (window.toastGagal) window.toastGagal('Belum ada barang di keranjang.');
                return;
            }

            this.sedangMenyimpan = true;

            const bayarFinal = this.jenisBayar === 'tunai' ? this.grandTotal : (this.uangMuka || 0);

            // Jika ada jasa/montir/plat gunakan service_langsung, jika tidak penjualan
            const adaJasaAtauMontir = this.keranjang.some(i => i.is_jasa) || this.platNomor || this.montirId;
            const tipe = adaJasaAtauMontir ? 'service_langsung' : 'penjualan';

            const payload = {
                tipe_transaksi: tipe,
                customer_id: this.customerId,
                telepon: this.teleponCustomer,
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
                    this.resetTransaksi();
                } else {
                    if (window.toastGagal) window.toastGagal(data.pesan || 'Gagal memproses transaksi.');
                }
            } catch (err) {
                this.sedangMenyimpan = false;
                if (window.toastGagal) window.toastGagal('Error: ' + err.message);
            }
        },

        bukaModalCustomerCepat() {
            this.formCustomer = {
                nama: '',
                telepon: this.teleponCustomer || '',
                plat_nomor: this.platNomor || '',
                jenis_kendaraan: this.merkType || '',
                kategori: 'umum',
            };
            this.$dispatch('buka-modal', { name: 'tambah-customer-cepat' });
        },

        async simpanCustomerCepat() {
            if (!this.formCustomer.nama.trim()) {
                if (window.toastGagal) window.toastGagal('Nama customer wajib diisi.');
                return;
            }

            try {
                const res = await fetch('/admin/customer/cepat', {
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
                    this.customerList.push({
                        id: data.customer.id,
                        nama: data.customer.nama,
                        plat: data.customer.plat_nomor,
                        motor: data.customer.jenis_kendaraan,
                        kategori: data.customer.kategori,
                        telepon: data.customer.telepon || data.customer.no_wa || '',
                        termin: data.customer.termin_hari || 30
                    });
                    this.pilihCustomer(data.customer);
                    this.$dispatch('tutup-modal', { name: 'tambah-customer-cepat' });
                    if (window.toastSukses) window.toastSukses('Customer baru berhasil didaftarkan.');
                } else {
                    if (window.toastGagal) window.toastGagal(data.pesan || 'Gagal menambahkan customer.');
                }
            } catch (err) {
                if (window.toastGagal) window.toastGagal('Error: ' + err.message);
            }
        },

        async dapatkanHargaTerakhir() {
            if (!this.customerId || this.barisAktif === null || !this.keranjang[this.barisAktif]) {
                this.hargaTerakhirInfo = null;
                return;
            }
            const item = this.keranjang[this.barisAktif];
            try {
                const res = await fetch(`/admin/kasir/harga-terakhir?customer_id=${this.customerId}&kode_barang=${item.kode}`);
                const data = await res.json();
                if (data.sukses && data.ditemukan) {
                    this.hargaTerakhirInfo = data;
                } else {
                    this.hargaTerakhirInfo = null;
                }
            } catch (e) {
                this.hargaTerakhirInfo = null;
            }
        },

        tanganiShortcut(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                this.bukaModalCari();
            } else if (e.key === 'F6') {
                e.preventDefault();
                this.$dispatch('buka-modal', { name: 'diskon-nota' });
            } else if (e.key === 'F12') {
                e.preventDefault();
                this.simpanNota();
            }
        }
    };
}

function bunyikanBeepSukses() {
    try {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (!AudioCtx) return;
        const ctx = new AudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(950, ctx.currentTime);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.12);
    } catch(e) {}
}
</script>
</x-app-layout>
