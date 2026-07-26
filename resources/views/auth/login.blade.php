<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Sistem — Rajawali Motor Surabaya</title>

    {{-- Fonts & Material Symbols --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .login-hero-bg {
            background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuD5JYvswa21ywwHQhN0yYz9yFx5WrXNr5eVDRzYnEAM9QAMAINPQQs4wMniaHMAZWQDmQJBUE8ZgKMneqnaMP279v-IkwmmgKidG7EWKXIARYA8Xe-MW5ILdnfF1aH-XtrXGvX5SJsYLzbb8bqVL0IOChvKvACiZ_Wy34kBBUD3xlZ6A5IlRXU-JvPSh7O_HQD1KZXISrzyzWNMsARSUbRw8_YPT2h2zKr2RXj3AKLemayIHKLcY95sE_CQlBMLw45JgqW5rwPfTJU');
            background-size: cover;
            background-position: center;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>
</head>
<body class="h-full bg-slate-100 text-slate-900 font-sans antialiased overflow-x-hidden">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-12 relative overflow-hidden">

    <!-- Left Panel: Hero Showcase (Clean Red Brand Theme - Hidden on Mobile) -->
    <div class="hidden lg:flex lg:col-span-7 login-hero-bg relative p-12 flex-col justify-between overflow-hidden">
        <!-- Racing Red Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#B0181C]/95 via-[#930011]/90 to-black/80 z-0"></div>

        <!-- Top Header Logo -->
        <div class="relative z-10 flex items-center gap-3">
            <div class="p-1.5 bg-white rounded-xl sm:rounded-2xl border border-white/80 shadow-md shrink-0 overflow-hidden">
                <img alt="Rajawali Motor Logo" class="h-9 w-auto object-contain rounded-lg" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
            </div>
            <div>
                <h2 class="font-black text-xl text-white tracking-wide">RAJAWALI MOTOR</h2>
                <p class="text-xs text-amber-300 font-bold uppercase tracking-wider">Surabaya Automotive Care</p>
            </div>
        </div>

        <!-- Middle Showcase Text & Badges -->
        <div class="relative z-10 space-y-6 max-w-xl">
            <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-white/20">
                <span class="material-symbols-outlined text-sm text-amber-300">verified</span>
                <span>Precision in Every Turn</span>
            </div>
            <h1 class="text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight drop-shadow-md">
                Sistem Operasional <br/>
                <span class="text-amber-300">&amp; POS Bengkel Motor.</span>
            </h1>
            <p class="text-white/85 text-sm sm:text-base leading-relaxed font-normal">
                Platform manajemen terintegrasi untuk kasir POS, stok barang multi-barcode, work order service kendaraan, hingga laporan laba rugi secara otomatis.
            </p>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/20">
                <div class="flex items-center gap-3 bg-black/20 backdrop-blur-md p-3.5 rounded-2xl border border-white/10">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white">point_of_sale</span>
                    </div>
                    <div>
                        <p class="font-bold text-xs text-white">Kasir POS Cepat</p>
                        <p class="text-[11px] text-white/70">Scan Barcode &amp; Struk</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-black/20 backdrop-blur-md p-3.5 rounded-2xl border border-white/10">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white">build</span>
                    </div>
                    <div>
                        <p class="font-bold text-xs text-white">Work Order Service</p>
                        <p class="text-[11px] text-white/70">Tracking Pengerjaan</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-black/20 backdrop-blur-md p-3.5 rounded-2xl border border-white/10">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white">inventory_2</span>
                    </div>
                    <div>
                        <p class="font-bold text-xs text-white">Stok Multi-Barcode</p>
                        <p class="text-[11px] text-white/70">Opname &amp; Rekap Stok</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-black/20 backdrop-blur-md p-3.5 rounded-2xl border border-white/10">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white">monitoring</span>
                    </div>
                    <div>
                        <p class="font-bold text-xs text-white">Laporan Realtime</p>
                        <p class="text-[11px] text-white/70">Omzet &amp; Laba Kotor</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="relative z-10 flex items-center justify-between text-xs text-white/70 border-t border-white/20 pt-6">
            <p>© {{ now()->year }} Rajawali Motor Surabaya.</p>
            <a href="{{ route('home') }}" class="text-white hover:text-amber-300 font-bold flex items-center gap-1 transition-colors">
                <span>Ke Website Utama</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- Right Panel: Clean White Form Container -->
    <div class="lg:col-span-5 flex items-center justify-center p-6 sm:p-12 bg-white relative z-10 border-l border-slate-200">
        <!-- Subtle Red Ambient Glow -->
        <div class="absolute top-10 right-10 w-72 h-72 bg-red-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>
        <div class="absolute bottom-10 left-10 w-72 h-72 bg-red-50 rounded-full blur-3xl pointer-events-none opacity-60"></div>

        <div class="w-full max-w-md space-y-8 relative">

            <!-- Logo Header (Mobile & Desktop) -->
            <div class="text-center space-y-3">
                <a href="{{ route('home') }}" class="inline-block group">
                    <div class="p-2 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-md group-hover:scale-105 group-hover:shadow-lg transition-all duration-300 overflow-hidden inline-block">
                        <img alt="Rajawali Motor Logo" class="h-14 sm:h-16 w-auto mx-auto object-contain rounded-xl" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc"/>
                    </div>
                </a>
                <h2 class="font-black text-2xl text-[#B0181C] tracking-wide">RAJAWALI MOTOR</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Portal Log Masuk Staf &amp; Admin</p>
            </div>

            <!-- Quick Demo Role Selector (1-Click Auto Fill) -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs text-[#B0181C]">touch_app</span> 1-Click Auto Fill Demo:
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">Pass: password</span>
                </div>
                <div class="flex flex-wrap gap-1.5" id="demo-role-buttons">
                    <button type="button" onclick="isiUsername('owner')" class="px-3 py-1 rounded-lg bg-[#B0181C] hover:bg-[#8f1013] text-white text-xs font-bold transition-all shadow-sm">Owner</button>
                    <button type="button" onclick="isiUsername('admin')" class="px-3 py-1 rounded-lg bg-white border border-slate-300 hover:border-[#B0181C] text-slate-700 hover:text-[#B0181C] text-xs font-semibold transition-all">Admin</button>
                    <button type="button" onclick="isiUsername('kasir1')" class="px-3 py-1 rounded-lg bg-white border border-slate-300 hover:border-[#B0181C] text-slate-700 hover:text-[#B0181C] text-xs font-semibold transition-all">Kasir</button>
                    <button type="button" onclick="isiUsername('gudang1')" class="px-3 py-1 rounded-lg bg-white border border-slate-300 hover:border-[#B0181C] text-slate-700 hover:text-[#B0181C] text-xs font-semibold transition-all">Gudang</button>
                    <button type="button" onclick="isiUsername('montir1')" class="px-3 py-1 rounded-lg bg-white border border-slate-300 hover:border-[#B0181C] text-slate-700 hover:text-[#B0181C] text-xs font-semibold transition-all">Montir</button>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 flex items-start gap-2 text-red-700 text-xs leading-relaxed">
                        <span class="material-symbols-outlined text-red-600 text-base shrink-0 mt-0.5">error</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label for="input-username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Username Pengguna</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-lg">person</span>
                        </div>
                        <input id="input-username" type="text" name="username" value="{{ old('username', 'owner') }}" required autofocus
                               placeholder="cth. owner / admin / kasir1"
                               class="w-full pl-10 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm font-mono placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#B0181C] focus:border-transparent transition-all shadow-sm"/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="input-password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="material-symbols-outlined text-lg">lock</span>
                        </div>
                        <input id="input-password" type="password" name="password" value="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm font-mono placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#B0181C] focus:border-transparent transition-all shadow-sm"/>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="ingat" value="1" checked class="w-4 h-4 rounded border-slate-300 text-[#B0181C] focus:ring-[#B0181C]">
                        <span class="text-xs text-slate-600 group-hover:text-slate-900 transition-colors">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit"
                        class="efek-kilau w-full bg-[#B0181C] hover:bg-[#8f1013] text-white py-3.5 rounded-xl font-bold text-sm shadow-xl shadow-red-900/20 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">login</span>
                    <span>Masuk Ke System</span>
                </button>
            </form>

            <div class="text-center pt-2">
                <a href="{{ route('home') }}" class="text-xs text-slate-500 hover:text-[#B0181C] font-semibold transition-colors inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Kembali ke Halaman Utama Website</span>
                </a>
            </div>

        </div>
    </div>

</div>

<script>
function isiUsername(username) {
    const userField = document.getElementById('input-username');
    const passField = document.getElementById('input-password');
    if (userField && passField) {
        userField.value = username;
        passField.value = 'password';
        userField.focus();
    }
}
</script>

</body>
</html>
