<!DOCTYPE html>
<html class="scroll-smooth" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Layanan Ganti Oli Presisi | Rajawali Motor Surabaya</title>
    <meta name="description" content="Servis ganti oli cepat, pelumas full synthetic original, filter oli baru, dan inspeksi 10 titik gratis di Rajawali Motor Surabaya.">

    <!-- OpenGraph Social Preview Meta Tags -->
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="https://rajawalimotor.com/layanan/ganti-oli"/>
    <meta property="og:title" content="Layanan Ganti Oli Presisi | Rajawali Motor Surabaya"/>
    <meta property="og:description" content="Servis ganti oli cepat, pelumas synthetic original, filter oli baru, dan inspeksi 10 titik gratis di Surabaya."/>
    <meta property="og:image" content="https://lh3.googleusercontent.com/aida-public/AB6AXuDEZ0D0oQ_Q4BLY0ebHDI4F0Z-A_rmFJ8D9NgnOeV_bghLl3vMnk7DgwjbJiinLvZPHotXYY4aoTCpyxlRhzsNSUozZpN9cQesggStSL210utLO7l64JvhnFR2pmkvgeBbeEW0eobzz0gGwEKaMPbSFGgOqy7FvsJQ-y5w_OBqsG5S-tSZ1gWWboRakqOlnt_Ac8lg-Klh9PlOWYPUj616OX6FyxTp9TXCv0ipBQCPEQ4zq2gUYr_n9b_RPclcGIwoFUUfpW4wiuWk"/>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/home.js'])

    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        .glass-nav { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .hero-gradient { background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.15) 100%); }
        .kartu-angkat { transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1); }
        .kartu-angkat:hover { transform: translateY(-8px); box-shadow: 0 30px 50px -12px rgba(193, 18, 31, 0.15); }
    </style>
</head>
<body class="bg-background text-on-surface font-sans antialiased overflow-x-hidden relative">

<!-- Floating WhatsApp Button (FAB) -->
<a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin ganti oli...') }}" target="_blank" rel="noopener" aria-label="Chat via WhatsApp"
   class="fixed bottom-6 right-6 z-[99] bg-[#25D366] text-white p-3.5 sm:p-4 rounded-full shadow-2xl hover:bg-[#20bd5a] hover:scale-110 transition-all duration-300 flex items-center justify-center group ring-4 ring-emerald-500/20 active:scale-95">
    <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
    <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-500 ease-in-out font-bold text-xs sm:text-sm pl-0 group-hover:pl-2">Chat WhatsApp</span>
</a>

<!-- Navbar -->
<nav class="fixed top-0 w-full z-50 glass-nav shadow-sm border-b border-outline-variant/20">
    <div class="flex justify-between items-center px-4 sm:px-8 py-3.5 max-w-7xl mx-auto">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img alt="Rajawali Motor Logo" class="h-10 sm:h-12 w-auto object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
            <span class="font-headline-md text-xl sm:text-2xl font-bold text-primary tracking-tight">Rajawali Motor</span>
        </a>
        <div class="hidden lg:flex items-center gap-8 font-medium text-sm text-secondary">
            <a class="hover:text-primary transition-colors" href="{{ route('home') }}">Home</a>
            <a class="text-primary font-bold border-b-2 border-primary pb-0.5" href="{{ route('layanan.ganti-oli') }}">Ganti Oli</a>
            <a class="hover:text-primary transition-colors" href="{{ route('layanan.tune-up') }}">Tune Up</a>
            <a class="hover:text-primary transition-colors" href="{{ route('layanan.injeksi') }}">Injeksi</a>
            <a class="hover:text-primary transition-colors" href="{{ route('layanan.ban-spooring') }}">Ban & Spooring</a>
            <a class="hover:text-primary transition-colors" href="{{ route('layanan.kelistrikan') }}">Kelistrikan</a>
            <a class="hover:text-primary transition-colors" href="{{ route('layanan.ac-mobil') }}">AC Mobil</a>
        </div>
        <div class="hidden sm:flex items-center gap-3">
            <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin booking servis ganti oli...') }}" target="_blank" rel="noopener"
               class="efek-kilau bg-[#25D366] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#20bd5a] transition-all active:scale-95 shadow-md shadow-emerald-500/20 inline-flex items-center gap-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
                <span>Booking Ganti Oli</span>
            </a>
        </div>
        <button class="lg:hidden text-primary p-2 rounded-xl bg-surface-container hover:bg-primary/10" onclick="toggleDrawer()">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
    </div>
</nav>

<!-- Mobile Navigation Drawer Overlay -->
<div class="fixed inset-0 bg-black/60 z-[60] opacity-0 pointer-events-none transition-opacity duration-300" id="drawer-overlay" onclick="toggleDrawer()"></div>

