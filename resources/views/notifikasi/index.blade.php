<x-app-layout title="Notifikasi & Aktivitas">

<div x-data="notifHalamanEngine()" class="space-y-4">
    <!-- Filter & Action Header -->
    <x-filter-bar>
        <div class="flex items-center gap-2 flex-wrap">
            <button
                type="button"
                x-on:click="filterKategori = 'semua'"
                :class="filterKategori === 'semua' ? 'bg-rajawali text-white font-bold' : 'bg-surface text-steel hover:bg-canvas border border-line'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150"
            >
                Semua (<span x-text="daftarNotif.length"></span>)
            </button>
            <button
                type="button"
                x-on:click="filterKategori = 'belum_dibaca'"
                :class="filterKategori === 'belum_dibaca' ? 'bg-rajawali text-white font-bold' : 'bg-surface text-steel hover:bg-canvas border border-line'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150 flex items-center gap-1.5"
            >
                Belum Dibaca
                <span x-show="totalBelumDibaca > 0" class="px-1.5 py-0.2 text-[10px] font-black rounded-full bg-white text-rajawali" x-text="totalBelumDibaca" x-cloak></span>
            </button>
            <button
                type="button"
                x-on:click="filterKategori = 'stok'"
                :class="filterKategori === 'stok' ? 'bg-amber-600 text-white font-bold' : 'bg-surface text-steel hover:bg-canvas border border-line'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150"
            >
                ⚠️ Stok
            </button>
            <button
                type="button"
                x-on:click="filterKategori = 'penjualan'"
                :class="filterKategori === 'penjualan' ? 'bg-emerald-600 text-white font-bold' : 'bg-surface text-steel hover:bg-canvas border border-line'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150"
            >
                💰 Penjualan
            </button>
            <button
                type="button"
                x-on:click="filterKategori = 'service'"
                :class="filterKategori === 'service' ? 'bg-blue-600 text-white font-bold' : 'bg-surface text-steel hover:bg-canvas border border-line'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150"
            >
                🛠️ Service
            </button>
            <button
                type="button"
                x-on:click="filterKategori = 'keuangan'"
                :class="filterKategori === 'keuangan' ? 'bg-purple-600 text-white font-bold' : 'bg-surface text-steel hover:bg-canvas border border-line'"
                class="px-3.5 py-1.5 rounded-lg text-xs transition duration-150"
            >
                🏦 Keuangan
            </button>
        </div>

        <div class="ml-auto flex items-center gap-2">
            <x-button variant="secondary" type="button" x-on:click="tandaiSemuaDibaca()" :disabled="totalBelumDibaca === 0">
                <x-icon name="check-check" class="w-4 h-4 text-emerald-600" /> Tandai Semua Dibaca
            </x-button>
        </div>
    </x-filter-bar>

    <!-- Main List Card -->
    <x-card :padded="false" class="overflow-hidden shadow-lg border border-slate-200/80">
        <div class="p-6 border-b border-line bg-surface flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="font-display font-black text-xl text-rajawali tracking-tight">RAJAWALI MOTOR SURABAYA</p>
                <p class="text-sm font-bold text-ink mt-0.5">Pusat Jurnal Notifikasi &amp; Aktivitas Operational</p>
                <p class="text-xs text-steel mt-0.5 italic">Jl. Siwalankerto Timur No. 231A, Surabaya</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-canvas text-steel text-xs font-mono border border-line">
                    <x-icon name="bell" class="w-3.5 h-3.5 text-rajawali" />
                    Total Belum Dibaca: <strong class="text-rajawali" x-text="totalBelumDibaca"></strong>
                </span>
            </div>
        </div>

        <div class="divide-y divide-line">
            <template x-for="item in notifFiltered" :key="item.id">
                <div
                    class="p-4 sm:p-5 hover:bg-canvas transition duration-150 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                    :class="!item.dibaca ? 'bg-amber-500/5' : ''"
                >
                    <div class="flex items-start gap-4 min-w-0">
                        <div
                            class="w-10 h-10 rounded-2xl shrink-0 flex items-center justify-center text-sm border shadow-sm"
                            :class="{
                                'bg-amber-500/10 text-amber-600 border-amber-500/20': item.kategori === 'stok',
                                'bg-emerald-500/10 text-emerald-600 border-emerald-500/20': item.kategori === 'penjualan',
                                'bg-blue-500/10 text-blue-600 border-blue-500/20': item.kategori === 'service',
                                'bg-purple-500/10 text-purple-600 border-purple-500/20': item.kategori === 'keuangan'
                            }"
                        >
                            <template x-if="item.kategori === 'stok'"><x-icon name="triangle-alert" class="w-5 h-5" /></template>
                            <template x-if="item.kategori === 'penjualan'"><x-icon name="receipt" class="w-5 h-5" /></template>
                            <template x-if="item.kategori === 'service'"><x-icon name="wrench" class="w-5 h-5" /></template>
                            <template x-if="item.kategori === 'keuangan'"><x-icon name="hand-coins" class="w-5 h-5" /></template>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-sm text-ink" x-text="item.judul"></h4>
                                <template x-if="!item.dibaca">
                                    <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-rajawali text-white">Baru</span>
                                </template>
                                <span class="text-xs text-steel font-mono">· <span x-text="item.waktu"></span></span>
                            </div>
                            <p class="text-sm text-steel mt-1 leading-relaxed" x-text="item.pesan"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto justify-end border-t sm:border-t-0 border-line/60 pt-3 sm:pt-0">
                        <template x-if="!item.dibaca">
                            <x-button variant="secondary" type="button" class="text-xs px-3 py-1.5" x-on:click="tandaiDibaca(item.id)">
                                <x-icon name="check" class="w-3.5 h-3.5 text-emerald-600" /> Tandai Dibaca
                            </x-button>
                        </template>
                        <x-button variant="primary" type="button" class="text-xs px-3.5 py-1.5" x-on:click="bukaNotif(item)">
                            Buka Modul <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                        </x-button>
                    </div>
                </div>
            </template>

            <template x-if="notifFiltered.length === 0">
                <div class="p-12 text-center text-steel">
                    <x-icon name="bell-off" class="w-10 h-10 mx-auto text-steel/40 mb-2" />
                    <p class="font-bold text-ink text-base">Tidak ada notifikasi pada kategori ini.</p>
                    <p class="text-xs text-steel mt-1">Semua sistem toko &amp; bengkel beroperasi normal.</p>
                </div>
            </template>
        </div>
    </x-card>
