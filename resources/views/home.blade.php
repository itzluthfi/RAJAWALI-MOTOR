<!DOCTYPE html>
<html class="scroll-smooth" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Rajawali Motor — Bengkel &amp; Sparepart Motor Surabaya</title>

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/png" href="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc">
    <link rel="apple-touch-icon" href="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc">
    <meta name="description" content="Bengkel & Service Center Spesialis Injeksi, Tune Up, Ganti Oli, AC Mobil, Kelistrikan, dan Body Repair dengan standar teknisi profesional di Siwalankerto, Surabaya.">

    <!-- OpenGraph Social Preview Meta Tags -->
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="https://rajawalimotor.com"/>
    <meta property="og:title" content="Rajawali Motor Surabaya | Precision Automotive Care"/>
    <meta property="og:description" content="Bengkel & Service Center Spesialis Injeksi, Tune Up, Ganti Oli, AC Mobil, Kelistrikan, dan Body Repair dengan standar teknisi profesional di Surabaya."/>
    <meta property="og:image" content="https://lh3.googleusercontent.com/aida-public/AB6AXuD5JYvswa21ywwHQhN0yYz9yFx5WrXNr5eVDRzYnEAM9QAMAINPQQs4wMniaHMAZWQDmQJBUE8ZgKMneqnaMP279v-IkwmmgKidG7EWKXIARYA8Xe-MW5ILdnfF1aH-XtrXGvX5SJsYLzbb8bqVL0IOChvKvACiZ_Wy34kBBUD3xlZ6A5IlRXU-JvPSh7O_HQD1KZXISrzyzWNMsARSUbRw8_YPT2h2zKr2RXj3AKLemayIHKLcY95sE_CQlBMLw45JgqW5rwPfTJU"/>
    <meta property="og:site_name" content="Rajawali Motor Surabaya"/>

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="Rajawali Motor Surabaya | Precision Automotive Care"/>
    <meta name="twitter:description" content="Bengkel & Service Center Spesialis Injeksi, Tune Up, Ganti Oli, AC Mobil, Kelistrikan, dan Body Repair di Surabaya."/>
    <meta name="twitter:image" content="https://lh3.googleusercontent.com/aida-public/AB6AXuD5JYvswa21ywwHQhN0yYz9yFx5WrXNr5eVDRzYnEAM9QAMAINPQQs4wMniaHMAZWQDmQJBUE8ZgKMneqnaMP279v-IkwmmgKidG7EWKXIARYA8Xe-MW5ILdnfF1aH-XtrXGvX5SJsYLzbb8bqVL0IOChvKvACiZ_Wy34kBBUD3xlZ6A5IlRXU-JvPSh7O_HQD1KZXISrzyzWNMsARSUbRw8_YPT2h2zKr2RXj3AKLemayIHKLcY95sE_CQlBMLw45JgqW5rwPfTJU"/>

    {{-- Fonts & Material Symbols Icon Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/home.js'])

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .hero-gradient {
            background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.5) 50%, rgba(0,0,0,0.15) 100%);
        }
        .kartu-angkat {
            transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .kartu-angkat:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px -12px rgba(193, 18, 31, 0.16);
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #970012; border-radius: 10px; }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md overflow-x-hidden relative">

<!-- Floating WhatsApp Button (FAB) -->
<a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin konsultasi / booking servis...') }}" target="_blank" rel="noopener" aria-label="Chat via WhatsApp"
   class="fixed bottom-6 right-6 z-[99] bg-[#25D366] text-white p-3.5 sm:p-4 rounded-full shadow-2xl hover:bg-[#20bd5a] hover:scale-110 transition-all duration-300 flex items-center justify-center group ring-4 ring-emerald-500/20 active:scale-95">
    <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
    <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-xs transition-all duration-500 ease-in-out font-bold text-xs sm:text-sm pl-0 group-hover:pl-2">Chat WhatsApp</span>
</a>

<!-- Navbar -->
<nav class="fixed top-0 w-full z-50 glass-nav shadow-sm border-b border-outline-variant/20">
    <div class="flex justify-between items-center px-4 sm:px-8 py-3.5 max-w-7xl mx-auto">
        <a href="#beranda" class="flex items-center gap-3 group">
            <img alt="Rajawali Motor Logo" class="h-10 sm:h-12 w-auto object-contain group-hover:scale-105 transition-transform duration-300" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
            <span class="font-headline-md text-xl sm:text-2xl font-bold text-primary tracking-tight">Rajawali Motor</span>
        </a>
        <div class="hidden lg:flex items-center gap-7 font-medium text-sm text-secondary">
            <a class="text-primary font-bold border-b-2 border-primary pb-0.5" href="#beranda">Home</a>
            <a class="hover:text-primary transition-colors" href="#tentang">Tentang</a>
            <a class="hover:text-primary transition-colors" href="#layanan">Layanan</a>
            <a class="hover:text-primary transition-colors" href="#kalkulator">Estimasi Biaya</a>
            <a class="hover:text-primary transition-colors" href="{{ route('layanan.injeksi') }}">Spesialis Injeksi</a>
            <a class="hover:text-primary transition-colors" href="#faq">FAQ</a>
            <a class="hover:text-primary transition-colors" href="#kontak">Kontak</a>
        </div>
        <div class="hidden sm:flex items-center gap-3">
            <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin booking servis...') }}" target="_blank" rel="noopener"
               class="efek-kilau bg-[#25D366] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#20bd5a] transition-all active:scale-95 shadow-md shadow-emerald-500/20 inline-flex items-center gap-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
                <span>Booking WhatsApp</span>
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
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="#beranda" onclick="toggleDrawer()">
            <span class="material-symbols-outlined text-primary">home</span> Home
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="#layanan" onclick="toggleDrawer()">
            <span class="material-symbols-outlined text-primary">build</span> Semua Layanan
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="#kalkulator" onclick="toggleDrawer()">
            <span class="material-symbols-outlined text-primary">calculate</span> Kalkulator Biaya
        </a>
        <a class="flex items-center gap-3.5 p-3.5 bg-primary-container text-white font-semibold rounded-xl shadow-md" href="{{ route('layanan.injeksi') }}">
            <span class="material-symbols-outlined text-white">precision_manufacturing</span> Layanan Spesialis Injeksi
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.ganti-oli') }}">
            <span class="material-symbols-outlined text-primary">oil_barrel</span> Layanan Ganti Oli
        </a>
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.tune-up') }}">
            <span class="material-symbols-outlined text-primary">settings</span> Layanan Tune Up
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
        <a class="flex items-center gap-3.5 p-3.5 text-on-surface font-medium hover:bg-surface-container transition-all rounded-xl" href="{{ route('layanan.body-repair') }}">
            <span class="material-symbols-outlined text-primary">car_repair</span> Body Repair
        </a>
    </div>

    <div class="mt-auto pt-6 border-t border-outline-variant/30 flex flex-col gap-3">
        <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin booking servis...') }}" target="_blank" rel="noopener" class="efek-kilau w-full bg-[#25D366] text-white text-center py-3.5 rounded-xl font-bold text-sm shadow-lg flex items-center justify-center gap-2 hover:bg-[#20bd5a] transition-all">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
            <span>WhatsApp Booking</span>
        </a>
    </div>
</nav>

<main>
    <!-- Hero Section -->
    <section class="relative min-h-[90vh] lg:min-h-screen flex items-center overflow-hidden pt-16" id="beranda">
        <div class="absolute inset-0 z-0">
            <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD5JYvswa21ywwHQhN0yYz9yFx5WrXNr5eVDRzYnEAM9QAMAINPQQs4wMniaHMAZWQDmQJBUE8ZgKMneqnaMP279v-IkwmmgKidG7EWKXIARYA8Xe-MW5ILdnfF1aH-XtrXGvX5SJsYLzbb8bqVL0IOChvKvACiZ_Wy34kBBUD3xlZ6A5IlRXU-JvPSh7O_HQD1KZXISrzyzWNMsARSUbRw8_YPT2h2zKr2RXj3AKLemayIHKLcY95sE_CQlBMLw45JgqW5rwPfTJU')"></div>
            <div class="absolute inset-0 hero-gradient"></div>
        </div>
        <div class="relative z-10 px-4 sm:px-8 max-w-7xl mx-auto w-full py-16 sm:py-24">
            <div class="max-w-3xl space-y-6" data-reveal>
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/20">
                    <span class="material-symbols-outlined text-primary-fixed-dim" style="font-variation-settings: 'FILL' 1;">star</span>
                    <span class="text-white font-label-bold text-xs sm:text-sm">4.9/5 Google Rating Customer Satisfaction</span>
                </div>
                <h1 class="font-display-lg text-3xl sm:text-5xl lg:text-6xl text-white font-extrabold leading-tight">Solusi Servis Kendaraan Terpercaya di Surabaya</h1>
                <p class="font-body-lg text-base sm:text-xl text-white/80 max-w-xl leading-relaxed">Perawatan presisi dengan teknologi modern dan mekanik ahli berpengalaman untuk memastikan performa kendaraan Anda tetap prima di setiap putaran roda.</p>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-4">
                    <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya ingin booking servis...') }}" target="_blank" rel="noopener"
                       class="efek-kilau bg-primary-container text-on-secondary px-8 sm:px-10 py-4 sm:py-5 rounded-xl font-label-bold text-base hover:bg-[#930011] transition-all shadow-xl shadow-primary/30 text-center flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">calendar_today</span>
                        <span>Booking Servis</span>
                    </a>
                    <a href="tel:{{ $toko['telepon'] }}"
                       class="efek-kilau border-2 border-white text-white px-8 sm:px-10 py-4 sm:py-5 rounded-xl font-label-bold text-base hover:bg-white/10 transition-all text-center flex items-center justify-center gap-2 active:scale-95">
                        <span class="material-symbols-outlined">call</span>
                        <span>Hubungi Kami</span>
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-6">
                    <div class="inline-flex flex-wrap items-center gap-3.5 sm:gap-5 bg-black/80 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/20 shadow-2xl">
                        <span data-status-badge class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/30 text-rose-200 border border-rose-500/50 shadow-sm">
                            <span data-status-dot class="w-2.5 h-2.5 rounded-full bg-rose-400 animate-pulse"></span>
                            <span data-status-buka class="font-mono tracking-wide">Tutup</span>
                        </span>
                        <span class="inline-flex items-center gap-2 text-white font-medium text-xs sm:text-sm">
                            <span class="material-symbols-outlined text-primary-fixed-dim text-lg">schedule</span>
                            <span>07:30 – 17:00 setiap hari</span>
                        </span>
                        <span class="hidden sm:inline-block text-white/30">•</span>
                        <span class="inline-flex items-center gap-2 text-white font-medium text-xs sm:text-sm">
                            <span class="material-symbols-outlined text-primary-fixed-dim text-lg">location_on</span>
                            <span>Siwalankerto Timur, Surabaya</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-inverse-surface py-12 border-y border-outline-variant/20 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 grid grid-cols-2 md:grid-cols-4 gap-6 text-center" data-reveal-stagger>
            <div data-reveal-item class="space-y-1 p-3">
                <p class="font-display-lg text-4xl sm:text-5xl lg:text-6xl text-primary-fixed-dim font-black drop-shadow-md tracking-tight" data-counter="15" data-suffix="+">0</p>
                <p class="font-label-bold text-xs sm:text-sm text-white/90 uppercase tracking-widest pt-1">Tahun Pengalaman</p>
            </div>
            <div data-reveal-item class="space-y-1 p-3">
                <p class="font-display-lg text-4xl sm:text-5xl lg:text-6xl text-primary-fixed-dim font-black drop-shadow-md tracking-tight" data-counter="5000" data-suffix="+">0</p>
                <p class="font-label-bold text-xs sm:text-sm text-white/90 uppercase tracking-widest pt-1">Pelanggan Puas</p>
            </div>
            <div data-reveal-item class="space-y-1 p-3">
                <p class="font-display-lg text-4xl sm:text-5xl lg:text-6xl text-primary-fixed-dim font-black drop-shadow-md tracking-tight" data-counter="100" data-suffix="%">0</p>
                <p class="font-label-bold text-xs sm:text-sm text-white/90 uppercase tracking-widest pt-1">Teknisi Bersertifikat</p>
            </div>
            <div data-reveal-item class="space-y-1 p-3">
                <p class="font-display-lg text-4xl sm:text-5xl lg:text-6xl text-primary-fixed-dim font-black drop-shadow-md tracking-tight" data-counter="100" data-suffix="%">0</p>
                <p class="font-label-bold text-xs sm:text-sm text-white/90 uppercase tracking-widest pt-1">Garansi Servis Terjamin</p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-20 sm:py-28 px-4 sm:px-8 bg-surface scroll-mt-16" id="layanan">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" data-reveal>
                <p class="text-primary font-label-bold text-xs sm:text-sm uppercase tracking-widest mb-2">Layanan Kami</p>
                <h2 class="font-headline-lg text-2xl sm:text-4xl text-on-surface font-bold">Solusi Teknikal Menyeluruh</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8" data-reveal-stagger>
                <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30 group">
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">build</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface">Servis Berkala</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Perawatan rutin terintegrasi untuk menjaga performa mesin tetap optimal.</p>
                </div>

                <a href="{{ route('layanan.tune-up') }}" data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30 group block">
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">settings</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface group-hover:text-primary transition-colors">Tune Up</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Pengaturan presisi komponen mesin untuk efisiensi bahan bakar maksimal.</p>
                    <span class="text-primary font-bold text-xs flex items-center gap-1">Lihat Detail Paket <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </a>

                <a href="{{ route('layanan.ganti-oli') }}" data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30 group block">
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">oil_barrel</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface group-hover:text-primary transition-colors">Ganti Oli</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Penggunaan pelumas grade premium sesuai spesifikasi pabrikan kendaraan.</p>
                    <span class="text-primary font-bold text-xs flex items-center gap-1">Lihat Detail Paket <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </a>

                <a href="{{ route('layanan.ac-mobil') }}" data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30 group block">
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">ac_unit</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface group-hover:text-primary transition-colors">AC Mobil</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Pembersihan dan pengisian freon untuk kenyamanan kabin yang maksimal.</p>
                    <span class="text-primary font-bold text-xs flex items-center gap-1">Lihat Detail Paket <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </a>

                <a href="{{ route('layanan.ban-spooring') }}" data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30 group block">
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">tire_repair</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface group-hover:text-primary transition-colors">Ban &amp; Spooring</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Penyelarasan kaki-kaki mobil untuk stabilitas berkendara yang sempurna.</p>
                    <span class="text-primary font-bold text-xs flex items-center gap-1">Lihat Detail Paket <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </a>

                <a href="{{ route('layanan.kelistrikan') }}" data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30 group block">
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">bolt</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface group-hover:text-primary transition-colors">Kelistrikan</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Diagnosa menyeluruh sistem elektrikal dan ECU dengan alat modern.</p>
                    <span class="text-primary font-bold text-xs flex items-center gap-1">Lihat Detail Paket <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </a>

                <a href="{{ route('layanan.injeksi') }}" data-reveal-item class="kartu-angkat efek-kilau bg-primary-container/5 p-8 rounded-2xl border-2 border-primary-container/40 group block relative">
                    <span class="absolute top-3 right-3 bg-primary-container text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Unggulan</span>
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">precision_manufacturing</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface group-hover:text-primary transition-colors">Injeksi</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Optimasi sistem pembakaran untuk tarikan mesin yang lebih responsif.</p>
                    <span class="text-primary font-bold text-xs flex items-center gap-1">Lihat Spesialis Injeksi <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </a>

                <a href="{{ route('layanan.body-repair') }}" data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30 group block">
                    <span class="material-symbols-outlined text-primary text-4xl mb-6 group-hover:scale-110 transition-transform">car_repair</span>
                    <h3 class="font-headline-sm text-xl font-bold mb-3 text-on-surface group-hover:text-primary transition-colors">Body Repair</h3>
                    <p class="text-secondary text-sm leading-relaxed mb-4">Pemulihan estetika eksterior dengan standar pengecatan premium.</p>
                    <span class="text-primary font-bold text-xs flex items-center gap-1">Lihat Detail Paket <span class="material-symbols-outlined text-xs">arrow_forward</span></span>
                </a>
            </div>
        </div>
    </section>

    <!-- NEW SECTION 1: Interactive Service Cost Calculator Widget -->
    <section class="py-20 sm:py-24 px-4 sm:px-8 bg-surface-container-low border-y border-outline-variant/20 scroll-mt-16" id="kalkulator">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12" data-reveal>
                <span class="inline-flex items-center gap-2 bg-primary-container/10 text-primary px-4 py-1.5 rounded-full font-bold text-xs uppercase tracking-widest mb-3">
                    <span class="material-symbols-outlined text-sm">calculate</span> Instant Price Estimator
                </span>
                <h2 class="font-headline-lg text-2xl sm:text-4xl text-on-surface font-bold">Kalkulator Estimasi Biaya Servis</h2>
                <p class="text-secondary text-sm sm:text-base mt-2 max-w-xl mx-auto">Pilih tipe kendaraan dan jenis servis yang Anda butuhkan untuk mendapatkan perkiraan transparan secara instan.</p>
            </div>

            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-outline-variant/30 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center" data-reveal>
                <div class="lg:col-span-7 space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2.5">1. Pilih Jenis Kendaraan</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="calc-vehicle-group">
                            <button type="button" onclick="setCalcVehicle('City Car / Hatchback', 1)" class="calc-v-btn active p-3 rounded-xl border-2 border-primary bg-primary/5 text-primary font-bold text-xs text-center transition-all flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-2xl">directions_car</span> City Car
                            </button>
                            <button type="button" onclick="setCalcVehicle('MPV / SUV Family', 1.2)" class="calc-v-btn p-3 rounded-xl border border-outline-variant/40 hover:border-primary/50 text-on-surface font-medium text-xs text-center transition-all flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-2xl">minor_crash</span> SUV / MPV
                            </button>
                            <button type="button" onclick="setCalcVehicle('Sedan Premium', 1.3)" class="calc-v-btn p-3 rounded-xl border border-outline-variant/40 hover:border-primary/50 text-on-surface font-medium text-xs text-center transition-all flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-2xl">car_rental</span> Sedan
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-secondary mb-2.5">2. Pilih Layanan Utama</label>
                        <select id="calc-service-select" onchange="updateCalcEstimator()" class="w-full p-4 rounded-xl border border-outline-variant/40 text-on-surface font-bold text-sm bg-surface focus:ring-2 focus:ring-primary focus:outline-none">
                            <option value="ganti_oli" data-min="135000" data-max="220000">Ganti Oli Synthetic & Filter (Rp 135rb - Rp 220rb)</option>
                            <option value="tune_up" data-min="120000" data-max="390000">Tune Up Presisi & Carbon Clean (Rp 120rb - Rp 390rb)</option>
                            <option value="injeksi" data-min="195000" data-max="595000">Servis Injeksi Ultrasonik & Scan ECU (Rp 195rb - Rp 595rb)</option>
                            <option value="spooring" data-min="100000" data-max="250000">Spooring 3D Laser & Balancing 4 Roda (Rp 100rb - Rp 250rb)</option>
                            <option value="ac_mobil" data-min="150000" data-max="550000">Servis AC Mobil & Flushing Freon (Rp 150rb - Rp 550rb)</option>
                            <option value="body_repair" data-min="500000" data-max="2500000">Body Repair & Cat Oven (Per Panel / Full Body)</option>
                        </select>
                    </div>
                </div>

                <div class="lg:col-span-5 bg-inverse-surface text-white p-6 sm:p-8 rounded-2xl flex flex-col justify-between space-y-6 shadow-2xl relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-primary/20 rounded-full blur-2xl"></div>
                    <div>
                        <p class="text-white/60 text-xs font-bold uppercase tracking-widest">Estimasi Total Biaya</p>
                        <h3 class="text-3xl sm:text-4xl font-extrabold text-primary-fixed-dim mt-2" id="calc-result-price">Rp 135.000 - Rp 220.000</h3>
                        <p class="text-xs text-white/70 mt-2 leading-relaxed" id="calc-result-desc">Termasuk jasa teknisi bersertifikat, garansi pengerjaan, dan pemeriksaan 10 titik vital kendaraan gratis.</p>
                    </div>
                    <a id="calc-wa-btn" href="#" target="_blank" rel="noopener"
                       class="efek-kilau w-full bg-[#25D366] hover:bg-[#20bd5a] text-white py-4 rounded-xl font-bold text-center flex items-center justify-center gap-2 shadow-lg transition-all active:scale-95 text-sm">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
                        <span>Booking Estimasi Ini via WA</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20 sm:py-28 px-4 sm:px-8 bg-surface-container" id="tentang">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8" data-reveal-stagger>
            <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30">
                <div class="w-16 h-16 bg-primary-container/10 flex items-center justify-center rounded-2xl mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">verified</span>
                </div>
                <h3 class="font-headline-md text-2xl font-bold text-on-surface">Teknisi Ahli</h3>
                <p class="text-secondary font-body-md text-sm leading-relaxed">Tim kami terdiri dari mekanik bersertifikat yang dilatih secara profesional untuk menangani berbagai merek kendaraan dengan standar pabrik.</p>
            </div>
            <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30">
                <div class="w-16 h-16 bg-primary-container/10 flex items-center justify-center rounded-2xl mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">biotech</span>
                </div>
                <h3 class="font-headline-md text-2xl font-bold text-on-surface">Alat Diagnostik Modern</h3>
                <p class="text-secondary font-body-md text-sm leading-relaxed">Menggunakan scanner dan peralatan terbaru untuk memastikan akurasi diagnosa masalah kendaraan Anda dalam waktu singkat.</p>
            </div>
            <div data-reveal-item class="kartu-angkat efek-kilau bg-white p-8 rounded-2xl border border-outline-variant/30">
                <div class="w-16 h-16 bg-primary-container/10 flex items-center justify-center rounded-2xl mb-4">
                    <span class="material-symbols-outlined text-primary text-3xl">handshake</span>
                </div>
                <h3 class="font-headline-md text-2xl font-bold text-on-surface">Transparansi Harga</h3>
                <p class="text-secondary font-body-md text-sm leading-relaxed">Estimasi biaya yang jelas di depan tanpa biaya tersembunyi. Kami hanya melakukan penggantian suku cadang atas persetujuan Anda.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-20 sm:py-28 px-4 sm:px-8 bg-white scroll-mt-16" id="galeri">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-end mb-12" data-reveal>
                <div>
                    <p class="text-primary font-label-bold text-xs uppercase tracking-widest mb-2">Workshop Galeri</p>
                    <h2 class="font-headline-lg text-2xl sm:text-4xl font-bold text-on-surface">Fasilitas Modern &amp; Terpercaya</h2>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6" data-reveal-stagger>
                <div data-reveal-item class="kartu-angkat efek-kilau aspect-[4/3] rounded-2xl overflow-hidden group border border-outline-variant/20 shadow-sm relative">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBaVBo1o87keGX7BojiuwmIQ9ZDdfukT-hvsqCFVHJOAGIhHKzJeZGna9hwH_iODo91-X979SLNps5WOMYN9phM7wC0IQQXvNnyv9nKjwgqxqOlN61lS2WwHuoHOeqQuh9xD4DskNgYJKXC_lJAGTcC_gG5nJqkUOlOTD6ZMf6Zhbz_hHBHJ3kgrz2gLup49HBltH2s0VddvozlZPilFk5o2JbXgT16dnWloGpXbg1DQUUpKBvgIgdY8Fgj6GybdL9mMG_AAbGgMbw" alt="Area Workshop Rajawali Motor"/>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau aspect-[4/3] rounded-2xl overflow-hidden group border border-outline-variant/20 shadow-sm relative">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAwZRhUVOA8dh9lRGumtA8A13TadrSkhT6gRIJx9nmjblYPdRE7-slJBrlnxmwPRH3EXfz16K-S7VAgHolox7HWgOVU0BZNgmikfrabup5gdrhnY2zEbxE-winTilsKQPjmfXS2o1jLxwgflWa30j8-c7QcsLIy1zY_TUEyUyG_Y-mK95fHMwQ4i5CXez0eEHaW9_xPWmB5C1Ymt0jX5bL1ZXQ1WyD5ZlHxGdDdl0h5QKKhTaHEPUYiQq_RcPOXM2x1bpAvX9_Flkg" alt="Diagnosa Scanner OBD Tablet"/>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau aspect-[4/3] rounded-2xl overflow-hidden group border border-outline-variant/20 shadow-sm relative">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBDEuN6fwPk07FUlEPlxbPa-QTBPAsAPs-RdXRzZ23jOYxjPpctulobPl8nnUGLEJ3ecFtfH_WnsXUA_o0CURkFwjhX_4fVt68VhSiqkb4EkCP3dnGNe7DiGhE1vHdc-4GeCpKLy2V5BwOfxrQ2YS35yd5XZDUTR6IgoAUrilAa45Eahj9fkvC05YlCbZdFkrqwj3Dmogj1OXpmlxJBujtCqMjgDBT1FrMlf2Ymfc2XGwIvL-amUXPnay8jWUZjJvYOQo7Ha0R78Uw" alt="Ruang Tunggu Workshop Rajawali"/>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau aspect-[4/3] rounded-2xl overflow-hidden group border border-outline-variant/20 shadow-sm relative">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDEZ0D0oQ_Q4BLY0ebHDI4F0Z-A_rmFJ8D9NgnOeV_bghLl3vMnk7DgwjbJiinLvZPHotXYY4aoTCpyxlRhzsNSUozZpN9cQesggStSL210utLO7l64JvhnFR2pmkvgeBbeEW0eobzz0gGwEKaMPbSFGgOqy7FvsJQ-y5w_OBqsG5S-tSZ1gWWboRakqOlnt_Ac8lg-Klh9PlOWYPUj616OX6FyxTp9TXCv0ipBQCPEQ4zq2gUYr_n9b_RPclcGIwoFUUfpW4wiuWk" alt="Sparepart & Pelumas Premium"/>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau aspect-[4/3] rounded-2xl overflow-hidden group border border-outline-variant/20 shadow-sm relative">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBpO0otRCjbc3xPC489K5Rqf_XwfDph2qNFST_6dsVTTfVnoGVbzyVvxcHfMh9hzdzYXcjicGD8fYQYpKjl8u6ojLdcaxIagqeAhkyxpiYlJCinQUich1LO2l93vye3s8fpniNuxyzsc0csP1z0oNMIsVuOofQOenTt39WLbpRY65zb9MCna8iO-vgTLPCs6gJzKbFbFuAvVnSAmtMDStJNc975INbs-0Otv0mXguRLRtppT5ogcA_r5YReQsI7O8_NItKWsHb95D0" alt="Spooring Alignment Machine"/>
                </div>
                <div data-reveal-item class="kartu-angkat efek-kilau aspect-[4/3] rounded-2xl overflow-hidden group border border-outline-variant/20 shadow-sm relative">
                    <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBFhydXEHgeLm7ZfscASgXtjpt6wKd5Db1WD8E4HAOnzL4UlWlN8HB1sekcGYVUm7901M3UrA5ITlCJ0pdwDBV51lnmlQyVnJ-EAMvnxO9lozz6QhpCU8Di2Cuhi4nfosmaLg2kQDMyQRJPpz2QYjafrWD6-O0Jd44dHvNEpqfxuWw510ohG0-LiCchUbvu33Vl13fT1EWZstA3URlwfKM6MJ2AsueIXO4hSBXZ7k_TjnbgWlj7yQ0zoujBxM42_IyTUpMJvQA0PUE" alt="Exterior Workshop Rajawali Motor"/>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 sm:py-28 px-4 sm:px-8 bg-surface-container-low scroll-mt-16" id="testimoni">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16" data-reveal>
                <p class="text-primary font-label-bold text-xs uppercase tracking-widest mb-2">Testimoni</p>
                <h2 class="font-headline-lg text-2xl sm:text-4xl font-bold text-on-surface">Apa Kata Pelanggan Kami?</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-reveal-stagger>
                <div data-reveal-item class="kartu-angkat bg-white p-8 rounded-2xl shadow-sm border border-outline-variant space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center gap-1 text-primary">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                        </div>
                        <p class="text-on-surface italic text-sm leading-relaxed">"Servis di sini sangat memuaskan. Mekaniknya komunikatif dan hasil kerjanya sangat presisi. Mobil saya terasa seperti baru kembali setelah ganti oli dan tune up."</p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-outline-variant/20">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high overflow-hidden shrink-0">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDI7h99ODY5qPNvrAt30HiOXPEStHxfX9NAO-a-zQKOU2ZPxeIwM8XZZjxC-HHJN-bPUTRSieE9ox3FPNDxiXKCsgy2PaNR6wEHUphIe_rdgRIt8SIdP5TeCJazA8c3PnH9Msrtampb3ffZ-v33Ei7QQOm6loChND5KtqtGumD7ovNrLmwK8RHpBZwGPeegi5kB4e_p6ObSHmHm3qOPFIKZqNJtWDtiB6Q7eFlPISo6bU9JtCpujlXRJNfixB6yMjkOP0MQQQkJ-3U" alt="Budi Santoso"/>
                        </div>
                        <div>
                            <p class="font-bold text-on-surface text-sm">Budi Santoso</p>
                            <p class="text-secondary text-xs">Pemilik Toyota Camry</p>
                        </div>
                    </div>
                </div>

                <div data-reveal-item class="kartu-angkat bg-white p-8 rounded-2xl shadow-sm border border-outline-variant space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center gap-1 text-primary">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                        </div>
                        <p class="text-on-surface italic text-sm leading-relaxed">"Paling suka dengan transparansinya. Sebelum dikerjakan selalu dikasih estimasi harga dan sparepart yang perlu diganti. Sangat rekomen untuk yang cari bengkel jujur."</p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-outline-variant/20">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high overflow-hidden shrink-0">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAoyMAOi5TI5gU4pBwQ6ET6Ct3B8MDym4JcUwgtzYoSJS-sG9VCBiVJib_fzdJt729vPX9Tj_LAi7pOl_MzI27ds7ZJ51FRYKlbgxtvlb5qws0ph3IYAEsP5OLilHqVvbym2-bK3c2BkcIHTgUHByYIbt-yOrlNMR0HAG4M8X3R43x7OQiV6CkAyeXSDOF_lt6NlGMVpa3ZhHVwkyyTSAKNniksqtNJHltd0Im99cJgImCcuvGdgpkgKjzVKVCZJoeP1TCfVexbUH8" alt="Maya Putri"/>
                        </div>
                        <div>
                            <p class="font-bold text-on-surface text-sm">Maya Putri</p>
                            <p class="text-secondary text-xs">Pemilik Honda CR-V</p>
                        </div>
                    </div>
                </div>

                <div data-reveal-item class="kartu-angkat bg-white p-8 rounded-2xl shadow-sm border border-outline-variant space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center gap-1 text-primary">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">star</span>
                        </div>
                        <p class="text-on-surface italic text-sm leading-relaxed">"Fasilitas ruang tunggunya nyaman sekali. Ada kopi dan Wi-Fi kencang, jadi bisa sambil kerja sementara mobil dikerjakan dengan alat-alat modern."</p>
                    </div>
                    <div class="flex items-center gap-4 pt-4 border-t border-outline-variant/20">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high overflow-hidden shrink-0">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBiAHDMelJxm1gk8jEwr_-wEaVfLGrdKXHY3LjheoDaijmk699wUiI374Vq6pi6ysKwwRgvRmsaHUsN4eo-IbGSrYLwvpukhfaEUr2K4USipmTTyGPc360P4ePF7Bzcj22lEwHebx02wSzmUQjDk88oWsWORJcUs-y-SW458j86i5cf6ahW_HKnPABBfpYhRGczkdyCGpH3OHfs-nqBpTnla9SrWL_a4zRt1jGO4K7su8rdsuRgprAgs0Cc1sxm4YcpDXONzUHb1ss" alt="Andra Wijaya"/>
                        </div>
                        <div>
                            <p class="font-bold text-on-surface text-sm">Andra Wijaya</p>
                            <p class="text-secondary text-xs">Pemilik BMW 3 Series</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW SECTION 2: FAQ Accordion Section -->
    <section class="py-20 sm:py-28 px-4 sm:px-8 bg-surface scroll-mt-16" id="faq">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-14" data-reveal>
                <p class="text-primary font-bold text-xs uppercase tracking-widest mb-2">TANYA JAWAB UMUM</p>
                <h2 class="font-headline-lg text-2xl sm:text-4xl font-bold text-on-surface">Pertanyaan Sering Diajukan (FAQ)</h2>
            </div>
            <div class="space-y-4" data-reveal-stagger>
                <div data-reveal-item class="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm">
                    <button type="button" onclick="toggleFaq(this)" class="w-full text-left p-6 font-bold text-base sm:text-lg text-on-surface flex justify-between items-center gap-4 hover:bg-surface-container/40 transition-colors">
                        <span>1. Apakah saya bisa booking servis terlebih dahulu secara online?</span>
                        <span class="material-symbols-outlined text-primary text-xl transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 text-secondary text-sm leading-relaxed border-t border-outline-variant/10 pt-4">
                        Ya, sangat disarankan! Anda dapat memilih tanggal dan waktu pengerjaan melalui WhatsApp Booking. Dengan booking terlebih dahulu, Anda mendapatkan prioritas tempat tanpa harus mengantri di workshop.
                    </div>
                </div>

                <div data-reveal-item class="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm">
                    <button type="button" onclick="toggleFaq(this)" class="w-full text-left p-6 font-bold text-base sm:text-lg text-on-surface flex justify-between items-center gap-4 hover:bg-surface-container/40 transition-colors">
                        <span>2. Berapa lama estimasi waktu pengerjaan Tune Up dan Ganti Oli?</span>
                        <span class="material-symbols-outlined text-primary text-xl transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 text-secondary text-sm leading-relaxed border-t border-outline-variant/10 pt-4">
                        Ganti oli express memakan waktu 15 - 30 menit. Servis Tune Up standar membutuhkan waktu sekitar 45 - 60 menit. Untuk paket Tune Up Full Carbon Clean & Injeksi Ultrasonik biasanya memerlukan waktu 1,5 hingga 2 jam.
                    </div>
                </div>

                <div data-reveal-item class="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm">
                    <button type="button" onclick="toggleFaq(this)" class="w-full text-left p-6 font-bold text-base sm:text-lg text-on-surface flex justify-between items-center gap-4 hover:bg-surface-container/40 transition-colors">
                        <span>3. Apakah oli dan suku cadang yang digunakan di jamin original?</span>
                        <span class="material-symbols-outlined text-primary text-xl transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 text-secondary text-sm leading-relaxed border-t border-outline-variant/10 pt-4">
                        100% Original. Rajawali Motor hanya menggunakan produk oli resmi distributor resmi (Shell, Motul, Castrol, Pertamina) serta suku cadang OEM/Original pabrikan dengan garansi resmi.
                    </div>
                </div>

                <div data-reveal-item class="bg-white rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm">
                    <button type="button" onclick="toggleFaq(this)" class="w-full text-left p-6 font-bold text-base sm:text-lg text-on-surface flex justify-between items-center gap-4 hover:bg-surface-container/40 transition-colors">
                        <span>4. Bagaimana jaminan garansi pengerjaan di Rajawali Motor?</span>
                        <span class="material-symbols-outlined text-primary text-xl transition-transform duration-300">expand_more</span>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 text-secondary text-sm leading-relaxed border-t border-outline-variant/10 pt-4">
                        Setiap pengerjaan Tune Up, Injeksi, Kelistrikan, dan AC Mobil dilengkapi dengan garansi servis 14 hingga 30 hari. Jika performa kendaraan belum memuaskan, teknisi kami akan melakukan pengecekan ulang gratis.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map & Location Section -->
    <section class="py-20 sm:py-28 px-4 sm:px-8 bg-white scroll-mt-16" id="kontak">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6" data-reveal>
                    <p class="text-primary font-label-bold text-xs uppercase tracking-widest mb-2">Lokasi Strategis</p>
                    <h2 class="font-headline-lg text-2xl sm:text-4xl font-bold text-on-surface">Kunjungi Workshop Kami di Surabaya</h2>
                    <p class="text-secondary text-sm sm:text-base">Berada di pusat kota, kami siap melayani Anda setiap hari untuk segala kebutuhan perawatan kendaraan Anda.</p>
                    <div class="space-y-5 pt-4">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-primary text-2xl shrink-0 mt-0.5">location_on</span>
                            <div>
                                <p class="font-bold text-on-surface">Alamat Utama</p>
                                <p class="text-secondary text-sm">{{ $toko['alamat'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-primary text-2xl shrink-0 mt-0.5">schedule</span>
                            <div>
                                <p class="font-bold text-on-surface">Jam Operasional</p>
                                <p class="text-secondary text-sm">Senin - Minggu: 07:30 - 17:00 WIB</p>
                                <div class="mt-2 inline-flex items-center gap-2 bg-surface-container px-3 py-1 rounded-lg">
                                    <span class="w-2 h-2 rounded-full bg-lunas animate-pulse"></span>
                                    <span data-status-buka class="font-mono text-xs font-bold"></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-primary text-2xl shrink-0 mt-0.5">call</span>
                            <div>
                                <p class="font-bold text-on-surface">Kontak Kami</p>
                                <p class="text-secondary text-sm">{{ $toko['teleponTampil'] }}<br/>+{{ $toko['whatsapp'] }} (WhatsApp)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-80 sm:h-[450px] rounded-3xl overflow-hidden shadow-xl border border-outline-variant relative" data-reveal>
                    <iframe src="{{ $toko['mapsEmbed'] }}" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi Rajawali Motor"></iframe>
                    <div class="absolute bottom-6 left-6 bg-white p-4 rounded-xl shadow-lg border border-outline-variant">
                        <p class="font-bold text-sm text-on-surface">Rajawali Motor Surabaya - Siwalankerto</p>
                        <a href="{{ $toko['mapsUrl'] }}" target="_blank" rel="noopener" class="text-primary text-xs font-bold mt-2 flex items-center gap-1">Dapatkan Petunjuk <span class="material-symbols-outlined text-xs">open_in_new</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="mx-4 sm:mx-8 my-16 sm:my-24 max-w-7xl mx-auto" data-reveal>
        <div class="bg-primary-container rounded-3xl p-8 sm:p-16 relative overflow-hidden text-center text-on-secondary shadow-2xl">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
            <div class="relative z-10 max-w-2xl mx-auto space-y-6">
                <h2 class="font-display-lg text-3xl sm:text-5xl font-extrabold text-white">Siap Rawat Kendaraan Anda?</h2>
                <p class="text-white/80 text-base sm:text-lg">Jangan tunggu sampai terjadi kerusakan. Lakukan perawatan rutin sekarang untuk performa yang lebih aman dan nyaman.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                    <a href="https://wa.me/{{ $toko['whatsapp'] }}?text={{ urlencode('Halo Rajawali Motor, saya berminat booking servis...') }}" target="_blank" rel="noopener"
                       class="efek-kilau w-full sm:w-auto bg-[#25D366] text-white px-10 py-4.5 rounded-xl font-label-bold font-bold text-base hover:bg-[#20bd5a] transition-all flex items-center justify-center gap-2 shadow-xl active:scale-95">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.012 2c-5.506 0-9.989 4.478-9.99 9.984 0 1.76.459 3.477 1.334 4.993l-1.417 5.176 5.297-1.389c1.463.799 3.111 1.22 4.773 1.221h.004c5.505 0 9.988-4.478 9.989-9.985 0-2.668-1.038-5.176-2.925-7.064-1.887-1.887-4.394-2.935-7.065-2.94zm5.882 14.409c-.244.689-1.227 1.297-1.696 1.378-.47.081-1.077.115-1.742-.097-.403-.129-.924-.298-1.597-.589-2.825-1.226-4.662-4.081-4.803-4.271-.141-.189-1.15-1.533-1.15-2.924 0-1.391.728-2.076.986-2.358.258-.282.564-.353.752-.353.188 0 .376.002.54.01.176.008.411-.067.644.492.245.587.822 2.01.893 2.153.071.143.118.31.024.499-.094.188-.141.305-.282.47-.141.165-.296.353-.423.475-.141.135-.288.282-.124.564.165.282.73 1.206 1.564 1.949 1.074.957 1.979 1.254 2.261 1.395.282.141.447.118.611-.071.165-.188.705-.823.893-1.105.188-.282.376-.235.634-.141.258.094 1.645.775 1.927.916.282.141.47.211.54.329.071.118.071.681-.173 1.37z"/></svg>
                        <span>Booking via WhatsApp</span>
                    </a>
                    <a href="tel:{{ $toko['telepon'] }}"
                       class="efek-kilau w-full sm:w-auto text-white font-label-bold font-bold flex items-center justify-center gap-2 border-2 border-white/80 px-8 py-4 rounded-xl hover:bg-white/10 transition-all">
                        <span class="material-symbols-outlined">call</span>
                        <span>Hubungi Kami</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<footer class="bg-surface-container-lowest dark:bg-inverse-surface border-t border-outline-variant">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 px-4 sm:px-8 py-16 max-w-7xl mx-auto">
        <div class="space-y-4">
            <div class="flex items-center gap-3 mb-4">
                <img alt="Rajawali Motor Logo" class="h-10 w-auto" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
                <span class="font-headline-sm text-xl font-bold text-primary">Rajawali Motor</span>
            </div>
            <p class="text-secondary font-body-md text-sm">Bengkel mobil &amp; motor modern dengan standar pelayanan premium di Surabaya. Precision in Every Turn.</p>
        </div>
        <div class="space-y-4">
            <h4 class="font-bold text-on-surface uppercase tracking-widest text-xs mb-4">Sub-Layanan Spesialis</h4>
            <ul class="space-y-3 text-sm">
                <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('layanan.ganti-oli') }}">Layanan Ganti Oli</a></li>
                <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('layanan.tune-up') }}">Layanan Tune Up Presisi</a></li>
                <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('layanan.injeksi') }}">Layanan Spesialis Injeksi</a></li>
                <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('layanan.ban-spooring') }}">Ban &amp; Spooring 3D Laser</a></li>
                <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('layanan.kelistrikan') }}">Perbaikan Kelistrikan &amp; ECU</a></li>
                <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('layanan.ac-mobil') }}">Perawatan AC Mobil</a></li>
                <li><a class="text-on-surface-variant hover:text-primary transition-all" href="{{ route('layanan.body-repair') }}">Body Repair &amp; Cat Oven</a></li>
            </ul>
        </div>
        <div class="space-y-4">
            <h4 class="font-bold text-on-surface uppercase tracking-widest text-xs mb-4">Jam Kerja</h4>
            <ul class="space-y-3 text-sm text-on-surface-variant">
                <li>Senin - Jumat: 07:30 - 17:00</li>
                <li>Sabtu - Minggu: 07:30 - 17:00</li>
                <li>Buka Setiap Hari</li>
            </ul>
        </div>
        <div class="space-y-4">
            <h4 class="font-bold text-on-surface uppercase tracking-widest text-xs mb-4">Lokasi &amp; Kontak</h4>
            <ul class="space-y-3 text-sm text-on-surface-variant">
                <li>{{ $toko['alamat'] }}</li>
                <li>Telp: {{ $toko['teleponTampil'] }}</li>
                <li>WA: +{{ $toko['whatsapp'] }}</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-outline-variant py-6 px-4 sm:px-8 max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 text-secondary text-xs">
        <p>© {{ now()->year }} Rajawali Motor Surabaya. Precision in Every Turn.</p>
        <a href="{{ route('login') }}" class="hover:text-primary underline">Akses Staf / Admin</a>
    </div>
