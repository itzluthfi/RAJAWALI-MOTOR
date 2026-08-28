<x-app-layout title="Pengaturan Web &amp; Profil Toko">
<div class="-m-3 p-3 max-w-4xl space-y-6">
    <div class="flex items-center justify-between pb-3 border-b border-line">
        <div>
            <h2 class="font-display font-black text-xl text-ink">Pengaturan Web &amp; Profil Toko</h2>
            <p class="text-xs text-steel mt-0.5 font-medium">Atur logo visual web, identitas kop nota, pesan struk kasir, hardware printer, dan kebijakan operasional.</p>
        </div>
    </div>

    @if(session('sukses'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-xs">
            <x-icon name="check-circle" class="w-5 h-5 text-emerald-600 shrink-0" />
            <span>{{ session('sukses') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-bold shadow-xs">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('pengaturan.toko.update') }}" enctype="multipart/form-data" class="space-y-6" x-data="{ logoPreview: '{{ $pengaturan->logo_url }}', hapusLogoFlag: false }">
        @csrf

        {{-- 1. IDENTITAS & BRANDING TOKO / WEB --}}
        <x-card class="shadow-sm border border-slate-200/80 p-5 rounded-2xl space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-line text-rajawali">
                <x-icon name="image" class="w-5 h-5" />
                <h3 class="font-display font-black text-sm text-ink uppercase tracking-wide">1. Branding &amp; Tampilan Visual Web</h3>
            </div>

            {{-- UPLOAD LOGO TOKO --}}
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col sm:flex-row items-center gap-5">
                <div class="w-24 h-24 rounded-2xl bg-white border-2 border-slate-300 p-2 flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                    <img :src="logoPreview" alt="Logo Toko" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-1 space-y-2 text-center sm:text-left">
                    <label class="block text-xs font-black text-slate-800 uppercase">Logo Toko &amp; Kop Dokumen</label>
                    <p class="text-xs text-steel font-medium leading-relaxed">
                        Logo ini akan ditampilkan pada navigasi sidebar web, kop faktur penjualan A5, dan lembar tanda terima servis. Format: PNG, JPG, WEBP, atau SVG (Maks. 2MB).
                    </p>
                    <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-start pt-1">
                        <label class="px-3.5 py-2 bg-rajawali/10 text-rajawali hover:bg-rajawali/20 border border-rajawali/20 rounded-xl text-xs font-black cursor-pointer transition flex items-center gap-1.5">
                            <x-icon name="upload" class="w-4 h-4" />
                            <span>Pilih Berkas Logo Baru</span>
                            <input
                                type="file"
                                name="logo"
                                accept="image/*"
                                class="sr-only"
                                x-on:change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        hapusLogoFlag = false;
                                        logoPreview = URL.createObjectURL(file);
                                    }
                                "
                            >
                        </label>
                        @if($pengaturan->logo_path)
                            <button
                                type="button"
                                x-on:click="hapusLogoFlag = true; logoPreview = '{{ asset('images/logo.png') }}'"
                                class="px-3 py-2 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 rounded-xl text-xs font-black transition flex items-center gap-1 cursor-pointer"
                            >
                                <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                <span>Reset ke Logo Default</span>
                            </button>
                        @endif
                        <input type="hidden" name="hapus_logo" :value="hapusLogoFlag ? '1' : '0'">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Nama Toko / Bengkel *" name="nama_toko" value="{{ old('nama_toko', $pengaturan->nama_toko) }}" :error="$errors->first('nama_toko')" required />
                <x-input label="Slogan / Tagline Toko" name="slogan" value="{{ old('slogan', $pengaturan->slogan) }}" placeholder="Contoh: Pusat Sparepart & Servis Terpercaya" />
            </div>
        </x-card>

        {{-- 2. KONTAK & ALAMAT OPERASIONAL --}}
        <x-card class="shadow-sm border border-slate-200/80 p-5 rounded-2xl space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-line text-rajawali">
                <x-icon name="map-pin" class="w-5 h-5" />
                <h3 class="font-display font-black text-sm text-ink uppercase tracking-wide">2. Kontak &amp; Alamat Operasional Dokumen</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Alamat Lengkap Workshop (Kop Dokumen) *" name="alamat" value="{{ old('alamat', $pengaturan->alamat) }}" class="md:col-span-2" required />
                <x-input label="Nomor Telepon / WhatsApp CS Resmi" name="telepon" value="{{ old('telepon', $pengaturan->telepon) }}" placeholder="Contoh: 08123456789" />
                <x-input label="Format Penomoran Nota Penjualan *" name="format_nota" value="{{ old('format_nota', $pengaturan->format_nota) }}" mono required />
            </div>
        </x-card>

        {{-- 3. KUSTOMISASI FOOTER STRUK KASIR --}}
        <x-card class="shadow-sm border border-slate-200/80 p-5 rounded-2xl space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-line text-rajawali">
                <x-icon name="receipt" class="w-5 h-5" />
                <h3 class="font-display font-black text-sm text-ink uppercase tracking-wide">3. Catatan Kaki (Footer) Struk Thermal Kasir</h3>
            </div>

            <div>
                <label class="block text-xs font-black text-slate-800 uppercase mb-1">Pesan Catatan Kaki Struk</label>
                <textarea
                    name="footer_struk"
                    rows="3"
                    placeholder="Contoh: Barang yang sudah dibeli tidak dapat ditukar/dikembalikan kecuali ada garansi resmi. Terima kasih!"
                    class="w-full text-xs font-bold rounded-xl border border-slate-300 p-3 focus:ring-2 focus:ring-rajawali focus:outline-none bg-white"
                >{{ old('footer_struk', $pengaturan->footer_struk) }}</textarea>
                <p class="text-[11px] text-steel mt-1 font-medium">Teks ini akan otomatis dicetak di bagian paling bawah pada struk mini thermal kasir (58mm/80mm).</p>
            </div>
        </x-card>

        {{-- 4. HARDWARE PRINTER --}}
        <x-card class="shadow-sm border border-slate-200/80 p-5 rounded-2xl space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-line text-rajawali">
                <x-icon name="printer" class="w-5 h-5" />
                <h3 class="font-display font-black text-sm text-ink uppercase tracking-wide">4. Integrasi Mesin Printer Kasir (Hardware)</h3>
            </div>

            <div class="space-y-3">
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-rajawali/10 text-rajawali flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="printer" class="w-5 h-5" />
                    </div>
                    <div class="flex-1">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="font-black text-sm text-slate-900">Mesin 1: Printer Thermal Struk Kasir (58mm / 80mm)</span>
                            <input type="hidden" name="printer_struk_aktif" value="0">
                            <input type="checkbox" name="printer_struk_aktif" value="1" @checked(old('printer_struk_aktif', $pengaturan->printer_struk_aktif)) class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rajawali relative"></div>
                        </label>
                        <p class="text-xs text-steel mt-1 font-medium">Jika aktif, kasir otomatis mencetak struk thermal kasir begitu transaksi disimpan.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="file-text" class="w-5 h-5" />
                    </div>
                    <div class="flex-1">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="font-black text-sm text-slate-900">Mesin 2: Printer Faktur Penjualan A5 (Kertas NCR / Inkjet)</span>
                            <input type="hidden" name="printer_faktur_aktif" value="0">
                            <input type="checkbox" name="printer_faktur_aktif" value="1" @checked(old('printer_faktur_aktif', $pengaturan->printer_faktur_aktif)) class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 relative"></div>
                        </label>
                        <p class="text-xs text-steel mt-1 font-medium">Jika aktif, sistem otomatis membuka pratinjau faktur A5 landscape untuk langganan grosir/tempo.</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- 5. KEBIJAKAN KASIR & STOK POS --}}
        <x-card class="shadow-sm border border-slate-200/80 p-5 rounded-2xl space-y-4">
            <div class="flex items-center gap-2.5 pb-2 border-b border-line text-rajawali">
                <x-icon name="sliders" class="w-5 h-5" />
                <h3 class="font-display font-black text-sm text-ink uppercase tracking-wide">5. Kebijakan Operasional Kasir &amp; Stok</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <x-input label="Batas Maksimal Diskon Kasir (%) *" name="batas_diskon_kasir_persen" type="number" step="0.01" min="0" max="100" mono value="{{ old('batas_diskon_kasir_persen', $pengaturan->batas_diskon_kasir_persen) }}" required />
                <div class="pt-2 sm:pt-6">
                    <label class="flex items-center gap-2.5 text-sm text-ink cursor-pointer font-bold">
                        <input type="hidden" name="izinkan_stok_minus" value="0">
                        <input type="checkbox" name="izinkan_stok_minus" value="1" @checked(old('izinkan_stok_minus', $pengaturan->izinkan_stok_minus)) class="rounded border-line text-rajawali focus:ring-rajawali w-4 h-4">
                        <span>Izinkan Penjualan Stok Minus</span>
                    </label>
                    <p class="text-xs text-steel mt-1 font-medium">Jika aktif, kasir tetap bisa melakukan penjualan barang walau jumlah stok di sistem 0.</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-line">
                <x-button type="submit" variant="primary" class="px-7 py-3 rounded-xl text-sm font-black shadow-lg">
                    <x-icon name="save" class="w-4 h-4" />
                    <span>Simpan Pengaturan Web &amp; Toko</span>
                </x-button>
            </div>
        </x-card>
    </form>
</div>
</x-app-layout>
