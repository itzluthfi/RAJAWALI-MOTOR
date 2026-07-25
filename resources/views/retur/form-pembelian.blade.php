<x-app-layout title="Retur Pembelian">
    <x-card class="mb-4">
        <div class="flex items-end gap-3">
            <x-input label="Cari No Pembelian Asal" placeholder="cth. PB2026000041" class="flex-1" />
            <x-button variant="primary"><x-icon name="search" class="w-4 h-4" /> Cari Pembelian</x-button>
        </div>
    </x-card>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Barang</th>
                    <th class="text-right font-semibold px-4 py-2.5">Qty Dibeli</th>
                    <th class="text-right font-semibold px-4 py-2.5">Bisa Diretur</th>
                    <th class="text-right font-semibold px-4 py-2.5">Qty Retur</th>
                    <th class="text-right font-semibold px-4 py-2.5">Harga Beli</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-line">
                    <td class="px-4 py-2.5">KAMPAS REM VARIO</td>
                    <td class="px-4 py-2.5 text-right font-mono">10</td>
                    <td class="px-4 py-2.5 text-right font-mono">10</td>
                    <td class="px-4 py-2.5 text-right"><input type="number" max="10" min="0" value="0" class="w-16 text-right font-mono border border-line rounded-md px-2 py-1"></td>
                    <td class="px-4 py-2.5 text-right font-mono">27.000</td>
                </tr>
            </tbody>
        </table>
    </x-card>

    <div class="flex justify-end mt-4">
        <x-button variant="primary" onclick="window.toastSukses('Retur RB2026000005 berhasil disimpan.')"><x-icon name="save" class="w-4 h-4" /> Simpan Retur</x-button>
    </div>
</x-app-layout>
