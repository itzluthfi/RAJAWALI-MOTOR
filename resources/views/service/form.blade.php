<x-app-layout title="Tanda Terima Service">
<div x-data="{ jenis: 'intern' }">
    <x-card class="mb-4">
        <h3 class="font-display font-semibold text-sm mb-4">Data Kendaraan &amp; Customer</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-input label="No Dokumen" value="SV2026000046" readonly />
            <x-input type="date" label="Tanggal Masuk" value="2026-07-22" />
            <x-select label="Customer">
                <option>Umum</option>
                <option>Toko Jaya Motor</option>
            </x-select>
            <x-input label="Merk / Type" placeholder="cth. Honda Vario 125" />
            <x-input label="No Rangka" />
            <x-input label="No Mesin" />
            <x-input label="Kelengkapan" placeholder="cth. STNK, Kunci Kontak" class="md:col-span-2" />
            <x-select label="Montir">
                <option>Wawan</option>
                <option>Slamet</option>
            </x-select>
        </div>
        <div class="mt-4">
            <label class="block text-xs font-semibold text-steel mb-1">Keluhan</label>
            <textarea rows="3" class="w-full rounded-md border border-line bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali" placeholder="cth. Suara kasar di CVT"></textarea>
        </div>

        <div class="mt-4 flex items-center gap-4">
            <span class="text-sm font-medium text-steel">Dikerjakan:</span>
            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis" x-model="jenis" value="intern" checked> Intern</label>
            <label class="flex items-center gap-2 text-sm"><input type="radio" name="jenis" x-model="jenis" value="extern"> Extern (kirim keluar)</label>
        </div>

        <div x-show="jenis === 'extern'" x-cloak class="mt-3 grid grid-cols-2 gap-4">
            <x-select label="Supplier Tujuan">
                <option>PT Astra Otoparts</option>
            </x-select>
            <x-input type="date" label="Tanggal Kirim" />
        </div>
    </x-card>

    <div class="flex justify-end gap-2">
        <x-button as="a" href="{{ route('cetak.tanda-terima-service', 'SV2026000046') }}" target="_blank" variant="secondary"><x-icon name="printer" class="w-4 h-4" /> Cetak Tanda Terima</x-button>
        <x-button variant="primary" onclick="window.toastSukses('Tanda terima SV2026000046 berhasil disimpan.')"><x-icon name="save" class="w-4 h-4" /> Simpan</x-button>
    </div>
</div>
</x-app-layout>
