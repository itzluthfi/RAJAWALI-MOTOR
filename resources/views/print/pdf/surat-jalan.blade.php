@php
    $pengaturan = \App\Models\PengaturanToko::current();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Surat Jalan {{ $penjualan->nomor_nota }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 8mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #b0181c;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header-table {
            width: 100%;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #b0181c;
            margin: 0;
        }
        .doc-no {
            font-size: 13px;
            font-weight: bold;
            color: #b0181c;
            text-align: right;
        }
        .info-box {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 8px;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
        }
        table.item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.item-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 6px 8px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        table.item-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-red { color: #b0181c; }
        .signatures {
            width: 100%;
            margin-top: 16px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="title">{{ strtoupper($pengaturan->nama_toko) }}</h1>
                    <div style="font-size: 10px; color: #475569; font-weight: bold;">{{ $pengaturan->alamat }} | WA: {{ $pengaturan->telepon }}</div>
                    @if($pengaturan->slogan)
                        <div style="font-size: 9px; color: #64748b;">{{ $pengaturan->slogan }}</div>
                    @endif
                </td>
                <td class="text-right">
                    <div style="font-size: 14px; font-weight: bold;">SURAT JALAN PENGIRIMAN</div>
                    <div class="doc-no">{{ $penjualan->nomor_nota }}</div>
                    <div style="font-size: 10px; color: #64748b;">Tanggal: {{ $penjualan->created_at->translatedFormat('d F Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; text-transform: uppercase;">Kepada Yth / Alamat Pengiriman:</div>
                    <div style="font-size: 12px; font-weight: bold; color: #0f172a;">{{ $penjualan->customer->nama ?? 'Pelanggan Umum' }}</div>
                    <div style="font-size: 10px; color: #475569;">{{ $penjualan->customer->alamat ?? 'Sidoarjo / Surabaya' }}</div>
                    @if($penjualan->customer && $penjualan->customer->telepon)
                        <div style="font-size: 10px; color: #475569;">Telp/WA: {{ $penjualan->customer->telepon }}</div>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right; font-size: 10px;">
                    <div>No. Referensi: <span class="font-bold">{{ $penjualan->nomor_nota }}</span></div>
                    <div>Petugas / Kasir: <span class="font-bold">{{ $penjualan->user->name ?? 'Staff' }}</span></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th style="width: 100px; text-align: left;">Kode Barang</th>
                <th style="text-align: left;">Nama / Deskripsi Barang</th>
                <th style="width: 80px; text-align: center;">Jumlah (Qty)</th>
                <th style="width: 100px; text-align: center;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->details as $index => $item)
                <tr>
                    <td class="text-center" style="color: #64748b;">{{ $index + 1 }}</td>
                    <td class="font-bold text-red">{{ $item->barang->kode }}</td>
                    <td class="font-bold">{{ $item->barang->nama }}</td>
                    <td class="text-center font-bold">{{ rtrim(rtrim(number_format((float) $item->qty, 3, ',', ''), '0'), ',') }}</td>
                    <td class="text-center" style="color: #64748b;">Baik / Sesuai</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="font-size: 9px; color: #64748b; font-style: italic; margin-top: 4px;">
        * Harap periksa fisik barang dengan teliti sebelum menandatangani surat jalan ini. Komplain setelah tanda tangan tidak dapat dilayani.
    </div>

    <table class="signatures">
        <tr>
            <td style="width: 33%; vertical-align: top;">
                <div style="font-size: 10px; font-weight: bold; margin-bottom: 45px;">Penerima / Customer,</div>
                <div style="border-bottom: 1px solid #94a3b8; width: 120px; margin: 0 auto; font-size: 10px; font-weight: bold;">{{ $penjualan->customer->nama ?? '.....................' }}</div>
            </td>
            <td style="width: 34%; vertical-align: top;">
                <div style="font-size: 10px; font-weight: bold; margin-bottom: 45px;">Pengirim / Sopir,</div>
                <div style="border-bottom: 1px solid #94a3b8; width: 120px; margin: 0 auto; font-size: 10px; font-weight: bold;">.....................</div>
            </td>
            <td style="width: 33%; vertical-align: top;">
                <div style="font-size: 10px; font-weight: bold; margin-bottom: 45px;">Hormat Kami (Toko),</div>
                <div style="border-bottom: 1px solid #94a3b8; width: 120px; margin: 0 auto; font-size: 10px; font-weight: bold;">{{ $penjualan->user->name ?? 'Staff' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>