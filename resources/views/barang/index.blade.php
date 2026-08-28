<x-app-layout title="Master Barang">
@php $peranSaya = auth()->user()?->peran ?? 'owner'; @endphp
<div x-data="formBarang({{ in_array($peranSaya, ['owner', 'admin'], true) ? 'true' : 'false' }})" class="space-y-4 -m-3 p-3">

    <!-- Filter & Action Bar -->
    <form method="GET" action="{{ route('barang.index') }}">
        <x-filter-bar>
            <x-input name="cari" value="{{ $filter['cari'] ?? '' }}" label="Cari" placeholder="Kode / nama / barcode" class="min-w-56" />
            <x-select name="group_id" label="Group">
                <option value="">Semua Group</option>
                @foreach($groupList as $g)
                    <option value="{{ $g->id }}" @selected(($filter['group_id'] ?? '') == $g->id)>{{ $g->nama }}</option>
                @endforeach
            </x-select>
            <label class="flex items-center gap-2 text-xs font-semibold text-ink self-end pb-2 cursor-pointer">
                <input type="checkbox" name="stok_menipis" value="1" @checked($filter['stok_menipis'] ?? false) class="rounded border-line text-rajawali focus:ring-rajawali">
                Stok menipis saja
            </label>
            <div class="self-end flex gap-2">
                <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
                <a href="{{ route('barang.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs inline-flex items-center gap-1 transition">Reset</a>
            </div>
            <div class="ml-auto self-end flex gap-2">
                <a href="{{ route('laporan.pdf', 'daftar-barang') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-bold transition">
                    <x-icon name="printer" class="w-4 h-4 text-red-600" /> Cetak PDF
                </a>
                <x-button type="button" variant="primary" x-on:click="tambah()"><x-icon name="plus" class="w-4 h-4" /> Tambah Barang</x-button>
            </div>
        </x-filter-bar>
    </form>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
        @forelse($barang as $b)
            @php $stokBarang = $stok[$b->id] ?? 0; @endphp
            <div class="bg-surface rounded-xl border border-line p-3.5 space-y-2.5">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="font-mono text-xs font-bold text-rajawali">{{ $b->kode }}</span>
                        <h4 class="font-bold text-ink text-sm">{{ $b->nama }}</h4>
                        <div class="text-xs text-steel flex gap-2 mt-0.5">
                            <span>{{ $b->group->nama }}</span>
                            <span>&bull;</span>
                            <span>{{ $b->satuan->nama }}</span>
                        </div>
                    </div>
                    <x-badge :status="$b->aktif ? 'lunas' : 'batal'">{{ $b->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge>
                </div>
                <div class="grid grid-cols-3 gap-2 text-xs border-t border-line/60 pt-2">
                    <div>
                        <span class="text-steel block">Stok Tersedia:</span>
                        <span class="font-mono font-bold {{ $stokBarang <= (float) $b->stok_minimum ? 'text-rajawali' : 'text-ink' }}">{{ rtrim(rtrim(number_format($stokBarang, 3, ',', ''), '0'), ',') }}</span>
                    </div>
                    <div>
                        <span class="text-steel block">Harga Eceran:</span>
                        <span class="font-mono font-bold text-ink">Rp {{ number_format($b->harga_eceran, 0, ',', '.') }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-steel block">Harga Grosir:</span>
                        <span class="font-mono font-bold text-ink">Rp {{ number_format($b->harga_grosir, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="border-t border-line/60 pt-2 flex justify-end gap-2">
                    <button type="button" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1"
                        x-on:click="ubah(@js(collect($b->toArray())->only(['id','kode','nama','group_id','sub_group_id','satuan_id','hpp','harga_eceran','harga_grosir','stok_minimum','lokasi_rak'])), @js($b->barcodes->first()?->barcode ?? ''), {{ (float)$stokBarang }})">
                        <x-icon name="pencil" class="w-3.5 h-3.5" /> Ubah
                    </button>
                    <button type="button" class="px-3 py-1.5 rounded-lg border border-line text-xs font-semibold text-ink hover:bg-canvas flex items-center gap-1"
                        x-on:click="kelolaBarcode({{ $b->id }}, @js($b->kode), @js($b->nama), {{ (float)$b->harga_eceran }}, @js($b->barcodes->map(fn($x) => ['id' => $x->id, 'barcode' => $x->barcode, 'utama' => $x->utama])))">
                        <x-icon name="barcode" class="w-3.5 h-3.5" /> Barcode &amp; QR
                    </button>
                    <form method="POST" action="{{ route('barang.toggle-aktif', $b) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $b->aktif ? 'true' : 'false' }}, @js($b->nama))">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rajawali/10 text-rajawali text-xs font-semibold hover:bg-rajawali/20 flex items-center gap-1">
                            <x-icon :name="$b->aktif ? 'ban' : 'check'" class="w-3.5 h-3.5" /> {{ $b->aktif ? 'Nonaktif' : 'Aktif' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <x-empty-state icon="package" judul="Belum ada barang" />
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <x-card :padded="false" class="hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                    <tr>
                        <th class="text-left font-semibold px-4 py-2.5">Kode</th>
                        <th class="text-left font-semibold px-4 py-2.5">Nama</th>
                        <th class="text-left font-semibold px-4 py-2.5">Group</th>
                        <th class="text-left font-semibold px-4 py-2.5">Satuan</th>
                        <th class="text-right font-semibold px-4 py-2.5">Stok</th>
                        <th class="text-right font-semibold px-4 py-2.5">Eceran</th>
                        <th class="text-right font-semibold px-4 py-2.5">Grosir</th>
                        <th class="text-left font-semibold px-4 py-2.5">Status</th>
                        <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barang as $b)
                        @php $stokBarang = $stok[$b->id] ?? 0; @endphp
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $b->kode }}</td>
                            <td class="px-4 py-2.5 font-medium">{{ $b->nama }}</td>
                            <td class="px-4 py-2.5 text-steel">{{ $b->group->nama }}</td>
                            <td class="px-4 py-2.5 text-steel">{{ $b->satuan->nama }}</td>
                            <td class="px-4 py-2.5 text-right font-mono {{ $stokBarang <= (float) $b->stok_minimum ? 'text-rajawali font-semibold' : '' }}">{{ rtrim(rtrim(number_format($stokBarang, 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono">Rp {{ number_format($b->harga_eceran, 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5 text-right font-mono">Rp {{ number_format($b->harga_grosir, 0, ',', '.') }}</td>
                            <td class="px-4 py-2.5">
                                <x-badge :status="$b->aktif ? 'lunas' : 'batal'">{{ $b->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="flex justify-end gap-1">
                                    <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas cursor-pointer" data-tooltip="Ubah Data"
                                        x-on:click="ubah(@js(collect($b->toArray())->only(['id','kode','nama','group_id','sub_group_id','satuan_id','hpp','harga_eceran','harga_grosir','stok_minimum','lokasi_rak'])), @js($b->barcodes->first()?->barcode ?? ''), {{ (float)$stokBarang }})">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas cursor-pointer" data-tooltip="Barcode &amp; QR Code"
                                        x-on:click="kelolaBarcode({{ $b->id }}, @js($b->kode), @js($b->nama), {{ (float)$b->harga_eceran }}, @js($b->barcodes->map(fn($x) => ['id' => $x->id, 'barcode' => $x->barcode, 'utama' => $x->utama])))">
                                        <x-icon name="barcode" class="w-4 h-4 text-slate-700" />
                                    </button>
                                    <form method="POST" action="{{ route('barang.toggle-aktif', $b) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $b->aktif ? 'true' : 'false' }}, @js($b->nama))">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="p-1.5 rounded-md text-steel hover:text-rajawali hover:bg-rajawali/5 cursor-pointer" data-tooltip="{{ $b->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <x-icon :name="$b->aktif ? 'ban' : 'check'" class="w-4 h-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9"><x-empty-state icon="package" judul="Belum ada barang" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination :paginator="$barang" />
    </x-card>

    {{-- MODAL TAMBAH & EDIT BARANG --}}
    <x-modal name="form-barang" title="Master Data Barang &amp; Sparepart" wide>
        <form method="POST" x-bind:action="modeEdit ? urlUpdate : '{{ route('barang.store') }}'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <template x-if="modeEdit">
                <div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_editing_id" x-bind:value="idSedangDiubah">
                </div>
            </template>

            <x-input label="Kode Barang (Unik)" name="kode" x-model="form.kode" placeholder="cth. DISVCBSTK" required />
            <x-input label="Nama Barang / Sparepart" name="nama" x-model="form.nama" placeholder="cth. DISC PAD VARIO CBS" required />

            {{-- FIELD BARCODE DENGAN SCANNER KAMERA & SAMAKAN KODE / TIMPA --}}
            <div class="col-span-1 sm:col-span-2 bg-amber-50/70 p-3 rounded-2xl border border-amber-200/80">
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-amber-950">Barcode / QR Code Kemasan (Opsional)</label>
                    <template x-if="modeEdit && form.barcode">
                        <span class="text-[11px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md">Bisa di-scan ulang untuk menimpa</span>
                    </template>
                </div>
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <input
                        type="text"
                        name="barcode"
                        x-model="form.barcode"
                        placeholder="Scan scanner tembak ke kemasan, ketik, atau klik Scan Kamera..."
                        class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3.5 py-2.5 bg-white focus:ring-2 focus:ring-rajawali focus:outline-none"
                    >
                    <button
                        type="button"
                        class="px-3.5 py-2.5 bg-rajawali hover:bg-rajawali-dark text-white text-xs font-bold rounded-xl whitespace-nowrap transition cursor-pointer flex items-center gap-1.5 shadow-xs shrink-0"
                        x-on:click="bukaScannerKameraForm()"
                    >
                        <x-icon name="camera" class="w-4 h-4" />
                        <span>Scan Kamera</span>
                    </button>
                    <button
                        type="button"
                        class="px-3 py-2.5 bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold rounded-xl whitespace-nowrap transition cursor-pointer shadow-xs shrink-0"
                        x-on:click="form.barcode = form.kode"
                        title="Samakan Barcode dengan Kode Barang"
                    >
                        Samakan Kode
                    </button>
                    <button
                        type="button"
                        x-show="form.barcode"
                        class="px-2.5 py-2.5 bg-white hover:bg-red-50 border border-red-200 text-red-600 text-xs font-bold rounded-xl whitespace-nowrap transition cursor-pointer shrink-0"
                        x-on:click="form.barcode = ''"
                        title="Kosongkan Barcode"
                    >
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>
                <p class="text-[11px] text-amber-800 font-medium mt-1.5 flex items-center gap-1">
                    <x-icon name="info" class="w-3.5 h-3.5 shrink-0" />
                    <span>Jika produk memiliki barcode di kardus/botol, cukup tembak barcode tersebut atau klik tombol Scan Kamera di atas. Form ini opsional.</span>
                </p>
            </div>

            <x-select label="Group Kategori" name="group_id" x-model="form.group_id" required>
                <option value="">Pilih Group</option>
                @foreach($groupList as $g)
                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                @endforeach
            </x-select>

            <x-select label="Sub Group" name="sub_group_id" x-model="form.sub_group_id">
                <option value="">Tanpa Sub Group</option>
                @foreach($subGroupList as $sg)
                    <option value="{{ $sg->id }}">{{ $sg->nama }}</option>
                @endforeach
            </x-select>

            <x-select label="Satuan Unit" name="satuan_id" x-model="form.satuan_id" required>
                <option value="">Pilih Satuan</option>
                @foreach($satuanList as $st)
                    <option value="{{ $st->id }}">{{ $st->nama }}</option>
                @endforeach
            </x-select>

            @if(in_array($peranSaya, ['owner', 'admin'], true))
                <x-input label="HPP (Modal Kulakan Beli)" name="hpp" type="number" min="0" mono x-model="form.hpp" required />
            @endif

            <x-input label="Harga Eceran Standar (Rp)" name="harga_eceran" type="number" min="0" mono x-model="form.harga_eceran" required />
            <x-input label="Harga Grosir Default (Rp)" name="harga_grosir" type="number" min="0" mono x-model="form.harga_grosir" required />

            <div class="col-span-1 sm:col-span-2 border-t border-slate-200 pt-3 mt-1">
                <h4 class="font-bold text-xs text-steel uppercase tracking-wider mb-2 flex items-center gap-1">
                    <x-icon name="tags" class="w-3.5 h-3.5 text-rajawali" /> Setting Grosir Bertingkat (Otomatis Sesuai Qty Pembelian)
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-slate-700 block">Tier 1: Semi-Grosir (Pembelian Sedang)</span>
                        <div class="grid grid-cols-2 gap-2">
                            <x-input label="Min Qty" name="min_qty_grosir_1" type="number" min="1" step="1" mono x-model="form.min_qty_grosir_1" placeholder="3" />
                            <x-input label="Harga (Rp)" name="harga_grosir_1" type="number" min="0" mono x-model="form.harga_grosir_1" placeholder="0" />
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-slate-700 block">Tier 2: Grosir Karton (Pembelian Besar)</span>
                        <div class="grid grid-cols-2 gap-2">
                            <x-input label="Min Qty" name="min_qty_grosir_2" type="number" min="1" step="1" mono x-model="form.min_qty_grosir_2" placeholder="24" />
                            <x-input label="Harga (Rp)" name="harga_grosir_2" type="number" min="0" mono x-model="form.harga_grosir_2" placeholder="0" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- PENGATURAN STOK AWAL / PENYESUAIAN & STOK MINIMUM --}}
            <div class="col-span-1 sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                <div>
                    <template x-if="!modeEdit">
                        <div>
                            <x-input label="Stok Awal Fisik (Saat Ini)" name="stok_awal" type="number" step="0.001" min="0" mono x-model="form.stok_awal" placeholder="0" />
                            <p class="text-[10px] text-slate-500 mt-1">Jumlah unit fisik yang sudah tersedia di toko/rak saat ini.</p>
                        </div>
                    </template>
                    <template x-if="modeEdit">
                        <div>
                            <x-input label="Penyesuaian Stok Fisik" name="stok_saat_ini" type="number" step="0.001" min="0" mono x-model="form.stok_saat_ini" />
                            <p class="text-[10px] text-amber-700 font-semibold mt-1">Stok fisik saat ini. Ubah nilai ini jika ada selisih stok.</p>
                        </div>
                    </template>
                </div>
                <div>
                    <x-input label="Batas Stok Minimum (Peringatan)" name="stok_minimum" type="number" step="0.001" min="0" mono x-model="form.stok_minimum" required />
                    <p class="text-[10px] text-slate-500 mt-1">Jika sisa stok &le; angka ini, sistem menandai <b>Stok Menipis</b> (merah).</p>
                </div>
            </div>

            <x-input label="Lokasi Rak Gudang" name="lokasi_rak" x-model="form.lokasi_rak" placeholder="cth. A-12" class="col-span-1 sm:col-span-2" />

            <div class="col-span-1 sm:col-span-2 flex justify-end gap-2 mt-2 pt-3 border-t border-slate-200">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'form-barang' })">Batal</x-button>
                <x-button type="submit" variant="primary">
                    <x-icon name="check" class="w-4 h-4" /> Simpan Data Barang
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL SCANNER KAMERA UNTUK FORM BARANG --}}
    <x-modal name="scan-kamera-barang" title="Scanner Kamera (Barcode &amp; QR Code)">
        <div class="space-y-3.5 text-center">
            <div class="relative w-full max-w-sm mx-auto overflow-hidden rounded-2xl border-2 transition-all duration-300 bg-black min-h-56 flex items-center justify-center"
                 :class="statusScanKameraForm === 'sukses' ? 'border-emerald-500 ring-4 ring-emerald-500/30' : 'border-slate-700'">
                
                <div id="html5-qr-code-reader-barang" class="w-full"></div>

                <!-- Laser scanning animation overlay -->
                <div x-show="kameraFormAktif && statusScanKameraForm === 'scanning'" class="absolute inset-0 pointer-events-none flex flex-col justify-center items-center">
                    <div class="w-52 h-36 border-2 border-red-500/70 rounded-xl relative overflow-hidden shadow-inner">
                        <div class="absolute inset-x-0 h-0.5 bg-red-500 shadow-[0_0_10px_#ef4444] animate-bounce"></div>
                    </div>
                </div>

                <!-- Success Overlay -->
                <div x-show="statusScanKameraForm === 'sukses'" class="absolute inset-0 bg-emerald-950/90 backdrop-blur-xs flex flex-col items-center justify-center text-white p-4 transition-all">
                    <div class="w-12 h-12 rounded-full bg-emerald-500 flex items-center justify-center text-white mb-2 shadow-lg animate-pulse">
                        <x-icon name="check" class="w-6 h-6 stroke-[3]" />
                    </div>
                    <span class="text-xs font-bold text-emerald-200 uppercase tracking-wide">BERHASIL TERDETEKSI!</span>
                    <p class="font-mono text-xs font-black text-white mt-1 break-all line-clamp-2" x-text="hasilScanFormTerakhir"></p>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 text-xs font-bold min-h-6" :class="statusScanKameraForm === 'sukses' ? 'text-emerald-600' : 'text-slate-600'">
                <template x-if="statusScanKameraForm === 'scanning'">
                    <span class="flex items-center gap-1.5 animate-pulse text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Arahkan kamera ke Barcode (Garis) atau QR Code produk...
                    </span>
                </template>
                <template x-if="statusScanKameraForm === 'sukses'">
                    <span class="flex items-center gap-1.5 text-emerald-700">
                        <x-icon name="check" class="w-4 h-4 text-emerald-600" /> Mengisi kolom barcode produk...
                    </span>
                </template>
            </div>

            <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'scan-kamera-barang' }); stopKameraForm()">Tutup Kamera</x-button>
        </div>
    </x-modal>

    {{-- MODAL LIHAT & CETAK BARCODE / QR CODE --}}
    <x-modal name="modal-barcode" title="Lihat &amp; Cetak Barcode / QR Code" wide>
        <div class="space-y-4">
            <!-- Header Info Produk -->
            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200 flex justify-between items-center flex-wrap gap-2">
                <div>
                    <span class="font-mono text-xs font-black text-rajawali" x-text="kodeBarangAktif"></span>
                    <h3 class="font-black text-slate-900 text-base" x-text="namaBarangAktif"></h3>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500 font-bold block">Harga Eceran</span>
                    <span class="font-mono font-black text-emerald-700 text-base" x-text="formatRp(hargaBarangAktif)"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kolom Kiri: Pratinjau Visual Barcode & QR -->
                <div class="bg-white p-4 rounded-2xl border-2 border-slate-200 text-center flex flex-col items-center justify-center min-h-64 shadow-xs">
                    <div class="inline-flex rounded-xl border border-slate-200 bg-slate-100 p-1 text-xs font-bold mb-3">
                        <button
                            type="button"
                            x-on:click="tipePratinjau = 'barcode'; renderVisual()"
                            :class="tipePratinjau === 'barcode' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500'"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer"
                        >
                            Barcode (Garis 1D)
                        </button>
                        <button
                            type="button"
                            x-on:click="tipePratinjau = 'qr'; renderVisual()"
                            :class="tipePratinjau === 'qr' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500'"
                            class="px-3 py-1.5 rounded-lg transition cursor-pointer"
                        >
                            QR Code (Kotak 2D)
                        </button>
                    </div>

                    <div class="w-full flex items-center justify-center p-2 min-h-32">
                        <div x-show="tipePratinjau === 'barcode'">
                            <svg id="visual-barcode-target" class="max-w-full h-auto"></svg>
                        </div>
                        <div x-show="tipePratinjau === 'qr'" class="flex justify-center">
                            <div id="visual-qr-target" class="p-2 bg-white"></div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 font-mono font-bold mt-2" x-text="'Kode Aktif: ' + (barcodeUtamaAktif || kodeBarangAktif)"></p>
                </div>

                <!-- Kolom Kanan: Pengaturan Cetak Stiker & Kelola Nomor -->
                <div class="space-y-4">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                        <h4 class="font-black text-xs text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak Stiker Label Rak / Produk
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-2.5 text-xs font-bold">
                            <div>
                                <label class="text-slate-600 block mb-1">Format Stiker</label>
                                <select x-model="tipeLabelCetak" class="w-full text-xs font-bold rounded-xl border border-slate-300 p-2 bg-white focus:ring-2 focus:ring-rajawali focus:outline-none">
                                    <option value="barcode">Barcode Garis (1D)</option>
                                    <option value="qr">QR Code (2D)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-slate-600 block mb-1">Jumlah Stiker</label>
                                <input type="number" x-model.number="jumlahLabelCetak" min="1" max="100" class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 p-2 bg-white focus:ring-2 focus:ring-rajawali focus:outline-none">
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="cetakStiker()"
                            class="w-full py-2.5 bg-rajawali hover:bg-rajawali-dark text-white rounded-xl font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition cursor-pointer"
                        >
                            <x-icon name="printer" class="w-4 h-4" />
                            <span>Buka Pratinjau &amp; Cetak Stiker</span>
                        </button>
                    </div>

                    <!-- Daftar Barcode Terdaftar -->
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-700 uppercase block">Daftar Barcode / Alias Terdaftar</label>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto">
                            <template x-for="b in daftarBarcode" :key="b.id">
                                <div class="flex items-center justify-between border border-slate-200 rounded-xl px-3 py-1.5 bg-white text-xs">
                                    <span class="font-mono font-bold text-slate-800" x-text="b.barcode"></span>
                                    <template x-if="b.utama">
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-black text-[10px]">Utama</span>
                                    </template>
                                    <template x-if="!b.utama">
                                        <form method="POST" x-bind:action="`{{ url('/admin/barang') }}/${barangIdAktif}/barcode/${b.id}/utama`">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-[11px] font-bold text-blue-600 hover:underline cursor-pointer">Jadikan utama</button>
                                        </form>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Form Tambah Barcode Baru -->
                        <form method="POST" x-bind:action="`{{ url('/admin/barang') }}/${barangIdAktif}/barcode`" class="pt-1">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="barcode" placeholder="Scan barcode tambahan..." class="w-full text-xs font-mono font-bold rounded-xl border border-slate-300 px-3 py-2 bg-white focus:ring-2 focus:ring-rajawali focus:outline-none" required>
                                <button type="submit" class="px-3.5 py-2 bg-slate-800 hover:bg-black text-white text-xs font-bold rounded-xl whitespace-nowrap transition cursor-pointer">
                                    + Tambah
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-200">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'modal-barcode' })">Tutup</x-button>
            </div>
        </div>
    </x-modal>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
function formBarang(adalahOwner) {
    return {
        modeEdit: false,
        adalahOwner: adalahOwner,
        form: {
            kode: '', nama: '', barcode: '', group_id: '', sub_group_id: '', satuan_id: '', hpp: 0, harga_eceran: 0, harga_grosir: 0,
            min_qty_grosir_1: 3, harga_grosir_1: 0, min_qty_grosir_2: 24, harga_grosir_2: 0,
            stok_awal: 0, stok_saat_ini: 0, stok_minimum: 0, lokasi_rak: ''
        },
        urlUpdate: '', idSedangDiubah: null,
        daftarBarcode: [],
        barangIdAktif: null,
        kodeBarangAktif: '',
        namaBarangAktif: '',
        hargaBarangAktif: 0,
        barcodeUtamaAktif: '',
        tipePratinjau: 'barcode',
        tipeLabelCetak: 'barcode',
        jumlahLabelCetak: 1,

        html5QrCodeForm: null,
        kameraFormAktif: false,
        statusScanKameraForm: 'idle',
        hasilScanFormTerakhir: '',

        formatRp(val) {
            return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
        },

        tambah() {
            this.modeEdit = false;
            this.form = {
                kode: 'BRG-' + Math.floor(10000 + Math.random() * 90000),
                nama: '',
                barcode: '',
                group_id: '',
                sub_group_id: '',
                satuan_id: '',
                hpp: 0,
                harga_eceran: 0,
                harga_grosir: 0,
                min_qty_grosir_1: 3,
                harga_grosir_1: 0,
                min_qty_grosir_2: 24,
                harga_grosir_2: 0,
                stok_awal: 0,
                stok_saat_ini: 0,
                stok_minimum: 5,
                lokasi_rak: ''
            };
            this.$dispatch('buka-modal', { name: 'form-barang' });
        },

        ubah(data, barcodePertama, stokFisik) {
            this.modeEdit = true;
            this.form = {
                kode: data.kode,
                nama: data.nama,
                barcode: barcodePertama || '',
                group_id: data.group_id ?? '',
                sub_group_id: data.sub_group_id ?? '',
                satuan_id: data.satuan_id ?? '',
                hpp: data.hpp ?? 0,
                harga_eceran: data.harga_eceran,
                harga_grosir: data.harga_grosir,
                min_qty_grosir_1: data.min_qty_grosir_1 ?? 3,
                harga_grosir_1: data.harga_grosir_1 ?? 0,
                min_qty_grosir_2: data.min_qty_grosir_2 ?? 24,
                harga_grosir_2: data.harga_grosir_2 ?? 0,
                stok_awal: 0,
                stok_saat_ini: stokFisik ?? 0,
                stok_minimum: data.stok_minimum,
                lokasi_rak: data.lokasi_rak ?? '',
            };
            this.idSedangDiubah = data.id;
            this.urlUpdate = `{{ url('/admin/barang') }}/${data.id}`;
            this.$dispatch('buka-modal', { name: 'form-barang' });
        },

        bukaScannerKameraForm() {
            this.statusScanKameraForm = 'idle';
            this.hasilScanFormTerakhir = '';
            this.$dispatch('buka-modal', { name: 'scan-kamera-barang' });
            setTimeout(() => this.mulaiKameraForm(), 300);
        },

        mulaiKameraForm() {
            if (typeof Html5Qrcode === 'undefined') {
                const el = document.getElementById('html5-qr-code-reader-barang');
                if (el) el.innerHTML = '<div class="p-4 text-xs text-amber-600 bg-amber-50 rounded-xl font-bold">Pustaka scanner kamera sedang dimuat...</div>';
                return;
            }
            if (this.html5QrCodeForm) this.stopKameraForm();
            try {
                this.statusScanKameraForm = 'scanning';
                this.html5QrCodeForm = new Html5Qrcode("html5-qr-code-reader-barang");
                const config = { fps: 15, qrbox: { width: 250, height: 160 } };
                this.html5QrCodeForm.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText) => {
                        if (this.statusScanKameraForm === 'sukses') return;
                        this.statusScanKameraForm = 'sukses';
                        this.hasilScanFormTerakhir = decodedText;
                        bunyikanBeepSukses();
                        this.form.barcode = decodedText.trim();
                        setTimeout(() => {
                            this.stopKameraForm();
                            this.$dispatch('tutup-modal', { name: 'scan-kamera-barang' });
                            this.statusScanKameraForm = 'idle';
                            if (window.toastSukses) window.toastSukses('Barcode berhasil terbaca: ' + decodedText);
                        }, 500);
                    },
                    () => {}
                ).then(() => {
                    this.kameraFormAktif = true;
                }).catch((err) => {
                    this.kameraFormAktif = false;
                    this.statusScanKameraForm = 'idle';
                    const el = document.getElementById('html5-qr-code-reader-barang');
                    if (el) el.innerHTML = '<div class="p-4 text-xs text-red-600 bg-red-50 rounded-xl font-bold">Kamera tidak dapat diakses. Pastikan izin kamera telah diizinkan pada browser.</div>';
                });
            } catch (e) {
                console.error(e);
            }
        },

        stopKameraForm() {
            if (this.html5QrCodeForm && this.kameraFormAktif) {
                this.html5QrCodeForm.stop().then(() => {
                    this.html5QrCodeForm.clear();
                    this.kameraFormAktif = false;
                    this.statusScanKameraForm = 'idle';
                }).catch(() => {
                    this.kameraFormAktif = false;
                    this.statusScanKameraForm = 'idle';
                });
            }
        },

        kelolaBarcode(id, kode, nama, harga, barcodes) {
            this.barangIdAktif = id;
            this.kodeBarangAktif = kode;
            this.namaBarangAktif = nama;
            this.hargaBarangAktif = harga;
            this.daftarBarcode = barcodes;
            const utama = barcodes.find(b => b.utama);
            this.barcodeUtamaAktif = utama ? utama.barcode : (barcodes[0]?.barcode || kode);
            this.$dispatch('buka-modal', { name: 'modal-barcode' });
            setTimeout(() => this.renderVisual(), 200);
        },

        renderVisual() {
            const targetCode = this.barcodeUtamaAktif || this.kodeBarangAktif;
            if (!targetCode) return;

            // Render Barcode
            const svgEl = document.getElementById('visual-barcode-target');
            if (svgEl && typeof JsBarcode !== 'undefined') {
                try {
                    JsBarcode(svgEl, targetCode, {
                        format: "CODE128",
                        width: 1.6,
                        height: 50,
                        displayValue: true,
                        fontSize: 12,
                        margin: 5
                    });
                } catch (e) {
                    console.error("Barcode render error:", e);
                }
            }

            // Render QR Code
            const qrEl = document.getElementById('visual-qr-target');
            if (qrEl && typeof QRCode !== 'undefined') {
                qrEl.innerHTML = '';
                new QRCode(qrEl, {
                    text: targetCode,
                    width: 100,
                    height: 100,
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        },

        cetakStiker() {
            if (!this.barangIdAktif) return;
            const url = `{{ url('/admin/barang') }}/${this.barangIdAktif}/cetak-label?tipe=${this.tipeLabelCetak}&jumlah=${this.jumlahLabelCetak}`;
            window.open(url, '_blank');
        },

        konfirmasiToggle(event, aktif, nama) {
            const form = event.target;
            window.Swal.fire({
                icon: 'warning',
                title: aktif ? `Nonaktifkan ${nama}?` : `Aktifkan ${nama}?`,
                text: aktif ? 'Barang dengan stok tersisa tidak bisa dinonaktifkan.' : '',
                showCancelButton: true,
                confirmButtonText: aktif ? 'Ya, nonaktifkan' : 'Ya, aktifkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B0181C',
                reverseButtons: true,
            }).then(hasil => {
                if (hasil.isConfirmed) form.submit();
            });
        },
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
