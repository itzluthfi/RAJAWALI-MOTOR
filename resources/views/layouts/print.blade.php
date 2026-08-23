<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Pratinjau Cetak Dokumen' }} — Rajawali Motor</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased text-slate-900 pb-12">
    <!-- Top Action Bar (No Print) -->
    <div class="no-print sticky top-0 z-50 bg-slate-900 text-white px-4 py-3 shadow-lg flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button type="button" onclick="history.back()" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-bold transition flex items-center gap-1.5">
                <span>← Kembali</span>
            </button>
            <div>
                <h2 class="font-bold text-sm leading-tight text-white">{{ $title ?? 'Pratinjau Cetak Dokumen' }}</h2>
                <p class="text-[11px] text-slate-400">Preview HTML Asli — Rajawali Motor Sidoarjo</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-[#B0181C] hover:bg-[#8f1013] text-white text-xs font-bold transition shadow-md flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Dokumen</span>
            </button>
        </div>
    </div>

    <!-- Document Preview Content Slot -->
    <div class="p-4 sm:p-6 flex justify-center">
        {{ $slot }}
    </div>
</body>
</html>
