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
            margin: 2mm 3mm;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: {{ $is58mm ? '8.5px' : '9.5px' }};
            line-height: 1.35;
            color: #000000;
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .dashed-line { border-bottom: 1px dashed #000000; margin: 4px 0; }
        .solid-line { border-bottom: 1.5px solid #000000; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1px 0; vertical-align: top; color: #000000; }
    </style>
</head>
<body>
    <div class="text-center font-bold" style="font-size: {{ $is58mm ? '11px' : '13px' }}; letter-spacing: 0.5px;">{{ strtoupper($pengaturan->nama_toko) }}</div>
    @if($pengaturan->slogan)
        <div class="text-center" style="font-size: {{ $is58mm ? '7px' : '8px' }}; font-style: italic;">{{ $pengaturan->slogan }}</div>
    @endif
    <div class="text-center" style="font-size: {{ $is58mm ? '7.5px' : '8.5px' }};">{{ $pengaturan->alamat }}</div>
    @if($pengaturan->telepon)
        <div class="text-center" style="font-size: {{ $is58mm ? '7.5px' : '8.5px' }};">Telp/WA: {{ $pengaturan->telepon }}</div>
    @endif

    <div class="dashed-line"></div>

    <table>
        <tr>
            <td>No. Nota:</td>
            <td class="text-right font-bold font-mono">{{ $penjualan->nomor_nota }}</td>
        </tr>
        <tr>
            <td>Tanggal:</td>
            <td class="text-right">{{ $penjualan->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Customer:</td>
            <td class="text-right font-bold">{{ $penjualan->customer->nama ?? 'Umum' }}</td>
        </tr>
        <tr>
            <td>Kasir:</td>
            <td class="text-right">{{ $penjualan->user->name ?? 'Staff' }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    @php
        $totalHematSemua = 0;
    @endphp
    <table>
        @foreach($penjualan->details as $item)
            @php
                $hargaNormal = $item->barang ? (float) $item->barang->harga_eceran : (float) $item->harga_satuan;
                $adaDiskonItem = (float) $item->diskon > 0;
                $adaTierHemat = $hargaNormal > (float) $item->harga_satuan;
                $hematPerPcs = $adaTierHemat ? ($hargaNormal - (float) $item->harga_satuan) : 0;

                if ($adaTierHemat) {
                    $totalHematSemua += ($hematPerPcs * (float) $item->qty);
                }
                if ($adaDiskonItem) {
                    $totalHematSemua += (float) $item->diskon;
                }
            @endphp
            <tr>
                <td colspan="2" class="font-bold" style="padding-top: 2px;">{{ $item->barang->nama }}</td>
            </tr>
            @if($adaTierHemat)
                <tr>
                    <td colspan="2" style="font-size: {{ $is58mm ? '7px' : '8px' }};">
                        * Khusus (Normal: {{ number_format($hargaNormal, 0, ',', '.') }}) [Hemat {{ number_format($hematPerPcs, 0, ',', '.') }}/pcs]
                    </td>
                </tr>
            @endif
            <tr>
                <td style="font-size: {{ $is58mm ? '7.5px' : '8.5px' }}; padding-left: 2px;">
                    {{ rtrim(rtrim(number_format((float) $item->qty, 3, ',', ''), '0'), ',') }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                    @if($adaDiskonItem)
                        (-{{ number_format($item->diskon, 0, ',', '.') }})
                    @endif
                </td>
                <td class="text-right font-bold font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    @php
        $totalHematSemua += (float) $penjualan->diskon;
    @endphp

    <div class="solid-line"></div>

    <table>
        <tr>
            <td class="font-bold">SUBTOTAL:</td>
            <td class="text-right font-bold font-mono">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($penjualan->diskon > 0)
            <tr>
                <td>Diskon Nota:</td>
                <td class="text-right font-bold font-mono">-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</td>
            </tr>
        @endif
        @if($penjualan->pajak > 0)
            <tr>
                <td>Pajak:</td>
                <td class="text-right font-mono">Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</td>
            </tr>
        @endif
        <tr>
            <td class="font-bold" style="font-size: {{ $is58mm ? '10px' : '11.5px' }}; padding-top: 2px;">TOTAL AKHIR:</td>
            <td class="text-right font-bold font-mono" style="font-size: {{ $is58mm ? '10px' : '11.5px' }}; padding-top: 2px;">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</td>
        </tr>
        @if($totalHematSemua > 0)
            <tr>
                <td style="font-weight: bold; padding-top: 3px;">Total Hemat (Diskon):</td>
                <td class="text-right font-bold font-mono" style="padding-top: 3px;">Rp {{ number_format($totalHematSemua, 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <div class="dashed-line"></div>

    <table>
        <tr>
            <td>Status:</td>
            <td class="text-right font-bold">
                [ {{ $penjualan->status_bayar === 'lunas' ? 'LUNAS (' . strtoupper($penjualan->metode_pembayaran) . ')' : 'TEMPO / PIUTANG' }} ]
            </td>
        </tr>
        @if($penjualan->status_bayar !== 'lunas' && $penjualan->uang_muka > 0)
            <tr>
                <td>Uang Muka (DP):</td>
                <td class="text-right font-mono">Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="font-bold">Sisa Piutang:</td>
                <td class="text-right font-bold font-mono">Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</td>
            </tr>
        @endif
    </table>

    <div class="dashed-line"></div>

    <div class="text-center" style="font-size: {{ $is58mm ? '7.5px' : '8.5px' }}; margin-top: 4px;">
        <div class="font-bold">Terima kasih atas kunjungan Anda!</div>
        <div>{{ $pengaturan->footer_struk ?? 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa perjanjian resmi.' }}</div>
    </div>
</body>
</html>
