<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $judul ?? 'Terjadi Kesalahan' }} — Rajawali Motor</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-canvas min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-sm">
        <p class="font-display font-bold text-6xl text-rajawali mb-2">{{ $kode ?? '' }}</p>
        <h1 class="font-display font-semibold text-lg text-ink mb-2">{{ $judul ?? 'Terjadi Kesalahan' }}</h1>
        <p class="text-sm text-steel mb-6">{{ $pesan ?? '' }}</p>
        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 rounded-md bg-rajawali text-white px-4 py-2 text-sm font-medium hover:bg-rajawali-dark">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
