<x-app-layout title="Retur Penjualan">
    <x-card class="mb-4">
        <div class="flex items-end gap-3">
            <x-input label="Cari No Nota Asal" placeholder="cth. PJ2026000098" class="flex-1" />
            <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Cari Nota</x-button>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Barang</th>
                    <th class="text-right font-semibold px-4 py-2.5">Qty Dibeli</th>
                    <th class="text-right font-semibold px-4 py-2.5">Sudah Diretur</th>
                    <th class="text-right font-semibold px-4 py-2.5">Bisa Diretur</th>
                    <th class="text-right font-semibold px-4 py-2.5">Qty Retur</th>
                    <th class="text-right font-semibold px-4 py-2.5">Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-line">
                    <td class="px-4 py-2.5">DISC PAD VARIO CBS</td>
                    <td class="px-4 py-2.5 text-right font-mono">2</td>
                    <td class="px-4 py-2.5 text-right font-mono">0</td>
                    <td class="px-4 py-2.5 text-right font-mono">2</td>
                    <td class="px-4 py-2.5 text-right"><input type="number" max="2" min="0" value="0" class="w-16 text-right font-mono border border-line rounded-md px-2 py-1"></td>
                    <td class="px-4 py-2.5 text-right font-mono">20.000</td>
                </tr>
            </tbody>
        </table>
    </x-card>

    <div class="flex justify-end mt-4">
        <x-button variant="primary" onclick="window.toastSukses('Retur RJ2026000013 berhasil disimpan.')"><x-icon name="save" class="w-4 h-4" /> Simpan Retur</x-button>
    </div>
</x-app-layout>