<!-- NavigationDrawer Mobile -->
<nav class="fixed inset-y-0 left-0 w-80 max-w-[85vw] rounded-r-2xl bg-surface z-[70] flex flex-col h-full p-6 -translate-x-full transition-transform duration-300 shadow-2xl overflow-y-auto" id="drawer">
    <div class="flex items-center justify-between pb-6 border-b border-outline-variant/30 mb-6">
        <div class="flex items-center gap-3">
            <img alt="Rajawali Motor Logo" class="h-10 w-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
            <div>
                <h3 class="font-bold text-primary text-base">Rajawali Motor</h3>
                <p class="text-xs text-secondary">Surabaya Care</p>
            </div>
        </div>
        <button class="text-secondary p-1 rounded-lg hover:bg-surface-container" onclick="toggleDrawer()">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <div class="flex flex-col gap-2">
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('home') }}">
            <span class="material-symbols-outlined text-primary">home</span> Home
        </a>
        <a class="flex items-center gap-3.5 p-3.5 bg-primary-container text-white font-semibold rounded-xl shadow-md" href="{{ route('layanan.ganti-oli') }}">
            <span class="material-symbols-outlined text-white">oil_barrel</span> Layanan Ganti Oli
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.tune-up') }}">
            <span class="material-symbols-outlined text-primary">settings</span> Layanan Tune Up
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.injeksi') }}">
            <span class="material-symbols-outlined text-primary">precision_manufacturing</span> Spesialis Injeksi
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.ban-spooring') }}">
            <span class="material-symbols-outlined text-primary">tire_repair</span> Ban & Spooring
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.kelistrikan') }}">
            <span class="material-symbols-outlined text-primary">bolt</span> Kelistrikan & ECU
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.ac-mobil') }}">
            <span class="material-symbols-outlined text-primary">ac_unit</span> AC Mobil
        </a>
    </div>

    <div class="mt-auto pt-6 border-t border-outline-variant/30 flex flex-col gap-3">
        <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin booking servis ganti oli...') }}" target="_blank" rel="noopener" class="efek-kilau w-full bg-[#25D366] text-white text-center py-3.5 rounded-xl font-bold text-sm shadow-lg flex items-center justify-center gap-2 hover:bg-[#20bd5a] transition-all">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
            <span>WhatsApp Booking</span>
        </a>
    </div>
</nav>

