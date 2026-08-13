<x-app-layout title="Retur Pembelian Baru">
    <div class="max-w-4xl mx-auto space-y-4" x-data="formReturPembelianApp(@js($barangs))">
        <form action="{{ route('retur.pembelian.store') }}" method="POST">
            @csrf
            <x-card>
                <div class="border-b border-line pb-3 mb-4 flex justify-between items-center">
                    <div>
                        <h2 class="font-display font-bold text-lg text-ink">Form Retur Pembelian (Supplier)</h2>
                        <p class="text-xs text-steel">Barang dikembalikan ke vendor/supplier. Stok barang toko akan berkurang otomatis dan penerimaan refund kas akan dicatat.</p>
                    </div>
                    <x-button as="a" href="{{ route('retur.index') }}" variant="secondary" size="xs">
                        <x-icon name="arrow-left" class="w-4 h-4" /> Kembali
                    </x-button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <x-label>Pilih Faktur Pembelian (Opsional)</x-label>
                        <x-select name="pembelian_id" class="w-full">
                            <option value="">-- Tanpa Referensi Faktur --</option>
                            @foreach($pembelians as $pb)
                                <option value="{{ $pb->id }}">{{ $pb->nomor_pembelian }} ({{ $pb->supplier->nama ?? '-' }} - Rp {{ number_format($pb->total, 0, ',', '.') }})</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div>
                        <x-input type="date" name="tanggal" label="Tanggal Retur" value="{{ date('Y-m-d') }}" required />
                    </div>
                    <div>
                        <x-input name="alasan" label="Alasan Retur Ke Supplier" placeholder="Contoh: Barang cacat pabrik / Rusak ekspedisi" required />
                    </div>
                </div>

                <div class="border-t border-line pt-4 mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-bold text-sm text-ink flex items-center gap-2">
                            <x-icon name="undo-2" class="w-4 h-4 text-rajawali" /> Item Barang Ditolak / Dibalikkan
                        </h3>
                        <x-button type="button" variant="secondary" size="xs" x-on:click="tambahBaris()">
                            <x-icon name="plus" class="w-3.5 h-3.5" /> Tambah Barang
                        </x-button>
                    </div>

                    <div class="overflow-x-auto border border-line rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-canvas text-steel text-xs uppercase font-bold border-b border-line">
                                <tr>
                                    <th class="px-3 py-2.5 text-left">Pilih Barang / Sparepart</th>
                                    <th class="px-3 py-2.5 text-center w-28">Jumlah (Qty)</th>
                                    <th class="px-3 py-2.5 text-right w-40">Harga Beli (Rp)</th>
                                    <th class="px-3 py-2.5 text-right w-44">Subtotal (Rp)</th>
                                    <th class="px-3 py-2.5 text-center w-12">#</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="hover:bg-canvas/50">
                                        <td class="p-2">
                                            <select
                                                :name="`items[${index}][barang_id]`"
                                                x-model="item.barang_id"
                                                x-on:change="updateHarga(index)"
                                                class="w-full text-xs font-semibold rounded border border-line px-2 py-1.5 focus:ring-1 focus:ring-rajawali"
                                                required
                                            >
                                                <option value="">-- Pilih Barang --</option>
                                                <template x-for="b in daftarBarang" :key="b.id">
                                                    <option :value="b.id" x-text="`${b.kode} - ${b.nama} (Stok: ${b.stok})`"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input
                                                type="number"
                                                min="1"
                                                :name="`items[${index}][jumlah]`"
                                                x-model.number="item.jumlah"
                                                class="w-full text-xs font-mono text-center font-bold rounded border border-line px-2 py-1.5"
                                                required
                                            >
                                        </td>
                                        <td class="p-2">
                                            <input
                                                type="number"
                                                min="0"
                                                :name="`items[${index}][harga]`"
                                                x-model.number="item.harga"
                                                class="w-full text-xs font-mono text-right font-bold rounded border border-line px-2 py-1.5"
                                                required
                                            >
                                        </td>
                                        <td class="p-2 text-right font-mono font-bold text-ink">
                                            <span x-text="formatRp(item.jumlah * item.harga)"></span>
                                        </td>
                                        <td class="p-2 text-center">
                                            <button
                                                type="button"
                                                x-on:click="hapusBaris(index)"
                                                class="text-rajawali hover:text-red-700 p-1 rounded hover:bg-red-50"
                                            >
                                                <x-icon name="trash-2" class="w-4 h-4" />
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-canvas border-t border-line font-bold">
                                <tr>
                                    <td colspan="3" class="px-3 py-3 text-right">Total Refund Diterima:</td>
                                    <td class="px-3 py-3 text-right font-mono text-base text-rajawali" x-text="formatRp(grandTotal)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-line">
                    <x-button as="a" href="{{ route('retur.index') }}" variant="secondary">Batal</x-button>
                    <x-button type="submit" variant="primary" x-bind:disabled="items.length === 0">
                        <x-icon name="save" class="w-4 h-4" /> Simpan Retur Pembelian
                    </x-button>
                </div>
            </x-card>
        </form>
    </div>

    <script>
    function formReturPembelianApp(daftarBarang) {
        return {
            daftarBarang: daftarBarang,
            items: [
                { barang_id: '', jumlah: 1, harga: 0 }
            ],
            tambahBaris() {
                this.items.push({ barang_id: '', jumlah: 1, harga: 0 });
            },
            hapusBaris(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            updateHarga(index) {
                const bId = this.items[index].barang_id;
                const b = this.daftarBarang.find(item => item.id == bId);
                if (b) {
                    this.items[index].harga = b.hpp || 0;
                }
            },
            get grandTotal() {
                return this.items.reduce((total, i) => total + ((i.jumlah || 0) * (i.harga || 0)), 0);
            },
            formatRp(angka) {
                return 'Rp ' + Math.round(angka || 0).toLocaleString('id-ID');
            }
        };
    }
    </script>
</x-app-layout>
