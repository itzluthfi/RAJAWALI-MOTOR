<x-app-layout title="Pembelian Baru">
<div x-data="pembelianForm()">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
        <x-select label="Supplier">
            <option>PT Astra Otoparts</option>
            <option>CV Sinar Motor Jaya</option>
        </x-select>
        <x-input label="No Faktur Supplier" placeholder="cth. INV-9021" />
        <x-input type="date" label="Tanggal" value="2026-07-22" />
        <x-select label="Jenis Bayar">
            <option>Tunai</option>
            <option>Tempo</option>
        </x-select>
    </div>

    <x-card :padded="false" class="mb-4">
        <div class="flex items-center gap-2 p-3 border-b border-line">
            <x-input placeholder="Cari / scan barang..." class="flex-1" />
            <x-button variant="secondary" onclick="window.dispatchEvent(new CustomEvent('buka-modal', {detail:{name:'tambah-cepat'}}))"><x-icon name="plus" class="w-4 h-4" /> Tambah Barang Cepat</x-button>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Barang</th>
                    <th class="text-right font-semibold px-4 py-2.5">Qty</th>
                    <th class="text-right font-semibold px-4 py-2.5">Harga Beli</th>
                    <th class="text-right font-semibold px-4 py-2.5">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-line">
                    <td class="px-4 py-2.5">DISC PAD VARIO CBS</td>
                    <td class="px-4 py-2.5 text-right font-mono">50</td>
                    <td class="px-4 py-2.5 text-right font-mono">15.000</td>
                    <td class="px-4 py-2.5 text-right font-mono font-medium">750.000</td>
                </tr>
                <tr>
                    <td class="px-4 py-2.5">OLI FEDERAL MATIC 1L</td>
                    <td class="px-4 py-2.5 text-right font-mono">20</td>
                    <td class="px-4 py-2.5 text-right font-mono">36.000</td>
                    <td class="px-4 py-2.5 text-right font-mono font-medium">720.000</td>
                </tr>
            </tbody>
        </table>
    </x-card>

    <div class="flex justify-end">
        <x-card class="w-80">
            <div class="flex justify-between items-baseline">
                <span class="font-display font-semibold">GRAND TOTAL</span>
                <span class="font-mono font-bold text-xl text-rajawali">Rp 1.470.000</span>
            </div>
            <x-button variant="primary" class="w-full mt-3" onclick="window.toastSukses('Pembelian PB2026000046 berhasil disimpan.')">
                <x-icon name="save" class="w-4 h-4" /> Simpan Pembelian
            </x-button>
        </x-card>
    </div>

    <x-modal name="tambah-cepat" title="Tambah Barang Cepat">
        <form class="space-y-4">
            <x-input label="Kode Barang" />
            <x-input label="Nama Barang" />
            <x-select label="Group"><option>Sparepart</option><option>Oli</option></x-select>
            <div class="flex justify-end gap-2">
                <x-button variant="secondary" onclick="window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'tambah-cepat'}}))">Batal</x-button>
                <x-button variant="primary" onclick="window.toastSukses('Barang baru siap dipakai di baris pembelian.'); window.dispatchEvent(new CustomEvent('tutup-modal', {detail:{name:'tambah-cepat'}}))">Tambah</x-button>
            </div>
        </form>
    </x-modal>
</div>
</x-app-layout>

<script>
function pembelianForm() {
    return {};
}
</script>
