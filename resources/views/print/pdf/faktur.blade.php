<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Faktur Penjualan {{ $penjualan->nomor_nota }}</title>
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
            border-bottom: 2px solid #000000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header-table {
            width: 100%;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #000000;
            margin: 0;
        }
        .faktur-no {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
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
            padding: 5px 6px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        table.item-table td {
            padding: 5px 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .total-box {
            width: 100%;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-red { color: #000000; }
        .text-green { color: #000000; }
        .text-slate { color: #475569; }
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
                    <h1 class="title">RAJAWALI MOTOR</h1>
                    <div style="font-size: 10px; color: #475569; font-weight: bold;">Jl. Samanhudi No.102, Jasem, Sidoarjo | WA: +62 856-4888-8441</div>
                    <div style="font-size: 9px; color: #64748b;">Spesialis Injeksi, Tune Up, Ganti Oli &amp; Body Repair</div>
                </td>
                <td class="text-right">
                    <div style="font-size: 14px; font-weight: bold;">FAKTUR PENJUALAN</div>
                    <div class="faktur-no">{{ $penjualan->nomor_nota }}</div>
                    <div style="font-size: 10px; color: #64748b;">Tanggal: {{ $penjualan->created_at->translatedFormat('d F Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td style="width: 50%;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; text-transform: uppercase;">Kepada Yth:</div>
                    <div style="font-weight: bold; font-size: 12px; color: #0f172a;">{{ $penjualan->customer->nama ?? 'Pelanggan Umum' }}</div>
                    <div style="font-size: 10px; color: #475569;">{{ $penjualan->customer->alamat ?? 'Sidoarjo / Surabaya' }}</div>
                </td>
                <td class="text-right" style="width: 50%; font-size: 10px;">
                    <div>Status Pembayaran: <strong class="{{ $penjualan->status_bayar === 'lunas' ? 'text-green' : 'text-red' }}">{{ $penjualan->status_bayar === 'lunas' ? 'LUNAS' : 'TEMPO / PIUTANG' }}</strong></div>
                    <div>Kasir / Staf: <strong>{{ $penjualan->user->name ?? 'Staff' }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Deskripsi Produk / Sparepart</th>
                <th style="width: 45px;" class="text-center">Qty</th>
                <th style="width: 90px;" class="text-right">Harga Satuan</th>
                @if($penjualan->details->sum('diskon') > 0)
                    <th style="width: 70px;" class="text-right">Diskon</th>
                @endif
                <th style="width: 95px;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($penjualan->details as $index => $item)
                <tr>
                    <td class="text-center text-slate">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item->barang->nama }}</td>
                    <td class="text-center font-bold">{{ rtrim(rtrim(number_format((float) $item->qty, 3, ',', ''), '0'), ',') }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    @if($penjualan->details->sum('diskon') > 0)
                        <td class="text-right text-red">-Rp {{ number_format($item->diskon, 0, ',', '.') }}</td>
                    @endif
                    <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-box">
        <tr>
            <td style="width: 55%; vertical-align: top; font-size: 9px; color: #64748b;">
                <strong>Catatan &amp; Garansi:</strong><br>
                • Suku cadang &amp; jasa terbukti original &amp; bergaransi resmi.<br>
                • Pembayaran sah setelah dana diterima Rajawali Motor.
            </td>
            <td style="width: 45%; text-align: right; vertical-align: top;">
                <table style="width: 100%; font-size: 11px;">
                    <tr>
                        <td class="text-slate">SUBTOTAL:</td>
                        <td class="text-right font-bold">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($penjualan->diskon > 0)
                        <tr>
                            <td class="text-red">DISKON NOTA:</td>
                            <td class="text-right font-bold text-red">-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($penjualan->pajak > 0)
                        <tr>
                            <td class="text-slate">PAJAK:</td>
                            <td class="text-right font-bold">Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="font-size: 12px; font-weight: bold; color: #000000; border-top: 2px solid #000000; padding-top: 4px;">TOTAL AKHIR:</td>
                        <td style="font-size: 12px; font-weight: bold; color: #000000; border-top: 2px solid #000000; padding-top: 4px;" class="text-right">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</td>
                    </tr>
                    @if($penjualan->metode_pembayaran === 'tempo')
                        <tr>
                            <td class="text-slate">UANG MUKA (DP):</td>
                            <td class="text-right font-bold">Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-red">SISA PIUTANG:</td>
                            <td class="text-right font-bold text-red">Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="signatures">
        <tr>
            <td style="width: 50%; font-size: 10px; text-align: center;">
                <div style="margin-bottom: 25px; font-weight: bold;">Penerima / Pelanggan,</div>
                <div style="font-weight: bold; border-bottom: 1px solid #94a3b8; width: 130px; margin: 0 auto;">{{ $penjualan->customer->nama ?? '..........................' }}</div>
            </td>
            <td style="width: 50%; font-size: 10px; text-align: center;">
                <div style="margin-bottom: 25px; font-weight: bold;">Hormat Kami (Rajawali Motor),</div>
                <div style="font-weight: bold; border-bottom: 1px solid #94a3b8; width: 130px; margin: 0 auto;">{{ $penjualan->user->name ?? 'Staff' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
