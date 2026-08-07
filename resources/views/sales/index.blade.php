<x-app-layout title="Master Sales">
<div
    x-data="formSales()"
    x-init="
        @if($errors->any())
            modeEdit = {{ old('_method') === 'PUT' ? 'true' : 'false' }};
            kodeSales = @js(old('kode_sales') ?? '');
            nama = @js(old('nama') ?? '');
            alamat = @js(old('alamat') ?? '');
            kota = @js(old('kota') ?? '');
            telepon = @js(old('telepon') ?? '');
            persentaseKomisi = {{ (float) old('persentase_komisi', 0) }};
            urlUpdate = modeEdit ? `{{ url('/admin/sales') }}/{{ old('_editing_id') }}` : '';
            $dispatch('buka-modal', { name: 'form-sales' });
        @endif
    "
>
    <form method="GET" action="{{ route('sales.index') }}">
        <x-filter-bar>
            <x-input name="cari" value="{{ $filter['cari'] ?? '' }}" label="Cari" placeholder="Nama / telepon" class="min-w-64" />
            <x-button type="submit" variant="secondary"><x-icon name="search" class="w-4 h-4" /> Cari</x-button>
            <div class="ml-auto">
                <x-button type="button" variant="primary" x-on:click="tambah()"><x-icon name="plus" class="w-4 h-4" /> Tambah Sales</x-button>
            </div>
        </x-filter-bar>
    </form>

    <x-card :padded="false">
        <table class="w-full text-sm">
            <thead class="bg-canvas text-steel text-xs uppercase tracking-wide border-b border-line">
                <tr>
                    <th class="text-left font-semibold px-4 py-2.5">Kode</th>
                    <th class="text-left font-semibold px-4 py-2.5">Nama</th>
                    <th class="text-left font-semibold px-4 py-2.5">Alamat / Kota</th>
                    <th class="text-left font-semibold px-4 py-2.5">Telepon</th>
                    <th class="text-right font-semibold px-4 py-2.5">Komisi</th>
                    <th class="text-left font-semibold px-4 py-2.5">Status</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $s)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-mono text-xs font-bold text-rajawali">{{ $s->kode_sales }}</td>
                        <td class="px-4 py-2.5 font-medium">{{ $s->nama }}</td>
                        <td class="px-4 py-2.5 text-xs text-steel">
                            {{ $s->alamat ?? '-' }} {{ $s->kota ? '(' . $s->kota . ')' : '' }}
                        </td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $s->telepon ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ rtrim(rtrim(number_format((float) $s->persentase_komisi, 2, ',', ''), '0'), ',') }}%</td>
                        <td class="px-4 py-2.5"><x-badge :status="$s->aktif ? 'lunas' : 'batal'">{{ $s->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex justify-end gap-1">
                                <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" title="Ubah Sales" data-tooltip="Ubah Sales"
                                    x-on:click="ubah({{ $s->id }}, @js($s->kode_sales), @js($s->nama), @js($s->alamat), @js($s->kota), @js($s->telepon), {{ $s->persentase_komisi }})">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </button>
                                <form method="POST" action="{{ route('sales.toggle-aktif', $s) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $s->aktif ? 'true' : 'false' }}, @js($s->nama))">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-md text-steel hover:text-rajawali hover:bg-rajawali/5" title="{{ $s->aktif ? 'Nonaktifkan' : 'Aktifkan' }}" data-tooltip="{{ $s->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <x-icon :name="$s->aktif ? 'ban' : 'check'" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state icon="user-check" judul="Belum ada sales" /></td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$sales" />
    </x-card>

    <x-modal name="form-sales" title="Sales">
        <form method="POST" x-bind:action="modeEdit ? urlUpdate : '{{ route('sales.store') }}'">
            @csrf
            <template x-if="modeEdit">
                <div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_editing_id" x-bind:value="idSedangDiubah">
                </div>
            </template>
            <div class="space-y-4">
                <x-input label="Kode Sales" name="kode_sales" x-model="kodeSales" required x-bind:readonly="modeEdit" />
                <x-input label="Nama Sales" name="nama" x-model="nama" required />
                <x-input label="Alamat" name="alamat" x-model="alamat" />
                <x-input label="Kota" name="kota" x-model="kota" />
                <x-input label="Telepon" name="telepon" x-model="telepon" />
                <x-input label="Persentase Komisi (%)" name="persentase_komisi" type="number" step="0.01" min="0" max="100" mono x-model="persentaseKomisi" required />
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('tutup-modal', { name: 'form-sales' })">Batal</x-button>
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
function formSales() {
    return {
        modeEdit: false,
        kodeSales: '', nama: '', alamat: '', kota: '', telepon: '', persentaseKomisi: 0,
        urlUpdate: '', idSedangDiubah: null,

        tambah() {
            this.modeEdit = false;
            this.kodeSales = ''; this.nama = ''; this.alamat = ''; this.kota = ''; this.telepon = ''; this.persentaseKomisi = 0;
            this.$dispatch('buka-modal', { name: 'form-sales' });
        },

        ubah(id, kodeSales, nama, alamat, kota, telepon, persentaseKomisi) {
            this.modeEdit = true;
            this.kodeSales = kodeSales; this.nama = nama; this.alamat = alamat; this.kota = kota; this.telepon = telepon; this.persentaseKomisi = persentaseKomisi;
            this.idSedangDiubah = id;
            this.urlUpdate = `{{ url('/admin/sales') }}/${id}`;
            this.$dispatch('buka-modal', { name: 'form-sales' });
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
