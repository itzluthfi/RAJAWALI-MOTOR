<x-app-layout title="Master Customer">
<div
    x-data="formCustomer()"
    x-init="
        @if($errors->any())
            modeEdit = {{ old('_method') === 'PUT' ? 'true' : 'false' }};
            nama = @js(old('nama') ?? '');
            platNomor = @js(old('plat_nomor') ?? '');
            jenisKendaraan = @js(old('jenis_kendaraan') ?? '');
            kategori = @js(old('kategori') ?? 'umum');
            telepon = @js(old('telepon') ?? '');
            noWa = @js(old('no_wa') ?? '');
            alamat = @js(old('alamat') ?? '');
            terminHari = {{ (int) old('termin_hari', 30) }};
            urlUpdate = modeEdit ? `{{ url('/admin/customer') }}/{{ old('_editing_id') }}` : '';
            $dispatch('buka-modal', { name: 'form-customer' });
        @endif
    "
>
    <form method="GET" action="{{ route('customer.index') }}">
        <x-filter-bar>
            <x-input name="cari" value="{{ $filter['cari'] ?? '' }}" label="Cari" placeholder="Nama / Plat / WA / Kendaraan" class="min-w-64" />
            <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
            <div class="ml-auto">
                <x-button type="button" variant="primary" x-on:click="tambah()"><x-icon name="plus" class="w-4 h-4" /> Tambah Customer</x-button>
            </div>
        </x-filter-bar>
    </form>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Nama & Kendaraan</th>
                    <th class="text-left font-semibold px-4 py-2.5">Plat Nomor</th>
                    <th class="text-left font-semibold px-4 py-2.5">Kategori</th>
                    <th class="text-left font-semibold px-4 py-2.5">Kontak & WA</th>
                    <th class="text-left font-semibold px-4 py-2.5">Alamat</th>
                    <th class="text-right font-semibold px-4 py-2.5">Termin</th>
                    <th class="text-left font-semibold px-4 py-2.5">Status</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer as $c)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-medium">
                            <div class="font-bold text-ink">{{ $c->nama }}</div>
                            @if($c->jenis_kendaraan)
                                <div class="text-xs text-steel font-mono">{{ $c->jenis_kendaraan }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $c->plat_nomor ?? '-' }}</td>
                        <td class="px-4 py-2.5">
                            <span class="px-2 py-0.5 rounded text-xs font-bold font-mono {{ $c->kategori === 'grosir' ? 'bg-amber-100 text-amber-800 border border-amber-300' : ($c->kategori === 'mitra' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-slate-100 text-slate-700') }}">
                                {{ strtoupper($c->kategori ?? 'UMUM') }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 font-mono text-xs">
                            <div>Telp: {{ $c->telepon ?? '-' }}</div>
                            @if($c->no_wa)
                                <div class="text-emerald-600 font-bold">WA: {{ $c->no_wa }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-xs text-steel max-w-xs truncate">{{ $c->alamat ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono text-xs">{{ $c->termin_hari }} hr</td>
                        <td class="px-4 py-2.5"><x-badge :status="$c->aktif ? 'lunas' : 'batal'">{{ $c->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex justify-end gap-1">
                                <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" data-tooltip="Ubah Customer"
                                    x-on:click="ubah({{ $c->id }}, @js($c->nama), @js($c->plat_nomor), @js($c->jenis_kendaraan), @js($c->kategori ?? 'umum'), @js($c->telepon), @js($c->no_wa), @js($c->alamat), {{ $c->termin_hari }})">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </button>
                                <form method="POST" action="{{ route('customer.toggle-aktif', $c) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $c->aktif ? 'true' : 'false' }}, @js($c->nama))">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-md text-steel hover:text-rajawali hover:bg-rajawali/5" data-tooltip="{{ $c->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <x-icon :name="$c->aktif ? 'ban' : 'check'" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><x-empty-state icon="users" judul="Belum ada customer" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$customer" />
    </x-card>

    <x-modal name="form-customer" title="Customer">
        <form method="POST" x-bind:action="modeEdit ? urlUpdate : '{{ route('customer.store') }}'">
            @csrf
            <template x-if="modeEdit">
                <div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_editing_id" x-bind:value="idSedangDiubah">
                </div>
            </template>
            <div class="space-y-4">
                <x-input label="Nama Customer" name="nama" x-model="nama" required />
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input label="Plat Nomor (Motor/Mobil)" name="plat_nomor" x-model="platNomor" placeholder="Misal: L 5432 AB" />
                    <x-input label="Jenis Kendaraan (Tipe/Merk)" name="jenis_kendaraan" x-model="jenisKendaraan" placeholder="Misal: Honda Vario 150" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-bold text-steel block mb-1">Kategori Pelanggan</label>
                        <select name="kategori" x-model="kategori" class="w-full rounded-md border border-line bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rajawali">
                            <option value="umum">Umum</option>
                            <option value="mitra">Mitra</option>
                            <option value="grosir">Grosir</option>
                        </select>
                    </div>
                    <x-input label="Termin Kredit (hari)" name="termin_hari" type="number" mono x-model="terminHari" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input label="No. Telepon" name="telepon" x-model="telepon" placeholder="08..." />
                    <x-input label="No. WhatsApp" name="no_wa" x-model="noWa" placeholder="08..." />
                </div>

                <x-input label="Alamat Lengkap" name="alamat" x-model="alamat" />
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'form-customer' })">Batal</x-button>
                <x-button type="submit" variant="primary">Simpan</x-button>
            </div>
        </form>
    </x-modal>
</div>
</x-app-layout>

@if(session('sukses'))
    <script>document.addEventListener('DOMContentLoaded', () => window.toastSukses(@js(session('sukses'))));</script>
@endif
@if($errors->any())
    <script>document.addEventListener('DOMContentLoaded', () => window.toastGagal(@js($errors->first())));</script>
@endif

<script>
function formCustomer() {
    return {
        modeEdit: false,
        nama: '', platNomor: '', jenisKendaraan: '', kategori: 'umum', telepon: '', noWa: '', alamat: '', terminHari: 30,
        urlUpdate: '', idSedangDiubah: null,

        tambah() {
            this.modeEdit = false;
            this.nama = ''; this.platNomor = ''; this.jenisKendaraan = ''; this.kategori = 'umum';
            this.telepon = ''; this.noWa = ''; this.alamat = ''; this.terminHari = 30;
            this.$dispatch('buka-modal', { name: 'form-customer' });
        },

        ubah(id, nama, platNomor, jenisKendaraan, kategori, telepon, noWa, alamat, terminHari) {
            this.modeEdit = true;
            this.nama = nama; this.platNomor = platNomor; this.jenisKendaraan = jenisKendaraan;
            this.kategori = kategori || 'umum'; this.telepon = telepon; this.noWa = noWa;
            this.alamat = alamat; this.terminHari = terminHari || 30;
            this.idSedangDiubah = id;
            this.urlUpdate = `{{ url('/admin/customer') }}/${id}`;
            this.$dispatch('buka-modal', { name: 'form-customer' });
        },

        konfirmasiToggle(event, aktif, nama) {
            const form = event.target;
            window.Swal.fire({
                icon: 'warning',
                title: aktif ? `Nonaktifkan ${nama}?` : `Aktifkan ${nama}?`,
                showCancelButton: true,
                confirmButtonText: aktif ? 'Ya, nonaktifkan' : 'Ya, aktifkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#B0181C',
                reverseButtons: true,
            }).then(hasil => {
                if (hasil.isConfirmed) form.submit();
            });
        },
    };
}
</script>