<main class="pt-20">
    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDEZ0D0oQ_Q4BLY0ebHDI4F0Z-A_rmFJ8D9NgnOeV_bghLl3vMnk7DgwjbJiinLvZPHotXYY4aoTCpyxlRhzsNSUozZpN9cQesggStSL210utLO7l64JvhnFR2pmkvgeBbeEW0eobzz0gGwEKaMPbSFGgOqy7FvsJQ-y5w_OBqsG5S-tSZ1gWWboRakqOlnt_Ac8lg-Klh9PlOWYPUj616OX6FyxTp9TXCv0ipBQCPEQ4zq2gUYr_n9b_RPclcGIwoFUUfpW4wiuWk')"></div>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        <div class="relative z-10 px-4 sm:px-8 max-w-7xl mx-auto w-full py-16 sm:py-24 text-white">
            <div class="max-w-3xl space-y-6" data-reveal>
                <span class="inline-flex items-center gap-2 bg-primary-container px-4 py-1.5 rounded-full font-bold text-xs uppercase tracking-widest text-white">
                    <span class="material-symbols-outlined text-sm">oil_barrel</span> Premium Lubricant & Care
                </span>
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold leading-tight">Layanan Ganti Oli Cepat & Presisi</h1>
                <p class="text-white/80 text-base sm:text-xl max-w-2xl leading-relaxed">Penggunaan pelumas synthetic original sesuai rekomendasi pabrikan untuk perlindungan maksimal gesekan mesin dan efisiensi bahan bakar.</p>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-4">
                    <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin ganti oli...') }}" target="_blank" rel="noopener"
                       class="efek-kilau bg-primary-container text-white px-8 py-4 rounded-xl font-bold text-base hover:bg-rajawali-dark transition-all shadow-xl text-center active:scale-95">
                        Booking Ganti Oli Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section class="py-20 px-4 sm:px-8 bg-surface">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-16" data-reveal>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-2">KEUNGGULAN GANTI OLI</p>
                <h2 class="font-bold text-2xl sm:text-4xl text-on-surface">Mengapa Pilih Ganti Oli di Rajawali Motor?</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" data-reveal-stagger>
                <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30">
                    <span class="material-symbols-outlined text-primary text-4xl mb-4">verified</span>
                    <h3 class="font-bold text-xl mb-2 text-on-surface">Pelumas 100% Original</h3>
                    <p class="text-secondary text-sm">Jaminan oli asli resmi distributor dari brand terkemuka (Shell, Motul, Castrol, Pertamina).</p>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30">
                    <span class="material-symbols-outlined text-primary text-4xl mb-4">filter_alt</span>
                    <h3 class="font-bold text-xl mb-2 text-on-surface">Filter Oli & Ring Baru</h3>
                    <p class="text-secondary text-sm">Penggantian ring tap oli dan karter filter untuk mencegah kebocoran pelumas.</p>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30">
                    <span class="material-symbols-outlined text-primary text-4xl mb-4">checklist</span>
                    <h3 class="font-bold text-xl mb-2 text-on-surface">Free 10 Point Check</h3>
                    <p class="text-secondary text-sm">Gratis pemeriksaan 10 titik vital kendaraan (rem, aki, air wiper, minyak rem, dll).</p>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30">
                    <span class="material-symbols-outlined text-primary text-4xl mb-4">timer</span>
                    <h3 class="font-bold text-xl mb-2 text-on-surface">Proses Cepat 15 Menit</h3>
                    <p class="text-secondary text-sm">Pengerjaan cepat dengan perlengkapan penyedot & penguras oli modern tanpa mengantri lama.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Packages -->
    <section class="py-20 px-4 sm:px-8 bg-surface-container-low">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-16" data-reveal>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-2">HARGA KERTAS TRANSPARAN</p>
                <h2 class="font-bold text-2xl sm:text-4xl text-on-surface">Paket Ganti Oli Rajawali Motor</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch" data-reveal-stagger>
                <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-3xl border border-outline-variant/40 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="font-bold text-2xl text-on-surface">Standard Care</h3>
                                <p class="text-secondary text-xs mt-1">Oli Mineral Premium</p>
                            </div>
                            <div class="text-right">
                                <p class="text-primary font-bold text-2xl">Rp 75.000</p>
                            </div>
                        </div>
                        <ul class="space-y-3 mb-8 text-sm text-secondary">
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">check</span> Oli Mesin Mineral 1L</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">check</span> Jasa Pengurasan Oli</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">check</span> Cek Kampas Rem</li>
                        </ul>
                    </div>
                    <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Saya ingin pesan Paket Ganti Oli Standard Care') }}" target="_blank" rel="noopener"
                       class="w-full border-2 border-primary text-primary py-3.5 rounded-xl font-bold text-center block hover:bg-primary hover:text-white transition-colors">Pilih Paket</a>
                </div>

                <div data-reveal-item class="kartu-angkat efek-kilau bg-inverse-surface p-8 rounded-3xl ring-4 ring-primary shadow-2xl relative flex flex-col justify-between">
                    <span class="absolute top-0 right-0 bg-primary text-white px-4 py-1.5 rounded-bl-2xl font-bold text-xs uppercase tracking-widest">Recommended</span>
                    <div>
                        <div class="flex justify-between items-start mb-6 pt-2">
                            <div>
                                <h3 class="font-bold text-2xl text-white">Synthetic Pro</h3>
                                <p class="text-primary-fixed-dim text-xs mt-1">Full Synthetic Oil</p>
                            </div>
                            <div class="text-right">
                                <p class="text-primary-fixed-dim font-bold text-2xl">Rp 135.000</p>
                            </div>
                        </div>
                        <ul class="space-y-3 mb-8 text-sm text-white/90">
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-fixed-dim text-base">verified</span> Oli Full Synthetic Premium</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-fixed-dim text-base">verified</span> Filter Oli Original Baru</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary-fixed-dim text-base">verified</span> Free 10 Point Inspection</li>
                        </ul>
                    </div>
                    <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Saya ingin pesan Paket Ganti Oli Synthetic Pro') }}" target="_blank" rel="noopener"
                       class="w-full bg-primary text-white py-3.5 rounded-xl font-bold text-center block shadow-lg hover:bg-[#930011] transition-colors">Ambil Promo</a>
                </div>

                <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-3xl border border-outline-variant/40 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="font-bold text-2xl text-on-surface">Complete Protection</h3>
                                <p class="text-secondary text-xs mt-1">Full Synthetic + Engine Flush</p>
                            </div>
                            <div class="text-right">
                                <p class="text-primary font-bold text-2xl">Rp 220.000</p>
                            </div>
                        </div>
                        <ul class="space-y-3 mb-8 text-sm text-secondary">
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">check</span> Oli Synthetic Grade Racing</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">check</span> Engine Flush Detergent Liquid</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">check</span> Oli Gardan Transmisi Baru</li>
                            <li class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-base">check</span> Scan Komputer ECU</li>
                        </ul>
                    </div>
                    <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Saya ingin pesan Paket Ganti Oli Complete Protection') }}" target="_blank" rel="noopener"
                       class="w-full border-2 border-primary text-primary py-3.5 rounded-xl font-bold text-center block hover:bg-primary hover:text-white transition-colors">Pilih Paket</a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-inverse-surface text-white py-12 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-white/50">
        <p>© {{ now()->year }} Rajawali Motor Surabaya. Precision Automotive Care.</p>
        <a href="{{ route('home') }}" class="hover:text-white underline">Kembali ke Beranda Utama</a>
    </div>
</footer>

<script>
    function toggleDrawer() {
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('drawer-overlay');
        if (!drawer || !overlay) return;
        const isHidden = drawer.classList.contains('-translate-x-full');
        if (isHidden) {
            drawer.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
        } else {
            drawer.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
        }
    }
</script>
</body>
</html>
