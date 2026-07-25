<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Cetak' }} — Rajawali Motor</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            @page { margin: 4mm; }
        }
    </style>
</head>
<body class="bg-canvas">
    <div class="no-print fixed top-3 right-3 z-10">
        <button onclick="window.print()" class="rounded-md bg-rajawali text-white px-4 py-2 text-sm font-medium shadow-sm">Cetak</button>
    </div>
    <div class="max-w-md mx-auto bg-white print:max-w-none">
        {{ $slot }}
    </div>
</body>
</html>
