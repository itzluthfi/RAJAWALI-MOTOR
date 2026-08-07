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
