@php
    $pengaturan = \App\Models\PengaturanToko::current();
    $is58mm = ($size ?? '80') === '58';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk {{ $penjualan->nomor_nota }}</title>
    <style>
        @page {
            size: {{ $is58mm ? '58mm' : '80mm' }} auto;
            margin: 1.5mm;
        }
        body {
            font-family: 'Courier', monospace;
            font-size: {{ $is58mm ? '8.5px' : '10px' }};
            line-height: 1.25;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div class="text-center font-bold" style="font-size: {{ $is58mm ? '10.5px' : '12px' }};">{{ strtoupper($pengaturan->nama_toko) }}</div>
    @if($pengaturan->slogan)
        <div class="text-center" style="font-size: {{ $is58mm ? '7px' : '8px' }}; font-style: italic;">{{ $pengaturan->slogan }}</div>
    @endif
    <div class="text-center" style="font-size: {{ $is58mm ? '7.5px' : '8px' }};">{{ $pengaturan->alamat }}</div>
    @if($pengaturan->telepon)
        <div class="text-center" style="font-size: {{ $is58mm ? '7.5px' : '8px' }};">WA/Telp: {{ $pengaturan->telepon }}</div>
    @endif
    <div class="line"></div>
    <table>
        <tr><td>No Nota:</td><td class="text-right font-bold">{{ $penjualan->nomor_nota }}</td></tr>
        <tr><td>Tanggal:</td><td class="text-right">{{ $penjualan->created_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Customer:</td><td class="text-right font-bold">{{ $penjualan->customer->nama ?? 'Umum / Tunai' }}</td></tr>
        <tr><td>Kasir:</td><td class="text-right">{{ $penjualan->user->name ?? 'Staff' }}</td></tr>
    </table>
    <div class="line"></div>
    <table>
        @foreach($penjualan->details as $item)
            <tr>
                <td colspan="2" class="font-bold">{{ $item->barang->nama }}</td>
            </tr>
            <tr>
                <td style="font-size: {{ $is58mm ? '7.5px' : '9px' }}; padding-left: 4px;">{{ rtrim(rtrim(number_format((float) $item->qty, 3, ',', ''), '0'), ',') }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <div class="line"></div>
    <table>
        <tr>
            <td class="font-bold">TOTAL:</td>
            <td class="text-right font-bold">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($penjualan->diskon > 0)
            <tr>
                <td>DISKON:</td>
                <td class="text-right">-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td class="font-bold" style="font-size: {{ $is58mm ? '9.5px' : '11px' }};">GRAND TOTAL:</td>
            <td class="text-right font-bold" style="font-size: {{ $is58mm ? '9.5px' : '11px' }};">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Status:</td>
            <td class="text-right font-bold">{{ $penjualan->status_bayar === 'lunas' ? 'LUNAS' : 'TEMPO / PIUTANG' }}</td>
        </tr>
    </table>
    <div class="line"></div>
    <div class="text-center" style="font-size: {{ $is58mm ? '7.5px' : '8.5px' }}; margin-top: 5px;">
        Terima kasih atas kunjungan Anda!<br>
        {{ $pengaturan->footer_struk ?? 'Garansi Servis & Sparepart Original' }}<br>
        Simpan Nota Ini
    </div>
</body>
</html>
