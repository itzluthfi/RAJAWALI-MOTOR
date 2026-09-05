@php
    $realId = \App\Services\IdHasher::decode($id);
    $penjualan = \App\Models\Penjualan::with(['customer', 'user', 'details.barang'])
        ->where('nomor_nota', $id)
        ->orWhere('id', $realId)
        ->orWhere('id', is_numeric($id) ? (int)$id : 0)
        ->first();
    $pengaturan = \App\Models\PengaturanToko::current();
@endphp
<x-print-layout title="Nota {{ $id }}">
    <x-slot:actions>
        <div class="inline-flex rounded-xl bg-slate-800 p-1 border border-slate-700 text-xs" x-data="{
            size: new URLSearchParams(window.location.search).get('size') || localStorage.getItem('thermal_paper_size') || '80',
            pilihSize(s) {
                this.size = s;
                localStorage.setItem('thermal_paper_size', s);
                const url = new URL(window.location);
                url.searchParams.set('size', s);
                window.history.replaceState({}, '', url);
                
                // Update pdf link
                const pdfBtn = document.querySelector('a[href*=\'/pdf\']');
                if (pdfBtn) {
                    const pdfUrl = new URL(pdfBtn.href);
                    pdfUrl.searchParams.set('size', s);
                    pdfBtn.href = pdfUrl.toString();
                }

                // Dispatch event ke nota
                window.dispatchEvent(new CustomEvent('change-paper-size', { detail: s }));
            }
        }">
            <button type="button" @click="pilihSize('80')" :class="size === '80' ? 'bg-[#B0181C] text-white shadow font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-lg transition text-xs flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full" :class="size === '80' ? 'bg-white' : 'bg-slate-500'"></span>
                <span>Thermal 80mm</span>
            </button>
            <button type="button" @click="pilihSize('58')" :class="size === '58' ? 'bg-[#B0181C] text-white shadow font-bold' : 'text-slate-300 hover:text-white'" class="px-3 py-1.5 rounded-lg transition text-xs flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full" :class="size === '58' ? 'bg-white' : 'bg-slate-500'"></span>
                <span>Thermal 58mm</span>
            </button>
        </div>
    </x-slot:actions>

    <style>
        /* Mode Layar (Screen Preview): Tampilan kartu struk kasir realistis */
        .thermal-receipt-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            padding: 12px 10px 40px;
        }
        
        .thermal-receipt {
            width: 100%;
            max-width: 380px; /* Ukuran preview fisik 80mm di layar */
            background: #ffffff;
            color: #0f172a;
            font-family: 'Consolas', 'SFMono-Regular', 'Menlo', 'Monaco', 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
            border: 1px solid #cbd5e1;
            padding: 24px 20px;
            margin: 0 auto;
            box-sizing: border-box;
            transition: max-width 0.2s ease, padding 0.2s ease, font-size 0.2s ease;
        }

        .thermal-receipt.size-58 {
            max-width: 280px; /* Ukuran preview fisik 58mm di layar */
            padding: 16px 14px;
            font-size: 10.5px;
            line-height: 1.35;
        }

        .dashed-line {
            border-top: 1px dashed #475569;
            margin: 10px 0;
            width: 100%;
        }

        .solid-line {
            border-top: 1.5px solid #0f172a;
            margin: 10px 0;
            width: 100%;
        }

        /* Mode Cetak Printer Thermal Asli (Print) */
        @media print {
            @page {
                size: var(--thermal-print-size, 80mm) auto;
                margin: 0 !important;
            }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .thermal-receipt-container {
                padding: 0 !important;
                margin: 0 !important;
                display: block !important;
            }
            .thermal-receipt {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 2mm 3mm !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                font-size: var(--thermal-font-size, 11px) !important;
                color: #000000 !important;
            }
            .dashed-line {
                border-top: 1px dashed #000000 !important;
                margin: 6px 0 !important;
            }
            .solid-line {
                border-top: 1.5px solid #000000 !important;
                margin: 6px 0 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <div x-data="{
        paperSize: new URLSearchParams(window.location.search).get('size') || localStorage.getItem('thermal_paper_size') || '80',
        init() {
            window.addEventListener('change-paper-size', (e) => {
                this.paperSize = e.detail;
            });
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(() => window.print(), 500);
            }
        }
    }" class="thermal-receipt-container">

    @if($penjualan)
    <div :class="paperSize === '58' ? 'size-58' : ''"
         :style="'--thermal-print-size: ' + (paperSize === '58' ? '58mm' : '80mm') + '; --thermal-font-size: ' + (paperSize === '58' ? '9.5px' : '11px') + ';'"
         class="thermal-receipt">
        
        <!-- Header Toko -->
        <div class="text-center space-y-1 mb-2">
            @if($pengaturan->logo_url)
                <img alt="{{ $pengaturan->nama_toko }} Logo" class="h-10 w-auto mx-auto object-contain mb-1.5 grayscale" src="{{ $pengaturan->logo_url }}"/>
            @endif
            <h1 class="font-black text-sm tracking-wider text-black leading-tight uppercase">{{ $pengaturan->nama_toko }}</h1>
            @if($pengaturan->slogan)
                <p class="text-[10px] text-black/80 italic leading-tight">{{ $pengaturan->slogan }}</p>
            @endif
            <p class="text-[10.5px] text-black leading-tight">{{ $pengaturan->alamat }}</p>
            @if($pengaturan->telepon)
                <p class="text-[10.5px] text-black leading-tight font-medium">Telp/WA: {{ $pengaturan->telepon }}</p>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- Meta Nota -->
        <div class="space-y-0.5 text-[11px] text-black">
            <div class="flex justify-between items-center">
                <span>No. Nota:</span>
                <span class="font-bold font-mono">{{ $penjualan->nomor_nota }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span>Tanggal:</span>
                <span>{{ $penjualan->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span>Customer:</span>
                <span class="font-bold">{{ $penjualan->customer->nama ?? 'Umum' }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span>Kasir:</span>
                <span>{{ $penjualan->user->name ?? 'Staff' }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        <!-- Daftar Barang -->
        @php
            $totalHematSemua = 0;
        @endphp
        <div class="space-y-2 my-1 text-black">
            @foreach($penjualan->details as $d)
                @php
                    $hargaNormal = $d->barang ? (float) $d->barang->harga_eceran : (float) $d->harga_satuan;
                    $adaDiskonItem = (float) $d->diskon > 0;
                    $adaTierHemat = $hargaNormal > (float) $d->harga_satuan;
                    $hematPerPcs = $adaTierHemat ? ($hargaNormal - (float) $d->harga_satuan) : 0;
                    
                    if ($adaTierHemat) {
                        $totalHematSemua += ($hematPerPcs * (float) $d->qty);
                    }
                    if ($adaDiskonItem) {
                        $totalHematSemua += (float) $d->diskon;
                    }
                @endphp
                <div>
                    <div class="font-bold text-black leading-tight break-words">{{ $d->barang->nama }}</div>
                    @if($adaTierHemat)
                        <div class="text-[10px] text-black font-semibold flex items-center gap-1 mt-0.5">
                            <span>* Khusus (Normal: {{ number_format($hargaNormal, 0, ',', '.') }})</span>
                            <span>[Hemat {{ number_format($hematPerPcs, 0, ',', '.') }}/pcs]</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center text-black text-[11px] mt-0.5">
                        <span>
                            {{ rtrim(rtrim(number_format((float) $d->qty, 3, ',', ''), '0'), ',') }} x 
                            {{ number_format($d->harga_satuan, 0, ',', '.') }}
                            @if($adaDiskonItem)
                                <span class="font-semibold">(-{{ number_format($d->diskon, 0, ',', '.') }})</span>
                            @endif
                        </span>
                        <span class="font-bold text-black font-mono">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @php
            $totalHematSemua += (float) $penjualan->diskon;
        @endphp

        <div class="solid-line"></div>

        <!-- Rincian Total -->
        <div class="space-y-1 text-[11.5px] text-black">
            <div class="flex justify-between items-center font-bold">
                <span>SUBTOTAL</span>
                <span class="font-mono">Rp {{ number_format($penjualan->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->diskon > 0)
                <div class="flex justify-between items-center font-medium">
                    <span>Diskon Nota</span>
                    <span class="font-mono">-Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
                </div>
            @endif
            @if($penjualan->pajak > 0)
                <div class="flex justify-between items-center">
                    <span>Pajak</span>
                    <span class="font-mono">Rp {{ number_format($penjualan->pajak, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center font-black text-sm text-black pt-1 border-t-2 border-black">
                <span>GRAND TOTAL</span>
                <span class="font-mono font-black">Rp {{ number_format($penjualan->total_akhir, 0, ',', '.') }}</span>
            </div>
            @if($totalHematSemua > 0)
                <div class="flex justify-between items-center text-black font-bold text-[11px] border border-dashed border-black px-2 py-1 mt-1.5">
                    <span>TOTAL HEMAT (DISKON):</span>
                    <span class="font-mono font-black">Rp {{ number_format($totalHematSemua, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>
        
        <div class="dashed-line"></div>

        <!-- Status Pembayaran -->
        @if($penjualan->status_bayar === 'lunas')
            <div class="flex justify-between items-center font-bold text-xs py-0.5 text-black">
                <span>Status:</span>
                <span class="font-black uppercase">[ LUNAS ({{ strtoupper($penjualan->metode_pembayaran) }}) ]</span>
            </div>
        @else
            <div class="flex justify-between items-center font-bold text-xs py-0.5 text-black">
                <span>Status:</span>
                <span class="font-black uppercase">[ TEMPO / PIUTANG ]</span>
            </div>
            @if($penjualan->uang_muka > 0)
                <div class="flex justify-between items-center text-[10.5px] text-black">
                    <span>Uang Muka (DP):</span>
                    <span class="font-mono">Rp {{ number_format($penjualan->uang_muka, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center font-bold text-[11px] text-black">
                <span>Sisa Piutang:</span>
                <span class="font-mono font-black">Rp {{ number_format(max(0, $penjualan->total_akhir - $penjualan->uang_muka), 0, ',', '.') }}</span>
            </div>
            @if($penjualan->jatuh_tempo)
                <div class="flex justify-between items-center text-[10.5px] text-black">
                    <span>Jatuh Tempo:</span>
                    <span>{{ $penjualan->jatuh_tempo->format('d/m/Y') }}</span>
                </div>
            @endif
        @endif

        <div class="dashed-line"></div>

        <!-- Footer Nota -->
        <div class="text-center space-y-1 text-[10.5px] pt-1 text-black">
            <p class="font-bold">Terima kasih atas kunjungan Anda!</p>
            <p class="leading-snug text-black/80">{{ $pengaturan->footer_struk ?? 'Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa perjanjian resmi.' }}</p>
        </div>
    </div>
    @else
    <div class="p-8 text-center text-slate-600 font-medium">Nota Penjualan tidak ditemukan.</div>
    @endif
    </div>
</x-print-layout>
