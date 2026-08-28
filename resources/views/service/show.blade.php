@php
    $tahapan = ['Masuk', 'Dikerjakan', 'Selesai', 'Diambil', 'Lunas'];
    $tahapMap = [
        'masuk' => 0,
        'dikerjakan' => 1,
        'selesai' => 2,
        'diambil' => 3,
        'lunas' => 4,
    ];
    $tahapAktif = $tahapMap[$service->status] ?? 0;
    
    $statusNextMap = [
        'masuk' => 'dikerjakan',
        'dikerjakan' => 'selesai',
        'selesai' => 'diambil',
        'diambil' => 'lunas',
    ];
    $nextStatus = $statusNextMap[$service->status] ?? null;
@endphp
<x-app-layout title="Detail Service {{ $id }}">
    
    @if(session('sukses'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600" />
            <span>{{ session('sukses') }}</span>
        </div>
    @endif

    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <div>
            <h2 class="font-display font-black text-xl text-rajawali font-mono">{{ $service->nomor_dokumen }}</h2>
            <p class="text-xs text-steel mt-0.5">
                Motor: <strong class="text-ink">{{ $service->merk_type ?? '-' }}</strong> · 
                Customer: <strong class="text-ink">{{ $service->customer->nama }}</strong> · 
                Montir: <strong class="text-ink">{{ $service->montir->name ?? '-' }}</strong>
            </p>
        </div>
        @php
            $badgeStatus = match($service->status) {
                'lunas' => 'lunas',
                'masuk' => 'batal',
                default => 'proses',
            };
        @endphp
        <x-badge :status="$badgeStatus">{{ ucfirst($service->status) }}</x-badge>
    </div>

    {{-- PROGRESS WORK ORDER --}}
    <x-card class="mb-4 shadow-lg border border-slate-200/80">
        <div class="flex items-center">
            @foreach($tahapan as $i => $t)
                <div class="flex items-center flex-1 last:flex-none">
                    <div class="flex flex-col items-center gap-1">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $i <= $tahapAktif ? 'bg-rajawali text-white shadow-xs' : 'bg-line text-steel' }}">
                            {{ $i + 1 }}
                        </div>
                        <span class="text-[11px] {{ $i <= $tahapAktif ? 'text-ink font-bold' : 'text-steel' }}">{{ $t }}</span>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-0.5 mx-2 {{ $i < $tahapAktif ? 'bg-rajawali' : 'bg-line' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        {{-- METODE & OUTSOURCE --}}
        <x-card class="lg:col-span-2 shadow-lg border border-slate-200/80">
            <h3 class="font-display font-bold text-sm text-ink mb-3 flex items-center gap-2">
                <x-icon name="clipboard" class="w-4 h-4 text-rajawali" /> Detail Diagnosa &amp; Pengerjaan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-steel block text-[11px] uppercase font-bold">Metode Pengerjaan:</span>
                    <strong class="text-ink text-sm">{{ $service->repaired_by === 'intern' ? 'Bengkel Sendiri (Intern)' : 'Bengkel Luar (Outsource)' }}</strong>
                </div>
                <div>
                    <span class="text-steel block text-[11px] uppercase font-bold">No WhatsApp / Telepon:</span>
                    <strong class="text-ink text-sm font-mono">{{ $service->customer->telepon ?: ($service->customer->no_wa ?: '-') }}</strong>
                </div>
                @if($service->repaired_by === 'extern')
                    <div>
                        <span class="text-steel block text-[11px] uppercase font-bold">Bengkel Rekanan:</span>
                        <strong class="text-ink">{{ $service->supplier->nama ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-steel block text-[11px] uppercase font-bold">Tanggal Kirim / Kembali:</span>
                        <strong class="text-ink font-mono">{{ $service->tanggal_kirim?->format('d M Y') ?? '-' }} ➔ {{ $service->tanggal_kembali?->format('d M Y') ?? '-' }}</strong>
                    </div>
                @endif
                <div class="sm:col-span-2 border-t border-line pt-2 mt-2">
                    <span class="text-steel block text-[11px] uppercase font-bold">Keluhan Pelanggan:</span>
                    <p class="text-ink italic bg-canvas p-2.5 rounded-lg mt-1 font-medium">{{ $service->keluhan ?? 'Tidak ada keluhan tertulis' }}</p>
                </div>
                @if($service->catatan)
                    <div class="sm:col-span-2 border-t border-line pt-2">
                        <span class="text-steel block text-[11px] uppercase font-bold">Catatan Tambahan:</span>
                        <p class="text-ink mt-0.5">{{ $service->catatan }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- SUMMARY KEUANGAN --}}
        <x-card class="flex flex-col justify-between shadow-lg border border-slate-200/80">
            <h3 class="font-display font-bold text-sm text-ink mb-3 flex items-center gap-2">
                <x-icon name="calculator" class="w-4 h-4 text-emerald-600" /> Ringkasan Biaya
            </h3>
            <div class="space-y-2 text-xs">
                @if($service->repaired_by === 'extern')
                    <div class="flex justify-between text-steel">
                        <span>Biaya Bengkel Luar:</span>
                        <span class="font-mono font-bold text-ink">Rp {{ number_format($service->grand_total_supplier, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-steel">
                    <span>Total Jasa &amp; Part:</span>
                    <span class="font-mono font-bold text-ink">Rp {{ number_format($service->grand_total_nett, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-line">
                    <span class="font-bold text-ink uppercase">TOTAL NETT:</span>
                    <span class="font-mono font-black text-xl text-rajawali">Rp {{ number_format($service->grand_total_nett, 0, ',', '.') }}</span>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        {{-- JASA --}}
        <x-card :padded="false" class="shadow-lg border border-slate-200/80 overflow-hidden flex flex-col">
            <div class="p-3.5 border-b border-line bg-surface">
                <h3 class="font-display font-bold text-sm text-ink flex items-center gap-2">
                    <x-icon name="sparkles" class="w-4 h-4 text-amber-500" /> Rincian Jasa Servis
                </h3>
            </div>
            <table class="w-full text-xs">
                <thead class="bg-canvas text-steel uppercase font-bold border-b border-line">
                    <tr>
                        <th class="text-left px-3 py-2.5">Nama Layanan Jasa</th>
                        @if($service->repaired_by === 'extern')
                            <th class="text-right px-3 py-2.5 w-28">Biaya Luar</th>
                        @endif
                        <th class="text-right px-3 py-2.5 w-32">Tarif Jasa (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($service->jasas as $jasa)
                        <tr class="hover:bg-canvas/50 transition">
                            <td class="px-3 py-2.5 font-bold text-ink">{{ $jasa->nama_jasa }}</td>
                            @if($service->repaired_by === 'extern')
                                <td class="px-3 py-2.5 text-right font-mono text-steel">Rp {{ number_format($jasa->harga_supplier, 0, ',', '.') }}</td>
                            @endif
                            <td class="px-3 py-2.5 text-right font-mono font-bold text-ink">Rp {{ number_format($jasa->harga_nett, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td :colspan="{{ $service->repaired_by === 'extern' ? 3 : 2 }}" class="text-center text-steel py-6 italic">Tidak ada komponen jasa servis</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        {{-- SPAREPARTS --}}
        <x-card :padded="false" class="shadow-lg border border-slate-200/80 overflow-hidden flex flex-col">
            <div class="p-3.5 border-b border-line bg-surface">
                <h3 class="font-display font-bold text-sm text-ink flex items-center gap-2">
                    <x-icon name="package" class="w-4 h-4 text-rajawali" /> Sparepart Yang Digunakan
                </h3>
            </div>
            <table class="w-full text-xs">
                <thead class="bg-canvas text-steel uppercase font-bold border-b border-line">
                    <tr>
                        <th class="text-left px-3 py-2.5">Barang</th>
                        <th class="text-right px-3 py-2.5 w-16">Qty</th>
                        <th class="text-right px-3 py-2.5 w-24">Harga (Rp)</th>
                        <th class="text-right px-3 py-2.5 w-28">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($service->details as $detail)
                        <tr class="hover:bg-canvas/50 transition">
                            <td class="px-3 py-2.5 font-bold text-ink">{{ $detail->barang->nama }}</td>
                            <td class="px-3 py-2.5 text-right font-mono">{{ rtrim(rtrim(number_format((float) $detail->qty, 3, ',', ''), '0'), ',') }}</td>
                            <td class="px-3 py-2.5 text-right font-mono text-steel">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold text-ink">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-steel py-6 italic">Tidak ada sparepart digunakan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>

    {{-- BOTTOM ACTION TOOLBAR --}}
    <div class="p-4 bg-surface rounded-xl border border-line flex flex-wrap items-center justify-between gap-3 shadow-md">
        <div class="flex items-center gap-2 flex-wrap">
            @php
                $noHpTarget = $service->customer->telepon ?: $service->customer->no_wa;
                $teksWa = \App\Services\WhatsAppReceiptService::buatTeksServis($service);
                $waUrl = \App\Services\WhatsAppReceiptService::buatUrlWhatsApp($noHpTarget ?? '', $teksWa);
            @endphp
            <a href="{{ $waUrl }}" target="_blank" class="px-4 py-2.5 rounded-lg bg-[#25D366] hover:bg-[#1ebd59] text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition" data-tooltip="Kirim status rincian servis via WhatsApp">
                <x-icon name="whatsapp" class="w-4 h-4 text-white" />
                <span>Kirim WA Servis ({{ $noHpTarget ?: 'Tanpa No HP' }})</span>
            </a>

            <a href="{{ route('cetak.tanda-terima-service', $service->nomor_dokumen) }}" target="_blank" class="px-4 py-2.5 rounded-lg border border-line bg-white hover:bg-canvas text-xs font-bold text-ink flex items-center gap-1.5 shadow-xs transition" data-tooltip="Cetak Tanda Terima Servis">
                <x-icon name="printer" class="w-4 h-4 text-rajawali" />
                <span>Cetak Tanda Terima</span>
            </a>

            @if($service->repaired_by === 'extern')
                <a href="{{ route('cetak.surat-jalan', $service->nomor_dokumen) }}" target="_blank" class="px-4 py-2.5 rounded-lg border border-line bg-white hover:bg-canvas text-xs font-bold text-ink flex items-center gap-1.5 shadow-xs transition" data-tooltip="Cetak Surat Jalan ke Bengkel Luar">
                    <x-icon name="truck" class="w-4 h-4 text-blue-600" />
                    <span>Surat Jalan</span>
                </a>
            @endif
        </div>

        @if($nextStatus)
            <form method="POST" action="{{ route('service.status', $service->id) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#B0181C] hover:bg-[#8f1013] text-white text-xs font-bold flex items-center gap-2 shadow-md transition cursor-pointer active:scale-98">
                    <span>Lanjutkan Ke: {{ ucfirst($nextStatus) }}</span>
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </button>
            </form>
        @else
            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3.5 py-2 rounded-lg border border-emerald-300 flex items-center gap-1.5">
                <x-icon name="check" class="w-4 h-4 text-emerald-700" />
                <span>Servis Selesai &amp; Lunas</span>
            </span>
        @endif
    </div>

</x-app-layout>
