<x-app-layout title="Pengaturan Toko">
    <form method="POST" action="{{ route('pengaturan.toko.update') }}">
        @csrf
        <x-card class="max-w-2xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Nama Toko" name="nama_toko" value="{{ old('nama_toko', $pengaturan->nama_toko) }}" class="md:col-span-2" :error="$errors->first('nama_toko')" />
                <x-input label="Alamat" name="alamat" value="{{ old('alamat', $pengaturan->alamat) }}" class="md:col-span-2" />
                <x-input label="Telepon" name="telepon" value="{{ old('telepon', $pengaturan->telepon) }}" />
                <x-input label="Format Nota" name="format_nota" value="{{ old('format_nota', $pengaturan->format_nota) }}" />
                <x-input label="Batas Diskon Kasir (%)" name="batas_diskon_kasir_persen" type="number" step="0.01" min="0" max="100" mono value="{{ old('batas_diskon_kasir_persen', $pengaturan->batas_diskon_kasir_persen) }}" />
                <label class="flex items-center gap-2 text-sm text-steel pt-6">
                    <input type="hidden" name="izinkan_stok_minus" value="0">
                    <input type="checkbox" name="izinkan_stok_minus" value="1" @checked(old('izinkan_stok_minus', $pengaturan->izinkan_stok_minus)) class="rounded border-line text-rajawali focus:ring-rajawali">
                    Izinkan stok minus
                </label>
            </div>
            <div class="flex justify-end mt-4">
                <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
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