</div>

<script>
function notifHalamanEngine() {
    return {
        filterKategori: 'semua',
        daftarNotif: [
            {
                id: 1,
                kategori: 'stok',
                judul: 'Peringatan Stok Menipis',
                pesan: 'OLI FEDERAL MATIC 1L tersisa 3 botol (stok minimum: 10 botol). Segera lakukan pembelian ke supplier.',
                waktu: '5m lalu',
                url: '{{ route('stok.menipis') }}',
                dibaca: false
            },
            {
                id: 2,
                kategori: 'penjualan',
                judul: 'Transaksi Penjualan Baru',
                pesan: 'Nota PJ2026000123 sebesar Rp 480.000 baru saja diterbitkan oleh Kasir.',
                waktu: '25m lalu',
                url: '{{ route('penjualan.index') }}',
                dibaca: false
            },
            {
                id: 3,
                kategori: 'service',
                judul: 'Servis Motor Selesai',
                pesan: 'Servis SV2026000045 (Honda Beat - Andi) telah diselesaikan oleh Montir Wawan.',
                waktu: '1j lalu',
                url: '{{ route('service.index') }}',
                dibaca: false
            },
            {
                id: 4,
                kategori: 'keuangan',
                judul: 'Tagihan Supplier Jatuh Tempo',
                pesan: 'Faktur PT Astra Otoparts (Rp 3.500.000) akan jatuh tempo dalam 3 hari.',
                waktu: 'Kemarin',
                url: '{{ route('keuangan.hutang') }}',
                dibaca: true
            },
            {
                id: 5,
                kategori: 'stok',
                judul: 'Penyesuaian Stok Opname',
                pesan: 'Stok Opname DISC PAD VARIO CBS disesuaikan oleh Gudang (Selisih -2 pcs).',
                waktu: '2 hari lalu',
                url: '{{ route('stok.opname') }}',
                dibaca: true
            }
        ],

        get totalBelumDibaca() {
            return this.daftarNotif.filter(n => !n.dibaca).length;
        },

        get notifFiltered() {
            if (this.filterKategori === 'semua') return this.daftarNotif;
            if (this.filterKategori === 'belum_dibaca') return this.daftarNotif.filter(n => !n.dibaca);
            return this.daftarNotif.filter(n => n.kategori === this.filterKategori);
        },

        tandaiDibaca(id) {
            const item = this.daftarNotif.find(n => n.id === id);
            if (item) {
                item.dibaca = true;
                if (window.toastSukses) window.toastSukses('Notifikasi ditandai sudah dibaca.');
            }
        },

        tandaiSemuaDibaca() {
            this.daftarNotif.forEach(n => n.dibaca = true);
            if (window.toastSukses) window.toastSukses('Semua notifikasi ditandai sudah dibaca.');
        },

        bukaNotif(item) {
            item.dibaca = true;
            if (item.url) {
                window.location.href = item.url;
            }
        }
    };
}
</script>

</x-app-layout>
