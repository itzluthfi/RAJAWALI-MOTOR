@php $peranSaya = auth()->user()->peran; @endphp
<!DOCTYPE html>
<html lang="id" x-data="{ sidebarTerbuka: window.innerWidth >= 768 }" @resize.window="if (window.innerWidth >= 768) { sidebarTerbuka = true; }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} — Rajawali Motor</title>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/png" href="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc">
    <link rel="apple-touch-icon" href="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-canvas text-ink"
      @if(session('sukses')) data-flash-sukses="{{ session('sukses') }}" @endif
      @if(session('gagal')) data-flash-gagal="{{ session('gagal') }}" @endif>
<div class="flex h-screen overflow-hidden relative">

    <!-- Mobile Drawer Overlay Backdrop -->
    <div
        x-show="sidebarTerbuka"
        x-cloak
        x-on:click="sidebarTerbuka = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 z-40 md:hidden"
    ></div>

    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-50 md:static shrink-0 border-r border-line bg-surface flex flex-col transition-all duration-300 shadow-2xl md:shadow-none overflow-x-hidden select-none"
        :class="{
            'translate-x-0': sidebarTerbuka,
            '-translate-x-full md:translate-x-0': !sidebarTerbuka,
            'w-64': sidebarTerbuka,
            'md:w-[72px]': !sidebarTerbuka
        }"
    >
        <!-- Sidebar Brand Header -->
        <div class="h-14 flex items-center justify-between px-4 border-b border-line shrink-0 overflow-x-hidden" :class="!sidebarTerbuka && 'justify-center px-0'">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0 group" :class="!sidebarTerbuka && 'justify-center'">
                <div class="w-9 h-9 rounded-2xl bg-white p-1 shadow-sm border border-slate-200/80 shrink-0 flex items-center justify-center overflow-hidden group-hover:scale-105 group-hover:shadow-md transition-all duration-300">
                    <img alt="Rajawali Motor Logo" class="w-full h-full object-contain rounded-xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
                </div>
                <span x-show="sidebarTerbuka" class="font-display font-bold text-sm tracking-tight truncate text-rajawali" x-cloak>RAJAWALI MOTOR</span>
            </a>
            <!-- Close button on mobile drawer -->
            <button type="button" x-on:click="sidebarTerbuka = false" class="md:hidden p-1.5 text-steel hover:text-ink rounded-md">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <!-- Sidebar Navigation Items -->
        <nav class="flex-1 overflow-y-auto overflow-x-hidden px-2 py-3 space-y-4" :class="!sidebarTerbuka && 'px-1.5'">
            <x-nav-group label="Utama">
                <x-nav-item href="{{ route('dashboard') }}" icon="layout-dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-nav-item>
                <x-nav-item href="{{ route('notifikasi.index') }}" icon="bell" :active="request()->routeIs('notifikasi.*')">Notifikasi &amp; Aktivitas</x-nav-item>
            </x-nav-group>

            @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                <x-nav-group label="Transaksi">
                    <x-nav-item href="{{ route('kasir') }}" icon="scan-barcode" :active="request()->routeIs('kasir')">Kasir POS</x-nav-item>
                    <x-nav-item href="{{ route('penjualan.index') }}" icon="receipt" :active="request()->routeIs('penjualan.*')" :badge="$sidebarNotaHariIniCount ?? 0" badgeColor="bg-emerald-600 text-white">Nota Penjualan</x-nav-item>
                    <x-nav-item href="{{ route('service.index') }}" icon="wrench" :active="request()->routeIs('service.*')">Antrean &amp; Servis</x-nav-item>
                    <x-nav-item href="{{ route('pembelian.index') }}" icon="truck" :active="request()->routeIs('pembelian.*')">Pembelian Stok</x-nav-item>
                    <x-nav-item href="{{ route('retur.index') }}" icon="undo-2" :active="request()->routeIs('retur.*')">Retur Barang</x-nav-item>
                </x-nav-group>
            @endif

            @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                <x-nav-group label="Master Data">
                    @if(in_array($peranSaya, ['owner', 'admin']))
                        <x-nav-item href="{{ route('barang.index') }}" icon="package" :active="request()->routeIs('barang.*')">Master Barang</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                        <x-nav-item href="{{ route('customer.index') }}" icon="users" :active="request()->routeIs('customer.*')">Master Customer</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin']))
                        <x-nav-item href="{{ route('supplier.index') }}" icon="factory" :active="request()->routeIs('supplier.*')">Master Supplier</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin']))
                        <x-nav-item href="{{ route('sales.index') }}" icon="user-check" :active="request()->routeIs('sales.*')">Master Sales</x-nav-item>
                    @endif
                </x-nav-group>
            @endif

            @if(in_array($peranSaya, ['owner', 'admin']))
                <x-nav-group label="Stok">
                    <x-nav-item href="{{ route('stok.kartu') }}" icon="notebook-text" :active="request()->routeIs('stok.kartu')">Kartu Stok</x-nav-item>
                    <x-nav-item href="{{ route('stok.rekap') }}" icon="layers" :active="request()->routeIs('stok.rekap')">Rekap Stok</x-nav-item>
                    <x-nav-item href="{{ route('stok.opname') }}" icon="clipboard-check" :active="request()->routeIs('stok.opname')">Stok Opname</x-nav-item>
                    <x-nav-item href="{{ route('stok.menipis') }}" icon="triangle-alert" :active="request()->routeIs('stok.menipis')" :badge="$sidebarStokMenipisCount ?? 0" badgeColor="bg-amber-500 text-white">Stok Menipis</x-nav-item>
                </x-nav-group>
            @endif

            @if(in_array($peranSaya, ['owner', 'admin']))
                <x-nav-group label="Keuangan">
                    <x-nav-item href="{{ route('keuangan.piutang') }}" icon="hand-coins" :active="request()->routeIs('keuangan.piutang')">Piutang Customer</x-nav-item>
                    <x-nav-item href="{{ route('keuangan.hutang') }}" icon="landmark" :active="request()->routeIs('keuangan.hutang')">Hutang Supplier</x-nav-item>
                    <x-nav-item href="{{ route('keuangan.kas') }}" icon="wallet" :active="request()->routeIs('keuangan.kas')">Kas Toko</x-nav-item>
                    @if($peranSaya === 'owner')
                        <x-nav-item href="{{ route('keuangan.kas-besar') }}" icon="vault" :active="request()->routeIs('keuangan.kas-besar')">Kas Besar (Owner)</x-nav-item>
                    @endif
                </x-nav-group>

                <x-nav-group label="Laporan">
                    <x-nav-item href="{{ route('laporan.index') }}" icon="chart-column" :active="request()->routeIs('laporan.*')">Semua Laporan</x-nav-item>
                </x-nav-group>
            @endif

            @if(in_array($peranSaya, ['owner', 'admin']))
                <x-nav-group label="Utility Sistem">
                    <x-nav-item href="{{ route('utility.index') }}" icon="history" :active="request()->routeIs('utility.*')">Utility Sistem</x-nav-item>
                </x-nav-group>
            @endif

            @if($peranSaya === 'owner')
                <x-nav-group label="Pengaturan">
                    <x-nav-item href="{{ route('pengaturan.toko') }}" icon="settings" :active="request()->routeIs('pengaturan.toko')">Profil Toko</x-nav-item>
                    <x-nav-item href="{{ route('pengaturan.user') }}" icon="user-cog" :active="request()->routeIs('pengaturan.user')">Pengguna System</x-nav-item>
                    <x-nav-item href="{{ route('pengaturan.audit') }}" icon="history" :active="request()->routeIs('pengaturan.audit')">Audit Log</x-nav-item>
                </x-nav-group>
            @endif
        </nav>

        <!-- Sidebar Footer Logout Button -->
        <div class="p-2 border-t border-line shrink-0 bg-surface">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    :data-tooltip="!sidebarTerbuka ? 'Keluar Sistem' : null"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-xs text-rajawali hover:bg-rajawali/10 active:scale-95 transition-all group relative overflow-hidden"
                    :class="!sidebarTerbuka && 'justify-center px-0'"
                >
                    <x-icon name="log-out" class="w-4 h-4 text-rajawali shrink-0 group-hover:scale-110 transition-transform" />
                    <span x-show="sidebarTerbuka" class="truncate" x-cloak>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Body -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <!-- Topbar Header -->
        <header
            class="h-14 shrink-0 border-b border-line bg-surface flex items-center justify-between px-4 z-20"
            x-data="{
                cariModalTerbuka: false,
                queryCari: '',
                modulDaftar: [
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir', 'gudang', 'montir']))
                        { judul: 'Dashboard Utama', ket: 'Ringkasan Penjualan, Stok, & Performa Toko', path: '{{ route('dashboard') }}', url: '/admin/dashboard', icon: 'layout-dashboard' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                        { judul: 'Kasir POS (Penjualan Langsung)', ket: 'Transaksi Kasir & Cetak Struk', path: '{{ route('kasir') }}', url: '/admin/kasir', icon: 'scan-barcode' },
                        { judul: 'Nota Penjualan', ket: 'Daftar Riwayat Nota & Invoice Penjualan', path: '{{ route('penjualan.index') }}', url: '/admin/penjualan', icon: 'receipt' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                        { judul: 'Pembelian Stok Barang', ket: 'Faktur Pembelian Barang dari Supplier', path: '{{ route('pembelian.index') }}', url: '/admin/pembelian', icon: 'truck' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir', 'gudang']))
                        { judul: 'Retur Barang', ket: 'Pengembalian Barang Retur Sales & Supplier', path: '{{ route('retur.index') }}', url: '/admin/retur', icon: 'undo-2' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir', 'montir']))
                        { judul: 'Service & Work Order Bengkel', ket: 'Pengerjaan Servis & Work Order Montir', path: '{{ route('service.index') }}', url: '/admin/service', icon: 'wrench' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                        { judul: 'Master Barang & Sparepart', ket: 'Katalog Produk, Sparepart, & Harga Jual', path: '{{ route('barang.index') }}', url: '/admin/barang', icon: 'package' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                        { judul: 'Master Customer / Pelanggan', ket: 'Data Pelanggan & Riwayat Kendaraan', path: '{{ route('customer.index') }}', url: '/admin/customer', icon: 'users' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                        { judul: 'Master Supplier / Vendor', ket: 'Data Vendor & Supplier Sparepart', path: '{{ route('supplier.index') }}', url: '/admin/supplier', icon: 'factory' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin']))
                        { judul: 'Master Sales', ket: 'Data Petugas / Tim Sales Penjualan', path: '{{ route('sales.index') }}', url: '/admin/sales', icon: 'user-check' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                        { judul: 'Kartu Stok & Riwayat Mutasi', ket: 'Riwayat Mutasi Masuk & Keluar Barang', path: '{{ route('stok.kartu') }}', url: '/admin/stok/kartu', icon: 'notebook-text' },
                        { judul: 'Rekap Stok Barang', ket: 'Ringkasan Total Saldo Stok Barang', path: '{{ route('stok.rekap') }}', url: '/admin/stok/rekap', icon: 'layers' },
                        { judul: 'Stok Opname', ket: 'Pemeriksaan & Penyesuaian Stok Fisik', path: '{{ route('stok.opname') }}', url: '/admin/stok/opname', icon: 'clipboard-check' },
                        { judul: 'Stok Menipis & Peringatan', ket: 'Peringatan Barang Di Bawah Stok Minimum', path: '{{ route('stok.menipis') }}', url: '/admin/stok/menipis', icon: 'triangle-alert' },
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin']))
                        { judul: 'Piutang Customer', ket: 'Daftar Piutang & Tagihan Pelanggan', path: '{{ route('keuangan.piutang') }}', url: '/admin/keuangan/piutang', icon: 'hand-coins' },
                        { judul: 'Hutang Supplier', ket: 'Daftar Hutang ke Vendor / Supplier', path: '{{ route('keuangan.hutang') }}', url: '/admin/keuangan/hutang', icon: 'landmark' },
                        { judul: 'Kas Toko', ket: 'Buku Kas & Arus Keluar Masuk Uang Tunai', path: '{{ route('keuangan.kas') }}', url: '/admin/keuangan/kas', icon: 'wallet' },
                        { judul: 'Bank', ket: 'Rekening Bank & Transaksi Non-Tunai', path: '{{ route('keuangan.bank') }}', url: '/admin/keuangan/bank', icon: 'building-2' },
                        { judul: 'Semua Laporan System', ket: 'Pusat Laporan Penjualan, Pembelian, & Keuangan', path: '{{ route('laporan.index') }}', url: '/admin/laporan', icon: 'chart-column' },
                        { judul: 'Laporan Penjualan Harian', ket: 'Rincian Transaksi Penjualan Per Hari', path: '{{ route('laporan.index') }}?kategori=penjualan_harian', url: '/admin/laporan/penjualan-harian', icon: 'receipt' },
                        { judul: 'Laporan Laba Rugi Kotor', ket: 'Laporan Perhitungan Laba Rugi Toko', path: '{{ route('laporan.index') }}?kategori=laba_rugi', url: '/admin/laporan/laba-rugi', icon: 'landmark' },
                    @endif
                    @if($peranSaya === 'owner')
                        { judul: 'Profil Toko & Legality', ket: 'Pengaturan Identitas Toko, Logo, & Alamat', path: '{{ route('pengaturan.toko') }}', url: '/admin/pengaturan/toko', icon: 'settings' },
                        { judul: 'Pengguna System & Peran', ket: 'Manajemen Akun User, Password, & Role', path: '{{ route('pengaturan.user') }}', url: '/admin/pengaturan/user', icon: 'user-cog' },
                        { judul: 'Audit Log System', ket: 'Catatan Jurnal Aktivitas Perubahan Data', path: '{{ route('pengaturan.audit') }}', url: '/admin/pengaturan/audit', icon: 'history' },
                    @endif
                ],
                get modulHasil() {
                    if (!this.queryCari || this.queryCari.trim() === '') {
                        return this.modulDaftar;
                    }
                    const q = this.queryCari.toLowerCase().trim();
                    return this.modulDaftar.filter(m =>
                        m.judul.toLowerCase().includes(q) ||
                        m.ket.toLowerCase().includes(q) ||
                        m.url.toLowerCase().includes(q)
                    );
                }
            }"
            @keydown.window.prevent.cmd.k="cariModalTerbuka = true"
            @keydown.window.prevent.ctrl.k="cariModalTerbuka = true"
        >
            <div class="flex items-center gap-3 min-w-0">
                <!-- Hamburger Toggle Button on Topbar (Primary Mobile Toggle) -->
                <button
                    type="button"
                    x-on:click="sidebarTerbuka = !sidebarTerbuka"
                    class="p-2 rounded-lg text-steel hover:text-ink hover:bg-canvas active:scale-95 transition-all"
                    title="Menu Navigasi"
                >
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
                <h1 class="font-display font-bold text-sm sm:text-base text-ink truncate">{{ $title ?? 'Dashboard' }}</h1>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <!-- Quick Search Input Trigger (Ctrl + K) -->
                <button type="button" x-on:click="cariModalTerbuka = true" class="hidden sm:flex items-center gap-2 bg-canvas hover:bg-line/40 px-3 py-1.5 rounded-lg border border-line text-xs text-steel transition-all">
                    <x-icon name="search" class="w-3.5 h-3.5" />
                    <span>Cari modul...</span>
                    <kbd class="bg-surface border border-line px-1.5 py-0.5 rounded text-[10px] font-mono text-ink">Ctrl K</kbd>
                </button>

                <!-- Clock -->
                <div
                    x-data="{ waktu: '' }"
                    x-init="
                        const tampilkan = () => {
                            waktu = new Intl.DateTimeFormat('id-ID', {
                                dateStyle: 'medium', timeStyle: 'medium', timeZone: 'Asia/Jakarta'
                            }).format(new Date());
                        };
                        tampilkan();
                        setInterval(tampilkan, 1000);
                    "
                    class="hidden lg:flex items-center gap-1.5 text-xs text-steel font-mono bg-canvas px-2.5 py-1 rounded-md border border-line"
                >
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    <span x-text="waktu"></span>
                </div>

                @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                    <x-button as="a" href="{{ route('kasir') }}" variant="primary" class="text-xs px-3 py-1.5">
                        <x-icon name="plus" class="w-3.5 h-3.5" /> <span class="hidden sm:inline">Kasir Baru</span>
                    </x-button>
                @endif

                <!-- Bell Notification Dropdown -->
                <div class="relative" x-data="notifEngine()" x-on:keydown.escape.window="terbuka = false">
                    <button
                        type="button"
                        x-on:click="terbuka = !terbuka"
                        class="relative p-2 rounded-xl text-steel hover:text-ink hover:bg-canvas active:scale-95 transition-all"
                        title="Notifikasi & Aktivitas"
                    >
                        <x-icon name="bell" class="w-5 h-5" />
                        <template x-if="totalBelumDibaca > 0">
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rajawali text-[10px] font-black text-white shadow-sm ring-2 ring-surface">
                                <span x-text="totalBelumDibaca > 9 ? '9+' : totalBelumDibaca"></span>
                            </span>
                        </template>
                    </button>

                    <!-- Notification Popup Card -->
                    <div
                        x-show="terbuka"
                        x-on:click.outside="terbuka = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-surface border border-line shadow-2xl z-50 overflow-hidden"
                        x-cloak
                    >
                        <!-- Header -->
                        <div class="p-4 border-b border-line bg-surface-container-low flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <x-icon name="bell" class="w-4 h-4 text-rajawali" />
                                <h4 class="font-display font-bold text-sm text-ink">Notifikasi &amp; Aktivitas</h4>
                                <span x-show="totalBelumDibaca > 0" class="px-2 py-0.5 text-[10px] font-black rounded-full bg-rajawali/10 text-rajawali border border-rajawali/20" x-text="totalBelumDibaca + ' baru'" x-cloak></span>
                            </div>
                            <button
                                type="button"
                                x-on:click="tandaiSemuaDibaca()"
                                class="text-xs font-semibold text-rajawali hover:underline disabled:opacity-40"
                                x-bind:disabled="totalBelumDibaca === 0"
                            >
                                Tandai semua dibaca
                            </button>
                        </div>

                        <!-- Notification List -->
                        <div class="max-h-80 overflow-y-auto divide-y divide-line">
                            <template x-for="item in daftarNotif" :key="item.id">
                                <div
                                    x-on:click="bukaNotif(item)"
                                    class="p-3.5 hover:bg-canvas cursor-pointer transition duration-150 flex items-start gap-3 relative group"
                                    :class="!item.dibaca ? 'bg-amber-500/5 font-medium' : ''"
                                >
                                    <!-- Icon Type -->
                                    <div
                                        class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-xs mt-0.5 border"
                                        :class="{
                                            'bg-amber-500/10 text-amber-600 border-amber-500/20': item.kategori === 'stok',
                                            'bg-emerald-500/10 text-emerald-600 border-emerald-500/20': item.kategori === 'penjualan',
                                            'bg-blue-500/10 text-blue-600 border-blue-500/20': item.kategori === 'service',
                                            'bg-purple-500/10 text-purple-600 border-purple-500/20': item.kategori === 'keuangan'
                                        }"
                                    >
                                        <template x-if="item.kategori === 'stok'"><x-icon name="triangle-alert" class="w-4 h-4" /></template>
                                        <template x-if="item.kategori === 'penjualan'"><x-icon name="receipt" class="w-4 h-4" /></template>
                                        <template x-if="item.kategori === 'service'"><x-icon name="wrench" class="w-4 h-4" /></template>
                                        <template x-if="item.kategori === 'keuangan'"><x-icon name="hand-coins" class="w-4 h-4" /></template>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="font-bold text-xs text-ink truncate" x-text="item.judul"></span>
                                            <span class="text-[10px] text-steel font-mono shrink-0" x-text="item.waktu"></span>
                                        </div>
                                        <p class="text-xs text-steel mt-0.5 leading-relaxed" x-text="item.pesan"></p>
                                    </div>

                                    <!-- Unread Dot Indicator -->
                                    <div class="flex items-center gap-1 shrink-0 self-center">
                                        <template x-if="!item.dibaca">
                                            <button
                                                type="button"
                                                x-on:click.stop="tandaiDibaca(item.id)"
                                                title="Tandai sudah dibaca"
                                                class="w-2.5 h-2.5 rounded-full bg-rajawali hover:scale-125 transition-transform"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="daftarNotif.length === 0">
                                <div class="p-8 text-center text-steel text-xs">Belum ada notifikasi.</div>
                            </template>
                        </div>

                        <!-- Footer Link -->
                        <div class="p-3 border-t border-line bg-canvas text-center">
                            <a href="{{ route('notifikasi.index') }}" class="text-xs font-bold text-rajawali hover:underline inline-flex items-center gap-1.5 transition">
                                <span>Lihat Semua Notifikasi &amp; Aktivitas</span>
                                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown Menu -->
                <div class="flex items-center gap-2 pl-2 sm:pl-3 border-l border-line relative" x-data="{ menuTerbuka: false }">
                    <button type="button" x-on:click="menuTerbuka = !menuTerbuka" class="flex items-center gap-2 focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-rajawali/10 text-rajawali flex items-center justify-center text-xs font-extrabold border border-rajawali/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block text-xs text-left">
                            <p class="font-bold text-ink leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-steel leading-tight capitalize text-[11px]">{{ auth()->user()->peran }}</p>
                        </div>
                    </button>
                    <div
                        x-show="menuTerbuka"
                        x-cloak
                        x-on:click.outside="menuTerbuka = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="absolute right-0 top-12 bg-surface border border-line rounded-xl shadow-xl w-48 py-2 z-50"
                    >
                        <div class="px-4 py-2 border-b border-line mb-1 sm:hidden">
                            <p class="font-bold text-xs text-ink">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-steel capitalize">{{ auth()->user()->peran }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rajawali hover:bg-rajawali/5 flex items-center gap-2">
                                <x-icon name="log-out" class="w-4 h-4" /> Keluar Sistem
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Global Search Modal (Ctrl + K) -->
            <div
                x-show="cariModalTerbuka"
                x-cloak
                x-on:keydown.escape.window="cariModalTerbuka = false"
                class="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 px-4 bg-black/60 backdrop-blur-sm"
            >
                <div
                    x-on:click.outside="cariModalTerbuka = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-surface border border-line rounded-2xl shadow-2xl w-full max-w-xl overflow-hidden"
                >
                    <div class="p-3 border-b border-line flex items-center gap-3 bg-canvas">
                        <x-icon name="search" class="w-5 h-5 text-steel shrink-0" />
                        <input
                            type="text"
                            x-model="queryCari"
                            x-ref="searchField"
                            x-init="$watch('cariModalTerbuka', value => value && setTimeout(() => $refs.searchField.focus(), 100))"
                            placeholder="Cari modul (cth: pembelian, nota, barang, service, supplier)..."
                            class="w-full bg-transparent text-sm text-ink placeholder-steel focus:outline-none font-medium"
                        />
                        <button type="button" x-on:click="cariModalTerbuka = false" class="p-1 text-steel hover:text-ink rounded-lg">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="p-3 max-h-80 overflow-y-auto space-y-1">
                        <div class="flex items-center justify-between px-3 py-1 mb-1">
                            <p class="text-[11px] font-bold text-steel uppercase tracking-wider">Hasil Navigasi Cepat:</p>
                            <span class="text-[10px] font-mono text-steel" x-text="modulHasil.length + ' modul'"></span>
                        </div>

                        <template x-for="item in modulHasil" :key="item.url">
                            <a :href="item.path" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors group">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-rajawali/10 text-rajawali flex items-center justify-center shrink-0 group-hover:bg-rajawali group-hover:text-white transition-colors">
                                        <x-icon :name="'search'" class="w-3.5 h-3.5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-ink text-xs sm:text-sm truncate" x-text="item.judul"></p>
                                        <p class="text-[11px] text-steel truncate" x-text="item.ket"></p>
                                    </div>
                                </div>
                                <span class="text-[11px] text-steel font-mono shrink-0 ml-2 bg-canvas px-2 py-0.5 rounded border border-line" x-text="item.url"></span>
                            </a>
                        </template>

                        <div x-show="modulHasil.length === 0" class="p-8 text-center text-steel text-xs">
                            <p class="font-semibold text-ink">Modul "<span x-text="queryCari"></span>" tidak ditemukan.</p>
                            <p class="mt-1 text-[11px] text-steel">Pastikan kata kunci sesuai atau periksa hak akses akun Anda (Peran: <span class="capitalize font-bold">{{ auth()->user()->peran }}</span>).</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page View Container -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-canvas/50">
            {{ $slot }}
        </main>
    </div>
</div>
@livewireScripts
<script>
function notifEngine() {
    return {
        terbuka: false,
        daftarNotif: [
            {
                id: 1,
                kategori: 'stok',
                judul: 'Peringatan Stok Menipis',
                pesan: 'OLI FEDERAL MATIC 1L tersisa 3 botol (stok minimum: 10 botol).',
                waktu: '5m lalu',
                url: '{{ route('stok.menipis') }}',
                dibaca: false
            },
            {
                id: 2,
                kategori: 'penjualan',
                judul: 'Transaksi Penjualan Baru',
                pesan: 'Nota PJ2026000123 sebesar Rp 480.000 baru saja diterbitkan kasir.',
                waktu: '25m lalu',
                url: '{{ route('penjualan.index') }}',
                dibaca: false
            },
            {
                id: 3,
                kategori: 'service',
                judul: 'Servis Motor Selesai',
                pesan: 'Servis SV2026000045 (Honda Beat - Andi) telah diselesaikan Montir Wawan.',
                waktu: '1j lalu',
                url: '{{ route('service.index') }}',
                dibaca: false
            },
            {
                id: 4,
                kategori: 'keuangan',
                judul: 'Tagihan Supplier Jatuh Tempo',
                pesan: 'Faktur PT Astra Otoparts (Rp 3.500.000) akan jatuh tempo 3 hari lagi.',
                waktu: 'Kemarin',
                url: '{{ route('keuangan.hutang') }}',
                dibaca: true
            }
        ],

        get totalBelumDibaca() {
            return this.daftarNotif.filter(n => !n.dibaca).length;
        },

        tandaiDibaca(id) {
            const item = this.daftarNotif.find(n => n.id === id);
            if (item) {
                item.dibaca = true;
            }
        },

        tandaiSemuaDibaca() {
            this.daftarNotif.forEach(n => n.dibaca = true);
            if (window.toastSukses) window.toastSukses('Semua notifikasi ditandai sudah dibaca.');
        },

        bukaNotif(item) {
            item.dibaca = true;
            if (item.url) {
                window.location.href = item.url;
            }
        }
    };
}
</script>
</body>
</html>
