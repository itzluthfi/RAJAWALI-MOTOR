<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Label - {{ $barang->nama }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <style>
        @page {
            size: auto;
            margin: 2mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            background-color: #f1f5f9;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .no-print {
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn {
            background: #B0181C;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #1e293b;
        }
        .label-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }
        .sticker-card {
            width: 50mm;
            height: 30mm;
            background: white;
            border: 1px dashed #cbd5e1;
            padding: 2mm 2.5mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .store-name {
            font-size: 8px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 100%;
            border-bottom: 0.5px solid #e2e8f0;
            padding-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .item-name {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.1;
            max-height: 2.2em;
            overflow: hidden;
            text-overflow: ellipsis;
            margin: 1px 0;
            width: 100%;
        }
        .barcode-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            overflow: hidden;
        }
        .barcode-svg {
            max-width: 95%;
            max-height: 12mm;
        }
        .qr-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 14mm;
            height: 14mm;
        }
        .qr-container img {
            width: 100%;
            height: 100%;
        }
        .price-tag {
            font-size: 10px;
            font-weight: 900;
            color: #000;
            font-family: 'Courier New', monospace;
            width: 100%;
            border-top: 0.5px solid #e2e8f0;
            padding-top: 1px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .label-grid {
                gap: 0;
            }
            .sticker-card {
                border: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <span style="font-size: 13px; font-weight: bold;">Label: {{ $barang->nama }} ({{ $jumlah }} Stiker)</span>
        <button class="btn" onclick="window.print()">🖨️ Cetak Stiker Sekarang</button>
        <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
    </div>

    <div class="label-grid">
        @for($i = 0; $i < $jumlah; $i++)
            <div class="sticker-card">
                <div class="store-name">{{ $pengaturan->nama_toko }}</div>
                <div class="item-name">{{ $barang->nama }}</div>
                
                <div class="barcode-container">
                    @if($tipe === 'qr')
                        <div class="qr-container qr-target" data-code="{{ $barcodeUtama }}"></div>
                    @else
                        <svg class="barcode-svg barcode-target" data-code="{{ $barcodeUtama }}"></svg>
                    @endif
                </div>

                <div class="price-tag">
                    Rp {{ number_format((float) $barang->harga_eceran, 0, ',', '.') }}
                    <span style="font-size: 7.5px; font-weight: normal; color: #475569;">({{ $barang->kode }})</span>
                </div>
            </div>
        @endfor
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Render Barcodes
            document.querySelectorAll('.barcode-target').forEach(svg => {
                const code = svg.dataset.code || '123456';
                JsBarcode(svg, code, {
                    format: "CODE128",
                    width: 1.2,
                    height: 35,
                    displayValue: true,
                    fontSize: 8.5,
                    margin: 0
                });
            });

            // Render QR Codes
            document.querySelectorAll('.qr-target').forEach(div => {
                const code = div.dataset.code || '123456';
                new QRCode(div, {
                    text: code,
                    width: 50,
                    height: 50,
                    correctLevel: QRCode.CorrectLevel.M
                });
            });
        });
    </script>
</body>
</html>
