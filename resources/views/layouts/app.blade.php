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
<body class="h-full bg-canvas text-ink">
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
        class="fixed inset-y-0 left-0 z-50 md:static shrink-0 border-r border-line bg-surface flex flex-col transition-all duration-300 shadow-2xl md:shadow-none"
        :class="{
            'translate-x-0': sidebarTerbuka,
            '-translate-x-full md:translate-x-0': !sidebarTerbuka,
            'w-64': sidebarTerbuka,
            'md:w-16': !sidebarTerbuka
        }"
    >
        <!-- Sidebar Brand Header -->
        <div class="h-14 flex items-center justify-between px-4 border-b border-line shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0 group">
                <img alt="Rajawali Motor Logo" class="h-9 w-auto object-contain shrink-0 group-hover:scale-105 transition-transform" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
                <span x-show="sidebarTerbuka" class="font-display font-bold text-sm tracking-tight truncate text-rajawali">RAJAWALI MOTOR</span>
            </a>
            <!-- Close button on mobile drawer -->
            <button type="button" x-on:click="sidebarTerbuka = false" class="md:hidden p-1.5 text-steel hover:text-ink rounded-md">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <!-- Sidebar Navigation Items -->
        <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-4" :class="!sidebarTerbuka && 'px-1.5'">
            <x-nav-group label="Utama">
                <x-nav-item href="{{ route('dashboard') }}" icon="layout-dashboard" :active="request()->routeIs('dashboard')">Dashboard</x-nav-item>
            </x-nav-group>

            @if(in_array($peranSaya, ['owner', 'admin', 'kasir', 'gudang', 'montir']))
                <x-nav-group label="Transaksi">
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                        <x-nav-item href="{{ route('kasir') }}" icon="scan-barcode" :active="request()->routeIs('kasir')">Kasir POS</x-nav-item>
                        <x-nav-item href="{{ route('penjualan.index') }}" icon="receipt" :active="request()->routeIs('penjualan.*')">Nota Penjualan</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                        <x-nav-item href="{{ route('pembelian.index') }}" icon="truck" :active="request()->routeIs('pembelian.*')">Pembelian</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir', 'gudang']))
                        <x-nav-item href="{{ route('retur.index') }}" icon="undo-2" :active="request()->routeIs('retur.*')">Retur Barang</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir', 'montir']))
                        <x-nav-item href="{{ route('service.index') }}" icon="wrench" :active="request()->routeIs('service.*')">Service Bengkel</x-nav-item>
                    @endif
                </x-nav-group>
            @endif

            @if(in_array($peranSaya, ['owner', 'admin', 'gudang', 'kasir']))
                <x-nav-group label="Master Data">
                    @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                        <x-nav-item href="{{ route('barang.index') }}" icon="package" :active="request()->routeIs('barang.*')">Master Barang</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'kasir']))
                        <x-nav-item href="{{ route('customer.index') }}" icon="users" :active="request()->routeIs('customer.*')">Master Customer</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                        <x-nav-item href="{{ route('supplier.index') }}" icon="factory" :active="request()->routeIs('supplier.*')">Master Supplier</x-nav-item>
                    @endif
                    @if(in_array($peranSaya, ['owner', 'admin']))
                        <x-nav-item href="{{ route('sales.index') }}" icon="user-check" :active="request()->routeIs('sales.*')">Master Sales</x-nav-item>
                    @endif
                </x-nav-group>
            @endif

            @if(in_array($peranSaya, ['owner', 'admin', 'gudang']))
                <x-nav-group label="Stok">
                    <x-nav-item href="{{ route('stok.kartu') }}" icon="notebook-text" :active="request()->routeIs('stok.kartu')">Kartu Stok</x-nav-item>
                    <x-nav-item href="{{ route('stok.rekap') }}" icon="layers" :active="request()->routeIs('stok.rekap')">Rekap Stok</x-nav-item>
                    <x-nav-item href="{{ route('stok.opname') }}" icon="clipboard-check" :active="request()->routeIs('stok.opname')">Stok Opname</x-nav-item>
                    <x-nav-item href="{{ route('stok.menipis') }}" icon="triangle-alert" :active="request()->routeIs('stok.menipis')">Stok Menipis</x-nav-item>
                </x-nav-group>
            @endif

            @if(in_array($peranSaya, ['owner', 'admin']))
                <x-nav-group label="Keuangan">
                    <x-nav-item href="{{ route('keuangan.piutang') }}" icon="hand-coins" :active="request()->routeIs('keuangan.piutang')">Piutang Customer</x-nav-item>
                    <x-nav-item href="{{ route('keuangan.hutang') }}" icon="landmark" :active="request()->routeIs('keuangan.hutang')">Hutang Supplier</x-nav-item>
                    <x-nav-item href="{{ route('keuangan.kas') }}" icon="wallet" :active="request()->routeIs('keuangan.kas')">Kas Toko</x-nav-item>
                    <x-nav-item href="{{ route('keuangan.bank') }}" icon="building-2" :active="request()->routeIs('keuangan.bank')">Bank</x-nav-item>
                </x-nav-group>

                <x-nav-group label="Laporan">
                    <x-nav-item href="{{ route('laporan.index') }}" icon="chart-column" :active="request()->routeIs('laporan.*')">Semua Laporan</x-nav-item>
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

        <!-- Desktop Collapse Button (Hidden on Mobile) -->
        <button
            type="button"
            x-on:click="sidebarTerbuka = !sidebarTerbuka"
            :aria-label="sidebarTerbuka ? 'Ciutkan sidebar' : 'Lebarkan sidebar'"
            class="hidden md:flex h-11 border-t border-line items-center justify-center text-steel hover:text-ink hover:bg-canvas shrink-0 transition-colors"
        >
            <x-icon name="panel-left" class="w-4 h-4" />
        </button>
    </aside>

    <!-- Main Content Body -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
        <!-- Topbar Header -->
        <header class="h-14 shrink-0 border-b border-line bg-surface flex items-center justify-between px-4 z-20" x-data="{ cariModalTerbuka: false, queryCari: '' }" @keydown.window.prevent.cmd.k="cariModalTerbuka = true" @keydown.window.prevent.ctrl.k="cariModalTerbuka = true">
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
                            placeholder="Cari modul (cth: kasir, nota, barang, service, customer)..."
                            class="w-full bg-transparent text-sm text-ink placeholder-steel focus:outline-none font-medium"
                        />
                        <button type="button" x-on:click="cariModalTerbuka = false" class="p-1 text-steel hover:text-ink rounded-lg">
                            <x-icon name="x" class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="p-3 max-h-80 overflow-y-auto space-y-1">
                        <p class="px-3 py-1 text-[11px] font-bold text-steel uppercase tracking-wider">Navigasi Cepat Modul:</p>

                        <a href="{{ route('kasir') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="scan-barcode" class="w-4 h-4 text-rajawali" />
                                <span>Kasir POS (Penjualan Langsung)</span>
                            </div>
                            <span class="text-xs text-steel font-mono">/admin/kasir</span>
                        </a>

                        <a href="{{ route('penjualan.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="receipt" class="w-4 h-4 text-rajawali" />
                                <span>Nota Penjualan</span>
                            </div>
                            <span class="text-xs text-steel font-mono">/admin/penjualan</span>
                        </a>

                        <a href="{{ route('service.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="wrench" class="w-4 h-4 text-rajawali" />
                                <span>Service &amp; Work Order Bengkel</span>
                            </div>
                            <span class="text-xs text-steel font-mono">/admin/service</span>
                        </a>

                        <a href="{{ route('barang.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="package" class="w-4 h-4 text-rajawali" />
                                <span>Master Barang &amp; Sparepart</span>
                            </div>
                            <span class="text-xs text-steel font-mono">/admin/barang</span>
                        </a>

                        <a href="{{ route('customer.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="users" class="w-4 h-4 text-rajawali" />
                                <span>Master Customer</span>
                            </div>
                            <span class="text-xs text-steel font-mono">/admin/customer</span>
                        </a>

                        <a href="{{ route('stok.kartu') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="notebook-text" class="w-4 h-4 text-rajawali" />
                                <span>Kartu Stok &amp; Riwayat</span>
                            </div>
                            <span class="text-xs text-steel font-mono">/admin/stok/kartu</span>
                        </a>

                        <a href="{{ route('laporan.index') }}" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-canvas text-sm font-medium text-ink transition-colors">
                            <div class="flex items-center gap-2.5">
                                <x-icon name="chart-column" class="w-4 h-4 text-rajawali" />
                                <span>Laporan Keuangan &amp; Laba Rugi</span>
                            </div>
                            <span class="text-xs text-steel font-mono">/admin/laporan</span>
                        </a>
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
</body>
</html>
