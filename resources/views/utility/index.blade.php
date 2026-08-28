<x-app-layout title="Utility Sistem">
<div x-data="utilityApp()" class="space-y-6 -m-3 p-3">
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

    {{-- SECTION 1: PERBAIKAN DATA & PEMELIHARAAN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- KARTU RECALCULATE STOK --}}
        <x-card class="flex flex-col justify-between shadow-sm border border-slate-200/80 p-5 rounded-2xl">
            <div>
                <div class="flex items-center gap-2.5 mb-3 text-rajawali">
                    <x-icon name="history" class="w-6 h-6" />
                    <h3 class="font-display font-black text-base text-ink">Hitung Ulang Stok (Recalculate)</h3>
                </div>
                <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                    Memvalidasi database dan menghitung kembali saldo stok mutasi barang dari kartu stok masuk dan keluar, serta membersihkan log stok yang tidak valid.
                </p>
            </div>
            <div>
                <x-button type="button" variant="primary" class="w-full" x-on:click="bukaPreviewRecalculate()">
                    <x-icon name="refresh-cw" class="w-4 h-4" />
                    <span>Cek &amp; Mulai Hitung Ulang Stok</span>
                </x-button>
            </div>
        </x-card>

        {{-- KARTU MAINTENANCE HPP --}}
        <x-card class="flex flex-col justify-between shadow-sm border border-slate-200/80 p-5 rounded-2xl">
            <div>
                <div class="flex items-center gap-2.5 mb-3 text-rajawali">
                    <x-icon name="settings" class="w-6 h-6" />
                    <h3 class="font-display font-black text-base text-ink">Maintenance HPP (COGS)</h3>
                </div>
                <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                    Menyelaraskan nilai Harga Pokok Penjualan (HPP) pada master barang berdasarkan harga pembelian supplier paling terakhir untuk akurasi laporan laba rugi.
                </p>
            </div>
            <div>
                <x-button type="button" variant="primary" class="w-full" x-on:click="bukaPreviewHpp()">
                    <x-icon name="check-check" class="w-4 h-4" />
                    <span>Cek &amp; Mulai Maintenance HPP</span>
                </x-button>
            </div>
        </x-card>

        {{-- IMPORT BARANG --}}
        <x-card class="shadow-sm border border-slate-200/80 p-5 rounded-2xl">
            <div class="flex items-center gap-2.5 mb-3 text-rajawali">
                <x-icon name="file-spreadsheet" class="w-6 h-6" />
                <h3 class="font-display font-black text-base text-ink">Import Barang dari Excel / CSV</h3>
            </div>
            <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                Unggah data master barang massal menggunakan berkas format CSV. Kolom berkas CSV harus tersusun: <br>
                <code class="font-mono text-rajawali bg-rajawali/5 px-1.5 py-0.5 rounded text-[11px] font-bold">kode, nama, hpp, harga_eceran, harga_grosir, stok_minimum</code>
            </p>
            <form method="POST" action="{{ route('utility.import-barang') }}" enctype="multipart/form-data" class="space-y-4 font-bold">
                @csrf
                <input type="file" name="file_csv" accept=".csv" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-rajawali/10 file:text-rajawali hover:file:bg-rajawali/20">
                <x-button type="submit" variant="secondary" class="w-full">
                    <x-icon name="upload" class="w-4 h-4" />
                    <span>Unggah &amp; Proses Import Barang</span>
                </x-button>
            </form>
        </x-card>

        {{-- IMPORT CUSTOMER --}}
        <x-card class="shadow-sm border border-slate-200/80 p-5 rounded-2xl">
            <div class="flex items-center gap-2.5 mb-3 text-rajawali">
                <x-icon name="users" class="w-6 h-6" />
                <h3 class="font-display font-black text-base text-ink">Import Customer dari Excel / CSV</h3>
            </div>
            <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                Unggah data pelanggan massal menggunakan berkas format CSV. Kolom berkas CSV harus tersusun: <br>
                <code class="font-mono text-rajawali bg-rajawali/5 px-1.5 py-0.5 rounded text-[11px] font-bold">nama, telepon, alamat, termin_hari</code>
            </p>
            <form method="POST" action="{{ route('utility.import-customer') }}" enctype="multipart/form-data" class="space-y-4 font-bold">
                @csrf
                <input type="file" name="file_csv" accept=".csv" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-rajawali/10 file:text-rajawali hover:file:bg-rajawali/20">
                <x-button type="submit" variant="secondary" class="w-full">
                    <x-icon name="upload" class="w-4 h-4" />
                    <span>Unggah &amp; Proses Import Customer</span>
                </x-button>
            </form>
        </x-card>
    </div>

    {{-- SECTION 2: PENCADANGAN DATABASE (SERASI DENGAN TEMA PUTIH/TERANG) --}}
    <x-card class="shadow-md border border-slate-200/80 p-5 rounded-2xl">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-line">
            <div>
                <div class="flex items-center gap-2.5 mb-1.5">
                    <x-icon name="database" class="w-6 h-6 text-rajawali" />
                    <h3 class="font-display font-black text-lg text-ink">Pencadangan Database (Backup &amp; Riwayat Berkas)</h3>
                </div>
                <p class="text-xs text-steel max-w-2xl leading-relaxed font-medium">
                    Cadangkan seluruh data transaksi penjualan, sparepart, hutang, piutang, dan keuangan toko ke dalam berkas SQL aman. Berkas dapat diunduh ke komputer atau dihapus sewaktu-waktu.
                </p>
                <div class="mt-2 flex items-center gap-2 text-[11px] text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-lg w-fit font-bold">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                    <span><strong>Jadwal Otomatis:</strong> Setiap hari pukul 23:59 WIB (Riwayat 14 hari terakhir)</span>
                </div>
            </div>
            <form method="POST" action="{{ route('utility.backup') }}" onsubmit="return confirm('Mulai proses pembuatan backup database sekarang?')">
                @csrf
                <x-button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-5 py-3 rounded-xl shadow-md flex items-center gap-2 shrink-0 cursor-pointer">
                    <x-icon name="download" class="w-4 h-4" />
                    <span>+ Buat Backup Manual Sekarang</span>
                </x-button>
            </form>
        </div>

        <!-- Tabel Riwayat Berkas Backup -->
        <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="p-3 bg-slate-50 border-b border-slate-200 flex justify-between items-center text-xs font-bold">
                <span class="text-slate-700">Riwayat Berkas Cadangan Tersimpan di Server</span>
                <span class="text-slate-500 font-mono">{{ count($backups) }} berkas</span>
            </div>
            <div class="overflow-x-auto max-h-72 overflow-y-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-100 text-slate-600 uppercase font-black border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-2.5">Nama Berkas Cadangan</th>
                            <th class="text-left px-4 py-2.5">Tanggal Dibuat</th>
                            <th class="text-right px-4 py-2.5">Ukuran</th>
                            <th class="text-center px-4 py-2.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-bold">
                        @forelse($backups as $b)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-2.5 font-mono text-emerald-700 font-black flex items-center gap-2">
                                    <x-icon name="file-code" class="w-4 h-4 text-emerald-600 shrink-0" />
                                    <span>{{ $b['filename'] }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-slate-600">{{ $b['created_at'] }} WIB</td>
                                <td class="px-4 py-2.5 text-right font-mono text-slate-700">{{ $b['size'] }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('utility.backup.download', $b['filename']) }}" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 font-black text-xs inline-flex items-center gap-1 transition shadow-xs">
                                            <x-icon name="download" class="w-3 h-3" /> Unduh
                                        </a>
                                        <form method="POST" action="{{ route('utility.backup.delete', $b['filename']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas backup {{ $b['filename'] }} dari server?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 font-black text-xs inline-flex items-center gap-1 transition cursor-pointer">
                                                <x-icon name="trash-2" class="w-3 h-3" /> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-400 italic">
                                    Belum ada berkas backup yang dibuat. Klik tombol <strong>"+ Buat Backup Manual Sekarang"</strong> di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>

    {{-- MODAL PREVIEW HITUNG ULANG STOK --}}
    <x-modal name="modal-recalculate-stok" title="Preview & Cek Hitung Ulang Stok">
        <div class="space-y-4">
            <template x-if="loadingRecalculate">
                <div class="p-8 text-center text-slate-500 space-y-2">
                    <x-icon name="loader-2" class="w-8 h-8 mx-auto animate-spin text-rajawali" />
                    <p class="text-xs font-bold">Sedang menganalisis log mutasi dan saldo kartu stok...</p>
                </div>
            </template>

            <template x-if="!loadingRecalculate && dataRecalculate">
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-[11px] text-steel font-bold block">Total Master Barang</span>
                            <strong class="font-mono text-base font-black text-ink" x-text="dataRecalculate.total_barang + ' Item'"></strong>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-[11px] text-steel font-bold block">Total Mutasi Stok</span>
                            <strong class="font-mono text-base font-black text-ink" x-text="dataRecalculate.total_mutasi + ' Baris'"></strong>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-[11px] text-steel font-bold block">Mutasi Yatim (Invalid)</span>
                            <strong class="font-mono text-base font-black" :class="dataRecalculate.orphan_count > 0 ? 'text-rose-600' : 'text-emerald-600'" x-text="dataRecalculate.orphan_count + ' Baris'"></strong>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl text-xs font-medium leading-relaxed" :class="dataRecalculate.orphan_count > 0 ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-emerald-50 text-emerald-900 border border-emerald-200'">
                        <template x-if="dataRecalculate.orphan_count > 0">
                            <div>
                                ⚠️ Ditemukan <strong><span x-text="dataRecalculate.orphan_count"></span> log mutasi stok yatim</strong> (transaksi tanpa referensi barang master). Proses hitung ulang akan otomatis membersihkan log tidak valid ini dan menyinkronkan saldo kartu stok.
                            </div>
                        </template>
                        <template x-if="dataRecalculate.orphan_count === 0">
                            <div>
                                ✅ Seluruh data mutasi stok barang terhubung valid ke master barang. Proses hitung ulang akan menyegarkan kalkulasi pergerakan saldo stok.
                            </div>
                        </template>
                    </div>

                    <form method="POST" action="{{ route('utility.recalculate-stok') }}" class="flex justify-end gap-2 pt-3 border-t border-line font-bold">
                        @csrf
                        <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'modal-recalculate-stok' })">Batal</x-button>
                        <x-button type="submit" variant="primary">
                            <x-icon name="refresh-cw" class="w-4 h-4" /> Mulai Sinkronisasi Stok Sekarang
                        </x-button>
                    </form>
                </div>
            </template>
        </div>
    </x-modal>

    {{-- MODAL PREVIEW MAINTENANCE HPP --}}
    <x-modal name="modal-maintenance-hpp" title="Preview & Cek Maintenance HPP (COGS)">
        <div class="space-y-4">
            <template x-if="loadingHpp">
                <div class="p-8 text-center text-slate-500 space-y-2">
                    <x-icon name="loader-2" class="w-8 h-8 mx-auto animate-spin text-rajawali" />
                    <p class="text-xs font-bold">Sedang mencocokkan HPP master dengan harga pembelian supplier terakhir...</p>
                </div>
            </template>

            <template x-if="!loadingHpp && dataHpp">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3.5 rounded-xl border" :class="dataHpp.total_perubahan > 0 ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-emerald-50 border-emerald-200 text-emerald-900'">
                        <div class="text-xs font-bold">
                            <template x-if="dataHpp.total_perubahan > 0">
                                <span>⚠️ Ditemukan <strong><span x-text="dataHpp.total_perubahan"></span> barang</strong> dengan nilai HPP yang berbeda dari harga kulakan supplier terakhir.</span>
                            </template>
                            <template x-if="dataHpp.total_perubahan === 0">
                                <span>✅ Seluruh HPP master barang sudah 100% selaras dengan harga pembelian supplier terakhir. Tidak ada selisih.</span>
                            </template>
                        </div>
                    </div>

                    <template x-if="dataHpp.items && dataHpp.items.length > 0">
                        <div class="border border-slate-200 rounded-xl overflow-hidden max-h-64 overflow-y-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-slate-100 text-slate-700 uppercase font-black border-b border-slate-200">
                                    <tr>
                                        <th class="text-left px-3 py-2">Barang</th>
                                        <th class="text-right px-3 py-2">HPP Saat Ini</th>
                                        <th class="text-right px-3 py-2">HPP Beli Baru</th>
                                        <th class="text-right px-3 py-2">Selisih</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-bold">
                                    <template x-for="item in dataHpp.items" :key="item.kode">
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-3 py-2">
                                                <div class="text-slate-900 font-black" x-text="item.nama"></div>
                                                <span class="text-[10px] font-mono text-rajawali" x-text="item.kode"></span>
                                            </td>
                                            <td class="px-3 py-2 text-right font-mono text-slate-600" x-text="'Rp ' + Number(item.hpp_lama).toLocaleString('id-ID')"></td>
                                            <td class="px-3 py-2 text-right font-mono text-emerald-700 font-black" x-text="'Rp ' + Number(item.hpp_baru).toLocaleString('id-ID')"></td>
                                            <td class="px-3 py-2 text-right font-mono" :class="item.selisih > 0 ? 'text-emerald-600' : 'text-rose-600'" x-text="(item.selisih > 0 ? '+' : '') + 'Rp ' + Number(item.selisih).toLocaleString('id-ID')"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>

                    <form method="POST" action="{{ route('utility.maintenance-hpp') }}" class="flex justify-end gap-2 pt-3 border-t border-line font-bold">
                        @csrf
                        <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'modal-maintenance-hpp' })">Batal</x-button>
                        <x-button type="submit" variant="primary">
                            <x-icon name="check-check" class="w-4 h-4" /> Mulai Selaraskan HPP Sekarang
                        </x-button>
                    </form>
                </div>
            </template>
        </div>
    </x-modal>
</div>

<script>
function utilityApp() {
    return {
        loadingRecalculate: false,
        dataRecalculate: null,

        loadingHpp: false,
        dataHpp: null,

        async bukaPreviewRecalculate() {
            this.loadingRecalculate = true;
            this.dataRecalculate = null;
            this.$dispatch('buka-modal', { name: 'modal-recalculate-stok' });
            try {
                const res = await fetch('{{ route("utility.preview-recalculate-stok") }}');
                const data = await res.json();
                this.dataRecalculate = data;
            } catch (err) {
                if (window.toastGagal) window.toastGagal('Gagal memuat analisis stok: ' + err.message);
            } finally {
                this.loadingRecalculate = false;
            }
        },

        async bukaPreviewHpp() {
            this.loadingHpp = true;
            this.dataHpp = null;
            this.$dispatch('buka-modal', { name: 'modal-maintenance-hpp' });
            try {
                const res = await fetch('{{ route("utility.preview-maintenance-hpp") }}');
                const data = await res.json();
                this.dataHpp = data;
            } catch (err) {
                if (window.toastGagal) window.toastGagal('Gagal memuat analisis HPP: ' + err.message);
            } finally {
                this.loadingHpp = false;
            }
        }
    };
}
</script>
</x-app-layout>
