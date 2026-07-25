<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman Tidak Ditemukan | Rajawali Motor</title>

    <link rel="icon" type="image/png" href="https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-900 flex items-center justify-center p-6 font-sans">
    <div class="max-w-md w-full text-center space-y-6 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
        <div class="w-20 h-20 bg-red-50 border border-red-200 text-[#B0181C] rounded-3xl flex items-center justify-center mx-auto shadow-sm">
            <span class="material-symbols-outlined text-4xl">search_off</span>
        </div>
        <div class="space-y-2">
            <span class="font-mono font-black text-4xl text-[#B0181C]">404</span>
            <h1 class="font-black text-2xl text-slate-900 tracking-tight">Halaman Tidak Ditemukan</h1>
            <p class="text-sm text-slate-500 leading-relaxed">
                Maaf, halaman atau alamat URL yang Anda tuju tidak ditemukan atau telah dipindahkan.
            </p>
        </div>
        <div class="pt-2 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('home') }}" class="flex-1 bg-[#B0181C] hover:bg-[#8f1013] text-white py-3 rounded-xl font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-sm">home</span>
                <span>Website Utama</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-800 py-3 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">dashboard</span>
                    <span>Dashboard Admin</span>
                </a>
            @endauth
        </div>
    </div>
</body>
</html>
