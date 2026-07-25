<x-app-layout title="Master Customer">
<div
    x-data="formCustomer()"
    x-init="
        @if($errors->any())
            modeEdit = {{ old('_method') === 'PUT' ? 'true' : 'false' }};
            nama = @js(old('nama') ?? '');
            telepon = @js(old('telepon') ?? '');
            alamat = @js(old('alamat') ?? '');
            terminHari = {{ (int) old('termin_hari', 0) }};
            urlUpdate = modeEdit ? `{{ url('/admin/customer') }}/{{ old('_editing_id') }}` : '';
            $dispatch('buka-modal', { name: 'form-customer' });
        @endif
    "
>
    <form method="GET" action="{{ route('customer.index') }}">
        <x-filter-bar>
            <x-input name="cari" value="{{ $filter['cari'] ?? '' }}" label="Cari" placeholder="Nama / telepon" class="min-w-64" />
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
                    <th class="text-left font-semibold px-4 py-2.5">Nama</th>
                    <th class="text-left font-semibold px-4 py-2.5">Telepon</th>
                    <th class="text-right font-semibold px-4 py-2.5">Termin (hari)</th>
                    <th class="text-left font-semibold px-4 py-2.5">Status</th>
                    <th class="text-right font-semibold px-4 py-2.5">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer as $c)
                    <tr class="border-b border-line last:border-0 hover:bg-canvas transition duration-150">
                        <td class="px-4 py-2.5 font-medium">{{ $c->nama }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $c->telepon ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-mono">{{ $c->termin_hari }}</td>
                        <td class="px-4 py-2.5"><x-badge :status="$c->aktif ? 'lunas' : 'batal'">{{ $c->aktif ? 'Aktif' : 'Nonaktif' }}</x-badge></td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex justify-end gap-1">
                                <button type="button" class="p-1.5 rounded-md text-steel hover:text-ink hover:bg-canvas" title="Ubah Customer" data-tooltip="Ubah Customer"
                                    x-on:click="ubah({{ $c->id }}, @js($c->nama), @js($c->telepon), @js($c->alamat), {{ $c->termin_hari }})">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </button>
                                <form method="POST" action="{{ route('customer.toggle-aktif', $c) }}" x-on:submit.prevent="konfirmasiToggle($event, {{ $c->aktif ? 'true' : 'false' }}, @js($c->nama))">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-md text-steel hover:text-rajawali hover:bg-rajawali/5" title="{{ $c->aktif ? 'Nonaktifkan' : 'Aktifkan' }}" data-tooltip="{{ $c->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <x-icon :name="$c->aktif ? 'ban' : 'check'" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><x-empty-state icon="users" judul="Belum ada customer" /></td></tr>
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
                <x-input label="Telepon" name="telepon" x-model="telepon" />
                <x-input label="Alamat" name="alamat" x-model="alamat" />
                <x-input label="Termin (hari)" name="termin_hari" type="number" mono x-model="terminHari" required />
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
        nama: '', telepon: '', alamat: '', terminHari: 0,
        urlUpdate: '', idSedangDiubah: null,

        tambah() {
            this.modeEdit = false;
            this.nama = ''; this.telepon = ''; this.alamat = ''; this.terminHari = 0;
            this.$dispatch('buka-modal', { name: 'form-customer' });
        },

        ubah(id, nama, telepon, alamat, terminHari) {
            this.modeEdit = true;
            this.nama = nama; this.telepon = telepon; this.alamat = alamat; this.terminHari = terminHari;
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
