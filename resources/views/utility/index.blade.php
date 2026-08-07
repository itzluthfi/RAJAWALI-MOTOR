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
