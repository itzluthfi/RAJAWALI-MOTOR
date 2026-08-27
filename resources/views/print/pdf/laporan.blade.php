<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan {{ $judul }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #b0181c;
            padding-bottom: 6px;
            margin-bottom: 10px;
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
        .summary-box {
            width: 100%;
            margin-bottom: 12px;
        }
        .summary-card {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 6px 10px;
            border-radius: 4px;
            text-align: center;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th {
            background-color: #b0181c;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #8e1215;
        }
        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="title">RAJAWALI MOTOR SIDOARJO</h1>
                    <div style="font-size: 10px; font-weight: bold; color: #334155;">LAPORAN {{ strtoupper($judul) }}</div>
                    <div style="font-size: 9px; color: #64748b;">Periode: {{ date('d M Y', strtotime($dariTanggal)) }} s/d {{ date('d M Y', strtotime($sampaiTanggal)) }} | Jl. Samanhudi No.102, Jasem, Sidoarjo</div>
                </td>
                <td class="text-right">
                    <div style="font-size: 9px; color: #64748b;">Dicetak: {{ now()->translatedFormat('d F Y H:i') }} WIB</div>
                    <div style="font-size: 9px; color: #64748b;">Oleh: {{ auth()->user()->name ?? 'Administrator' }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if(count($ringkasan) > 0)
        <table class="summary-box">
            <tr>
                @foreach($ringkasan as $rk)
                    <td style="padding: 0 4px;">
                        <div class="summary-card">
                            <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">{{ $rk['label'] }}</div>
                            <div style="font-size: 11px; font-weight: bold; margin-top: 2px;">{{ $rk['nilai'] }}</div>
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                @foreach($headers as $idx => $hdr)
                    <th class="{{ $idx >= count($headers) - 2 ? 'text-right' : 'text-left' }}">
                        {{ $hdr }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td class="font-bold">{{ $row['kolom1'] ?? '-' }}</td>
                    <td>{{ $row['kolom2'] ?? '-' }}</td>
                    <td>{{ $row['kolom3'] ?? '-' }}</td>
                    <td>{{ $row['kolom4'] ?? '-' }}</td>
                    <td class="text-right">{{ $row['kolom5'] ?? '-' }}</td>
                    <td class="text-right font-bold">{{ $row['kolom6'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center" style="padding: 16px; color: #64748b;">
                        Tidak ada data transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
