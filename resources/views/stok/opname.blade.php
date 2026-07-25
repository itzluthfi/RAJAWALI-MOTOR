<x-app-layout title="Opname Stok">
<div x-data="{ stokFisik: 68, stokSistem: 70 }">
    <x-card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <x-select label="Barang" class="md:col-span-2">
                <option>DISC PAD VARIO CBS</option>
                <option>OLI FEDERAL MATIC 1L</option>
            </x-select>
            <div>
                <label class="block text-xs font-semibold text-steel mb-1">Stok Sistem</label>
                <p class="font-mono font-medium py-2" x-text="stokSistem"></p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-steel mb-1">Stok Fisik</label>
                <input type="number" x-model.number="stokFisik" class="w-full rounded-md border border-line bg-white px-3 py-2 text-sm font-mono text-right focus:outline-none focus:ring-2 focus:ring-rajawali">
            </div>
        </div>

        <div class="mt-3 text-sm" :class="stokFisik - stokSistem < 0 ? 'text-rajawali' : 'text-lunas'">
            Selisih: <span class="font-mono font-semibold" x-text="(stokFisik - stokSistem > 0 ? '+' : '') + (stokFisik - stokSistem)"></span>
        </div>

        <div class="mt-3">
            <x-input label="Alasan Selisih (wajib diisi)" placeholder="cth. Rusak, hilang, salah hitung" />
        </div>

        <div class="flex justify-end mt-4">
            <x-button variant="primary" onclick="window.toastSukses('Penyesuaian stok berhasil disimpan.')"><x-icon name="save" class="w-4 h-4" /> Simpan Penyesuaian</x-button>
        </div>
    </x-card>
</div>
</x-app-layout>
