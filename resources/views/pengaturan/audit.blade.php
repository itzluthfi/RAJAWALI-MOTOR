<x-app-layout title="Audit Log System">
    <x-filter-bar class="no-print" action="{{ route('pengaturan.audit') }}" method="GET">
        <x-select name="user_id" label="User / Pengguna">
            <option value="semua">Semua User</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
            @endforeach
        </x-select>
        <x-select name="modul" label="Modul Fitur">
            <option value="semua">Semua Modul</option>
            <option value="Kasir POS" {{ request('modul') === 'Kasir POS' ? 'selected' : '' }}>Kasir POS</option>
            <option value="Penjualan" {{ request('modul') === 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
            <option value="Pembelian" {{ request('modul') === 'Pembelian' ? 'selected' : '' }}>Pembelian</option>
            <option value="Retur" {{ request('modul') === 'Retur' ? 'selected' : '' }}>Retur</option>
            <option value="Service" {{ request('modul') === 'Service' ? 'selected' : '' }}>Service Bengkel</option>
            <option value="Barang" {{ request('modul') === 'Barang' ? 'selected' : '' }}>Master Barang</option>
            <option value="Pengaturan" {{ request('modul') === 'Pengaturan' ? 'selected' : '' }}>Pengaturan</option>
        </x-select>
        <x-input type="date" name="tanggal" label="Tanggal" value="{{ request('tanggal') }}" />
        <x-button type="submit" variant="primary"><x-icon name="search" class="w-4 h-4" /> Filter</x-button>
        <div class="ml-auto">
            <x-button type="button" variant="secondary" onclick="window.print()">
                <x-icon name="printer" class="w-4 h-4 text-rajawali" /> Cetak Log
            </x-button>
        </div>
    </x-filter-bar>

    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex justify-between items-center print-header">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SIDOARJO</p>
                <p class="text-sm font-bold text-ink mt-0.5">Jurnal Audit Aktivitas &amp; Perubahan System Real-time</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Samanhudi No.102, Jasem, Sidoarjo</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                    Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#B0181C] text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Waktu &amp; Tanggal</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">User / Pengguna</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Modul</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Aksi</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Objek</th>
                        <th class="text-left font-bold px-4 py-3 bg-[#B0181C] text-white border-b border-red-900">Detail Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $a)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                            <td class="px-4 py-3 font-mono text-xs text-steel whitespace-nowrap">{{ $a->created_at->format('d M Y H:i') }} WIB</td>
                            <td class="px-4 py-3 font-bold text-ink">
                                {{ $a->nama_user }}
                                @if($a->ip_address)
                                    <span class="text-[10px] text-steel font-mono block font-normal">{{ $a->ip_address }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-800 font-bold border border-slate-200">
                                    {{ $a->modul }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-ink">{{ $a->aksi }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-rajawali font-bold">{{ $a->objek ?? '-' }}</td>
                            <td class="px-4 py-3 text-steel text-xs font-mono">{{ $a->perubahan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-steel italic">Belum ada jurnal aktivitas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($auditLogs->hasPages())
            <div class="p-4 border-t border-line no-print">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </x-card>
</x-app-layout>
