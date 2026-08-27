<x-app-layout title="Utility Sistem">
<div class="space-y-6 -m-3 p-3">
    @if(session('sukses'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm font-bold">
            {{ session('sukses') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm font-bold">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Kartu Backup Database Utama -->
    <x-card class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 rounded-2xl shadow-xl border border-slate-700">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <x-icon name="database-backup" class="w-6 h-6 text-emerald-400" />
                    <h3 class="font-display font-black text-xl text-white tracking-tight">Pencadangan Database (Auto &amp; Manual Backup)</h3>
                </div>
                <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                    Cadangkan seluruh transaksi penjualan, data barang, hutang, piutang, dan keuangan toko ke dalam berkas format SQL terenkripsi. Berkas cadangan dapat diunduh ke laptop/komputer atau dihapus sewaktu-waktu.
                </p>
            </div>
            <form method="POST" action="{{ route('utility.backup') }}" onsubmit="return confirm('Mulai proses pembuatan backup database sekarang?')">
                @csrf
                <x-button type="submit" variant="primary" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 shrink-0">
                    <x-icon name="download" class="w-4 h-4" />
                    <span>+ Buat Backup Sekarang</span>
                </x-button>
            </form>
        </div>

        <!-- Tabel Riwayat Berkas Backup -->
        <div class="mt-6 overflow-hidden rounded-xl border border-slate-700 bg-slate-950/60">
            <div class="p-3 bg-slate-900/80 border-b border-slate-700 flex justify-between items-center text-xs">
                <span class="font-bold text-slate-200">Riwayat Berkas Cadangan Tersimpan di Server (storage/app/backups)</span>
                <span class="text-slate-400 font-mono">{{ count($backups) }} berkas cadangan</span>
            </div>
            <div class="overflow-x-auto max-h-56 overflow-y-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-900 text-slate-400 uppercase font-bold border-b border-slate-800">
                        <tr>
                            <th class="text-left px-4 py-2.5">Nama Berkas Cadangan</th>
                            <th class="text-left px-4 py-2.5">Tanggal Dibuat</th>
                            <th class="text-right px-4 py-2.5">Ukuran</th>
                            <th class="text-center px-4 py-2.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @forelse($backups as $b)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-2.5 font-mono text-emerald-400 font-bold flex items-center gap-2">
                                    <x-icon name="file-code" class="w-4 h-4 text-emerald-400 shrink-0" />
                                    <span>{{ $b['filename'] }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-slate-300">{{ $b['created_at'] }} WIB</td>
                                <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-300">{{ $b['size'] }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('utility.backup.download', $b['filename']) }}" class="px-2.5 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] inline-flex items-center gap-1 transition shadow" data-tooltip="Unduh Berkas ke Laptop">
                                            <x-icon name="download" class="w-3 h-3" /> Unduh
                                        </a>
                                        <form method="POST" action="{{ route('utility.backup.delete', $b['filename']) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas backup {{ $b['filename'] }} dari server?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded bg-red-600/80 hover:bg-red-600 text-white font-bold text-[11px] inline-flex items-center gap-1 transition" data-tooltip="Hapus Berkas dari Server">
                                                <x-icon name="trash-2" class="w-3 h-3" /> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-slate-400 italic">
                                    Belum ada berkas backup yang dibuat. Klik tombol <strong>"+ Buat Backup Sekarang"</strong> di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- KARTU RECALCULATE --}}
        <x-card class="flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3 text-rajawali">
                    <x-icon name="history" class="w-6 h-6" />
                    <h3 class="font-display font-bold text-base text-ink">Hitung Ulang Stok (Recalculate)</h3>
                </div>
                <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                    Melakukan validasi database dan menghitung kembali saldo stok mutasi barang dari kartu stok masuk dan keluar, serta membersihkan log stok yang tidak valid.
                </p>
            </div>
            <form method="POST" action="{{ route('utility.recalculate-stok') }}" onsubmit="return confirm('Apakah Anda yakin ingin memproses hitung ulang stok?')">
                @csrf
                <x-button type="submit" variant="primary" class="w-full">
                    Mulai Hitung Ulang Stok
                </x-button>
            </form>
        </x-card>

        {{-- KARTU MAINTENANCE HPP --}}
        <x-card class="flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3 text-rajawali">
                    <x-icon name="settings" class="w-6 h-6" />
                    <h3 class="font-display font-bold text-base text-ink">Maintenance HPP (COGS)</h3>
                </div>
                <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                    Menyelaraskan nilai Harga Pokok Penjualan (HPP) pada master barang berdasarkan harga pembelian supplier paling terakhir untuk akurasi laporan laba rugi.
                </p>
            </div>
            <form method="POST" action="{{ route('utility.maintenance-hpp') }}" onsubmit="return confirm('Apakah Anda yakin ingin memproses maintenance HPP?')">
                @csrf
                <x-button type="submit" variant="primary" class="w-full">
                    Mulai Maintenance HPP
                </x-button>
            </form>
        </x-card>

        {{-- IMPORT BARANG --}}
        <x-card>
            <div class="flex items-center gap-3 mb-3 text-rajawali">
                <x-icon name="file-spreadsheet" class="w-6 h-6" />
                <h3 class="font-display font-bold text-base text-ink">Import Barang dari Excel / CSV</h3>
            </div>
            <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                Unggah data master barang massal menggunakan berkas format CSV. Kolom berkas CSV harus tersusun: <br>
                <code class="font-mono text-rajawali bg-rajawali/5 px-1 py-0.5 rounded">kode, nama, hpp, harga_eceran, harga_grosir, stok_minimum</code>
            </p>
            <form method="POST" action="{{ route('utility.import-barang') }}" enctype="multipart/form-data" class="space-y-4 font-bold">
                @csrf
                <input type="file" name="file_csv" accept=".csv" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-rajawali/10 file:text-rajawali hover:file:bg-rajawali/20">
                <x-button type="submit" variant="secondary" class="w-full">
                    Unggah &amp; Proses Import Barang
                </x-button>
            </form>
        </x-card>

        {{-- IMPORT CUSTOMER --}}
        <x-card>
            <div class="flex items-center gap-3 mb-3 text-rajawali">
                <x-icon name="users" class="w-6 h-6" />
                <h3 class="font-display font-bold text-base text-ink">Import Customer dari Excel / CSV</h3>
            </div>
            <p class="text-xs text-steel leading-relaxed mb-4 font-semibold">
                Unggah data pelanggan massal menggunakan berkas format CSV. Kolom berkas CSV harus tersusun: <br>
                <code class="font-mono text-rajawali bg-rajawali/5 px-1 py-0.5 rounded">nama, telepon, alamat, termin_hari</code>
            </p>
            <form method="POST" action="{{ route('utility.import-customer') }}" enctype="multipart/form-data" class="space-y-4 font-bold">
                @csrf
                <input type="file" name="file_csv" accept=".csv" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-rajawali/10 file:text-rajawali hover:file:bg-rajawali/20">
                <x-button type="submit" variant="secondary" class="w-full">
                    Unggah &amp; Proses Import Customer
                </x-button>
            </form>
        </x-card>
    </div>
</div>
</x-app-layout>
