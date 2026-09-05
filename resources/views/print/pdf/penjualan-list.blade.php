<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Nota Penjualan</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 9pt;
            color: #000000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #000000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .toko-nama {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .toko-meta {
            font-size: 8pt;
            color: #333333;
        }
        .judul {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 4px;
            text-transform: uppercase;
        }
        .filter-info {
            font-size: 8pt;
            color: #444444;
            margin-bottom: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th {
            border-top: 1.5px solid #000000;
            border-bottom: 1.5px solid #000000;
            padding: 6px 5px;
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: bold;
            text-align: left;
            background-color: #f5f5f5;
        }
        td {
            border-bottom: 1px solid #cccccc;
            padding: 5px 5px;
            font-size: 8pt;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', monospace; }
        .summary-box {
            margin-top: 12px;
            border-top: 2px solid #000000;
            padding-top: 6px;
        }
        .summary-table td {
            border: none;
            padding: 2px 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="float: left; width: 60%;">
            <div class="toko-nama">{{ strtoupper($pengaturan->nama_toko ?? 'RAJAWALI MOTOR SURABAYA') }}</div>
            <div class="toko-meta">{{ $pengaturan->alamat ?? 'Jl. Samanhudi No.102, Jasem, Sidoarjo' }} | Telp/WA: {{ $pengaturan->telepon ?? '-' }}</div>
            <div class="judul">Daftar Transaksi Nota Penjualan</div>
        </div>
        <div style="float: right; width: 38%; text-align: right;">
            <div style="font-size: 8pt;">Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB</div>
            <div class="filter-info">
                Status: <strong>{{ strtoupper($status ?? 'SEMUA') }}</strong> | 
                Jenis: <strong>{{ strtoupper($tipe ?? 'SEMUA') }}</strong>
                @if(!empty($search))
                    | Filter: "{{ $search }}"
                @endif
            </div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 100px;">No. Nota</th>
                <th style="width: 95px;">Waktu</th>
                <th>Customer / Pelanggan</th>
                <th style="width: 85px;">Kasir</th>
                <th style="width: 150px;">Item Terjual (Barang / Jasa)</th>
                <th class="text-center" style="width: 65px;">Metode</th>
                <th class="text-center" style="width: 65px;">Status</th>
                <th class="text-right" style="width: 90px;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOmzet = 0;
                $totalNota = 0;
            @endphp
            @forelse($penjualans as $idx => $p)
                @php
                    $totalOmzet += (float) $p->total_akhir;
                    $totalNota++;
                    $itemList = $p->details->map(fn($d) => ($d->barang->nama ?? 'Item') . ' (' . (float)$d->qty . ')')->join(', ');
                @endphp
                <tr>
                    <td class="text-center font-mono">{{ $idx + 1 }}</td>
                    <td class="font-mono font-bold">{{ $p->nomor_nota }}</td>
                    <td class="font-mono">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td class="font-bold">{{ $p->customer->nama ?? 'Umum / Tunai' }}</td>
                    <td>{{ $p->user->name ?? 'Staff' }}</td>
                    <td style="font-size: 7.5pt; color: #333333;">{{ Str::limit($itemList, 50) }}</td>
                    <td class="text-center uppercase" style="font-size: 7.5pt;">{{ $p->metode_pembayaran }}</td>
                    <td class="text-center font-bold" style="font-size: 7.5pt;">
                        {{ strtoupper($p->status_bayar) }}
                    </td>
                    <td class="text-right font-mono font-bold">
                        Rp {{ number_format($p->total_akhir, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; font-style: italic; color: #666666;">
                        Tidak ada transaksi penjualan yang cocok dengan filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table class="summary-table" style="width: 100%;">
            <tr>
                <td style="width: 25%;">Total Transaksi: <strong>{{ number_format($totalNota, 0, ',', '.') }} Nota</strong></td>
                <td style="width: 25%;">Total Lunas: <strong>Rp {{ number_format($penjualans->where('status_bayar', 'lunas')->sum('total_akhir'), 0, ',', '.') }}</strong></td>
                <td style="width: 25%;">Total Piutang/Tempo: <strong>Rp {{ number_format($penjualans->where('status_bayar', 'piutang')->sum('total_akhir'), 0, ',', '.') }}</strong></td>
                <td class="text-right" style="width: 25%; font-size: 10pt;">TOTAL OMZET: <strong class="font-mono">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>
