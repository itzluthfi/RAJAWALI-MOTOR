<x-app-layout title="Pengaturan Toko">
    <form method="POST" action="{{ route('pengaturan.toko.update') }}" class="max-w-3xl space-y-6">
        @csrf
        <x-card title="Informasi Identitas & Lokasi Toko">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Nama Bengkel / Toko" name="nama_toko" value="{{ old('nama_toko', $pengaturan->nama_toko) }}" class="md:col-span-2" :error="$errors->first('nama_toko')" required />
                <x-input label="Alamat Lengkap Workshop" name="alamat" value="{{ old('alamat', $pengaturan->alamat) }}" class="md:col-span-2" required />
                <x-input label="Nomor Telepon / WhatsApp Operasional" name="telepon" value="{{ old('telepon', $pengaturan->telepon) }}" placeholder="Contoh: 08123456789" />
                <x-input label="Format Penomoran Nota" name="format_nota" value="{{ old('format_nota', $pengaturan->format_nota) }}" mono required />
            </div>
        </x-card>

        <x-card title="Integrasi Mesin Printer Kasir & Faktur (Hardware)">
            <div class="space-y-4">
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-rajawali/10 text-rajawali flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="printer" class="w-5 h-5" />
                    </div>
                    <div class="flex-1">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="font-bold text-sm text-slate-900">Mesin 1: Printer Thermal Struk Kasir (58mm / 80mm)</span>
                            <input type="hidden" name="printer_struk_aktif" value="0">
                            <input type="checkbox" name="printer_struk_aktif" value="1" @checked(old('printer_struk_aktif', $pengaturan->printer_struk_aktif)) class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rajawali relative"></div>
                        </label>
                        <p class="text-xs text-steel mt-1">Jika aktif, sistem otomatis membuka pratinjau/cetak struk thermal kasir begitu transaksi berhasil disimpan.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                        <x-icon name="file-text" class="w-5 h-5" />
                    </div>
                    <div class="flex-1">
                        <label class="flex items-center justify-between cursor-pointer">
                            <span class="font-bold text-sm text-slate-900">Mesin 2: Printer Faktur Penjualan A5 (Kertas NCR / Inkjet)</span>
                            <input type="hidden" name="printer_faktur_aktif" value="0">
                            <input type="checkbox" name="printer_faktur_aktif" value="1" @checked(old('printer_faktur_aktif', $pengaturan->printer_faktur_aktif)) class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 relative"></div>
                        </label>
                        <p class="text-xs text-steel mt-1">Jika aktif, sistem otomatis membuka pratinjau/cetak faktur A5 landscape begitu transaksi tempo/grosir disimpan.</p>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card title="Kebijakan Kasir & Stok POS">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                <x-input label="Batas Maksimal Diskon Kasir (%)" name="batas_diskon_kasir_persen" type="number" step="0.01" min="0" max="100" mono value="{{ old('batas_diskon_kasir_persen', $pengaturan->batas_diskon_kasir_persen) }}" required />
                <div class="pt-6">
                    <label class="flex items-center gap-2.5 text-sm text-ink cursor-pointer font-medium">
                        <input type="hidden" name="izinkan_stok_minus" value="0">
                        <input type="checkbox" name="izinkan_stok_minus" value="1" @checked(old('izinkan_stok_minus', $pengaturan->izinkan_stok_minus)) class="rounded border-line text-rajawali focus:ring-rajawali w-4 h-4">
                        <span>Izinkan Transaksi Stok Minus</span>
                    </label>
                    <p class="text-xs text-steel mt-1">Jika diaktifkan, kasir tetap bisa melakukan penjualan barang walau jumlah stok di sistem 0.</p>
                </div>
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t border-line">
                <x-button type="submit" variant="primary"><x-icon name="check" class="w-4 h-4" /> Simpan Pengaturan Toko</x-button>
            </div>
        </x-card>
    </form>
</x-app-layout>

@if(session('sukses'))
    <script>document.addEventListener('DOMContentLoaded', () => window.toastSukses(@js(session('sukses'))));</script>
@endif
@if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => window.toastGagal(@js($errors->first())));</script>
@endif