</footer>

<script>
    let calcMultiplier = 1;
    let selectedVehicleName = 'City Car / Hatchback';

    function setCalcVehicle(name, mult) {
        calcMultiplier = mult;
        selectedVehicleName = name;
        document.querySelectorAll('#calc-vehicle-group button').forEach(btn => {
            btn.classList.remove('border-2', 'border-primary', 'bg-primary/5', 'text-primary');
            btn.classList.add('border', 'border-outline-variant/40', 'text-on-surface');
        });
        event.currentTarget.classList.remove('border', 'border-outline-variant/40', 'text-on-surface');
        event.currentTarget.classList.add('border-2', 'border-primary', 'bg-primary/5', 'text-primary');
        updateCalcEstimator();
    }

    function updateCalcEstimator() {
        const select = document.getElementById('calc-service-select');
        const option = select.options[select.selectedIndex];
        const minBase = parseInt(option.getAttribute('data-min'), 10);
        const maxBase = parseInt(option.getAttribute('data-max'), 10);
        const serviceName = option.text.split(' (')[0];

        const minEst = Math.round(minBase * calcMultiplier / 5000) * 5000;
        const maxEst = Math.round(maxBase * calcMultiplier / 5000) * 5000;

        const priceText = `Rp ${minEst.toLocaleString('id-ID')} - Rp ${maxEst.toLocaleString('id-ID')}`;
        document.getElementById('calc-result-price').textContent = priceText;

        const waText = `Halo Rajawali Motor, saya mau booking servis:\n• Kendaraan: ${selectedVehicleName}\n• Layanan: ${serviceName}\n• Estimasi: ${priceText}`;
        document.getElementById('calc-wa-btn').href = `https://wa.me/{{ $toko['whatsapp'] }}?text=${encodeURIComponent(waText)}`;
    }

    function toggleFaq(btn) {
        const content = btn.nextElementSibling;
        const icon = btn.querySelector('.material-symbols-outlined');
        const isHidden = content.classList.contains('hidden');
        if (isHidden) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function toggleDrawer() {
        const drawer = document.getElementById('drawer');
        const overlay = document.getElementById('drawer-overlay');
        if (!drawer || !overlay) return;

        const isHidden = drawer.classList.contains('-translate-x-full');
        if (isHidden) {
            drawer.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        } else {
            drawer.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = 'auto';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCalcEstimator();
    });
</script>
</body>
</html>
